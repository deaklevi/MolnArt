<?php

namespace App\Services;

use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class AppointmentService
{
    public function book(StoreAppointmentRequest $request, int $userId): Appointment
    {
        return DB::transaction(function () use ($request, $userId) {

            $data = $request->validated();

            // conflict check
            $conflict = Appointment::where('user_id', $userId)
                ->where('appointment_from', '<', $data['appointment_to'])
                ->where('appointment_to', '>', $data['appointment_from'])
                ->exists();

            if ($conflict) {
                throw new \RuntimeException('Time slot taken');
            }

            // customer
            $customer = Customer::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'phone_number' => $data['phone_number'],
                    'user_id' => $userId,
                ]
            );

            // appointment
            $appointment = Appointment::create([
                'appointment_from' => $data['appointment_from'],
                'appointment_to'   => $data['appointment_to'],
                'service'          => $data['service'],
                'customer_id'      => $customer->id,
                'user_id'          => $userId,
            ]);

            // products
            foreach ($data['used_products'] ?? [] as $item) {

                $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                if ($product->stock < $item['quantity']) {
                    throw new \RuntimeException("Out of stock: {$product->name}");
                }

                $appointment->products()->attach($product->id, [
                    'quantity' => $item['quantity'],
                ]);

                $product->decrement('stock', $item['quantity']);
            }

            return $appointment->load('customer', 'products');
        });

    }

    public function update(UpdateAppointmentRequest $request, Appointment $appointment): Appointment
    {
        return DB::transaction(function () use ($request, $appointment) {
            $data = $request->validated();
    
            $appointment->update(
                collect($data)->only([
                    'service',
                    'appointment_from',
                    'appointment_to'
                ])->toArray()
            );
    
            if ($appointment->customer) {
                $appointment->customer->update(
                    collect($data)->only(['name', 'email', 'phone_number'])->toArray()
                );
            }
    
            if (isset($data['used_products'])) {
                $this->syncProducts($appointment, $data['used_products']);
            }
    
            return $appointment->load('customer', 'products');
        });
    }

    private function syncProducts(Appointment $appointment, array $incomingProducts): void
    {
        // Key current pivot by product_id for easy lookup
        $current = $appointment->products()
            ->withPivot('quantity')
            ->get()
            ->keyBy('id');
    
        $incoming = collect($incomingProducts)->keyBy('product_id');
    
        // 1. Restore stock for removed products
        foreach ($current as $productId => $product) {
            if (!$incoming->has($productId)) {
                $product->increment('stock', $product->pivot->quantity);
            }
        }
    
        // 2. Adjust stock for added or changed products
        foreach ($incoming as $productId => $item) {
            $product = Product::lockForUpdate()->findOrFail($productId);
    
            if ($current->has($productId)) {
                // Already existed — only adjust the diff
                $diff = $item['quantity'] - $current[$productId]->pivot->quantity;
    
                if ($diff > 0 && $product->stock < $diff) {
                    throw new \RuntimeException("Out of stock: {$product->name}");
                }
    
                if ($diff !== 0) {
                    // decrement handles negative too (restores if diff < 0)
                    $product->decrement('stock', $diff);
                }
            } else {
                // Brand new product on this appointment
                if ($product->stock < $item['quantity']) {
                    throw new \RuntimeException("Out of stock: {$product->name}");
                }
    
                $product->decrement('stock', $item['quantity']);
            }
        }
    
        // 3. Sync pivot — now stock is already correct
        $syncData = $incoming
            ->mapWithKeys(fn($item, $id) => [
                $id => ['quantity' => $item['quantity']]
            ])
            ->toArray();
    
        $appointment->products()->sync($syncData);
    }
}
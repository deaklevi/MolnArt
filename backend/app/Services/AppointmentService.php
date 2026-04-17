<?php

namespace App\Services;

use App\Http\Requests\StoreAppointmentRequest;
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
}
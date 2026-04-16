<?php

namespace App\Services;

use App\Http\Requests\StoreAppointmentRequest;
use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AppointmentService
{
    public function book(StoreAppointmentRequest $request): Appointment
    {
        return DB::transaction(function () use ($request) {
            $data = $request->validated();

            // 1. Check for time conflict (per user)
            $conflict = Appointment::where('user_id', $data['user_id'])
                ->where('appointment_from', '<', $data['appointment_to'])
                ->where('appointment_to', '>', $data['appointment_from'])
                ->exists();

            if ($conflict) {
                throw new \InvalidArgumentException(
                    'This time slot is already booked.'
                );
            }

            // 2. Find or create customer
            $customer = Customer::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name'         => $data['name'],
                    'phone_number' => $data['phone_number'],
                    'user_id'      => $data['user_id'],
                ]
            );

            // 3. Create appointment ✅ FIXED (user_id added)
            $appointment = Appointment::create([
                'appointment_from' => $data['appointment_from'],
                'appointment_to'   => $data['appointment_to'],
                'service'          => $data['service'],
                'customer_id'      => $customer->id,
                'user_id'          => $data['user_id'], // 🔥 THIS FIXES YOUR ERROR
            ]);

            // 4. Attach products + deduct stock (NO quantity anymore)
            foreach ($data['used_products'] ?? [] as $productId) {
                $product = Product::lockForUpdate()->findOrFail($productId);

                if ($product->stock <= 0) {
                    throw new \RuntimeException(
                        "Out of stock: {$product->name}"
                    );
                }

                $appointment->products()->attach($product->id);

                $product->decrement('stock', 1); // default usage = 1
            }

            // 5. Send email (safe)
            $worker = User::find($data['user_id']);

            try {
                Mail::to($customer->email)->send(new \App\Mail\AppointmentBooked([
                    'name'        => $customer->name,
                    'service'     => $appointment->service,
                    'start'       => $appointment->appointment_from->format('Y-m-d H:i'),
                    'worker_name' => $worker?->user_name ?? '',
                ]));
            } catch (\Exception $e) {
                Log::warning('Appointment email failed: ' . $e->getMessage());
            }

            return $appointment->load('customer', 'products');
        });
    }
}
<?php

// app/Services/AppointmentService.php

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

            // 1. Check for time conflict across ALL stylists
            // (your schema has no user_id on appointment, so we check globally)
            $conflict = Appointment::where('appointment_from', '<', $data['appointment_to'])
                                   ->where('appointment_to', '>', $data['appointment_from'])
                                   ->exists();

            if ($conflict) {
                throw new \InvalidArgumentException(
                    'This time slot is already booked.'
                );
            }

            // 2. Find or create customer by email
            $customer = Customer::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name'         => $data['name'],
                    'phone_number' => $data['phone_number'],
                    'user_id'      => $data['worker_id'], 
                ]
            );

            // 3. Create appointment
            $appointment = Appointment::create([
                'appointment_from' => $data['appointment_from'],
                'appointment_to'   => $data['appointment_to'],
                'service'          => $data['service'],
                'customer_id'      => $customer->id,
            ]);

            // 4. Attach products + deduct stock
            foreach ($data['products'] ?? [] as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                if ($product->stock < $item['quantity']) {
                    throw new \RuntimeException(
                        "Insufficient stock for: {$product->name}"
                    );
                }

                $appointment->products()->attach($product->id, [
                    'quantity' => $item['quantity'],
                ]);

                $product->decrement('stock', $item['quantity']);
            }

            // 5. Send confirmation email — failure won't roll back the booking
            $worker = User::find($data['worker_id']);

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
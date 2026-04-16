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

            // ── 1. TIME CONFLICT CHECK ─────────────────────
            $conflict = Appointment::where('appointment_from', '<', $data['appointment_to'])
                ->where('appointment_to', '>', $data['appointment_from'])
                ->where('user_id', $data['user_id']) // IMPORTANT: per user
                ->exists();

            if ($conflict) {
                abort(422, 'This time slot is already booked.');
            }

            // ── 2. CUSTOMER ───────────────────────────────
            $customer = Customer::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name'         => $data['name'] ?? '',
                    'phone_number' => $data['phone_number'] ?? '',
                    'user_id'      => $data['user_id'],
                ]
            );

            // ── 3. CREATE APPOINTMENT ─────────────────────
            $appointment = Appointment::create([
                'appointment_from' => $data['appointment_from'],
                'appointment_to'   => $data['appointment_to'],
                'service'          => $data['service'],
                'customer_id'      => $customer->id,
                'user_id'          => $data['user_id'], // IMPORTANT
            ]);

            // ── 4. HANDLE PRODUCTS (NO PIVOT QUANTITY) ────
            if (!empty($data['used_products'])) {

                $productIds = [];

                foreach ($data['used_products'] as $item) {

                    $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                    $usedAmount = $item['quantity'] ?? 1; // fallback if not provided

                    if ($product->stock < $usedAmount) {
                        abort(422, "Not enough stock for {$product->name}");
                    }

                    // deduct stock
                    $product->decrement('stock', $usedAmount);

                    $productIds[] = $product->id;
                }

                // attach WITHOUT pivot data
                $appointment->products()->sync($productIds);
            }

            // ── 5. EMAIL ──────────────────────────────────
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
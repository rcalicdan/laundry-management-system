<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    public function processPayment(Order $order, array $paymentData): Payment
    {
        return DB::transaction(function () use ($order, $paymentData) {
            $payment = $order->payment ?? new Payment();
            
            $payment->fill([
                'order_id' => $order->id,
                'amount' => $paymentData['amount'],
                'payment_method' => PaymentMethod::from($paymentData['payment_method']),
                'status' => PaymentStatus::from($paymentData['status']),
                'transaction_id' => $paymentData['transaction_id'] ?? null,
                'notes' => $paymentData['notes'] ?? null,
            ]);

            if ($payment->status === PaymentStatus::PAID) {
                $payment->paid_at = now();
            }

            $payment->save();

            Log::info('Payment processed successfully', [
                'order_id' => $order->id,
                'payment_id' => $payment->id,
                'amount' => $payment->amount,
                'method' => $payment->payment_method->value,
                'status' => $payment->status->value,
            ]);

            return $payment;
        });
    }

    public function getPaymentData(Order $order): array
    {
        $payment = $order->payment;
        
        return [
            'amount' => $payment ? $payment->amount : $order->total_amount,
            'payment_method' => $payment ? $payment->payment_method->value : '',
            'status' => $payment ? $payment->status->value : PaymentStatus::PENDING->value,
            'transaction_id' => $payment ? $payment->transaction_id : '',
            'notes' => $payment ? $payment->notes : '',
        ];
    }

    public function canProcessPayment(Order $order): bool
    {
        return $order->total_amount > 0;
    }
}
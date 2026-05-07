<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\NotificationOutbox;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'payee_id' => ['required', 'exists:users,id'],
            'group_id' => ['nullable', 'exists:groups,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'currency' => ['required', 'string', 'size:3'],
            'method' => ['required', 'in:cash,instapay,vcash,bank,other'],
            'reference' => ['nullable', 'string', 'max:120'],
            'paid_at' => ['required', 'date'],
            'note' => ['nullable', 'string', 'max:200'],
        ]);
        $payment = Payment::create([
            'payer_id' => $request->user()->id,
            ...$data,
        ]);
        Activity::log('payment.made', [
            'amount' => (float) $data['amount'], 'currency' => $data['currency'], 'method' => $data['method'],
            'payee_id' => (int) $data['payee_id'],
        ], $data['group_id'] ?? null, Payment::class, $payment->id);

        $payee = User::find($data['payee_id']);
        if ($payee) {
            NotificationOutbox::create([
                'user_id' => $payee->id,
                'title' => 'Payment received',
                'body' => $request->user()->name . ' paid you ' . number_format($data['amount'], 2) . ' ' . $data['currency'],
                'url' => route('dashboard'),
            ]);
        }
        return back()->with('flash', 'Payment recorded');
    }
}

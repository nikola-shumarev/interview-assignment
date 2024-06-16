<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use Inertia\Inertia;
use App\Services\PaymentService;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function create()
    {
        return Inertia::render('Payment/Create');
    }

    public function store(StorePaymentRequest $request)
    {
        try {
            $isOverpayment = $this->paymentService->processPayment($request->validated());

            if ($isOverpayment) {
                return redirect()->route('payment.create')->with('warning', 'Overpayment detected. The amount was adjusted to the maximum payable amount. The consumer has been notified.');
            }

            return redirect()->route('payment.create')->with('success', 'Payment submitted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('payment.create')->with('error', 'Error processing payment: ' . $e->getMessage());
        }
    }
}

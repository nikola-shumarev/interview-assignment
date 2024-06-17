<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use Inertia\Inertia;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create(): Response
    {
        return Inertia::render('Payment/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StorePaymentRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StorePaymentRequest $request): RedirectResponse
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

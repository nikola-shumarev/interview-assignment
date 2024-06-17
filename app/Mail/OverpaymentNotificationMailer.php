<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OverpaymentNotificationMailer extends Mailable
{
    use Queueable, SerializesModels;

    public $uniqueId;
    public $owedAmount;
    public $overpaidAmount;

    public function __construct(string $uniqueId, float $owedAmount, float $overpaidAmount)
    {
        $this->uniqueId = $uniqueId;
        $this->owedAmount = $owedAmount;
        $this->overpaidAmount = $overpaidAmount;
    }

    public function build(): Mailable
    {
        return $this->view('emails.overpayment')
                    ->with([
                        'uniqueId' => $this->uniqueId,
                        'owedAmount' => $this->owedAmount,
                        'overpaidAmount' => $this->overpaidAmount,
                    ]);
    }
}

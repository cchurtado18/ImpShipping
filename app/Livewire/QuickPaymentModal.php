<?php

namespace App\Livewire;

use App\Models\Shipment;
use App\Models\Payment;
use Livewire\Component;

class QuickPaymentModal extends Component
{
    public $showModal = false;
    public $shipmentId;
    public $shipment;
    public $amount = 0;
    public $paymentMethod = 'cash';
    public $reference = '';
    public $editingPayment = null;
    public $showPaymentHistory = false;
    public $payments = [];
    public $paymentType = 'new'; // 'new', 'partial', 'full'

    protected $listeners = ['openPaymentModal'];

    public function openPaymentModal($shipmentId)
    {
        $this->shipmentId = $shipmentId;
        $this->shipment = Shipment::with(['client', 'recipient', 'payments'])->find($shipmentId);
        
        if ($this->shipment) {
            $this->payments = $this->shipment->payments()->orderBy('created_at', 'desc')->get();
            $this->calculatePaymentType();
            $this->showModal = true;
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['shipmentId', 'shipment', 'amount', 'paymentMethod', 'reference', 'editingPayment', 'showPaymentHistory', 'payments', 'paymentType']);
    }

    public function calculatePaymentType()
    {
        $remaining = $this->shipment->remaining_amount;
        $total = $this->shipment->sale_price_usd;
        
        if ($remaining <= 0) {
            $this->paymentType = 'full';
            $this->amount = 0;
        } elseif ($remaining == $total) {
            $this->paymentType = 'new';
            $this->amount = $total;
        } else {
            $this->paymentType = 'partial';
            $this->amount = $remaining;
        }
    }

    public function updatedAmount()
    {
        if ($this->amount > $this->shipment->remaining_amount) {
            $this->amount = $this->shipment->remaining_amount;
        }
    }

    public function setFullPayment()
    {
        $this->amount = $this->shipment->remaining_amount;
        $this->paymentType = 'full';
    }

    public function setPartialPayment()
    {
        $this->paymentType = 'partial';
        $this->amount = $this->shipment->remaining_amount;
    }

    public function editPayment(Payment $payment)
    {
        $this->editingPayment = $payment;
        $this->amount = $payment->amount_usd;
        $this->paymentMethod = $payment->method;
        $this->reference = $payment->reference;
    }

    public function cancelEdit()
    {
        $this->editingPayment = null;
        $this->calculatePaymentType();
        $this->paymentMethod = 'cash';
        $this->reference = '';
    }

    public function updatePayment()
    {
        $this->validate([
            'amount' => 'required|numeric|min:0.01',
            'paymentMethod' => 'required|in:cash,card,transfer',
        ]);

        if (!$this->editingPayment) {
            return;
        }

        // Actualizar el pago
        $this->editingPayment->update([
            'amount_usd' => $this->amount,
            'method' => $this->paymentMethod,
            'reference' => $this->reference,
        ]);

        // Actualizar el estado del envío
        $this->updateShipmentPaymentStatus();

        $this->cancelEdit();
        $this->refreshPayments();
        session()->flash('message', 'Pago actualizado exitosamente.');
    }

    public function deletePayment(Payment $payment)
    {
        $payment->delete();
        $this->updateShipmentPaymentStatus();
        $this->refreshPayments();
        session()->flash('message', 'Pago eliminado exitosamente.');
    }

    public function processPayment()
    {
        $this->validate([
            'amount' => 'required|numeric|min:0.01',
            'paymentMethod' => 'required|in:cash,card,transfer',
        ]);

        if (!$this->shipment) {
            return;
        }

        // Crear el pago
        Payment::create([
            'shipment_id' => $this->shipment->id,
            'amount_usd' => $this->amount,
            'method' => $this->paymentMethod,
            'reference' => $this->reference,
            'paid_at' => now(),
        ]);

        // Actualizar el estado del envío
        $this->updateShipmentPaymentStatus();

        $this->refreshPayments();
        $this->closeModal();
        $this->dispatch('shipmentUpdated');
        session()->flash('message', 'Pago procesado exitosamente.');
    }

    public function togglePaymentHistory()
    {
        $this->showPaymentHistory = !$this->showPaymentHistory;
    }

    private function updateShipmentPaymentStatus()
    {
        $totalPaid = $this->shipment->payments()->sum('amount_usd');
        $totalAmount = $this->shipment->sale_price_usd;
        
        if ($totalPaid >= $totalAmount) {
            $this->shipment->update(['payment_status' => 'paid']);
        } elseif ($totalPaid > 0) {
            $this->shipment->update(['payment_status' => 'partial']);
        } else {
            $this->shipment->update(['payment_status' => 'pending']);
        }
    }

    private function refreshPayments()
    {
        $this->payments = $this->shipment->payments()->orderBy('created_at', 'desc')->get();
        $this->calculatePaymentType();
    }

    public function render()
    {
        return view('livewire.quick-payment-modal');
    }
}

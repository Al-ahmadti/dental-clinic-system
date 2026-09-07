<?php

namespace App\Livewire;

use App\Livewire\Concerns\AuthorizesPatientAccess;
use App\Models\Visit;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Livewire\Component;

class VisitFinancialPanel extends Component
{
    use AuthorizesPatientAccess;

    public int $visitId;

    public ?string $paymentFilter = null;

    public bool $showPaymentModal = false;

    public string $payment_amount = '';

    public string $payment_method = 'cash';

    public string $check_number = '';

    public string $paid_at = '';

    public function mount(int $visitId, mixed $openPaymentOnMount = false): void
    {
        $this->authorizeVisitAccess($visitId);
        $this->visitId = $visitId;
        if (filter_var($openPaymentOnMount, FILTER_VALIDATE_BOOLEAN)) {
            $this->openPaymentModal();
        }
    }

    private function resolveVisit(): Visit
    {
        return Visit::query()
            ->with(['payments' => fn ($q) => $q->orderByDesc('paid_at'), 'lineItems.service', 'patient'])
            ->findOrFail($this->visitId);
    }

    public function setPaymentFilter(?string $method): void
    {
        $this->paymentFilter = $method;
    }

    public function openPaymentModal(): void
    {
        $visit = $this->resolveVisit();
        $balance = round(max(0, $visit->balanceDue()), 2);
        $this->payment_amount = $balance > 0 ? (string) $balance : '';
        $this->paid_at = now()->format('Y-m-d\TH:i');
        $this->payment_method = 'cash';
        $this->check_number = '';
        $this->showPaymentModal = true;
    }

    public function closePaymentModal(): void
    {
        $this->showPaymentModal = false;
    }

    public function savePayment(): void
    {
        $rules = [
            'payment_amount' => ['required', 'numeric', 'min:0.01'],
            'paid_at' => ['required', 'date'],
            'payment_method' => ['required', 'in:cash,card,transfer,check'],
        ];

        if ($this->payment_method === 'check') {
            $rules['check_number'] = ['required', 'string', 'max:64'];
        } else {
            $rules['check_number'] = ['nullable', 'string', 'max:64'];
        }

        $visit = $this->resolveVisit();
        $balance = round(max(0, $visit->balanceDue()), 2);

        $rules['payment_amount'][] = 'max:'.$balance;

        $this->validate($rules, [
            'payment_amount.max' => 'المبلغ يتجاوز المتبقي على الزيارة ('.number_format($balance, 2).').',
        ], [
            'payment_amount' => 'المبلغ',
            'paid_at' => 'تاريخ الدفع',
            'payment_method' => 'طريقة الدفع',
            'check_number' => 'رقم الشيك',
        ]);

        if ($balance <= 0.01) {
            $this->addError('payment_amount', 'لا يوجد مبلغ متبقٍ على هذه الزيارة.');

            return;
        }
        $visit->payments()->create([
            'amount' => round((float) $this->payment_amount, 2),
            'payment_method' => $this->payment_method,
            'check_number' => $this->payment_method === 'check' ? $this->check_number : null,
            'paid_at' => Carbon::parse($this->paid_at),
            'notes' => null,
        ]);

        $this->showPaymentModal = false;

        Notification::make()->title('تم تسجيل الدفعة')->success()->send();
    }

    public function render(): View
    {
        $visit = $this->resolveVisit();
        $payments = $visit->payments;
        if ($this->paymentFilter !== null) {
            $payments = $payments->where('payment_method', $this->paymentFilter);
        }

        return view('livewire.visit-financial-panel', [
            'visit' => $visit,
            'paymentsList' => $payments->values(),
            'totalDue' => round($visit->totalLineAmount(), 2),
            'totalPaid' => round($visit->totalPayments(), 2),
            'balanceDue' => round($visit->balanceDue(), 2),
        ]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Services\BillServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Bill\ProcessPaymentRequest;
use App\Http\Requests\Bill\StoreBillRequest;
use App\Models\Bill;
use App\Models\Order;
use Illuminate\Http\Request;

class BillController extends Controller
{
    public function __construct(
        protected BillServiceInterface $billService
    ) {}

    public function index()
    {
        $this->authorize('viewAny', Bill::class);
        $bills = $this->billService->getAllBills();
        return view('admin.bills.index', compact('bills'));
    }

    public function store(StoreBillRequest $request)
    {
        $this->authorize('create', Bill::class);
        
        $bill = $this->billService->generateBill($request->order_id);

        return redirect()->route('admin.bills.edit', $bill)
            ->with('success', 'Bill generated successfully.');
    }

    public function show(Bill $bill)
    {
        $this->authorize('view', $bill);
        $bill = $this->billService->findBill($bill->id);
        
        return view('admin.bills.invoice', compact('bill'));
    }

    public function edit(Bill $bill)
    {
        $this->authorize('processPayment', $bill);
        
        if ($bill->isPaid()) {
            return redirect()->route('admin.bills.show', $bill)
                ->with('info', 'This bill has already been paid.');
        }

        if ($bill->isCancelled()) {
            return redirect()->route('admin.bills.index')
                ->with('error', 'Cannot process payment for a cancelled bill.');
        }

        $bill = $this->billService->findBill($bill->id);
        return view('admin.bills.pay', compact('bill'));
    }

    public function update(ProcessPaymentRequest $request, Bill $bill)
    {
        $this->authorize('processPayment', $bill);

        if (!$bill->isPending()) {
            return redirect()->route('admin.bills.index')
                ->with('error', 'Only pending bills can be paid.');
        }

        $this->billService->processPayment($bill->id, $request->validated());

        return redirect()->route('admin.bills.show', $bill)
            ->with('success', 'Payment processed successfully.');
    }

    public function destroy(Bill $bill)
    {
        $this->authorize('delete', $bill);

        if ($bill->isPaid()) {
            return back()->with('error', 'Cannot cancel a paid bill.');
        }

        $this->billService->cancelBill($bill->id);

        return redirect()->route('admin.bills.index')
            ->with('success', 'Bill cancelled successfully.');
    }
}

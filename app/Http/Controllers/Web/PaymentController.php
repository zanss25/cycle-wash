<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    public function history()
    {
        $payments = Payment::where('user_id', Auth::id())->latest()->paginate(15);
        $balance = $this->walletService->getBalance(Auth::id());
        return view('payment.history', compact('payments', 'balance'));
    }

    public function topup(Request $request)
    {
        $request->validate(['amount' => 'required|numeric|min:10000']);
        $this->walletService->topUp(Auth::id(), $request->amount, 'Top Up Saldo');
        return back()->with('success', 'Top up berhasil!');
    }
}

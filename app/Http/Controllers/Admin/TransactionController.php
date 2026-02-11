<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Yajra\DataTables\Facades\DataTables;

class TransactionController extends Controller
{
    public function index()
    {
        return Inertia::render('Transactions/Index');
    }

    public function getData()
    {
        $query = Transaction::with(['user', 'order'])->select('transactions.*');
        return DataTables::of($query)
            ->editColumn('created_at', function ($transaction) {
                return $transaction->created_at->format('Y-m-d H:i:s');
            })
            ->editColumn('amount', function ($transaction) {
                return $transaction->currency . ' ' . $transaction->amount;
            })
            ->addColumn('user_name', function ($transaction) {
                return $transaction->user ? $transaction->user->name : 'N/A';
            })
            ->addColumn('order_number', function ($transaction) {
                return $transaction->order ? $transaction->order->order_number : 'N/A';
            })
            ->toJson();
    }
}

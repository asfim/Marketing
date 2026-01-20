<?php

namespace App\Http\Controllers;

use App\Models\BankInfo;
use App\InvestorStatement;
use Illuminate\Http\Request;
use App\Models\BankStatement;
use App\Models\CashStatement;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class FundTransferController extends Controller
{
    public function create()
    {
        $banks = BankInfo::all()->map(function ($bank) {
            $balance = DB::table('bank_statements')
                ->where('bank_info_id', $bank->id)
                ->sum(DB::raw('credit - debit'));
            $bank->balance = $balance;
            return $bank;
        });

        return view('admin.fund-transfer.transfer_form', compact('banks'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'transfer_type' => 'required|in:cash_to_bank,bank_to_cash,bank_to_bank',
            'from_bank_id' => 'required_if:transfer_type,bank_to_cash,bank_to_bank|nullable|exists:bank_infos,id',
            'to_bank_id' => 'required_if:transfer_type,cash_to_bank,bank_to_bank|nullable|exists:bank_infos,id',
            'amount' => 'required|numeric|min:0.01',
            'transfer_date' => 'required|date',
        ]);

        DB::beginTransaction();

        try {
            // ✅ Fix: Check last transaction from both cash and bank statements
            $lastCash = DB::table('cash_statements')
                ->where('transaction_id', 'like', 'Fund-%')
                ->orderByDesc('id')
                ->value('transaction_id');

            $lastBank = DB::table('bank_statements')
                ->where('transaction_id', 'like', 'Fund-%')
                ->orderByDesc('id')
                ->value('transaction_id');

            $lastNumberCash = $lastCash ? (int)str_replace('Fund-', '', $lastCash) : 0;
            $lastNumberBank = $lastBank ? (int)str_replace('Fund-', '', $lastBank) : 0;

            $lastNumber = max($lastNumberCash, $lastNumberBank);
            $nextNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
            $transaction_id = 'Fund-' . $nextNumber;

            $user_id = Auth::id();
            $branchId = Auth::user()->branchId;
            // $today = now()->format('Y-m-d');
            $transfer_date = $request->transfer_date;
            $description = $request->description ?? ucfirst(str_replace('_', ' ', $request->transfer_type)) . " transfer";

            switch ($request->transfer_type) {
                case 'cash_to_bank':
                    $latest_cash_balance = DB::table('cash_statements')->orderByDesc('id')->value('balance') ?? 0;
                    $latest_bank_balance = DB::table('bank_statements')
                        ->where('bank_info_id', $request->to_bank_id)
                        ->orderByDesc('id')
                        ->value('balance') ?? 0;

                    DB::table('cash_statements')->insert([
                        'transaction_id' => $transaction_id,
                        'table_name' => 'fund_transfers',
                        'posting_date' => $transfer_date,
                        'ref_date' => $transfer_date,
                        'debit' => $request->amount,
                        'credit' => 0,
                        'balance' => $latest_cash_balance - $request->amount,
                        'branchId' => $branchId,
                        'description' => $description,
                        'user_id' => $user_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    DB::table('bank_statements')->insert([
                        'transaction_id' => $transaction_id,
                        'table_name' => 'fund_transfers',
                        'bank_info_id' => $request->to_bank_id,
                        'posting_date' => $transfer_date,
                        'ref_date' => $transfer_date,
                        'debit' => 0,
                        'credit' => $request->amount,
                        'balance' => $latest_bank_balance + $request->amount,
                        'branchId' => $branchId,
                        'description' => $description,
                        'user_id' => $user_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    break;

                case 'bank_to_cash':
                    $latest_bank_balance = DB::table('bank_statements')
                        ->where('bank_info_id', $request->from_bank_id)
                        ->orderByDesc('id')
                        ->value('balance') ?? 0;

                    $latest_cash_balance = DB::table('cash_statements')->orderByDesc('id')->value('balance') ?? 0;

                    DB::table('bank_statements')->insert([
                        'transaction_id' => $transaction_id,
                        'table_name' => 'fund_transfers',
                        'bank_info_id' => $request->from_bank_id,
                        'posting_date' => $transfer_date,
                        'ref_date' => $transfer_date,
                        'debit' => $request->amount,
                        'credit' => 0,
                        'balance' => $latest_bank_balance - $request->amount,
                        'branchId' => $branchId,
                        'description' => $description,
                        'user_id' => $user_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    DB::table('cash_statements')->insert([
                        'transaction_id' => $transaction_id,
                        'table_name' => 'fund_transfers',
                        'posting_date' => $transfer_date,
                        'ref_date' => $transfer_date,
                        'debit' => 0,
                        'credit' => $request->amount,
                        'balance' => $latest_cash_balance + $request->amount,
                        'branchId' => $branchId,
                        'description' => $description,
                        'user_id' => $user_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    break;

                case 'bank_to_bank':
                    $from_balance = DB::table('bank_statements')
                        ->where('bank_info_id', $request->from_bank_id)
                        ->orderByDesc('id')
                        ->value('balance') ?? 0;

                    $to_balance = DB::table('bank_statements')
                        ->where('bank_info_id', $request->to_bank_id)
                        ->orderByDesc('id')
                        ->value('balance') ?? 0;

                    DB::table('bank_statements')->insert([
                        'transaction_id' => $transaction_id,
                        'table_name' => 'fund_transfers',
                        'bank_info_id' => $request->from_bank_id,
                        'posting_date' => $transfer_date,
                        'ref_date' => $transfer_date,
                        'debit' => $request->amount,
                        'credit' => 0,
                        'balance' => $from_balance - $request->amount,
                        'branchId' => $branchId,
                        'description' => "Transfer to bank ID: {$request->to_bank_id}. $description",
                        'user_id' => $user_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    DB::table('bank_statements')->insert([
                        'transaction_id' => $transaction_id,
                        'table_name' => 'fund_transfers',
                        'bank_info_id' => $request->to_bank_id,
                        'posting_date' => $transfer_date,
                        'ref_date' => $transfer_date,
                        'debit' => 0,
                        'credit' => $request->amount,
                        'balance' => $to_balance + $request->amount,
                        'branchId' => $branchId,
                        'description' => "Received from bank ID: {$request->from_bank_id}. $description",
                        'user_id' => $user_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    break;
            }
            

            DB::commit();

            return redirect()->back()->with('success', 'Fund transferred successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Transfer failed: ' . $e->getMessage());
        }
    }


    public function statement(Request $request)
    {
        if ($request->filled('from_date') && $request->filled('to_date')){
            $fromDate = Carbon::parse($request->from_date)->format('Y-m-d');
            $toDate = Carbon::parse($request->to_date)->format('Y-m-d');
        }else{
            $fromDate = date('Y-m-d',strtotime('-30 days'));
            $toDate = date('Y-m-d');
        }



        $cashStatements = \DB::table('cash_statements')
            ->where('table_name', 'fund_transfers')
            ->when($request->has('from_date') && $request->has('to_date'), function ($query) use ($fromDate, $toDate) {
                $query->whereBetween('posting_date', [$fromDate, $toDate]);
            });

        $bankStatements = \DB::table('bank_statements')
            ->where('table_name', 'fund_transfers')
            ->when($request->has('from_date') && $request->has('to_date'), function ($query) use ($fromDate, $toDate) {

                $query->whereBetween('posting_date', [
                    Carbon::parse($fromDate)->format('Y-m-d'),
                    Carbon::parse($toDate)->format('Y-m-d')
                ]);
            });

        if ($request->filled('search_text')) {
            $search = $request->search_text;

            $cashStatements = $cashStatements->where(function ($q) use ($search) {
                $q->where('transaction_id', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%");
            });

            $bankStatements = $bankStatements->where(function ($q) use ($search) {
                $q->where('transaction_id', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%");
            });
        }

        $cashStatements = $cashStatements->get();
        $bankStatements = $bankStatements->get();

        $statements = $cashStatements->merge($bankStatements)->sortByDesc('created_at');

        return view('admin.fund-transfer.statement', compact('statements', 'fromDate', 'toDate'));
    }







}

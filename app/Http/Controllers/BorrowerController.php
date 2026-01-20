<?php

namespace App\Http\Controllers;
use Psy\Util\Str;
use Carbon\Carbon;
use App\Models\BankInfo;
use App\Models\Borrower;
use App\BorrowerStatement;
use Illuminate\Http\Request;
use App\Models\BankStatement;
use App\Models\CashStatement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class BorrowerController
{
    public function index()
    {
        $borrowers = Borrower::orderBy('updated_at', 'desc')->get();

        return view('admin.borrowers.index', compact('borrowers'));
    }

    public function create()
    {
        $banks = BankInfo::all();

        return view('admin.borrowers.create', compact('banks'));
    }

    public function store(Request $request)
    {
       // dd($request);
        $request->validate([
            'name' => 'required|string',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $borrowerData = [
                'name' => $request->name,
                'phone' => $request->phone,
                'address' => $request->address,
            ];

            Borrower::create($borrowerData);

            DB::commit();

            return redirect()->route('admin.borrowers.index')->with('success', 'Borrower added successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

   public function show($id)
{
    $borrower = Borrower::with('bank', 'branch')->findOrFail($id);

    $totalCredit = DB::table('borrower_statements')
        ->where('borrower_id', $id)
        ->sum('credit');

    $totalDebit = DB::table('borrower_statements')
        ->where('borrower_id', $id)
        ->sum('debit');

    // Default 30 days range
    $default_from = now()->subDays(30)->format('Y-m-d');
    $default_to = now()->format('Y-m-d');

    // -------- Investment History (Credits) --------
    $investmentQuery = DB::table('borrower_statements')
        ->where('borrower_id', $id)
        ->where('credit', '>', 0)
        ->orderByDesc('updated_at');

    $loan_date_range = request()->filled('loan_date_range') 
        ? date_range_to_arr(request()->loan_date_range)
        : [$default_from, $default_to];

    $investmentQuery->whereBetween('posting_date', [
        Carbon::parse($loan_date_range[0])->format('Y-m-d'),
        Carbon::parse($loan_date_range[1])->format('Y-m-d')
    ]);

    if (request('investment_search_text')) {
        $search = request('investment_search_text');
        $investmentQuery->where(function ($query) use ($search) {
            $query->where('description', 'like', "%{$search}%")
                  ->orWhere('deposit_type', 'like', "%{$search}%");
        });
    }

    $investmentHistory = $investmentQuery->get();

    foreach ($investmentHistory as $inv) {
        $inv->payment_mode = $inv->deposit_type;
        $inv->transaction_id = $inv->transaction_id;

        if (strtolower($inv->deposit_type) === 'bank') {
            $bank = DB::table('bank_infos')->where('id', $inv->bank_id)->first();
            $branch = DB::table('branches')->where('id', $inv->branchId)->first();

            $inv->account_name = $bank->account_name ?? 'Unknown';
            $inv->bank_name = $bank->bank_name ?? 'Unknown';
            $inv->branch_name = $branch->name ?? 'N/A';
        } elseif (strtolower($inv->deposit_type) === 'cash') {
            $inv->account_name = 'Cash';
            $inv->bank_name = 'N/A';
            $inv->branch_name = 'N/A';
        } else {
            $inv->account_name = 'Unknown';
            $inv->bank_name = 'Unknown';
            $inv->branch_name = 'N/A';
        }
    }

    // -------- Return History (Debits) --------
    $returnQuery = DB::table('borrower_statements')
        ->where('borrower_id', $id)
        ->where('debit', '>', 0)
        ->orderByDesc('updated_at');

    $repayment_date_range = request()->filled('repayment_date_range')
        ? date_range_to_arr(request()->repayment_date_range)
        : [$default_from, $default_to];

    $returnQuery->whereBetween('posting_date', [
        Carbon::parse($repayment_date_range[0])->format('Y-m-d'),
        Carbon::parse($repayment_date_range[1])->format('Y-m-d')
    ]);

    if (request('return_search_text')) {
        $search = request('return_search_text');
        $returnQuery->where(function ($query) use ($search) {
            $query->where('description', 'like', "%{$search}%")
                  ->orWhere('deposit_type', 'like', "%{$search}%");
        });
    }

    $returnHistory = $returnQuery->get();

    foreach ($returnHistory as $ret) {
        $ret->payment_mode = $ret->deposit_type;
        $ret->transaction_id = $ret->transaction_id;

        if (strtolower($ret->deposit_type) === 'bank') {
            $bank = DB::table('bank_infos')->where('id', $ret->bank_id)->first();
            $branch = DB::table('branches')->where('id', $ret->branchId)->first();

            $ret->account_name = $bank->account_name ?? 'Unknown';
            $ret->bank_name = $bank->bank_name ?? 'Unknown';
            $ret->branch_name = $branch->name ?? 'N/A';
        } elseif (strtolower($ret->deposit_type) === 'cash') {
            $ret->account_name = 'Cash';
            $ret->bank_name = 'N/A';
            $ret->branch_name = 'N/A';
        } else {
            $ret->account_name = 'Unknown';
            $ret->bank_name = 'Unknown';
            $ret->branch_name = 'N/A';
        }
    }

    // -------- Combined Statements --------
    $statementsQuery = DB::table('borrower_statements')
        ->where('borrower_id', $id)
        ->orderByDesc('created_at');

    $statements_date_range = request()->filled('investment_date_range')
        ? date_range_to_arr(request()->investment_date_range)
        : [$default_from, $default_to];

    $statements = $statementsQuery->whereBetween('posting_date', [
        Carbon::parse($statements_date_range[0])->format('Y-m-d'),
        Carbon::parse($statements_date_range[1])->format('Y-m-d')
    ])->get();

    foreach ($statements as $stmt) {
        $stmt->payment_mode = $stmt->deposit_type;
        $stmt->transaction_id = $stmt->transaction_id;
        $stmt->type = $stmt->credit > 0 ? 'Investment' : 'Return';

        if (strtolower($stmt->deposit_type) === 'bank') {
            $bank = DB::table('bank_infos')->where('id', $stmt->bank_id)->first();
            $branch = DB::table('branches')->where('id', $stmt->branchId)->first();

            $stmt->account_name = $bank->account_name ?? 'Unknown';
            $stmt->bank_name = $bank->bank_name ?? 'Unknown';
            $stmt->branch_name = $branch->name ?? 'N/A';
        } elseif (strtolower($stmt->deposit_type) === 'cash') {
            $stmt->account_name = 'Cash';
            $stmt->bank_name = 'N/A';
            $stmt->branch_name = 'N/A';
        } else {
            $stmt->account_name = 'Unknown';
            $stmt->bank_name = 'Unknown';
            $stmt->branch_name = 'N/A';
        }
    }

    return view('admin.borrowers.show', [
        'borrower' => $borrower,
        'totalCredit' => $totalCredit,
        'totalDebit' => $totalDebit,
        'investmentHistory' => $investmentHistory,
        'returnHistory' => $returnHistory,
        'statements' => $statements,
        'selected_tab' => request('tab_type') ?? 'tab-borrower-info',
        'loan_date_range' => $loan_date_range,
        'repayment_date_range' => $repayment_date_range,
        'statements_date_range' => $statements_date_range,
    ]);
}


    public function addLoan($id)
    {
        $borrower = Borrower::findOrFail($id);
        $banks = BankInfo::all();

        return view('admin.borrowers.add-borrowers', compact('borrower', 'banks'));
    }
    public function returnForm($id)
    {
        $borrower  = Borrower::findOrFail($id);
        $banks = BankInfo::all();

        return view('admin.borrowers.return', compact('borrower', 'banks'));
    }


    public function processReturn(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'method' => 'required|in:cash,bank',
            'note' => 'nullable|string',
            'bank_id' => 'required_if:method,bank|nullable|exists:bank_infos,id',
            'ref_date' => 'required|date',
        ]);

        DB::beginTransaction();

        try {
            $borrower = Borrower::findOrFail($id);
            $userId = Auth::id();
            $branchId = Auth::user()->branchId;
            $today = now()->format('Y-m-d');
            $refDate = $request->input('ref_date', $today);
            $method = ucfirst($request->method);
            $amount = $request->amount;

            // ✅ Generate Incremental Transaction ID
            $lastTransaction = DB::table('borrower_statements')
                ->where('transaction_id', 'like', 'Borrow-R-%')
                ->orderByDesc('id')
                ->value('transaction_id');

            if ($lastTransaction && preg_match('/Borrow-R-(\d+)/', $lastTransaction, $matches)) {
                $nextId = (int)$matches[1] + 1;
            } else {
                $nextId = 1;
            }

            $transactionId = 'Borrow-R-' . $nextId;

            // ✅ Get previous balance
            $lastStatement = DB::table('borrower_statements')
                ->where('borrower_id', $id)
                ->latest('id')
                ->first();

            $previousBalance = $lastStatement ? $lastStatement->balance : 0;
            $newBalance = $previousBalance - $amount;

            // ✅ Borrower Statement Entry
            DB::table('borrower_statements')->insert([
                'transaction_id' => $transactionId,
                'posting_date' => $today,
                'deposit_type' => $method,
                'description' => $request->note ?? 'Borrower return',
                'table_name' => 'borrowers_returns',
                'debit' => $amount,
                'credit' => 0,
                'balance' => $newBalance,
                'borrower_id' => $id,
                'user_id' => $userId,
                'bank_id' => $request->bank_id,
                'branchId' => $branchId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // ✅ Statement Entry to Cash or Bank
            if (strtolower($request->method) === 'cash') {
                DB::table('cash_statements')->insert([
                    'transaction_id' => $transactionId,
                    'posting_date' => $today,
                    'ref_date' => $refDate,
                    'description' => $request->note ?? 'Return from borrower',
                    'table_name' => 'borrowers_returns',
                    'debit' => $amount,
                    'credit' => 0,
                    'balance' => $newBalance,
                    'branchId' => $branchId,
                    'user_id' => $userId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('bank_statements')->insert([
                    'transaction_id' => $transactionId,
                    'posting_date' => $today,
                    'ref_date' => $refDate,
                    'description' => $request->note ?? 'Return from borrower',
                    'table_name' => 'borrowers_returns',
                    'debit' => $amount,
                    'credit' => 0,
                    'balance' => $newBalance,
                    'bank_info_id' => $request->bank_id,
                    'branchId' => $branchId,
                    'user_id' => $userId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            return redirect()->route('admin.borrowers.show', $id)
                ->with('success', 'Return processed successfully.')
                ->with('tab_type', 'tab-return-history');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing borrower return:', [
                'error' => $e->getMessage(),
                'stack' => $e->getTraceAsString(),
                'borrower_id' => $id,
            ]);
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }



    public function saveLoan(Request $request)
    {
       // dd($request);
        $request->validate([
            'borrower_id' => 'required|exists:borrowers,id',
            'paid_amount' => 'required|numeric',
            'payment_date' => 'required|date',
            'deposit_type' => 'required|in:Cash,Bank',
            'bank_id' => 'required_if:deposit_type,Bank|nullable|exists:bank_infos,id',
        ]);

        DB::beginTransaction();

        try {
          //  Log::info('Starting saveLoan', ['request' => $request->all()]);

            $lastTransaction = DB::table('borrower_statements')
                ->where('transaction_id', 'like', 'Borrow-P-%')
                ->orderByDesc('id')
                ->value('transaction_id');

            if ($lastTransaction) {
                preg_match('/Borrow-P-(\d+)/', $lastTransaction, $matches);
                $nextId = isset($matches[1]) ? (int)$matches[1] + 1 : 1;
            } else {
                $nextId = 1;
            }

            $transaction_id = 'Borrow-P-' . $nextId;

            $user_id = Auth::id();
            $branchId = Auth::user()->branchId;
            $today = now()->format('Y-m-d');
            $description = $request->description ?? "Investment from borrower ID {$request->borrower_id}";

            $lastStatement = DB::table('borrower_statements')
                ->where('borrower_id', $request->borrower_id)
                ->latest('id')
                ->first();

            $previousBalance = $lastStatement ? $lastStatement->balance : 0;
            $newBalance =  $request->paid_amount + $previousBalance;

            if ($request->deposit_type === 'Cash') {
                Log::info('Processing Cash investment');

                DB::table('cash_statements')->insert([
                    'transaction_id' => $transaction_id,
                    'table_name' => 'borrowers_payments',
                    'posting_date' => $today,
                    'ref_date' => $request->payment_date,
                    'debit' => 0,
                    'credit' => $request->paid_amount,
                    'balance' => $newBalance,
                    'branchId' => $branchId,
                    'description' => $description,
                    'user_id' => $user_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('borrower_statements')->insert([
                    'transaction_id' => $transaction_id,
                    'borrower_id' => $request->borrower_id,
                    'deposit_type' => 'Cash',
                    'table_name' => 'borrowers_payments',
                    'credit' => $request->paid_amount,
                    'debit' => 0,
                    'balance' => $newBalance,
                    'branchId' => $branchId,
                    'user_id' => $user_id,
                    'description' => $description,
                    'posting_date' => $request->payment_date,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
              //  dd($branchId);
             //   Log::info('Processing Bank investment');
                DB::table('bank_statements')->insert([
                    'transaction_id' => $transaction_id,
                    'table_name' => 'borrowers_payments',
                    'bank_info_id' => $request->bank_id,
                    'posting_date' => $today,
                    'ref_date' => $request->payment_date,
                    'debit' => 0,
                    'credit' => $request->paid_amount,
                    'balance' => $newBalance,

                    'branchId' => $branchId,
                    'description' => $description,
                    'user_id' => $user_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('borrower_statements')->insert([
                    'transaction_id' => $transaction_id,
                    'borrower_id' => $request->borrower_id,
                    'deposit_type' => 'Bank',
                    'credit' => $request->paid_amount,
                    'table_name' => 'borrowers_payments',
                    'debit' => 0,
                    'balance' => $newBalance,
                    'branchId' => $branchId,
                    'bank_id' => $request->bank_id,
                    'user_id' => $user_id,
                    'description' => $description,
                    'posting_date' => $request->payment_date,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            return redirect()->route('admin.borrowers.show', $request->borrower_id)
                ->with('success', 'Borrow Amount saved successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving Borrow Amount', ['error' => $e->getMessage()]);
            return back()->with('error', 'Error occurred while saving the investment.');
        }
    }



    public function update(Request $request, $id)
    {
        $borrower = Borrower::findOrFail($id);

        $borrower->name = $request->name;
        $borrower->phone = $request->phone;
        $borrower->address = $request->address;
        $borrower->save();

        return redirect()->back()->with('success', 'Investment updated successfully.');
    }


    public function destroy($id)
    {
        $borrower = Borrower::findOrFail($id);

        // Check if the investor has any statements
        $statementCount = DB::table('borrower_statements')->where('borrower_id', $borrower->id)->count();

        if ($statementCount > 0) {
            return redirect()->route('admin.borrowers.index')->with('error', 'Cannot delete: Borrower has related transactions.');
        }

        // No statements, safe to delete
        $borrower->delete();

        return redirect()->route('admin.borrowers.index')->with('success', 'Borrower deleted successfully.');
    }

    public function deleteReturnHistory($id)
{
   $statement = BorrowerStatement::where('id', $id)->first();

    //    dd($statement);  
        if($statement->deposit_type === "Bank"){
            $value=BankStatement::where('transaction_id',$statement->transaction_id)->first();
            
            $value->delete();
        }
        if($statement->deposit_type === "Cash"){
            $value=CashStatement::where('transaction_id',$statement->transaction_id)->first();
            
            $value->delete();

        }

        if (!$statement) {
            return back()->with('error', 'Record not found!');
        }

        // if ($statement->debit > 0) {
        //     return back()->with('error', 'Only return history can be deleted!');
        // }
        $statement->delete();
        return back()->with('success', 'Return history deleted successfully!');
}


public function deleteLoanHistory($id)
{
    $statement = BorrowerStatement::where('id', $id)->first();

    //    dd($statement);  
        if($statement->deposit_type === "Bank"){
            $value=BankStatement::where('transaction_id',$statement->transaction_id)->first();
            
            $value->delete();
        }
        if($statement->deposit_type === "Cash"){
            $value=CashStatement::where('transaction_id',$statement->transaction_id)->first();
            
            $value->delete();

        }

        if (!$statement) {
            return back()->with('error', 'Record not found!');
        }

        // if ($statement->debit > 0) {
        //     return back()->with('error', 'Only return history can be deleted!');
        // }
        $statement->delete();
        return back()->with('success', 'Return history deleted successfully!');
}



}

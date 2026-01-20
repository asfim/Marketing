<?php

namespace App\Http\Controllers;

use App\InvestmentStatement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Investment;
use App\Models\BankStatement;
use App\Models\CashStatement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\BankInfo;
use Illuminate\Support\Facades\Log;
use Psy\Util\Str;


class InvestmentController extends Controller
{
    public function index()
    {
        $investors = Investment::orderBy('updated_at', 'desc')->get();

        return view('admin.investments.index', compact('investors'));
    }

    public function create()
    {
        $banks = BankInfo::all();

        return view('admin.investments.create', compact('banks'));

    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',

        ]);

        DB::beginTransaction();

        try {
            // Log request data
           // Log::info('Investment Store Request Data', $request->all());

            $investorData = [
                'name' => $request->name,
                'phone' => $request->phone,
                'address' => $request->address,

            ];

            Log::info('Investment Insert Data', $investorData);

            Investment::create($investorData);

            DB::commit();

            return redirect()->route('admin.investments.index')->with('success', 'Investment added successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Investment Store Error: ' . $e->getMessage());
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }




  public function show(Request $request, $id)
{
    $investor = Investment::with('bank', 'branch')->findOrFail($id);

    $totalCredit = DB::table('investment_statements')
        ->where('investor_id', $id)
        ->sum('credit');

    $totalDebit = DB::table('investment_statements')
        ->where('investor_id', $id)
        ->sum('debit');

    // Default 30 days range
    $default_from = now()->subDays(30)->format('Y-m-d');
    $default_to = now()->format('Y-m-d');

    // -------- Investment History (Credits) --------
    $investmentQuery = DB::table('investment_statements')
        ->where('investor_id', $id)
        ->where('credit', '>', 0)
        ->orderByDesc('updated_at');

    if ($request->filled('return_date_range')) {
        $date_range = date_range_to_arr($request->get('return_date_range'));
    } else {
        $date_range = [$default_from, $default_to];
    }

    $investmentQuery->whereBetween('posting_date', [
        Carbon::parse($date_range[0])->format('Y-m-d'),
        Carbon::parse($date_range[1])->format('Y-m-d')
    ]);

    if ($request->filled('investment_search_text')) {
        $search = $request->get('investment_search_text');
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
    $returnQuery = DB::table('investment_statements')
        ->where('investor_id', $id)
        ->where('debit', '>', 0)
        ->orderByDesc('updated_at');

    if ($request->filled('investment_date_range')) {
        $return_date_range = date_range_to_arr($request->get('investment_date_range'));
    } else {
        $return_date_range = [$default_from, $default_to];
    }

    $returnQuery->whereBetween('posting_date', [
        Carbon::parse($return_date_range[0])->format('Y-m-d'),
        Carbon::parse($return_date_range[1])->format('Y-m-d')
    ]);

    if ($request->filled('return_search_text')) {
        $search = $request->get('return_search_text');
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
    $statementsQuery = DB::table('investment_statements')
        ->where('investor_id', $id)
        ->orderByDesc('created_at');

    if ($request->filled('investment_date_range')) {
        $statements_date_range = date_range_to_arr($request->get('investment_date_range'));
    } else {
        $statements_date_range = [$default_from, $default_to];
    }

    $statementsQuery->whereBetween('posting_date', [
        Carbon::parse($statements_date_range[0])->format('Y-m-d'),
        Carbon::parse($statements_date_range[1])->format('Y-m-d')
    ]);

    $statements = $statementsQuery->get();

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

    return view('admin.investments.show', [
        'investor' => $investor,
        'totalCredit' => $totalCredit,
        'totalDebit' => $totalDebit,
        'investmentHistory' => $investmentHistory,
        'returnHistory' => $returnHistory,
        'statements' => $statements,
        'selected_tab' => request('tab_type') ?? 'tab-investor-info',
        'return_date_range' => $date_range,
        'investment_date_range' => $return_date_range,
        'statements_date_range' => $statements_date_range,
    ]);
}





    public function addInvestment($id)
    {
        $investor = Investment::findOrFail($id);
        $banks = BankInfo::all();

        return view('admin.investments.add-investment', compact('investor', 'banks'));
    }


    public function saveInvestment(Request $request)
    {


        $request->validate([
            'investor_id' => 'required|exists:investments,id',
            'paid_amount' => 'required|numeric',
            'payment_date' => 'required|date',
            'deposit_type' => 'required|in:Cash,Bank',
            'bank_id' => 'required_if:deposit_type,Bank|nullable|exists:bank_infos,id',
        ]);

        DB::beginTransaction();

        try {
       //     Log::info('Starting saveInvestment', ['request' => $request->all()]);

            $lastTransaction = DB::table('investment_statements')
                ->where('transaction_id', 'like', 'Invest-P-%')
                ->orderByDesc('id')
                ->value('transaction_id');

            if ($lastTransaction) {
                preg_match('/Investment-P-(\d+)/', $lastTransaction, $matches);
                $nextId = isset($matches[1]) ? (int)$matches[1] + 1 : 1;
            } else {
                $nextId = 1;
            }

            $transaction_id = 'Investment-P-' . $nextId;

            $user_id = Auth::id();
            $branchId = Auth::user()->branchId;
            $today = now()->format('Y-m-d');
            $description = $request->description ?? "Investment from investor ID {$request->investor_id}";

            $lastStatement = DB::table('investment_statements')
                ->where('investor_id', $request->investor_id)
                ->latest('id')
                ->first();

            $previousBalance = $lastStatement ? $lastStatement->balance : 0;
            $newBalance =  $request->paid_amount + $previousBalance;

            if ($request->deposit_type === 'Cash') {
               // Log::info('Processing Cash investment');

                DB::table('cash_statements')->insert([
                    'transaction_id' => $transaction_id,
                    'table_name' => 'investment_returns',
                    'posting_date' => $today,
                    'ref_date' => $request->payment_date,
                    'debit' => $request->paid_amount,
                    'credit' => 0,
                    'balance' => $newBalance,
                    'branchId' => $branchId,
                    'description' => $description,
                    'user_id' => $user_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('investment_statements')->insert([
                    'transaction_id' => $transaction_id,
                    'posting_date' => $request->payment_date,
                    'description' => $description,
                    'table_name' => 'investment_returns',
                    'deposit_type' => 'Cash',

                    'debit' =>  $request->paid_amount,
                    'credit' =>0,
                    'balance' => $newBalance,
                    'investor_id' => $request->investor_id,
                    'user_id' => $user_id,
                    'bank_id' => $request->bank_id,
                    'branchId' => $branchId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

            } else {
              //  Log::info('Processing Bank investment');

                DB::table('bank_statements')->insert([
                    'transaction_id' => $transaction_id,
                    'table_name' => 'investment_returns',
                    'bank_info_id' => $request->bank_id,
                    'posting_date' => $today,
                    'ref_date' => $request->payment_date,
                    'debit' => $request->paid_amount,
                    'credit' => 0,
                    'balance' => $newBalance,
                    'branchId' => $branchId,
                    'description' => $description,
                    'user_id' => $user_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('investment_statements')->insert([
                    'transaction_id' => $transaction_id,
                    'posting_date' => $request->payment_date,
                    'description' => $description,
                    'table_name' => 'investment_returns',
                    'deposit_type' => 'Bank',
                    'debit' => $request->paid_amount,

                    'credit' => 0,
                    'balance' => $newBalance,
                    'investor_id' => $request->investor_id,
                    'user_id' => $user_id,
                    'bank_id' => $request->bank_id,
                    'branchId' => $branchId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

           // Log::info('Investment saved successfully', ['transaction_id' => $transaction_id]);

            return redirect()
                ->route('admin.investments.show', $request->investor_id)
                ->with('success', 'Investment added successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error saving investment:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }






    public function returnForm($id)
    {
        $investor = Investment::findOrFail($id);
        $banks = BankInfo::all();

        return view('admin.investments.return', compact('investor', 'banks'));
    }


    public function processReturn(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'method' => 'required|in:cash,bank',
            'note' => 'nullable|string',
            'bank_id' => 'required_if:method,bank|nullable|exists:bank_infos,id',
            'ref_date' => 'required|date', // Validate that ref_date is a valid date
        ]);

        DB::beginTransaction();

        try {
            $investor = Investment::findOrFail($id);
            $userId = Auth::id();
            $branchId = Auth::user()->branchId;
            $today = now()->format('Y-m-d');
            $refDate = $request->input('ref_date', $today); // Use ref_date from form, default to today if not provided
            $method = ucfirst($request->method);
            $amount = $request->amount;

            // ✅ Generate Incremental Transaction ID
            $lastTransaction = DB::table('investment_statements')
                ->where('transaction_id', 'like', 'Invest-R-%')
                ->orderByDesc('id')
                ->value('transaction_id');

            if ($lastTransaction && preg_match('/Investment-R-(\d+)/', $lastTransaction, $matches)) {
                $nextId = (int)$matches[1] + 1;
            } else {
                $nextId = 1;
            }

            $transactionId = 'Investment-R-' . $nextId;

            // ✅ Get previous balance
            $lastStatement = DB::table('investment_statements')
                ->where('investor_id', $id)
                ->latest('id')
                ->first();

            $previousBalance = $lastStatement ? $lastStatement->balance : 0;
            $newBalance = $previousBalance - $amount;

            // ✅ Investment Statement Entry
            DB::table('investment_statements')->insert([
                'transaction_id' => $transactionId,
                'posting_date' => $today,
                'deposit_type' => $method,
                'description' => $request->note ?? 'Investment return',
                'table_name' => 'investment_returns',
                'debit' => 0,
                'credit' => $amount,
                'balance' => $newBalance,
                'investor_id' => $id,
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
                    'ref_date' => $refDate, // Store ref_date here
                    'description' => $request->note ?? 'Return to investor',
                    'table_name' => 'investment_returns',
                    'debit' => 0,
                    'credit' => $amount,
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
                    'ref_date' => $refDate, // Store ref_date here
                    'description' => $request->note ?? 'Return to investor',
                    'table_name' => 'investment_returns',
                    'debit' => 0,
                    'credit' => $amount,
                    'balance' => $newBalance,
                    'bank_info_id' => $request->bank_id,
                    'branchId' => $branchId,
                    'user_id' => $userId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            return redirect()->route('admin.investments.show', $id)
                ->with('success', 'Return processed successfully.')
                ->with('tab_type', 'tab-return-history');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing return:', [
                'error' => $e->getMessage(),
                'stack' => $e->getTraceAsString(),
                'investor_id' => $id,
            ]);
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }





    public function update(Request $request, $id)
    {
        $investor = Investment::findOrFail($id);

        $investor->name = $request->name;
        $investor->phone = $request->phone;
        $investor->address = $request->address;
        $investor->save();

        return redirect()->back()->with('success', 'Investment updated successfully.');
    }


    public function destroy($id)
    {
        $investor = Investment::findOrFail($id);

        // Check if the investor has any statements
        $statementCount = DB::table('investment_statements')->where('investor_id', $investor->id)->count();

        if ($statementCount > 0) {
            return redirect()->route('admin.investments.index')->with('error', 'Cannot delete: Investment has related transactions.');
        }

        // No statements, safe to delete
        $investor->delete();

        return redirect()->route('admin.investments.index')->with('success', 'Investment deleted successfully.');
    }

    public function deleteReturnHistory($id)
    {
          $statement = InvestmentStatement::findOrFail($id);

    DB::transaction(function () use ($statement) {

    if ($statement->deposit_type === 'Bank') {

        DB::table('bank_statements')
            ->whereRaw('TRIM(transaction_id) = ?', [trim($statement->transaction_id)])
            ->where('table_name', $statement->table_name)
            ->where('bank_info_id', $statement->bank_id)
            ->where('debit', $statement->debit)
            ->whereDate('posting_date', $statement->posting_date)
            ->limit(1) 
            ->delete();
    }

    if ($statement->deposit_type === 'Cash') {

        DB::table('cash_statements')
            ->whereRaw('TRIM(transaction_id) = ?', [trim($statement->transaction_id)])
            ->where('table_name', $statement->table_name)
            ->where('debit', $statement->debit)
            ->whereDate('posting_date', $statement->posting_date)
            ->limit(1)
            ->delete();
    }

 
    $statement->delete();
    });
        return back()->with('success', 'Return history deleted successfully!');
    }




    public function deleteInvestmentHistory($id)
    {
          $statement = InvestmentStatement::findOrFail($id);

DB::transaction(function () use ($statement) {

    if ($statement->deposit_type === 'Bank') {

        DB::table('bank_statements')
            ->whereRaw('TRIM(transaction_id) = ?', [trim($statement->transaction_id)])
            ->where('table_name', $statement->table_name)
            ->where('bank_info_id', $statement->bank_id)
            ->where('debit', $statement->debit)
            ->whereDate('posting_date', $statement->posting_date)
            ->limit(1) 
            ->delete();
    }

    if ($statement->deposit_type === 'Cash') {

        DB::table('cash_statements')
            ->whereRaw('TRIM(transaction_id) = ?', [trim($statement->transaction_id)])
            ->where('table_name', $statement->table_name)
            ->where('debit', $statement->debit)
            ->whereDate('posting_date', $statement->posting_date)
            ->limit(1)
            ->delete();
    }

 
    $statement->delete();
});



return back()->with('success', 'Investment history deleted successfully!');

 }
    




}

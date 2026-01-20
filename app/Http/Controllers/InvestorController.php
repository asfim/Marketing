<?php

namespace App\Http\Controllers;


use Psy\Util\Str;
use Carbon\Carbon;
use App\Models\BankInfo;

use App\Models\Investor;
use App\InestorStatement;
use App\InvestorStatement;
use App\InvestorStatements;
use App\InvestmentStatement;
use Illuminate\Http\Request;
use App\Models\BankStatement;
use App\Models\CashStatement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;


class InvestorController extends Controller
{
    public function index()
    {
        $investors = Investor::orderBy('updated_at', 'desc')->get();

        return view('admin.investors.index', compact('investors'));
    }

    public function create()
    {
        $banks = BankInfo::all();

        return view('admin.investors.create', compact('banks'));

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
            // Log::info('Investor Store Request Data', $request->all());

            $investorData = [
                'name' => $request->name,
                'phone' => $request->phone,
                'address' => $request->address,

            ];

         //  Log::info('Investor Insert Data', $investorData);

            Investor::create($investorData);

            DB::commit();

            return redirect()->route('admin.investors.index')->with('success', 'Investor added successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Investor Store Error: ' . $e->getMessage());
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }




    public function show($id)
    {
        $investor = Investor::with('bank', 'branch')->findOrFail($id);

        $totalCredit = DB::table('investor_statements')
            ->where('investor_id', $id)
            ->sum('credit');

        $totalDebit = DB::table('investor_statements')
            ->where('investor_id', $id)
            ->sum('debit');

        // Investor History (Credits)
        $investorQuery = DB::table('investor_statements')
            ->where('investor_id', $id)
            ->where('credit', '>', 0)
            ->orderByDesc('updated_at');

        if (request('investment_date_range')) {
            [$start, $end] = explode(' - ', request('investment_date_range'));
            $investorQuery->whereBetween('posting_date', [$start, $end]);
        }

        if (request('investment_search_text')) {
            $search = request('investment_search_text');
            $investorQuery->where(function ($query) use ($search) {
                $query->where('description', 'like', "%{$search}%")
                    ->orWhere('deposit_type', 'like', "%{$search}%");
            });
        }

        $investorHistory = $investorQuery->get();

        foreach ($investorHistory as $inv) {
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

        // Return History (Debits)
        $returnQuery = DB::table('investor_statements')
            ->where('investor_id', $id)
            ->where('debit', '>', 0)
            ->orderByDesc('updated_at');

        if (request('return_date_range')) {
            [$start, $end] = explode(' - ', request('return_date_range'));
            $returnQuery->whereBetween('posting_date', [$start, $end]);
        }

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
        //  dd($returnHistory);
        // after $returnHistory

// New: Merged Statements (Investor + return)
        $statements = DB::table('investor_statements')
            ->where('investor_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($statements as $stmt) {
            $stmt->payment_mode = $stmt->deposit_type;
            $stmt->transaction_id = $stmt->transaction_id;
            $stmt->type = $stmt->credit > 0 ? 'Investor' : 'Return'; // Identify type

            if (strtolower($stmt->deposit_type) === 'Bank') {
                $bank = DB::table('bank_infos')->where('id', $stmt->bank_id)->first();
                $branch = DB::table('branches')->where('id', $stmt->branchId)->first();

                $stmt->account_name = $bank->account_name ?? 'Unknown';
                $stmt->bank_name = $bank->bank_name ?? 'Unknown';
                $stmt->branch_name = $branch->name ?? 'N/A';
            } elseif (strtolower($stmt->deposit_type) === 'Cash') {
                $stmt->account_name = 'Cash';
                $stmt->bank_name = 'N/A';
                $stmt->branch_name = 'N/A';
            } else {
                $stmt->account_name = 'Unknown';
                $stmt->bank_name = 'Unknown';
                $stmt->branch_name = 'N/A';
            }
        }



        return view('admin.investors.show', [
            'investor' => $investor,
            'totalCredit' => $totalCredit,
            'totalDebit' => $totalDebit,
            'investmentHistory' => $investorHistory,
            'returnHistory' => $returnHistory,
            'statements' => $statements, // <-- Added
            'selected_tab' => request('tab_type') ?? 'tab-investor-info',
        ]);

    }





    public function addInvestment($id)
    {
        $investor = Investor::findOrFail($id);
        $banks = BankInfo::all();

        return view('admin.investors.add-investor', compact('investor', 'banks'));
    }


    public function saveInvestment(Request $request)
    {



        $request->validate([
            'investor_id' => 'required|exists:investors,id',
            'paid_amount' => 'required|numeric',
            'payment_date' => 'required|date',
            'deposit_type' => 'required|in:Cash,Bank',
            'bank_id' => 'required_if:deposit_type,Bank|nullable|exists:bank_infos,id',
        ]);

        DB::beginTransaction();

        try {
            //     Log::info('Starting saveInvestment', ['request' => $request->all()]);

            $lastTransaction = DB::table('investor_statements')
                ->where('transaction_id', 'like', 'Invest-P-%')
                ->orderByDesc('id')
                ->value('transaction_id');

            if ($lastTransaction) {
                preg_match('/Invest-P-(\d+)/', $lastTransaction, $matches);
                $nextId = isset($matches[1]) ? (int)$matches[1] + 1 : 1;
            } else {
                $nextId = 1;
            }

            $transaction_id = 'Invest-P-' . $nextId;

            $user_id = Auth::id();
            $branchId = Auth::user()->branchId;
            $today = now()->format('Y-m-d');
            $description = $request->description ?? "Investor from investor ID {$request->investor_id}";

            $lastStatement = DB::table('investor_statements')
                ->where('investor_id', $request->investor_id)
                ->latest('id')
                ->first();

            $previousBalance = $lastStatement ? $lastStatement->balance : 0;
            $newBalance =  $request->paid_amount + $previousBalance;

            if ($request->deposit_type === 'Cash') {
                // Log::info('Processing Cash investment');

                DB::table('cash_statements')->insert([
                    'transaction_id' => $transaction_id,
                    'table_name' => 'investors_payments',
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

                DB::table('investor_statements')->insert([
                    'transaction_id' => $transaction_id,
                    'posting_date' => $request->payment_date,
                    'description' => $description,
                    'table_name' => 'investors_payments',
                    'deposit_type' => 'Cash',

                    'debit' => 0,
                    'credit' => $request->paid_amount,
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
                    'table_name' => 'investors_payments',
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

                DB::table('investor_statements')->insert([
                    'transaction_id' => $transaction_id,
                    'posting_date' => $request->payment_date,
                    'description' => $description,
                    'table_name' => 'investors_payments',
                    'deposit_type' => 'Bank',
                    'debit' => 0,

                    'credit' => $request->paid_amount,
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
                ->route('admin.investors.show', $request->investor_id)
                ->with('success', 'Investment added successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error saving Investor:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }






    public function returnForm($id)
    {
        $investor = Investor::findOrFail($id);
        $banks = BankInfo::all();

        return view('admin.investors.return', compact('investor', 'banks'));
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
            $investor = Investor::findOrFail($id);
            $userId = Auth::id();
            $branchId = Auth::user()->branchId;
            $today = now()->format('Y-m-d');
            $refDate = $request->input('ref_date', $today); // Use ref_date from form, default to today if not provided
            $method = ucfirst($request->method);
            $amount = $request->amount;

            // ✅ Generate Incremental Transaction ID
            $lastTransaction = DB::table('investor_statements')
                ->where('transaction_id', 'like', 'Invest-R-%')
                ->orderByDesc('id')
                ->value('transaction_id');

            if ($lastTransaction && preg_match('/Invest-R-(\d+)/', $lastTransaction, $matches)) {
                $nextId = (int)$matches[1] + 1;
            } else {
                $nextId = 1;
            }

            $transactionId = 'Invest-R-' . $nextId;

            // ✅ Get previous balance
            $lastStatement = DB::table('investor_statements')
                ->where('investor_id', $id)
                ->latest('id')
                ->first();

            $previousBalance = $lastStatement ? $lastStatement->balance : 0;
            $newBalance = $previousBalance - $amount;

            // ✅ Investment Statement Entry
            DB::table('investor_statements')->insert([
                'transaction_id' => $transactionId,
                'posting_date' => $today,
                'deposit_type' => $method,
                'description' => $request->note ?? 'Investor return',
                'table_name' => 'investors_returns',
                'debit' => $amount,
                'credit' => 0,
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
                    'table_name' => 'investors_returns',
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
                    'ref_date' => $refDate, // Store ref_date here
                    'description' => $request->note ?? 'Return to investor',
                    'table_name' => 'investors_returns',
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

            return redirect()->route('admin.investors.show', $id)
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
        $investor = Investor::findOrFail($id);

        $investor->name = $request->name;
        $investor->phone = $request->phone;
        $investor->address = $request->address;
        $investor->save();

        return redirect()->back()->with('success', 'Investor updated successfully.');
    }


    public function destroy($id)
    {
        
        $investor = Investor::findOrFail($id);

        // Check if the investor has any statements
        $statementCount = DB::table('investor_statements')->where('investor_id', $investor->id)->count();

        if ($statementCount > 0) {
            return redirect()->route('admin.investors.index')->with('error', 'Cannot delete: Investor has related transactions.');
        }

        // No statements, safe to delete
        $investor->delete();

        return redirect()->route('admin.investors.index')->with('success', 'Investor deleted successfully.');
    }

    public function deleteReturnHistory($id)
    
    {
      $statement = InvestorStatement::where('id', $id)->first();

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

 public function deleteInvestmentHistory($id)
{
    $statement = InvestorStatement::find($id);

    if (!$statement) {
        return back()->with('error', 'Record not found!');
    }

    // Delete from Bank
    if ($statement->deposit_type === "Bank") {
        $value = BankStatement::where('transaction_id', $statement->transaction_id)->first();
        if ($value) {
            $value->delete();
        }
    }

    // Delete from Cash
    if ($statement->deposit_type === "Cash") {
        $value = CashStatement::where('transaction_id', $statement->transaction_id)->first();
        if ($value) {
            $value->delete();
        }
    }

    // Delete main record
    $statement->delete();

    return back()->with('success', 'Investor history deleted successfully!');
}

    



}

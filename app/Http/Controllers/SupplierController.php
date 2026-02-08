<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\BankInfo;
use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Models\BankStatement;
use App\Models\CashStatement;
use Illuminate\Support\Carbon;
use App\Models\ProductPurchase;
use App\Models\SupplierPayment;
use App\Models\SupplierStatement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SupplierController extends Controller
{
    public function updateBalance(Request $request)
    {
        // For updating all balances
        if ($request->has('balances')) {
            foreach ($request->balances as $supplierId => $debitValue) {
                if (!empty($debitValue)) {
                    // Check if a record exists for this supplier
                    $existingStatement = SupplierStatement::where('supplier_id', $supplierId)->first();

                    // Get user ID and branchId from the request or the current user
                    $userId = auth()->id() ?? 1;
                    $branchId = $request->branchId ?? null;  // Assuming you have a branchId coming from the request

                    if ($existingStatement) {
                        // Update the existing record
                        $existingStatement->update([
                            'transaction_id' => $existingStatement->transaction_id,  // Keep the same transaction ID
                            'posting_date' => now()->toDateString(),
                            'description' => 'Opening Balance',
                            'table_name' => 'supplier_payments',
                            'debit' => $debitValue,
                            'credit' => 0,  // Assuming you don't have credit for this
                            'balance' => 0,
                            'user_id' => $userId,
                            'branchId' => $branchId,
                            'updated_at' => now(),
                        ]);
                    } else {
                        // Insert new record
                        $lastTransaction = SupplierStatement::latest('id')->first();
                        $nextTransactionId = $lastTransaction
                            ? 'supplier_payment_' . ((int)str_replace('supplier_payment_', '', $lastTransaction->transaction_id) + 1)
                            : 'supplier_payment_1';

                        SupplierStatement::create([
                            'transaction_id' => $nextTransactionId,
                            'posting_date' => now()->toDateString(),
                            'description' => 'Opening Balance',
                            'table_name' => 'supplier_payments',
                            'debit' => $debitValue,
                            'credit' => 0,
                            'balance' => 0,
                            'supplier_id' => $supplierId,
                            'user_id' => $userId,
                            'branchId' => $branchId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }

        // For updating a single balance
        if ($request->has('update_single')) {
            foreach ($request->update_single as $supplierId => $value) {
                $debitValue = $request->balances[$supplierId] ?? 0; // Handle if no balance is provided

                // Get user ID and branchId from the request or the current user
                $userId = auth()->id() ?? 1;
                $branchId = $request->branchId ?? null;  // Assuming you have a branchId coming from the request

                // Handle the update of a single supplier
                $existingStatement = SupplierStatement::where('supplier_id', $supplierId)->first();
                if ($existingStatement) {
                    $existingStatement->update([
                        'transaction_id' => $existingStatement->transaction_id,  // Keep the same transaction ID
                        'posting_date' => now()->toDateString(),
                        'description' => 'Opening Balance',
                        'table_name' => 'supplier_payments',
                        'debit' => $debitValue,
                        'credit' => 0,
                        'balance' => 0,
                        'user_id' => $userId,
                        'branchId' => $branchId,
                        'updated_at' => now(),
                    ]);
                } else {
                    // If there's no existing statement, create a new one
                    $lastTransaction = SupplierStatement::latest('id')->first();
                    $nextTransactionId = $lastTransaction
                        ? 'supplier_payment_' . ((int)str_replace('supplier_payment_', '', $lastTransaction->transaction_id) + 1)
                        : 'supplier_payment_1';

                    SupplierStatement::create([
                        'transaction_id' => $nextTransactionId,
                        'posting_date' => now()->toDateString(),
                        'description' => 'Opening Balance',
                        'table_name' => 'supplier_payments',
                        'debit' => $debitValue,
                        'credit' => 0,
                        'balance' => 0,
                        'supplier_id' => $supplierId,
                        'user_id' => $userId,
                        'branchId' => $branchId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        return redirect()->back()->with('success', 'Balances updated successfully!');
    }


    public function index(Request $request)
    {
        $suppliers = new Supplier();
        if ($request->search_text != "") {
            $suppliers = $suppliers->where('name', 'LIKE', '%' . $request->search_text . '%')
                ->orWhere('email', 'LIKE', '%' . $request->search_text . '%')
                ->orWhere('phone', 'LIKE', $request->search_text . '%')
                ->orWhere('extra_phone_no', 'LIKE', '%' . $request->search_text . '%')
                ->orderBy('id', 'DESC')
                ->get();
        } else {
            $suppliers = $suppliers->orderBy('id', 'DESC')->get();
        }
        return view('admin.supplier.view_supplier', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
            'phone' => 'required',
        ];

        $this->validate($request, $rules);

        $user_data = Auth::user();
        $supplier = new Supplier();
        $supplier->name = $request->name;
        $supplier->email = $request->email;
        $supplier->phone = $request->phone;
        $supplier->address = $request->address;
        $supplier->extra_phone_no = $request->extra_phone_no;
        $supplier->user_id = $user_data->id;
        $status = $supplier->save();

        if ($status) {

            Session::flash('message', 'Data Saved Successfully!');
            Session::flash('m-class', 'alert-success');
            return redirect()->route('supplier.index');
        } else {
            Session::flash('message', 'Data Save Failed!');
            Session::flash('m-class', 'alert-danger');
            return redirect()->back();
        }

    }


        //SEARCH ON PURCHASE TAB
//     public function show(Request $request, $id)
// {
    
//     $user_data = Auth::user();
//     $supplier = Supplier::findOrFail($id);
//     $branches = Branch::all();
//     $selected_tab = $request->tab_type ?? 'tab-profile';
//     $purchases = $supplier->purchases();
//     $check_p = $supplier->checkedPurchases();

//     // Initialize default date ranges
//     $billinfo_date_range = $request->filled('billinfo_date_range')
//         ? date_range_to_arr($request->billinfo_date_range)
//         : [date('Y-m-d', strtotime('-30 days')), date('Y-m-d')];

//     $payment_date_range = $request->filled('payment_date_range')
//         ? date_range_to_arr($request->payment_date_range)
//         : [date('Y-m-d', strtotime('-30 days')), date('Y-m-d')];

//     $statement_date_range = $request->filled('statement_date_range')
//         ? date_range_to_arr($request->statement_date_range)
//         : [date('Y-m-d', strtotime('-30 days')), date('Y-m-d')];

//     // SEARCH ON PURCHASE TAB
//     // if ($selected_tab == 'tab-purchase') {
//     //     // Apply date range filter
//     //     $purchases = $purchases->whereBetween('received_date', [
//     //          Carbon::parse($purchase_date_range[0])->format('Y-m-d'),
//     //         Carbon::parse($purchase_date_range[1])->format('Y-m-d'),
//     //     ]);

//     //     $check_p = $check_p->whereBetween('received_date', [
//     //         Carbon::parse($purchase_date_range[0])->format('Y-m-d'),
//     //         Carbon::parse($purchase_date_range[1])->format('Y-m-d'),
//     //     ]);

//     //     // Apply branch filter for non-admin users if tutul is want then open it 
//     //     // if ($user_data->id != 1) {
//     //     //     $purchases = $purchases->where('branchId', $user_data->branchId);
//     //     //     $check_p = $check_p->where('branchId', $user_data->branchId); 
//     //     // }
//     //     $admin_ids = [1,21,22,23];

//     //     if (!in_array($user_data->id, $admin_ids)) {
//     //         $purchases = $purchases->where('branchId', $user_data->branchId);
//     //         $check_p = $check_p->where('branchId', $user_data->branchId);
//     //     }

//     //     // Apply search filter
//     //     if ($request->filled('purchase_search_text')) {
//     //         $purchases = $purchases->where(function ($query) use ($request) {
//     //             $query->where('dmr_no', $request->purchase_search_text)
//     //                 ->orWhere('chalan_no', $request->purchase_search_text)
//     //                 ->orWhere('product_qty', $request->purchase_search_text)
//     //                 ->orWhere('rate_per_unit', $request->purchase_search_text)
//     //                 ->orWhere('material_cost', $request->purchase_search_text)
//     //                 ->orWhere('total_material_cost', $request->purchase_search_text)
//     //                 ->orWhereHas('product_name', function ($q) use ($request) {
//     //                     $q->where('name', 'like', '%' . $request->purchase_search_text . '%');
//     //                 });
//     //         });

//     //         $check_p = $check_p->where(function ($query) use ($request) {
//     //             $query->where('dmr_no', $request->purchase_search_text)
//     //                 ->orWhere('chalan_no', $request->purchase_search_text)
//     //                 ->orWhere('product_qty', $request->purchase_search_text)
//     //                 ->orWhere('rate_per_unit', $request->purchase_search_text)
//     //                 ->orWhere('material_cost', $request->purchase_search_text)
//     //                 ->orWhere('total_material_cost', $request->purchase_search_text)
//     //                 ->orWhereHas('product_name', function ($q) use ($request) {
//     //                     $q->where('name', 'like', '%' . $request->purchase_search_text . '%');
//     //                 });
//     //         });
//     //     }

//     //     // Execute queries
//     //     $purchases = $purchases->orderByDesc('received_date')->get();
//     //     $check_p = $check_p->orderByDesc('received_date')->get();
//     // } else {
//     //     // Default purchase data for other tabs
//     //     $purchases = $supplier->purchases()->get();
//     //     $check_p = $supplier->checkedPurchases()->get();
//     // }



// $purchase_date_range = [date('Y-m-d', strtotime('-30 days')), date('Y-m-d')];

// $start_date = $purchase_date_range[0];
// $end_date   = $purchase_date_range[1];

// // Apply default date range for all tabs
// $purchases = $purchases->whereBetween('received_date', [$start_date, $end_date]);

// // When tab is purchase → apply filters
// if ($selected_tab == 'tab-purchase') {
// $check_p   = $check_p->whereBetween('received_date', [$start_date, $end_date]);


//     // If user selects a date range
//     if ($request->filled('purchase_date_range')) {
//         $purchase_date_range = date_range_to_arr($request->purchase_date_range);

//         $start_date = Carbon::parse($purchase_date_range[0])->format('Y-m-d');
//         $end_date   = Carbon::parse($purchase_date_range[1])->format('Y-m-d');

//         $purchases = $purchases->whereBetween('received_date', [$start_date, $end_date]);
//         $check_p   = $check_p->whereBetween('received_date', [$start_date, $end_date]);
//     }

//     // Branch filter except admins
//     $admin_ids = [1, 21, 22, 23];
//     if (!in_array($user_data->id, $admin_ids)) {
//         $purchases = $purchases->where('branchId', $user_data->branchId);
//         $check_p   = $check_p->where('branchId', $user_data->branchId);
//     }

//     // Search filter
//     if ($request->filled('purchase_search_text')) {
//         $search = $request->purchase_search_text;

//         $purchases = $purchases->where(function ($query) use ($search) {
//             $query->where('dmr_no', $search)
//                   ->orWhere('chalan_no', $search)
//                   ->orWhere('product_qty', $search)
//                   ->orWhere('rate_per_unit', $search)
//                   ->orWhere('material_cost', $search)
//                   ->orWhere('total_material_cost', $search)
//                   ->orWhereHas('product_name', function ($q) use ($search) {
//                       $q->where('name', 'like', '%' . $search . '%');
//                   });
//         });

//         $check_p = $check_p->where(function ($query) use ($search) {
//             $query->where('dmr_no', $search)
//                   ->orWhere('chalan_no', $search)
//                   ->orWhere('product_qty', $search)
//                   ->orWhere('rate_per_unit', $search)
//                   ->orWhere('material_cost', $search)
//                   ->orWhere('total_material_cost', $search)
//                   ->orWhereHas('product_name', function ($q) use ($search) {
//                       $q->where('name', 'like', '%' . $search . '%');
//                   });
//         });
//     }
// }

// // Final output
// $purchases = $purchases->orderByDesc('received_date')->get();
// $check_p   = $check_p->orderByDesc('received_date')->get();


//     if ($selected_tab === 'tab-billinfo') {
//     // START FROM FRESH CHECKED PURCHASES QUERY
//     $check_p = $supplier->checkedPurchases();

//     // 🔍 Date Range Filter (billinfo_date_range)
//     if ($request->filled('billinfo_date_range')) {
//         $dates = explode(' - ', $request->billinfo_date_range);
//         if (count($dates) === 2) {
//             try {
//                 $start = Carbon::parse($dates[0])->startOfDay();
//                 $end = Carbon::parse($dates[1])->endOfDay();
//                 $check_p = $check_p->whereBetween('received_date', [$start, $end]);
//             } catch (\Exception $e) {
//                 Log::warning('Invalid date range in Bill Info tab', ['input' => $request->billinfo_date_range]);
//             }
//         }
//     }

//     // 🔎 Search Filter (billinfo_search_text)
//     if ($request->filled('billinfo_search_text')) {
//         $term = '%' . $request->billinfo_search_text . '%';
//         $check_p = $check_p->where(function ($q) use ($term) {
//             $q->where('dmr_no', 'like', $term)
//               ->orWhere('chalan_no', 'like', $term)
//               ->orWhere('bill_no', 'like', $term)
//               ->orWhere('vehicle_no', 'like', $term)
//               ->orWhere('product_qty', 'like', $term)
//               ->orWhere('rate_per_unit', 'like', $term)
//               ->orWhere('material_cost', 'like', $term)
//               ->orWhere('truck_rent', 'like', $term)
//               ->orWhere('unload_bill', 'like', $term)
//               ->orWhere('total_material_cost', 'like', $term)
//               ->orWhereHas('product_name', fn($sq) => $sq->where('name', 'like', $term))
//               ->orWhereHas('supplier', fn($sq) => $sq->where('name', 'like', $term));
//         });
//     }

//     // 🌐 Branch Filter (non-admins)
//     $admin_ids = [1, 21, 22, 23];
//     if (!in_array($user_data->id, $admin_ids)) {
//         $check_p = $check_p->where('branchId', $user_data->branchId);
//     }

//     // ✅ Execute query
//     $check_p = $check_p->orderByDesc('received_date')->get();
    
//     // $purchases unchanged (other tabs will use it)
// }
        
//         //SEARCH ON PAYMENT TAB

//         if ($selected_tab == 'tab-payment') {

//             //  Use query builder
//             $payments = $supplier->payments();
            

//             if ($request->filled('payment_date_range')) {

//                 $payment_date_range = date_range_to_arr($request->payment_date_range);

//                 $payments->whereBetween('ref_date', [
//                     Carbon::parse($payment_date_range[0])->format('Y-m-d'),
//                     Carbon::parse($payment_date_range[1])->format('Y-m-d')
//                 ]);
//             }

//             if ($request->payment_search_text) {
//                 $payments->where(function ($query) use ($request) {
//                     $query->where('voucher_no', 'LIKE', $request->payment_search_text . '%')
//                         ->orWhere('payment_mode', 'LIKE', '%' . $request->payment_search_text . '%')
//                         ->orWhere('paid_amount', $request->payment_search_text)
//                         ->orWhereHas('bank_info', function ($q) use ($request) {
//                             $q->where('bank_name', 'LIKE', '%' . $request->payment_search_text . '%');
//                         });
//                 });
//             }

//             //  Apply order and get results
//             $payments = $payments->orderByDesc('ref_date')->get();

//         } else {
//             $payment_date_range = [date('Y-m-d', strtotime('-30 days')), date('Y-m-d')];



//             // Use query builder here too
//             $payments = $supplier->payments()
            
//                 ->when($request->filled('statement_date_range'), function ($q) use ($payment_date_range) {

//                     $q->whereBetween('ref_date', [
//                         Carbon::parse($payment_date_range[0])->format('Y-m-d'),
//                         Carbon::parse($payment_date_range[1])->format('Y-m-d'),
//                     ]);
//                 })
//                 ->orderByDesc('ref_date')
//                 ->get();
//         }




//         //SEARCH ON STATEMENT TAB
//         if ($selected_tab == 'tab-statement') {

//             //  Use query builder
//             $statements = $supplier->supplierStatements();


//             // Apply date range filter
//             if ($request->filled('date_range')) {
//                 $statement_date_range = date_range_to_arr($request->date_range);

//             }else{
//                 $statement_date_range = [date('Y-m-d', strtotime('-30 days')), date('Y-m-d')];
//             }
//                 $statements->whereBetween('posting_date',
//                     [
//                         Carbon::parse($statement_date_range[0])->format('Y-m-d'),
//                         Carbon::parse($statement_date_range[1])->format('Y-m-d')
//                     ]
//                 );

//             //  Apply search filter
//             if ($request->statement_search_text != '') {
//                 $statements->where(function ($query) use ($request) {
//                     $query->where('transaction_id', 'LIKE', '%' . $request->statement_search_text . '%')
//                         ->orWhere('description', '=', $request->statement_search_text)
//                         ->orWhere('debit', 'LIKE', '%' . $request->statement_search_text . '%')
//                         ->orWhere('credit', 'LIKE', '%' . $request->statement_search_text . '%')
//                         ->orWhere('balance', 'LIKE', '%' . $request->statement_search_text . '%')
//                         ->orWhereHas('supplier', function ($q) use ($request) {
//                             $q->where('name', 'LIKE', '%' . $request->statement_search_text . '%');
//                         });
//                 });
//             }

//             //  Order and get
//             $statements = $statements->orderByDesc('posting_date')->get();

//         } else {
//             $statement_date_range = [date('Y-m-d', strtotime('-30 days')), date('Y-m-d')];


//             //  Also use query builder here
//             $statements = $supplier->supplierStatements()->whereBetween('posting_date', [
//                 Carbon::parse($statement_date_range[0])->format('Y-m-d'),
//                 Carbon::parse($statement_date_range[1])->format('Y-m-d'),
//             ])->orderByDesc('posting_date')->get();

//         }




//         $total_purchase = ProductPurchase::where('supplier_id', $id)->sum('material_cost');
//         $total_payment = SupplierPayment::where('supplier_id', $id)->sum('paid_amount');
//         // dd(isset($purchases), $purchases->get() ?? 'NO');

      


//         return view('admin.supplier.supplier_profile', compact('supplier', 'branches', 'selected_tab',
//             'purchases', 'check_p', 'payments', 'statements', 'total_purchase', 'total_payment'));
//     }



public function show(Request $request, $id)
{
    $user_data = Auth::user();
    $supplier = Supplier::findOrFail($id);
    $branches = Branch::all();
    $selected_tab = $request->tab_type ?? 'tab-profile';
    
    $admin_ids = [1, 21, 22, 23];
    $is_admin = in_array($user_data->id, $admin_ids);

    // ========== PURCHASE DATA (for profile tab summary + purchase tab) ==========
    // Always load last 30 days data for profile tab summary
    $purchases = $supplier->purchases()
        ->whereBetween('received_date', [
            date('Y-m-d', strtotime('-30 days')),
            date('Y-m-d')
        ])
        ->orderByDesc('received_date')
        ->get();
    
    $check_p = $supplier->checkedPurchases()
        ->whereBetween('received_date', [
            date('Y-m-d', strtotime('-30 days')),
            date('Y-m-d')
        ])
        ->orderByDesc('received_date')
        ->get();
    
    // If on PURCHASE tab, apply filters
    if ($selected_tab == 'tab-purchase') {
        $purchase_date_range = $request->filled('purchase_date_range') 
            ? date_range_to_arr($request->purchase_date_range)
            : [date('Y-m-d', strtotime('-30 days')), date('Y-m-d')];
        
        $start_date = Carbon::parse($purchase_date_range[0])->format('Y-m-d');
        $end_date = Carbon::parse($purchase_date_range[1])->format('Y-m-d');
        
        // Fresh queries with filters
        $purchases = $supplier->purchases()
            ->whereBetween('received_date', [$start_date, $end_date])
            ->when(!$is_admin, function ($q) use ($user_data) {
                return $q->where('branchId', $user_data->branchId);
            })
            ->when($request->filled('purchase_search_text'), function ($q) use ($request) {
                $search = $request->purchase_search_text;
                return $q->where(function ($query) use ($search) {
                    $query->where('dmr_no', $search)
                        ->orWhere('chalan_no', $search)
                        ->orWhere('product_qty', $search)
                        ->orWhere('rate_per_unit', $search)
                        ->orWhere('material_cost', $search)
                        ->orWhere('total_material_cost', $search)
                        ->orWhereHas('product_name', function ($q) use ($search) {
                            $q->where('name', 'like', '%' . $search . '%');
                        });
                });
            })
            ->orderByDesc('received_date')
            ->get();
        
        $check_p = $supplier->checkedPurchases()
            ->whereBetween('received_date', [$start_date, $end_date])
            ->when(!$is_admin, function ($q) use ($user_data) {
                return $q->where('branchId', $user_data->branchId);
            })
            ->when($request->filled('purchase_search_text'), function ($q) use ($request) {
                $search = $request->purchase_search_text;
                return $q->where(function ($query) use ($search) {
                    $query->where('dmr_no', $search)
                        ->orWhere('chalan_no', $search)
                        ->orWhere('product_qty', $search)
                        ->orWhere('rate_per_unit', $search)
                        ->orWhere('material_cost', $search)
                        ->orWhere('total_material_cost', $search)
                        ->orWhereHas('product_name', function ($q) use ($search) {
                            $q->where('name', 'like', '%' . $search . '%');
                        });
                });
            })
            ->orderByDesc('received_date')
            ->get();
    }

    // ========== BILL INFO TAB ==========
    // Load ALL data by default (initial state)
    $check_pb = $supplier->checkedPurchases()
        ->when(!$is_admin, function ($q) use ($user_data) {
            return $q->where('branchId', $user_data->branchId);
        })
        ->orderByDesc('received_date')
        ->get();
    
    // If on BILL INFO tab, apply filters
    if ($selected_tab === 'tab-billinfo') {
        $check_pb_query = $supplier->checkedPurchases();
        
        // ONLY apply date filter if user selects a range
        if ($request->filled('billinfo_date_range')) {
            $dates = explode(' - ', $request->billinfo_date_range);
            if (count($dates) === 2) {
                try {
                    $start = Carbon::parse($dates[0])->startOfDay();
                    $end = Carbon::parse($dates[1])->endOfDay();
                    $check_pb_query = $check_pb_query->whereBetween('received_date', [$start, $end]);
                } catch (\Exception $e) {
                    Log::warning('Invalid date range in Bill Info tab', ['input' => $request->billinfo_date_range]);
                }
            }
        }

        
        // Search filter
        if ($request->filled('billinfo_search_text')) {
    $term = '%' . $request->billinfo_search_text . '%';
    $check_pb_query = $check_pb_query->where(function ($q) use ($term) {
        $q->where('dmr_no', 'like', $term)
            ->orWhere('chalan_no', 'like', $term)
            ->orWhere('bill_no', 'like', $term)
            ->orWhere('vehicle_no', 'like', $term)
            ->orWhere('product_qty', 'like', $term)
            ->orWhere('rate_per_unit', 'like', $term)
            ->orWhere('material_cost', 'like', $term)
            ->orWhere('truck_rent', 'like', $term)
            ->orWhere('unload_bill', 'like', $term)
            ->orWhere('total_material_cost', 'like', $term)
            ->orWhereHas('product_name', function ($sq) use ($term) {
                $sq->where('name', 'like', $term);
            })
            ->orWhereHas('supplier', function ($sq) use ($term) {
                $sq->where('name', 'like', $term);
            });
    });
}
        
        // Branch filter
        if (!$is_admin) {
            $check_pb_query = $check_pb_query->where('branchId', $user_data->branchId);
        }
        
        $check_pb = $check_pb_query->orderByDesc('received_date')->get();
    }

    // ========== PAYMENT TAB ==========
    $payments = $supplier->payments()
        ->whereBetween('ref_date', [
            date('Y-m-d', strtotime('-30 days')),
            date('Y-m-d')
        ])
        ->orderByDesc('ref_date')
        ->get();
    
    if ($selected_tab == 'tab-payment') {
        $payment_date_range = $request->filled('payment_date_range') 
            ? date_range_to_arr($request->payment_date_range)
            : [date('Y-m-d', strtotime('-30 days')), date('Y-m-d')];
        
        $payments = $supplier->payments()
            ->whereBetween('ref_date', [
                Carbon::parse($payment_date_range[0])->format('Y-m-d'),
                Carbon::parse($payment_date_range[1])->format('Y-m-d')
            ])
            ->when($request->filled('payment_search_text'), function ($q) use ($request) {
                return $q->where(function ($query) use ($request) {
                    $query->where('voucher_no', 'LIKE', '%' . $request->payment_search_text . '%')
                        ->orWhere('payment_mode', 'LIKE', '%' . $request->payment_search_text . '%')
                        ->orWhere('paid_amount', $request->payment_search_text)
                        ->orWhereHas('bank_info', function ($q) use ($request) {
                            $q->where('bank_name', 'LIKE', '%' . $request->payment_search_text . '%');
                        });
                });
            })
            ->orderByDesc('ref_date')
            ->get();
    }

    // ========== STATEMENT TAB ==========
    // $statements = $supplier->supplierStatements()
    //     ->whereBetween('posting_date', [
    //         date('Y-m-d', strtotime('-30 days')),
    //         date('Y-m-d')
    //     ])
    //     ->orderByDesc('posting_date')
    //     ->get();
    
    // if ($selected_tab == 'tab-statement') {
    //     $statement_date_range = $request->filled('date_range') 
    //         ? date_range_to_arr($request->date_range)
    //         : [date('Y-m-d', strtotime('-30 days')), date('Y-m-d')];

            
        
    //     $statements = $supplier->supplierStatements()
    //         ->whereBetween('posting_date', [
    //             Carbon::parse($statement_date_range[0])->format('Y-m-d'),
    //             Carbon::parse($statement_date_range[1])->format('Y-m-d')
    //         ])
    //         ->when($request->filled('statement_search_text'), function ($q) use ($request) {
    //             return $q->where(function ($query) use ($request) {
    //                 $query->where('transaction_id', 'LIKE', '%' . $request->statement_search_text . '%')
    //                     ->orWhere('description', 'LIKE', '%' . $request->statement_search_text . '%')
    //                     ->orWhere('debit', 'LIKE', '%' . $request->statement_search_text . '%')
    //                     ->orWhere('credit', 'LIKE', '%' . $request->statement_search_text . '%')
    //                     ->orWhere('balance', 'LIKE', '%' . $request->statement_search_text . '%')
    //                     ->orWhereHas('supplier', function ($q) use ($request) {
    //                         $q->where('name', 'LIKE', '%' . $request->statement_search_text . '%');
    //                     });
    //             });
    //         })
    //         ->orderByDesc('posting_date')
    //         ->get();
    // }
$statements = $supplier->supplierStatements()
    ->whereBetween('posting_date', [
        date('Y-m-d', strtotime('-30 days')),
        date('Y-m-d')
    ])
    ->orderByDesc('posting_date')
    ->get();
$opening_balance = 0;
$opening_date = date('Y-m-d');

if ($selected_tab == 'tab-statement') {
    $statement_date_range = $request->filled('date_range') 
        ? date_range_to_arr($request->date_range)
        : [date('Y-m-d', strtotime('-30 days')), date('Y-m-d')];

    // Calculate Opening Balance (sum of all transactions BEFORE the start date)
    $opening_balance = $supplier->supplierStatements()
        ->where('posting_date', '<', Carbon::parse($statement_date_range[0])->format('Y-m-d'))
        ->sum(DB::raw('credit - debit'));
    
    // Get opening balance date (last transaction before start date or first day of range - 1)
    $opening_date = Carbon::parse($statement_date_range[0])->subDay()->format('Y-m-d');

    $statements = $supplier->supplierStatements()
        ->whereBetween('posting_date', [
            Carbon::parse($statement_date_range[0])->format('Y-m-d'),
            Carbon::parse($statement_date_range[1])->format('Y-m-d')
        ])
        ->when($request->filled('statement_search_text'), function ($q) use ($request) {
            return $q->where(function ($query) use ($request) {
                $query->where('transaction_id', 'LIKE', '%' . $request->statement_search_text . '%')
                    ->orWhere('description', 'LIKE', '%' . $request->statement_search_text . '%')
                    ->orWhere('debit', 'LIKE', '%' . $request->statement_search_text . '%')
                    ->orWhere('credit', 'LIKE', '%' . $request->statement_search_text . '%')
                    ->orWhere('balance', 'LIKE', '%' . $request->statement_search_text . '%')
                    ->orWhereHas('supplier', function ($q) use ($request) {
                        $q->where('name', 'LIKE', '%' . $request->statement_search_text . '%');
                    });
            });
        })
        ->with(['supplier_payment.bank_info', 'product_purchase'])
        ->orderBy('posting_date')
        ->orderBy('id')
        ->get();
        $purchases = $supplier->purchases()->get();
}
    // ========== TOTALS ==========
    $total_purchase = ProductPurchase::where('supplier_id', $id)->sum('material_cost');
    $total_payment = SupplierPayment::where('supplier_id', $id)->sum('paid_amount');

    return view('admin.supplier.supplier_profile', compact(
        'supplier', 'branches', 'selected_tab',
        'purchases', 'check_p', 'check_pb', 'payments', 'statements', 
        'total_purchase', 'total_payment','opening_balance','opening_date'
    ));
}
    public function update(Request $request)
    {
        $rules = [
            'id' => 'required',
            'name' => 'required',
            'phone' => 'required',
        ];

        $this->validate($request, $rules);

        $supplier = Supplier::find($request->id);
        $supplier->name = $request->name;
        $supplier->email = $request->email;
        $supplier->phone = $request->phone;
        $supplier->address = $request->address;
        $supplier->extra_phone_no = $request->extra_phone_no;

        $status = $supplier->save();

        if ($status) {
            Session::flash('message', 'Data Updated Successfully!');
            Session::flash('m-class', 'alert-success');
            return redirect()->route('supplier.index');
        } else {
            Session::flash('message', 'Data Save Failed!');
            Session::flash('m-class', 'alert-danger');
            return redirect()->back();
        }
    }

    public function destroy($id)
    {
        $supplier = Supplier::find($id);
        $supplier->delete();

        Session::flash('message', 'Deleted Successfully!');
        Session::flash('m-class', 'alert-success');
        return redirect()->back();
    }

    public function viewPaymentForm()
    {
        $voucher_no = SupplierPayment::max('id') + 1;
        $suppliers = Supplier::orderBy('name')->get();
        $banks = BankInfo::orderBy('bank_name')->get();
        $branches = Branch::orderBy('name')->get();
        return view('admin.supplier.supplier_payment', compact('voucher_no', 'suppliers', 'banks', 'branches'));
    }

    public function loadSupplierBalance(Request $request)
    {
        $supplier = Supplier::find($request->supplier_id);
        return response()->json(['balance' => $supplier->balanceText()]);
    }

    public function saveSupplierPayment(Request $request)
    {
        $rules = [
            'voucher_no' => 'required|unique:supplier_payments',
            'supplier_id' => 'required|numeric',
            'payment_mode' => 'required',
            'bank_id' => 'required_if:payment_mode,==,Bank',
            'paid_amount' => 'required|numeric|min:0',
            'adjustment_amount' => 'nullable|numeric',
            'file.*' => 'nullable|mimes:jpeg,bmp,png,doc,docx,pdf,xls,xlsx,xlsm,txt'
        ];

        $this->validate($request, $rules);
        $user_data = Auth::user();
        $supplier = Supplier::find($request->supplier_id);
        $adjustment_amount = $request->adjustment_amount ?? 0;
        $cheque_date = date('Y-m-d', strtotime($request->cheque_date));

        DB::beginTransaction();
        try {
            $voucher_no = $request->voucher_no;
            $description = $request->description;

            if ($request->payment_mode == 'Bank') {
                $bank_info = BankInfo::find($request->bank_id);
                $bank_bal = $bank_info->balance();
                if ($request->paid_amount > $bank_bal) {
                    throw new \Exception('Insufficient Balance in bank!');
                }

                //SAVE DATA IN BANK STATEMENT
                $b_statement = new BankStatement();
                $b_statement->transaction_id = 'SP-' . $voucher_no;
                $b_statement->posting_date = date('Y-m-d');
                $b_statement->description = 'Paid to ' . $supplier->name . ', ' . $description;
                $b_statement->table_name = 'supplier_payments';
                $b_statement->ref_date = $cheque_date;
                $b_statement->cheque_no = $request->cheque_no;
                $b_statement->debit = $request->paid_amount;
                $b_statement->balance = $bank_bal - $request->paid_amount;;
                $b_statement->bank_info_id = $request->bank_id;
                $b_statement->branchId = $user_data->branchId;
                $b_statement->user_id = $user_data->id;
                $b_statement->save();
            }

            if ($request->payment_mode == 'Cash') {
                $cash_bal = cashBalance($request->branchId);
                if ($request->paid_amount > $cash_bal) {
                    throw new \Exception('Insufficient Balance in Cash!');
                }

                $c_statement = new CashStatement();
                $c_statement->transaction_id = 'SP-' . $voucher_no;
                $c_statement->posting_date = date('Y-m-d');
                $c_statement->description = 'Paid to ' . $supplier->name . ', ' . $description;
                $c_statement->table_name = 'supplier_payments';
                $c_statement->debit = $request->paid_amount;
                $c_statement->balance = $cash_bal - $request->paid_amount;
                $c_statement->receipt_no = $request->cheque_no;
                $c_statement->ref_date = $cheque_date;
                $c_statement->branchId = $user_data->branchId;
                $c_statement->user_id = $user_data->id;
                $c_statement->save();
            }

            //SAVE DATA IN SUPPLIER STATEMENT
            $current_bal = $supplier->balance() - ($request->paid_amount + $adjustment_amount);
            $s_statement = new SupplierStatement();
            $s_statement->transaction_id = 'SP-' . $voucher_no;
            $s_statement->posting_date = $cheque_date;
            $s_statement->description = $request->description;
            $s_statement->table_name = 'supplier_payments';
            $s_statement->adjustment_qty = $adjustment_amount;
            $s_statement->debit = $request->paid_amount + $adjustment_amount;
            $s_statement->balance = $current_bal;
            $s_statement->supplier_id = $request->supplier_id;
            $s_statement->branchId = $user_data->branchId;
            $s_statement->user_id = $user_data->id;
            $s_statement->save();

            //SAVE UPLOADED FILES
            $file_names = array();
            if ($request->hasFile('file')) {
                $files = $request->file('file');
                foreach ($files as $file) {
                    $ext = $file->extension();
                    $original_file_name = $file->getClientOriginalName();
                    $ex_file_name = explode('.', $original_file_name);
                    $file_name = $ex_file_name[0] . '-' . rand(1, 1500000) . '.' . $ext;
                    $destinationPath = "";
                    if (is_dir(public_path())) {
                        $destinationPath = public_path('img/files/expense_files/supplier_payment/');
                    } else {
                        $destinationPath = base_path('img/files/expense_files/supplier_payment/');
                    }

                    $file->move($destinationPath, $file_name);
                    $file_names[] = $file_name;
                }

            }
            $com_file_names = implode(',', $file_names);

            //SAVE SUPPLIER PAYMENT DATA
            $s_payment = new SupplierPayment();
            $s_payment->transaction_id = 'SP-' . $voucher_no;
            $s_payment->supplier_id = $request->supplier_id;
            $s_payment->payment_date = date('Y-m-d');
            $s_payment->voucher_no = $voucher_no;
            $s_payment->payment_mode = $request->payment_mode;
            $s_payment->paid_amount = $request->paid_amount;
            $s_payment->adjustment_amount = $adjustment_amount;
            $s_payment->description = $description;
            $s_payment->file = $com_file_names;
            $s_payment->cheque_no = $request->cheque_no;
            $s_payment->ref_date = $cheque_date;
            $s_payment->bank_id = $request->bank_id;
            $s_payment->branchId = $user_data->branchId;
            $s_payment->user_id = $user_data->id;
            $status = $s_payment->save();

            if ($status) {
                DB::commit();
                Session::flash('message', 'Supplier Payment Saved Successfully!');
                Session::flash('m-class', 'alert-success');
                return redirect()->back();
            } else {
                DB::rollback();
                Session::flash('message', 'Saving Data failed!');
                Session::flash('m-class', 'alert-danger');
                return redirect()->back()->withInput();
            }
        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('message', $e->getMessage());
            Session::flash('m-class', 'alert-danger');
            return redirect()->back()->withInput();
        }

    }

    public function paymentDetails(Request $request)
    {
        if ($request->filled("date_range")) {
            $date_range = date_range_to_arr($request->date_range);
        }
       else {
           $date_range = [date('Y-m-d', strtotime('-30 days')), date('Y-m-d')];
       }
        $sup_payments = new SupplierPayment();

        if (isset($date_range)) {
            $sup_payments = $sup_payments->whereBetween('ref_date', [
                Carbon::parse($date_range[0])->format('Y-m-d'),
                Carbon::parse($date_range[1])->format('Y-m-d')
            ]);
        }


        if ($request->search_text) {
            $sup_payments = $sup_payments->where(function ($query) use ($request) {
                $query->where('transaction_id', 'LIKE', $request->search_text . '%')
                    ->orwhere('voucher_no', 'LIKE', '%' . $request->search_text . '%')
                    ->orwhere('payment_mode', 'LIKE', '%' . $request->search_text . '%')
                    // ->orwhere('paid_amount', $request->search_text)
                    ->orWhereHas('supplier', function ($q) use ($request) {
                        $q->where('name','=', $request->search_text);
                    })
                    ->orWhereHas('bank_info', function ($q) use ($request) {
                        $q->where('bank_name', 'LIKE', '%' . $request->search_text . '%');
                    });
            });
        }
      
        $sup_payments = $sup_payments->orderBy('ref_date', 'DESC')->get();


        return view('admin.supplier.view_supplier_payment_details', compact('sup_payments'));
    }

    public function deleteSupplierPayment($tran_id)
    {
        DB::beginTransaction();
        try {
            $s_payment = SupplierPayment::where('transaction_id', $tran_id)->first();

            if ($s_payment->payment_mode == "Bank") BankStatement::where('transaction_id', $tran_id)->delete();
            if ($s_payment->payment_mode == "Cash") CashStatement::where('transaction_id', $tran_id)->delete();

            SupplierStatement::where('transaction_id', $tran_id)->delete();

            //delete files
            $file_text = $s_payment->file;
            if ($file_text != "") {
                $files = explode(",", $file_text);
                foreach ($files as $file) {
                    if (is_dir(public_path())) {
                        unlink(public_path('img/files/expense_files/supplier_payment/' . $file));
                    } else {
                        unlink(base_path('img/files/expense_files/supplier_payment/' . $file));
                    }
                }
            }

            //finally delete from supplier payment
            $status = $s_payment->delete();

            if ($status) {
                DB::commit();
                Session::flash('message', "Data Deleted Successfully!");
                Session::flash('m-class', 'alert-success');
                return redirect()->back();
            } else {
                DB::rollBack();
                Session::flash('message', "Data saving failed!");
                Session::flash('m-class', 'alert-danger');
                return back()->withInput();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('message', $e->getMessage());
            Session::flash('m-class', 'alert-danger');
            return back()->withInput();
        }

    }

    public function viewSupplierStatement(Request $request)
{
    $user = Auth::user();
    $branchId = $request->branchId ?? $user->branchId;

    // ALWAYS set default date range first (last 30 days)
    $start_date = date('Y-m-d', strtotime('-30 days'));
    $end_date = date('Y-m-d');
    $date_range = [$start_date, $end_date];
    
    // Override with user's date range if provided
    if ($request->filled('date_range')) {
        $dates = explode(' - ', $request->date_range);
        
        if (count($dates) === 2) {
            try {
                // Parse dates (handles MM/DD/YYYY format from date picker)
                $start_date = date('Y-m-d', strtotime(trim($dates[0])));
                $end_date = date('Y-m-d', strtotime(trim($dates[1])));
                
                $date_range = [$start_date, $end_date];
            } catch (\Exception $e) {
                // If parsing fails, keep default date range
                Log::warning('Invalid date range format', [
                    'input' => $request->date_range,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    // Build query
    $statements = SupplierStatement::query();

    // Branch filter
    if ($branchId == 'head_office') {
        $statements = $statements->whereNull('branchId');
    } elseif ($branchId != '') {
        $statements = $statements->where('branchId', $branchId);
    }

    // Date range filter (always applied)
    $statements = $statements->whereBetween('posting_date', $date_range);

    // Search filter
    if ($request->filled('search_text')) {
        $search_term = '%' . trim($request->search_text) . '%';
        $statements = $statements->where(function ($query) use ($search_term) {
            $query->where('transaction_id', 'LIKE', $search_term)
                ->orWhere('description', 'LIKE', $search_term)
                ->orWhere('debit', 'LIKE', $search_term)
                ->orWhere('credit', 'LIKE', $search_term)
                ->orWhere('balance', 'LIKE', $search_term)
                ->orWhereHas('supplier', function ($q) use ($search_term) {
                    $q->where('name', 'LIKE', $search_term);
                });
        });
    }

    // Execute query
    $statements = $statements->orderBy('posting_date', 'DESC')->get();

    // Calculate totals
    $grant_totalq_qty = ProductPurchase::sum('product_qty');
    $grant_total_qty = ProductPurchase::sum('product_qty') - SupplierStatement::sum('adjustment_qty');
    $grant_total_tr = ProductPurchase::sum('truck_rent');
    $grant_total_u = ProductPurchase::sum('unload_bill');
    $grant_total_adj = SupplierPayment::sum('adjustment_amount') + 
                       SupplierStatement::where('transaction_id', 'like', '%BILLAD%')->sum('debit');

    $branches = Branch::orderBy('name')->get();

    // Pass date range info to view for display
    $date_range_display = date('d-m-Y', strtotime($date_range[0])) . ' to ' . date('d-m-Y', strtotime($date_range[1]));

    return view('admin.supplier.view_supplier_statement', compact(
        'statements', 
        'grant_total_qty', 
        'grant_total_tr', 
        'grant_total_u', 
        'grant_total_adj', 
        'branches',
        'date_range_display'
    ));
}


public function Billinfo(Request $request)
{
    $billNumbers = ProductPurchase::select('bill_no')
        ->where('check_status', 1);

    if ($request->filled('billinfo_date_range')) {
        $dates = explode(' - ', $request->billinfo_date_range);
        if (count($dates) === 2) {
            try {
                $start = Carbon::parse($dates[0])->startOfDay();
                $end = Carbon::parse($dates[1])->endOfDay();
                $billNumbers->whereBetween('received_date', [$start, $end]);
            } catch (\Exception $e) {
                Log::warning('Invalid date range', ['input' => $request->billinfo_date_range]);
            }
        }
    } else {
        $billNumbers->where('received_date', '>=', Carbon::now()->subDays(30)->startOfDay());
    }

    // Search filter (for bill selection)
    if ($request->filled('billinfo_search_text')) {
        $term = '%' . trim($request->billinfo_search_text) . '%';
        $billNumbers->where(function ($q) use ($term) {
            $q->where('dmr_no', 'like', $term)
              ->orWhere('chalan_no', 'like', $term)
              ->orWhere('bill_no', 'like', $term)
              ->orWhereHas('product_name', fn($sq) => $sq->where('name', 'like', $term))
              ->orWhereHas('supplier', fn($sq) => $sq->where('name', 'like', $term));
        });
    }

    $billNumbers = $billNumbers->distinct()->pluck('bill_no');

    $check_pb_query = ProductPurchase::select(
        DB::raw('bill_no'),
        DB::raw('COUNT(*) as total_items'), 
        DB::raw('MAX(received_date) as received_date'),
        DB::raw('MAX(dmr_no) as dmr_no'),
        DB::raw('MAX(chalan_no) as chalan_no'),
        DB::raw('MAX(supplier_id) as supplier_id'),
        DB::raw('MAX(branchId) as branchId'),
        DB::raw('SUM(product_qty) as total_qty'),
        DB::raw('SUM(total_material_cost) as total_material_cost'),
        DB::raw('SUM(truck_rent) as total_truck_rent'),
        DB::raw('SUM(unload_bill) as total_unload_bill'),
        DB::raw('SUM(total_material_cost + truck_rent + unload_bill) as grand_total')
    )
    ->with(['supplier', 'product_name', 'branch'])
    ->whereIn('bill_no', $billNumbers); 

    // Group and order
    $check_pb = $check_pb_query
        ->groupBy('bill_no')
        ->orderBy('bill_no', 'ASC')
        ->get();

    $grand_total = [
        'total_bills' => $check_pb->count(),
        'total_items' => $check_pb->sum('total_items'),
        'total_qty' => $check_pb->sum('total_qty'),
        'total_material_cost' => $check_pb->sum('total_material_cost'),
        'total_truck_rent' => $check_pb->sum('total_truck_rent'),
        'total_unload_bill' => $check_pb->sum('total_unload_bill'),
        'grand_total' => $check_pb->sum('grand_total')
    ];

    return view('admin.supplier.supplier_billinfo', compact('check_pb', 'grand_total'));
}




public function monthlyReport2(Request $request)
{
    // -------------------------------
    // 1. General Date Range
    // -------------------------------
    $date_range = $request->filled('from_date') && $request->filled('to_date')
        ? [$request->from_date, $request->to_date]
        : [date('Y-m-d', strtotime('-30 days')), date('Y-m-d')];

    $search_name = $request->search_name ?? '';

    // -------------------------------
    // 2. Relevant Suppliers
    // -------------------------------
    $supplierIdsFromPurchases = ProductPurchase::whereBetween('received_date', $date_range)
        ->pluck('supplier_id')
        ->toArray();

    $supplierIdsFromPayments = SupplierPayment::whereBetween('ref_date', $date_range)
        ->pluck('supplier_id')
        ->toArray();

    $relevantSupplierIds = array_unique(array_merge($supplierIdsFromPurchases, $supplierIdsFromPayments));

    $suppliersQuery = Supplier::whereIn('id', $relevantSupplierIds);
    
    if ($search_name) {
        $suppliersQuery->where(function ($q) use ($search_name) {
            $q->where('name', 'LIKE', "%{$search_name}%")
              ->orWhere('email', 'LIKE', "%{$search_name}%")
              ->orWhere('phone', 'LIKE', "%{$search_name}%")
              ->orWhere('address', 'LIKE', "%{$search_name}%");
        });
    }

    $suppliers = $suppliersQuery->orderBy('name', 'ASC')->get();

    $supplierReports = [];

    foreach ($suppliers as $supplier) {
        // -------------------------------
        // 3. Calculate outstanding balance based on request
        // -------------------------------
        $outstandingBalance = 0;
        $outstandingBalanceLabel = '';
        $outstandingBalanceText = '';
        
        if ($request->filled('from_date')) {
            // Date range থাকলে: শুরুর তারিখের আগের সব ডেটা
            $totalPurchases = $supplier->purchases()
                ->where('received_date', '<', $request->from_date)
                ->sum('material_cost');
                
            $totalPayments = $supplier->payments()
                ->where('ref_date', '<', $request->from_date)
                ->sum('paid_amount');
        } else {
            // Date range না থাকলে: last 30 days বাদে সব ডেটা
            $thirtyDaysAgo = Carbon::now()->subDays(30)->format('Y-m-d');
            $totalPurchases = $supplier->purchases()
                ->where('received_date', '<', $thirtyDaysAgo)
                ->sum('material_cost');
                
            $totalPayments = $supplier->payments()
                ->where('ref_date', '<', $thirtyDaysAgo)
                ->sum('paid_amount');
        }
        
        $outstandingBalance = $totalPurchases - $totalPayments;
        $outstandingBalanceAbs = abs($outstandingBalance);
        $outstandingBalanceLabel = $outstandingBalance < 0 ? 'Advance' : 'Due';
        
        // HTML text তৈরি
        if ($outstandingBalance < 0) {
            $outstandingBalanceText = '<span style="background:#007bff;color:#fff;padding:3px 6px;border-radius:4px;">'
                                    . number_format($outstandingBalanceAbs, 2) . ' TK Advance</span>';
        } elseif ($outstandingBalance > 0) {
            $outstandingBalanceText = '<span style="background:#dc3545;color:#fff;padding:3px 6px;border-radius:4px;">'
                                    . number_format($outstandingBalanceAbs, 2) . ' TK Due</span>';
        } else {
            $outstandingBalanceText = '<span style="background:#6c757d;color:#fff;padding:3px 6px;border-radius:4px;">0 TK</span>';
        }

        // -------------------------------
        // 4. Current Period Purchases
        // -------------------------------
        $purchases = $supplier->purchases()
            ->whereBetween('received_date', $date_range)
            ->get();

        $totalTon = 0;
        $totalCft = 0;
        $totalMaterialCost = 0;

        foreach ($purchases as $purchase) {
            if (strtolower($purchase->unit) == 'ton') {
                $totalTon += $purchase->product_qty;
            } else {
                $totalCft += $purchase->product_qty;
            }
            $totalMaterialCost += $purchase->material_cost;
        }

        // -------------------------------
        // 5. Current Period Payments 
        // -------------------------------
        $paymentsQuery = $supplier->payments();

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $start = Carbon::parse($request->from_date)->startOfDay();
            $end   = Carbon::parse($request->to_date)->endOfDay();
            $paymentsQuery->whereBetween('ref_date', [$start, $end]);
        }
        else {
            $paymentsQuery->where('ref_date', '>=', Carbon::now()->subDays(30)->startOfDay());
        }

        $collectionAmount = $paymentsQuery->sum('paid_amount');

        // -------------------------------
        // 6. Previous Period Calculation
        // -------------------------------
        $previousPurchases = $supplier->purchases()
            ->where('received_date', '<', $date_range[0])
            ->sum('material_cost');

        $previousPayments = $supplier->payments()
            ->where('ref_date', '<', $date_range[0])
            ->sum('paid_amount');

        $previousBalance = $previousPurchases - $previousPayments;
        
        $previousBalanceAbs = abs($previousBalance);
        $previousBalanceLabel = $previousBalance < 0 ? 'Advance' : 'Due';

        // -------------------------------
        // 7. Current Period Balance
        // -------------------------------
        $balance = $totalMaterialCost - $collectionAmount;
        $balanceAbs = abs($balance);
        $balanceLabel = $balance < 0 ? 'Advance' : 'Due';

        // -------------------------------
        // 8. Monthly Ending (Opening + Current)
        // -------------------------------
        if (($previousBalance >= 0 && $balance >= 0) || ($previousBalance < 0 && $balance < 0)) {
         
            $monthlyEnding = $previousBalance + $balance;
        } else {
          
            $monthlyEnding =  $balance + $previousBalance;
        }
        $monthlyEndingAbs = abs($monthlyEnding);
        $monthlyEndingLabel = $monthlyEnding < 0 ? 'Advance' : 'Due';

        // -------------------------------
        // 9. Prepare Report
        // -------------------------------
        $supplierReports[] = [
            's_no' => count($supplierReports) + 1,
            'supplier_id' => $supplier->id,
            'supplier_name' => $supplier->name,
            'qty_ton' => number_format($totalTon, 2),
            'qty_cft' => number_format($totalCft, 3),
            'total_amount' => number_format($totalMaterialCost, 2),
            'collection_amount' => number_format($collectionAmount, 2),
            'balance_amount' => number_format($balanceAbs, 2),
            'balance_label' => $balanceLabel,
            'previous_balance_amount' => number_format($previousBalanceAbs, 2),
            'previous_balance_label' => $previousBalanceLabel,
            'monthly_ending_amount' => number_format($monthlyEndingAbs, 2),
            'monthly_ending_label' => $monthlyEndingLabel,
            'outstanding_balance_text' => $outstandingBalanceText,
            'outstanding_balance_amount' => $outstandingBalanceAbs,
            'outstanding_balance_label' => $outstandingBalanceLabel,
        ];
    }

    $date_info = "Report from {$date_range[0]} to {$date_range[1]}";

    return view('admin.supplier.monthly_supplier_report', compact('supplierReports', 'date_info'));
}
    

}

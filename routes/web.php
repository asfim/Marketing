<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\BankController;
use App\Http\Controllers\CashController;

use App\Http\Controllers\AssetController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\LenderController;
use App\Http\Controllers\reportController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\BorrowerController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\employeeController;
use App\Http\Controllers\InvestorController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\InvestmentController;
use App\Http\Controllers\ProductSaleController;
use App\Http\Controllers\CustomerBillController;
use App\Http\Controllers\FundTransferController;
use App\Http\Controllers\PropertyRentController;
use App\Http\Controllers\BankInstallmentController;
use App\Http\Controllers\ProductPurchaseController;

Route::get('/clear', function () {
    // Clear configuration cache
    Artisan::call('config:clear');
    echo "Configuration cache cleared!<br>";

    // Clear route cache
    Artisan::call('route:clear');
    echo "Route cache cleared!<br>";

    // Clear view cache
    Artisan::call('view:clear');
    echo "View cache cleared!<br>";

    // Clear application cache
    Artisan::call('cache:clear');
    echo "Application cache cleared!<br>";

    // Reset permission cache (for Spatie Permission package)
    // Artisan::call('permission:cache-reset');
    // echo "Permission cache cleared!<br>";

    // Rebuild configuration cache
    Artisan::call('config:cache');
    echo "Configuration cache rebuilt!<br>";

    return "All caches cleared and configuration cache rebuilt successfully!";
});

Route::get('/update/depreciation', function () {
    $exitCode = Artisan::call('command:updateDepreciation');
    return $exitCode;
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::group(['middleware' => ['auth']], function () {

    Route::get('login/{token}/sec', [App\Http\Controllers\UserController::class, 'secretLogin'])->name('secret.login');
Route::group(['middleware' => ['role:super-admin|admin|manager']], function () {

    Route::resource('role', RoleController::class);
    Route::resource('user', UserController::class);

    Route::get('settings', [ConfigController::class, 'index'])->name('config.index');
    Route::post('settings', [ConfigController::class, 'store'])->name('config.store');

});
;

//search routes
    Route::get('/autocomplete', [SearchController::class, 'autocomplete'])->name('autoComplete');
    Route::get('/search', [SearchController::class, 'searchGlobal'])->name('search');
//Route::put('autocomplete/{table}', 'SearchController@autocomplete');

    /** Route LogOut */
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/', function () {
        return redirect('/dashboard');
    });

    Route::get('/dashboard', [DashboardController::class, 'view'])->name('admin.home');

    //Branches Routes
    Route::resource('branches', BranchController::class);


    //supplier routes
    // Route::post('/supplier/updateBalance', 'SupplierController@updateBalance')->name('supplier.updateBalance');

    Route::get('/suppliers', [SupplierController::class, 'index'])->name('supplier.index');
    Route::post('/supplier/store', [SupplierController::class, 'store'])->name('supplier.store');
    Route::post('/supplier/update', [SupplierController::class, 'update'])->name('supplier.update');
    Route::get('/supplier/{id}/profile', [SupplierController::class, 'show'])->name('supplier.profile');
    //Route::get('/delete-supplier/{id}', 'SupplierController@destroy')->name('supplier.delete');
    Route::get('/monthly-supplier-report', [SupplierController::class, 'monthlyReport2'])->name('monthly.supplier.report.index');

    Route::get('/add-supplier-payment', [SupplierController::class, 'viewPaymentForm'])->name('supplier.payment');
    Route::post('/save-supplier-payment', [SupplierController::class, 'saveSupplierPayment'])->name('supplier.payment.store');
    Route::get('/supplier/payment-details', [SupplierController::class, 'paymentDetails'])->name('supplier.payment.details');
    Route::get('/delete-supplier-payment/{tansaction_id}', [SupplierController::class, 'deleteSupplierPayment'])->name('supplier.payment.delete');
    Route::post('/supplier/balance', [SupplierController::class, 'loadSupplierBalance'])->name('supplier.balance');
    Route::get('/supplier/statement', [SupplierController::class, 'viewSupplierStatement'])->name('supplier.statement');
    Route::get('/supplier/billinfo', [SupplierController::class, 'Billinfo'])->name('supplier.billinfo');
    Route::get('ajax', function () {
        return view('ajax');
    });

    //PRODUCT ROUTES
    Route::get('/product/list', [ProductController::class, 'index'])->name('product.list');
    Route::post('/product/store', [ProductController::class, 'saveProductName'])->name('product.store');
    Route::post('/product/update', [ProductController::class, 'editProductName'])->name('product.update');
    Route::get('/product/delete/{id}', [ProductController::class, 'deleteProductName'])->name('product.delete');

    //PRODUCT PURCHASE
    Route::get('/product/purchase/list', [ProductPurchaseController::class, 'productPurchaseList'])->name('product.purchase.list');
    Route::post('/view-products', [ProductPurchaseController::class, 'productPurchaseList'])->name('productView');
    Route::get('/product/purchase', [ProductPurchaseController::class, 'productPurchaseForm'])->name('product.purchase');
    Route::post('/product/purchase', [ProductPurchaseController::class, 'saveProductPurchase'])->name('product.purchase.store');
    Route::get('/purchase/reset', [ProductPurchaseController::class, 'resetForm'])->name('purchase.reset');

    Route::get('/product/purchase/edit/{id}', [ProductPurchaseController::class, 'editProductPurchase'])->name('product.purchase.edit');
    Route::post('/product/purchase/update', [ProductPurchaseController::class, 'updateProductPurchase'])->name('product.purchase.update');
    Route::get('/product/purchase/delete/{id}', [ProductPurchaseController::class, 'deleteProductPurchase'])->name('product.purchase.delete');
    Route::get('/product/purchase/checked/details/{bill_no}', [ProductPurchaseController::class, 'viewProductCheckDetails'])->name('purchase.checked.details');
    Route::post('/product/purchase/bill-check', [ProductPurchaseController::class, 'purchaseCheck'])->name('product.purchase.check');

    //PRODUCT STOCK
    Route::get('/product/stock', [ProductController::class, 'viewProductStock'])->name('product.stock');
    Route::post('/product/stock', [ProductController::class, 'updateProductStock'])->name('product.stock.update');
    Route::get('/product/stock/adjustment', [ProductController::class, 'viewStockAdjust'])->name('product.stock.adjustment');
    Route::get('/product/stock/adjustment/create', [ProductController::class, 'viewStockAdjustmentForm'])->name('product.stock.adjustment.create');
    Route::post('/product/stock/adjustment/store', [ProductController::class, 'saveStockAdjustment'])->name('product.stock.adjustment.store');

    Route::get('/product/consumption', [ProductController::class, 'viewProductConsumption'])->name('product.consumption');

    //PRODUCT SALE
    Route::get('/customer/mix-design/create', [ProductSaleController::class, 'viewMixDesignForm'])->name('mix.design.create');
    Route::post('/customer/mix-design/store', [ProductSaleController::class, 'saveMixDesign'])->name('mix.design.store');
    //Route::get('/customer/mix-designs/{id}', 'ProductSaleController@viewMixDesign')->name('customer.mix-design.show');
    Route::get('/customer/mix-design/{id}/edit', [ProductSaleController::class, 'editMixDesign'])->name('mix.design.edit');
    Route::post('/customer/mix-design/update', [ProductSaleController::class, 'updateMixDesign'])->name('mix.design.update');
    Route::get('/customer/mix-design/{id}/delete', [ProductSaleController::class, 'deleteMixDesign'])->name('mix.design.delete');

    Route::get('/customer/challan/index', [ProductSaleController::class, 'index'])->name('customer.challan.index');
    Route::get('/customer/challan/create', [ProductSaleController::class, 'create'])->name('customer.challan.create');
    Route::post('/load-sell-product-info', [ProductSaleController::class, 'loadSellProductInfo'])->name('customer.loadSellProductInfo');
    Route::post('/customer/challan/save', [ProductSaleController::class, 'store'])->name('customer.challan.store');
    Route::post('/customer/challan/update', [ProductSaleController::class, 'update'])->name('customer.challan.update');
    Route::post('/customer/challan/check', [ProductSaleController::class, 'checkChallanList'])->name('customer.challan.check');
    Route::get('/customer/challan/{id}/delete', [ProductSaleController::class, 'destroy'])->name('customer.challan.delete');

    //CUSTOMER BILL
    Route::post('/customer/load-project-psi', [CustomerBillController::class, 'loadCustomerProjectPSI'])->name('customer.project.psi');
    Route::post('/customer/generate/bill', [CustomerBillController::class, 'generateBillView'])->name('customer.bill.generate');
    Route::post('/customer/demo/generate/bill', [CustomerBillController::class, 'generateDemoBillView'])->name('customer.demo.bill.generate');
    Route::get('/customer/bills', [CustomerBillController::class, 'viewBills'])->name('customer.bill.index');
    Route::post('/customer/bill/store', [CustomerBillController::class, 'saveBill'])->name('customer.bill.store');
    Route::post('/customer/demo/bill/store', [CustomerBillController::class, 'saveDemoBill'])->name('customer.demo.bill.store');
    Route::post('/customer/bill/update', [CustomerBillController::class, 'updateBill'])->name('customer.bill.update');
    Route::get('/customer/bill/{id}/edit', [CustomerBillController::class, 'editBill'])->name('customer.bill.edit');
    Route::get('/customer/bill/{id}/details', [CustomerBillController::class, 'viewBillDetails'])->name('customer.bill.details');
    Route::get('/customer/demo/bill/{id}/details', [CustomerBillController::class, 'viewDemoBillDetails'])->name('customer.demo.bill.details');
    Route::get('/customer/bill/{id}/delete', [CustomerBillController::class, 'deleteBill'])->name('customer.bill.delete');
    Route::get('/customer/demo/bill/{id}/delete', [CustomerBillController::class, 'demoDeleteBill'])->name('customer.demo.bill.delete');
    Route::put('/bill/{id}/update-user-bill-no', [CustomerBillController::class, 'updateUserBillNo'])->name('bill.updateUserBillNo');
    Route::put('/bill/{id}/update-user-work-order-no', [CustomerBillController::class, 'updateUserWorkOrderNo'])->name('bill.updateUserWorkOrderNo');

    //customer routes
    // Route::post('/customer/balance/update', 'CustomerController@updateBalance')->name('customer.balance.update');

    Route::get('/customer/list', [CustomerController::class, 'index'])->name('customer.list');
    Route::get('/monthly-report', [CustomerController::class, 'monthlyReport'])->name('monthly.report.index');

    Route::post('/customer/store', [CustomerController::class, 'store'])->name('customer.store');
    Route::post('/customer/update', [CustomerController::class, 'update'])->name('customer.update');
    Route::get('/customer/{id}/profile', [CustomerController::class, 'show'])->name('customer.profile');
    //Route::get('/delete-customer/{id}', 'CustomerController@deleteCustomer');
    Route::post('/customer/balance', [CustomerController::class, 'loadCustomerBalance'])->name('customer.balance');

    Route::get('/customer/payment/receive', [CustomerController::class, 'customerPaymentForm'])->name('customer.payment.create');
    Route::post('/customer/payment/store', [CustomerController::class, 'saveCustomerPayment'])->name('customer.payment.store');
    Route::get('/customer/payment/details', [CustomerController::class, 'paymentDetails'])->name('customer.payment.details');
    Route::get('/customer/payment/delete/{tansaction_id}', [CustomerController::class, 'deleteCustomerPayment'])->name('customer.payment.delete');

    Route::get('/customer/project/create/{id}', [CustomerController::class, 'viewCustomerProjectForm'])->name('customer.project.create');
    Route::post('/customer/project/store', [CustomerController::class, 'saveCustomerProject'])->name('customer.project.store');
    Route::get('/customer/project/{id}', [CustomerController::class, 'viewCustomerProject'])->name('customer.project.show');
    Route::post('/customer/project/update', [CustomerController::class, 'updateCustomerProject'])->name('customer.project.update');
    Route::get('/customer/project/delete/{id}', [CustomerController::class, 'deleteCustomerProject'])->name('customer.project.delete');

    //Employee routes
    Route::get('/add-employee', [employeeController::class, 'viewForm'])->name('employee.create');
    Route::get('/view-employee', [employeeController::class, 'view'])->name('employee.view');
    Route::post('/view-employee', [employeeController::class, 'view'])->name('employee.search');
    Route::post('/save-employee', [employeeController::class, 'save'])->name('employee.store');
    Route::post('/edit-employee/{id}', [employeeController::class, 'update'])->name('employee.update');
    Route::post('/delete-employee/{id}', [employeeController::class, 'destroy'])->name('employee.destroy');
    Route::get('/employee-salary-setting', [employeeController::class, 'employeeSalaryCreate'])->name('employee.salarySet');
    Route::post('/employee-salary-setting', [employeeController::class, 'employeeSalarySetting']);
    Route::get('/employee-salary/report', [employeeController::class, 'employeeSalaryReport'])->name('employee.salaryReport');
    Route::post('/employee-salary/report/delete/{id}', [employeeController::class, 'employeeSalaryReportDelete'])->name('employee.salaryReportDelete');


    //INCOME ROUTES
    Route::get('/income/type', [IncomeController::class, 'incomeType'])->name('income.type');
    Route::post('/income/type/store', [IncomeController::class, 'saveIncomeType'])->name('income.type.store');
    Route::post('/income/type/update', [IncomeController::class, 'updateIncomeType'])->name('income.type.update');
    Route::get('/income/type/delete', [IncomeController::class, 'delete'])->name('income.type.delete');

    Route::get('/income/general', [IncomeController::class, 'viewGeneralIncome'])->name('income.general.index');
    Route::get('/income/general/create', [IncomeController::class, 'generalIncomeForm'])->name('income.general.create');
    Route::post('/income/general/store', [IncomeController::class, 'saveGeneralIncome'])->name('income.general.store');
    Route::post('/income/update', [IncomeController::class, 'updateIncome'])->name('income.update');
    Route::get('/income/delete/{id}', [IncomeController::class, 'deleteIncome'])->name('income.delete');

//waste income
//    Route::get('/add-waste-income-type', 'IncomeController@viewWasteTypeForm');
//    Route::post('/save-waste-income-type', 'IncomeController@saveWasteIncomeType');
//    Route::post('/edit-waste-income-type', 'IncomeController@editWasteIncomeType');

    Route::get('/income/waste', [IncomeController::class, 'viewWasteIncome'])->name('income.waste.index');
    Route::get('/income/waste/create', [IncomeController::class, 'wasteIncomeForm'])->name('income.waste.create');
    Route::post('/income/waste/store', [IncomeController::class, 'saveWasteIncome'])->name('income.waste.store');
    Route::post('/income/waste/update', [IncomeController::class, 'updateWasteIncome'])->name('income.waste.update');
//    Route::get('/income/waste/delete', 'IncomeController@deleteWasteIncome')->name('income.waste.delete');

    //EXPENSE ROUTES
    Route::get('/expense/type', [ExpenseController::class, 'expenseType'])->name('expense.type');
    Route::post('/expense/type', [ExpenseController::class, 'saveExpenseType'])->name('expense.type.store');
    Route::post('/expense/type/update', [ExpenseController::class, 'updateExpenseType'])->name('expense.type.update');
    Route::get('/expense/type/delete/{id}', [ExpenseController::class, 'deleteExpenseType']);
//    Route::get('/add-production-expense-type', 'ExpenseController@viewProductionTypeForm');
//    Route::post('/save-production-expense-type', 'ExpenseController@saveProductionExpenseType');
//    Route::post('/edit-product-expense-type', 'ExpenseController@editproductionExpenseType');

    Route::get('/expense/general', [ExpenseController::class, 'viewGeneralExpense'])->name('expense.general.index');
    Route::get('/expense/general/create', [ExpenseController::class, 'generalExpenseForm'])->name('expense.general.create');
    Route::post('/expense/general/store', [ExpenseController::class, 'saveGeneralExpense'])->name('expense.general.store');
    Route::post('/expense/update', [ExpenseController::class, 'updateExpense'])->name('expense.update');
    Route::get('/expense/delete/{id}', [ExpenseController::class, 'deleteExpense'])->name('expense.delete');

    Route::get('/expense/production', [ExpenseController::class, 'viewProductExpense'])->name('expense.production.index');
    Route::get('/expense/production/create', [ExpenseController::class, 'productionExpForm'])->name('expense.production.create');
    Route::post('/expense/production/store', [ExpenseController::class, 'saveProductionExpense'])->name('expense.production.store');
//    Route::get('/delete-product-expense/{id}', 'ExpenseController@deleteProductExpense')->name('expense.production.delete');
    Route::post('/load-engineertips', [ExpenseController::class, 'loadEngineerTips'])->name('engineer-tips');

//    Route::get('/show-engineer-tips', 'ExpenseController@viewEngineerTips');
//    Route::post('/show-engineer-tips', 'ExpenseController@viewEngineerTips')->name('searchEngineerTips');

    Route::get('/property/owners', [PropertyRentController::class, 'viewLandHouseOwner'])->name('owner.index');
    Route::get('/property/owner/create', [PropertyRentController::class, 'createOwner'])->name('owner.create');
    Route::post('/property/owner/store', [PropertyRentController::class, 'saveOwner'])->name('owner.store');
    Route::post('/property/owner/update', [PropertyRentController::class, 'updateOwner'])->name('owner.update');
    Route::get('/property/owner/delete/{id}', [PropertyRentController::class, 'deleteOwner'])->name('owner.delete');

    Route::post('/property/save-land-location', [PropertyRentController::class, 'saveLocation'])->name('owner.location.store');
    Route::get('/property/edit-location/{id}', [PropertyRentController::class, 'editLocation'])->name('owner.location.edit');
    Route::post('/property/edit-location', [PropertyRentController::class, 'updateLocation'])->name('owner.location.update');
    Route::post('/property/load-locations', [PropertyRentController::class, 'loadLocation'])->name('locations.load');
    Route::post('/property/load-owners', [PropertyRentController::class, 'loadOwner'])->name('owners.load');
//    Route::get('/show-land-owner', 'PropertyRentController@viewLandOwner')->name('land.owner');
//    Route::post('/show-land-owner', 'PropertyRentController@viewLandOwner')->name('searchLandOwner');

    Route::get('/property/rent/create', [PropertyRentController::class, 'createRentInfo'])->name('property.rent.create');
    Route::post('/property/rent/store', [PropertyRentController::class, 'saveRentInfo'])->name('property.rent.store');
    Route::post('/property/rent/update', [PropertyRentController::class, 'updateLandRentInfo'])->name('property.rent.update');
    Route::post('/property/load-rent-info', [PropertyRentController::class, 'loadRentInfo'])->name('rent.info.load');

    Route::get('/property/rent/payment/{id}', [PropertyRentController::class, 'viewRentPayment'])->name('property.rent.pay.index');
    Route::get('/property/rent/payment/create/{id}', [PropertyRentController::class, 'payRent'])->name('property.rent.pay.create');
    Route::post('/property/rent/payment/store', [PropertyRentController::class, 'saveRentPayment'])->name('property.rent.pay.store');
    Route::get('/property/rent/payment/delete/{tranid}', [PropertyRentController::class, 'deleteRentPayment'])->name('property.rent.pay.delete');

///Asset Routes
    Route::get('/asset/type', [AssetController::class, 'assetType'])->name('asset.type');
    Route::post('/asset/type/store', [AssetController::class, 'saveAssetType'])->name('asset.type.store');
    Route::post('/asset/type/update', [AssetController::class, 'updateAssetType'])->name('asset.type.update');
    Route::get('/asset/type/delete/{id}', [AssetController::class, 'deleteAssetType'])->name('asset.type.delete');

    Route::get('/asset', [AssetController::class, 'index'])->name('asset.index');
    Route::get('/asset/create', [AssetController::class, 'create'])->name('asset.create');
    Route::post('/asset/store', [AssetController::class, 'store'])->name('asset.store');
    Route::post('/asset/update', [AssetController::class, 'update'])->name('asset.update');
    Route::get('/asset/delete/{id}', [AssetController::class, 'destroy'])->name('asset.delete');

    Route::get('/asset/installment/create/{id}', [AssetController::class, 'assetInstallmentCreate'])->name('asset.installment.create');
    Route::get('/asset/installment/{id}', [AssetController::class, 'viewAssetInstallment'])->name('asset.installment.show');
    Route::post('/asset/installment/store', [AssetController::class, 'saveAssetInstallment'])->name('asset.installment.store');

//bank routes
//    Route::get('/add-bank-info', 'BankController@create');
    Route::get('/banks', [BankController::class, 'index'])->name('bank.index');
    Route::post('/bank/store', [BankController::class, 'store'])->name('bank.store');
    Route::post('/bank/update', [BankController::class, 'update'])->name('bank.update');
    Route::get('/bank/delete/{id}', [BankController::class, 'destroy'])->name('bank.delete');
    Route::post('/bank/balance', [BankController::class, 'bankBalance'])->name('bank.balance');

    Route::get('/bank/installment', [BankInstallmentController::class, 'index'])->name('bank.installment.index');
    Route::get('/bank/installment/create', [BankInstallmentController::class, 'create'])->name('bank.installment.create');
    Route::post('/bank/installment/store', [BankInstallmentController::class, 'store'])->name('bank.installment.store');
    Route::get('/bank/installment/{id}', [BankInstallmentController::class, 'edit'])->name('bank.installment.edit');
    Route::post('/bank/installment/update', [BankInstallmentController::class, 'update'])->name('bank.installment.update');
    Route::post('/load-bank-info', [BankController::class, 'loadBankInfo']);

    Route::get('/bank/installment/{id}/payments', [BankInstallmentController::class, 'viewInstallmentPayment'])->name('bank.installment.payment');
    Route::post('/show-installment-payment/{id}', [BankInstallmentController::class, 'viewInstallmentPayment'])->name('searchInstallmentLog');
    Route::get('/bank/installment/payment/create/{id}', [BankInstallmentController::class, 'payInstallment'])->name('bank.installment.payment.create');
    Route::post('/bank/installment/payment/store', [BankInstallmentController::class, 'saveInstallmentPayment'])->name('bank.installment.payment.store');
    Route::get('/bank/installment/payment/delete/{id}', [BankInstallmentController::class, 'deleteInstallmentPayment'])->name('bank.installment.payment.delete');

    Route::get('/bank/investments', [BankController::class, 'bankInvestments'])->name('bank.investment');
    Route::post('/bank/investment/store', [BankController::class, 'saveBankInvestment'])->name('bank.investment.store');
    Route::get('/bank/investment/delete/{trxId}', [BankController::class, 'deleteBankAmount'])->name('bank.investment.delete');

    Route::get('/bank/withdraw', [BankController::class, 'viewWithdrawBankAmount'])->name('bank.withdraw.index');
    Route::post('bank/withdraw/save', [BankController::class, 'saveWithdrawBankAmount'])->name('bank.withdraw.store');

//cash in hand routes
    Route::get('/cash/create', [CashController::class, 'index'])->name('cash.index');
    Route::post('/cash/store', [CashController::class, 'saveCash'])->name('cash.store');
    Route::post('/cash/update', [CashController::class, 'updateCash'])->name('cash.update');
    Route::get('/cash/delete/{trxId}', [CashController::class, 'deleteCashAmount'])->name('cash.delete');
    Route::get('/cash/withdraw', [CashController::class, 'viewWithdrawCash'])->name('cash.withdraw');
    Route::post('/cash/withdraw/store', [CashController::class, 'saveWithdrawCash'])->name('cash.withdraw.store');
    Route::get('/blance-transfer', [CashController::class, 'viewBlanceTransfer'])->name('cash.transfer');
    Route::post('/blance-transfer', [CashController::class, 'viewBlanceTransfer'])->name('searchBlanceTransfer');

//statement routes
    Route::get('/customer/statement', [CustomerController::class, 'viewCustomerStatement'])->name('customer.statement');
    Route::post('/customer-statement', [CustomerController::class, 'viewCustomerStatement'])->name('searchCustomerStatement');


    //report routes
    Route::get('/report/cash-statement', [CashController::class, 'viewCashStatement'])->name('cash.statement');
    Route::get('/report/bank-statement', [BankController::class, 'viewBankStatement'])->name('bank.statement');
    Route::get('/report/balance-report', [reportController::class, 'balanceReport'])->name('report.balance');
    Route::get('/report/profit-report', [reportController::class, 'profitReport'])->name('report.profit');
    Route::get('/report/expense-report', [reportController::class, 'expenseReport'])->name('report.expense');
    Route::post('/report/expense-report', [reportController::class, 'expenseReport'])->name('searchExpenseReport');
    Route::get('/report/income-report', [reportController::class, 'incomeReport'])->name('report.income');
    Route::post('/report/income-report', [reportController::class, 'incomeReport'])->name('searchIncomeReport');
    Route::get('/report/investment-report', [reportController::class, 'investmentReport'])->name('report.investment');
    Route::get('/report/overhead-report', [reportController::class, 'overheadReport'])->name('report.overhead');
    Route::get('/report/all-customer-balance-report', [reportController::class, 'allCustomerBalanceReport'])->name('report.customer');
    Route::post('/report/all-customer-balance-report', [reportController::class, 'allCustomerBalanceReport'])->name('searchAllCustomerBalance');
    Route::get('/report/all-supplier-balance-report', [reportController::class, 'allSupplierBalanceReport'])->name('report.supplier');
    Route::post('/report/all-supplier-balance-report', [reportController::class, 'allSupplierBalanceReport'])->name('searchAllSupplierBalance');
    Route::get('/report/pl-report', [reportController::class, 'PLReport'])->name('report.pl');
    Route::get('/report/balance-sheet', [reportController::class, 'balanceSheet'])->name('report.balance.sheet');
    Route::get('/report/trial-balance', [reportController::class, 'trialBalance'])->name('report.trial.balance');
    Route::get('/report/quick-report', [reportController::class, 'quickReport'])->name('report.quick');
    Route::get('/report/balance-sheet', [reportController::class, 'balanceSheet'])->name('report.balance.sheet');
    //  Use this one route definition:
    Route::match(['get', 'post'], '/report/customer-discount', [reportController::class, 'customerDiscount'])->name('customer.discount');

    //ACTIVITY LOG
    Route::get('/activity-log', [ActivityController::class, 'index'])->name('activity.log');

//    Route::get('activity-logs', function (){
//        $log = \Spatie\Activitylog\Models\Activity::all();
//        dd($log);
//    });


// Investment route
    Route::get('investment/list', [InvestmentController::class, 'index'])->name('admin.investments.index');
    Route::get('investment/create', [InvestmentController::class, 'create'])->name('admin.investments.create');
    Route::post('investment/store', [InvestmentController::class, 'store'])->name('admin.investments.store');
    Route::post('investment/return', [InvestmentController::class, 'returnInstallment'])->name('investments.return');
    Route::get('investment/show/{id}', [InvestmentController::class, 'show'])->name('admin.investments.show');
     Route::delete('/investment/return/{id}', [InvestmentController::class, 'deleteReturnHistory'])->name('investment.return.destroy');
    Route::delete('/investment/history/{id}', [InvestmentController::class, 'deleteInvestmentHistory'])->name('investment.history.destroy');
    Route::get('investment/edit/{id}', [InvestmentController::class, 'show'])->name('admin.investments.edit');
    Route::get('investment/add-investment/{id}', [InvestmentController::class, 'addInvestment'])->name('admin.investments.addInvestment');
    Route::post('investment/save-investment', [InvestmentController::class, 'saveInvestment'])->name('admin.investments.saveInvestment');
    Route::get('investment/{id}/return', [InvestmentController::class, 'returnForm'])->name('investments.return.form');
    Route::post('investment/{id}/return', [InvestmentController::class, 'processReturn'])->name('investments.return.process');
    Route::post('investment/update/{id}', [InvestmentController::class, 'update'])->name('admin.investments.update');
    Route::delete('investment/delete/{id}', [InvestmentController::class, 'destroy'])->name('admin.investments.destroy');


    //  Loan Taken from Another Company (Borrower) route
    Route::get('borrower/list', [BorrowerController::class, 'index'])->name('admin.borrowers.index');
    Route::get('borrower/create', [BorrowerController::class, 'create'])->name('admin.borrowers.create');
    Route::post('borrower/store', [BorrowerController::class, 'store'])->name('admin.borrowers.store');
    Route::post('borrower/return', [BorrowerController::class, 'returnInstallment'])->name('borrowers.return');
    Route::get('borrower/show/{id}', [BorrowerController::class, 'show'])->name('admin.borrowers.show');
    Route::delete('/borrower/return/{id}', [BorrowerController::class, 'deleteReturnHistory'])->name('borrower.return.delete');
    Route::delete('/borrower/history/{id}', [BorrowerController::class, 'deleteLoanHistory'])->name('borrower.history.delete');

    Route::get('borrower/edit/{id}', [BorrowerController::class, 'show'])->name('admin.borrowers.edit');
    Route::get('borrower/add-loan/{id}', [BorrowerController::class, 'addLoan'])->name('admin.borrowers.addLoan');
    Route::post('borrower/save-loan', [BorrowerController::class, 'saveLoan'])->name('admin.borrowers.saveLoan');
    Route::get('borrower/{id}/return', [BorrowerController::class, 'returnForm'])->name('borrowers.return.form');
    Route::post('borrower/{id}/return', [BorrowerController::class, 'processReturn'])->name('borrowers.return.process');
    Route::post('borrower/update/{id}', [BorrowerController::class, 'update'])->name('admin.borrowers.update');
    Route::delete('borrower/delete/{id}', [BorrowerController::class, 'destroy'])->name('admin.borrowers.destroy');


//              Loan Given by a Company (Lender)
    Route::get('lender/list', [LenderController::class, 'index'])->name('admin.lenders.index');
    Route::get('lender/create', [LenderController::class, 'create'])->name('admin.lenders.create');
    Route::post('lender/store', [LenderController::class, 'store'])->name('admin.lenders.store');
    Route::post('lender/return', [LenderController::class, 'returnInstallment'])->name('lenders.return');
    Route::get('lender/show/{id}', [LenderController::class, 'show'])->name('admin.lenders.show');
    Route::delete('/repayments/{id}', [LenderController::class, 'destroyreturn'])->name('admin.repayments.destroy');
    Route::delete('/loan/repayment/{id}', [LenderController::class, 'destroyRepayment'])->name('loan.repayment.destroy');
    Route::get('lender/edit/{id}', [LenderController::class, 'show'])->name('admin.lenders.edit');
    Route::get('lender/add-loan/{id}', [LenderController::class, 'addLoan'])->name('admin.lenders.addLoan');
    Route::post('lender/save-loan', [LenderController::class, 'saveLoan'])->name('admin.lenders.saveLoan');
    Route::get('lender/{id}/return', [LenderController::class, 'returnForm'])->name('lenders.return.form');
    Route::post('lender/{id}/return', [LenderController::class, 'processReturn'])->name('lenders.return.process');
    Route::post('lender/update/{id}', [LenderController::class, 'update'])->name('admin.lenders.update');
    Route::delete('lender/delete/{id}', [LenderController::class, 'destroy'])->name('admin.lenders.destroy');


    // investor
    //Investor
    Route::get('investor/list', [InvestorController::class, 'index'])->name('admin.investors.index');
    Route::get('investor/create', [InvestorController::class, 'create'])->name('admin.investors.create');
    Route::post('investor/store', [InvestorController::class, 'store'])->name('admin.investors.store');
    Route::post('investor/return', [InvestorController::class, 'returnInstallment'])->name('investors.return');
    Route::get('investor/show/{id}', [InvestorController::class, 'show'])->name('admin.investors.show');
    Route::delete('/investor/return/{id}', [InvestorController::class, 'deleteReturnHistory'])->name('investor.return.destroy');
    Route::delete('/investor/history/{id}', [InvestorController::class, 'deleteInvestmentHistory'])->name('investor.history.destroy');
    Route::get('investor/edit/{id}', [InvestorController::class, 'show'])->name('admin.investors.edit');
    Route::get('investor/add-investment/{id}', [InvestorController::class, 'addInvestment'])->name('admin.investors.addInvestment');
    Route::post('investor/save-investment', [InvestorController::class, 'saveInvestment'])->name('admin.investors.saveInvestment');
    Route::get('investor/{id}/return', [InvestorController::class, 'returnForm'])->name('investors.return.form');
    Route::post('investor/{id}/return', [InvestorController::class, 'processReturn'])->name('investors.return.process');
    Route::post('investor/update/{id}', [InvestorController::class, 'update'])->name('admin.investors.update');
    Route::delete('investor/delete/{id}', [InvestorController::class, 'destroy'])->name('admin.investors.destroy');


    // Fund Transfer
// Show transfer form


    Route::get('fund-transfer-form', [FundTransferController::class, 'create'])->name('admin.fund.transfer.form');
    Route::get('fund-transfer-statement', [FundTransferController::class, 'statement'])->name('admin.fund.transfer.statement');
    Route::get('/investment/delete/{id}',[FundTransferController::class, 'deleteInvestmentHistory'])->name('investment.delete');
    Route::post('fund-transfer', [FundTransferController::class, 'store'])->name('admin.fund.transfer');


});


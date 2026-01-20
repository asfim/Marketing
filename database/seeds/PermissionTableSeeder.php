<?php

use App\Permission;
use Illuminate\Database\Seeder;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $permissions    = [
            // User Section
            [
                'name'          => 'role-list',
                'display_name'  => 'Role List',
                'description'   => 'See only Listing of Role'
            ],
            [
                'name'          => 'role-create',
                'display_name'  => 'Create Role',
                'description'   => 'Create new role'
            ],
            [
                'name'          => 'role-edit',
                'display_name'  => 'Edit Role',
                'description'   => 'Edit Role'
            ],
            [
                'name'          => 'role-delete',
                'display_name'  => 'Delete Role',
                'description'   => 'Delete Role'
            ],
            [
                'name'          => 'user-list',
                'display_name'  => 'User List',
                'description'   => 'See only Listing of User'
            ],
            [
                'name'          => 'user-create',
                'display_name'  => 'Create User',
                'description'   => 'Create New User'
            ],
            [
                'name'          => 'user-edit',
                'display_name'  => 'Edit User',
                'description'   => 'Edit User'
            ],
            [
                'name'          => 'user-delete',
                'display_name'  => 'Delete User',
                'description'   => 'Delete User'
            ],
            // Branch Section
            [
                'name'          => 'branch-list',
                'display_name'  => 'View all branch',
                'description'   => 'See Only Listing of Branch'
            ],
            [
                'name'          => 'branch-create',
                'display_name'  => 'Add Branch',
                'description'   => 'Add New Branch'
            ],
            [
                'name'          => 'branch-edit',
                'display_name'  => 'Edit Branch',
                'description'   => 'Edit Branch'
            ],
            [
                'name'          => 'branch-delete',
                'display_name'  => 'Delete Branch',
                'description'   => 'Delete Branch'
            ],
            // Supplier Section
            [
                'name'          => 'supplier-create',
                'display_name'  => 'Add Supplier',
                'description'   => 'Add New Supplier'
            ],
            [
                'name'          => 'supplier-list',
                'display_name'  => 'View Supplier',
                'description'   => 'View All Supplier'
            ],
            [
                'name'          => 'supplier-edit',
                'display_name'  => 'Edit Supplier',
                'description'   => 'Edit Supplier Info'
            ],
            [
                'name'          => 'supplier-payment',
                'display_name'  => 'Supplier Payment',
                'description'   => 'Add Supplier Payment'
            ],
            [
                'name'          => 'supplier-payment-details',
                'display_name'  => 'Supplier Payment Details',
                'description'   => 'View Supplier Payment Details'
            ],
            [
                'name'          => 'supplier-payment-delete',
                'display_name'  => 'Delete Supplier Payment',
                'description'   => 'Delete Supplier Payment'
            ],
            // Product Purchase Section
            [
                'name'          => 'product-create',
                'display_name'  => 'Add Product',
                'description'   => 'Add New Product'
            ],
            [
                'name'          => 'product-purchase',
                'display_name'  => 'Product Purchase',
                'description'   => 'New Product Purchase'
            ],
            [
                'name'          => 'product-purchase-list',
                'display_name'  => 'View Purchase Product',
                'description'   => 'View Purchase Product'
            ],
            [
                'name'          => 'product-purchase-list-details',
                'display_name'  => 'View Purchase Product Details',
                'description'   => 'View Purchase Product Details'
            ],
            [
                'name'          => 'product-stock-view',
                'display_name'  => 'View Product Stock',
                'description'   => 'View Product Stock'
            ],
            [
                'name'          => 'product-stock-edit',
                'display_name'  => 'Edit Product Stock',
                'description'   => 'Edit Product Stock'
            ],
            // Product Sell Section
            [
                'name'          => 'mixDesign-create',
                'display_name'  => 'Add MixDesign',
                'description'   => 'Add New MixDesign'
            ],
            [
                'name'          => 'challan-create',
                'display_name'  => 'Add Challan',
                'description'   => 'Add New Challan'
            ],
            [
                'name'          => 'challan-list',
                'display_name'  => 'View Challan',
                'description'   => 'View Challan'
            ],
            [
                'name'          => 'challan-delete',
                'display_name'  => 'Delete Challan',
                'description'   => 'Delete Challan'
            ],
            [
                'name'          => 'bill-list',
                'display_name'  => 'View Bill',
                'description'   => 'View All Bill'
            ],
            [
                'name'          => 'bill-create',
                'display_name'  => 'Add Bill',
                'description'   => 'Add New Bill'
            ],
            [
                'name'          => 'bill-delete',
                'display_name'  => 'Delete Bill',
                'description'   => 'Delete Bill'
            ],
            [
                'name'          => 'bill-details',
                'display_name'  => 'Bill Details',
                'description'   => 'Bill Details'
            ],
            // Customer Section
            [
                'name'          => 'customer-create',
                'display_name'  => 'Add Customer',
                'description'   => 'Add New Customer'
            ],
            [
                'name'          => 'customer-list',
                'display_name'  => 'View Customers',
                'description'   => 'View All Customers'
            ],
            [
                'name'          => 'customer-edit',
                'display_name'  => 'Edit Customer',
                'description'   => 'Edit Customer'
            ],
            [
                'name'          => 'customer-mix-design',
                'display_name'  => 'Customer Mix Design',
                'description'   => 'Customer Mix Design'
            ],
            [
                'name'          => 'customer-add-project',
                'display_name'  => 'Add Customer Project',
                'description'   => 'Add Customer Project'
            ],
            [
                'name'          => 'customer-show-project',
                'display_name'  => 'Show Customer Project',
                'description'   => 'Show Customer Project'
            ],
            [
                'name'          => 'customer-project-edit',
                'display_name'  => 'Edit Customer Project',
                'description'   => 'Edit Customer Project'
            ],
            [
                'name'          => 'customer-project-delete',
                'display_name'  => 'Delete Customer Project',
                'description'   => 'Delete Customer Project'
            ],
            [
                'name'          => 'customer-payment',
                'display_name'  => 'Customer Payment',
                'description'   => 'Customer Payment'
            ],
            [
                'name'          => 'customer-payment-details',
                'display_name'  => 'Customer Payment Details',
                'description'   => 'Customer Payment Details'
            ],
            // Expense Section
            [
                'name'          => 'add-general-expense-type',
                'display_name'  => 'Add General Expense Type',
                'description'   => 'Add General Expense Type'
            ],
            [
                'name'          => 'edit-general-expense-type',
                'display_name'  => 'Edit General Expense Type',
                'description'   => 'Edit General Expense Type'
            ],
            [
                'name'          => 'add-general-expense',
                'display_name'  => 'Add General Expense',
                'description'   => 'Add New General Expense'
            ],
            [
                'name'          => 'show-general-expense',
                'display_name'  => 'Show General Expense',
                'description'   => 'Show General Expense'
            ],
            [
                'name'          => 'add-land-house-owner',
                'display_name'  => 'Add Land/House Owner',
                'description'   => 'Add Land/House Owner'
            ],
            [
                'name'          => 'add-land-rent-info',
                'display_name'  => 'Add Land Rent Info',
                'description'   => 'Add Land Rent Info'
            ],
            [
                'name'          => 'show-land-owners',
                'display_name'  => 'Show Land Owners',
                'description'   => 'Show Land Owners'
            ],
            [
                'name'          => 'edit-land-owners',
                'display_name'  => 'Edit Land Owners',
                'description'   => 'Edit Land Owners'
            ],
            [
                'name'          => 'show-house-owners',
                'display_name'  => 'Show House Owners',
                'description'   => 'Show House Owners'
            ],
            [
                'name'          => 'edit-house-owners',
                'display_name'  => 'Edit House Owners',
                'description'   => 'Edit House Owners'
            ],
            [
                'name'          => 'payment-rent',
                'display_name'  => 'Payment Rent',
                'description'   => 'Payment Rent'
            ],
            [
                'name'          => 'show-rent-payment',
                'display_name'  => 'Show Rent Payment',
                'description'   => 'Show Rent Payment'
            ],
            // Asset Section
            [
                'name'          => 'add-asset-type',
                'display_name'  => 'Add Asset Type',
                'description'   => 'Add Asset Type'
            ],
            [
                'name'          => 'edit-asset-type',
                'display_name'  => 'Edit Asset Type',
                'description'   => 'Edit Asset Type'
            ],
            [
                'name'          => 'add-assets',
                'display_name'  => 'Add Assets',
                'description'   => 'Add New Asset'
            ],
            [
                'name'          => 'show-asstes',
                'display_name'  => 'Show Assets',
                'description'   => 'Show All Assets'
            ],
            [
                'name'          => 'edit-assets',
                'display_name'  => 'Edit Asset',
                'description'   => 'Edit Asset'
            ],
            [
                'name'          => 'delete-assets',
                'display_name'  => 'Delete Assets',
                'description'   => 'Delete Assets'
            ],
            //Income Section
            [
                'name'          => 'add-general-income-type',
                'display_name'  => 'Add General Income Type',
                'description'   => 'Add General Income Type'
            ],
            [
                'name'          => 'edit-general-income-type',
                'display_name'  => 'Edit General Income Type',
                'description'   => 'Edit General Income Type'
            ],
            [
                'name'          => 'add-general-income',
                'display_name'  => 'Add General Income',
                'description'   => 'Add General Income'
            ],
            [
                'name'          => 'show-general-income',
                'display_name'  => 'Show General Income',
                'description'   => 'Show General Income'
            ],
            [
                'name'          => 'add-waste-income-type',
                'display_name'  => 'Add Waste Income Type',
                'description'   => 'Add Waste Income Type'
            ],
            [
                'name'          => 'edit-waste-income-type',
                'display_name'  => 'Edit Waste Income Type',
                'description'   => 'Edit Waste Income Type'
            ],
            [
                'name'          => 'add-waste-income',
                'display_name'  => 'Add Waste Income',
                'description'   => 'Add Waste Income'
            ],
            [
                'name'          => 'show-waste-income',
                'display_name'  => 'Show Waste Income',
                'description'   => 'Show Waste Income'
            ],
            //Cash In Hand Section
            [
                'name'          => 'add-cash',
                'display_name'  => 'Add Cash',
                'description'   => 'Add New Capital'
            ],
            [
                'name'          => 'delete-cash',
                'display_name'  => 'Delete Cash',
                'description'   => 'Delete Cash'
            ],
            [
                'name'          => 'withdraw-cash',
                'display_name'  => 'Withdraw Cash',
                'description'   => 'Withdraw Cash'
            ],
            [
                'name'          => 'withdraw-cash-delete',
                'display_name'  => 'Delete Withdraw Cash',
                'description'   => 'Delete Withdraw Cash'
            ],
            //Bank Section
            [
                'name'          => 'add-bank-info',
                'display_name'  => 'Add Bank Info',
                'description'   => 'Add Bank Info'
            ],
            [
                'name'          => 'show-bank-info',
                'display_name'  => 'Show Bank Info',
                'description'   => 'Show Bank Info'
            ],
            [
                'name'          => 'edit-bank-info',
                'display_name'  => 'Edit Bank Info',
                'description'   => 'Edit Bank Info'
            ],
            [
                'name'          => 'delete-bank-info',
                'display_name'  => 'Delete Bank Info',
                'description'   => 'Delete Bank Info'
            ],
            [
                'name'          => 'add-bank-investment',
                'display_name'  => 'Add Bank Investment',
                'description'   => 'Add Bank Investment'
            ],
            [
                'name'          => 'delete-bank-investment',
                'display_name'  => 'Delete Bank Investment',
                'description'   => 'Delete Bank Investment'
            ],
            [
                'name'          => 'withdraw-bank-amount',
                'display_name'  => 'Withdraw Bank Amount',
                'description'   => 'Withdraw Bank Amount'
            ],
            [
                'name'          => 'withdraw-bank-amount-delete',
                'display_name'  => 'Delete Withdraw Bank Amount',
                'description'   => 'Delete Withdraw Bank Amount'
            ],
            [
                'name'          => 'add-bank-installment-info',
                'display_name'  => 'Add Bank Installment Info',
                'description'   => 'Add Bank Installment Info'
            ],
            [
                'name'          => 'view-bank-installments',
                'display_name'  => 'View Bank Installments',
                'description'   => 'View Bank Installments'
            ],
            [
                'name'          => 'edit-bank-installments',
                'display_name'  => 'Edit Bank Installments',
                'description'   => 'Edit Bank Installments'
            ],
            [
                'name'          => 'pay-installments',
                'display_name'  => 'Pay Installments',
                'description'   => 'Pay Installments'
            ],
            [
                'name'          => 'show-installment-payments',
                'display_name'  => 'Show Installment Payments',
                'description'   => 'Show Installment Payments'
            ],
            //Report Section
            [
                'name'          => 'supplier-statement-report',
                'display_name'  => 'Supplier Statement Report',
                'description'   => 'Supplier Statement Report'
            ],
            [
                'name'          => 'customer-statement-report',
                'display_name'  => 'Customer Statement Report',
                'description'   => 'Customer Statement Report'
            ],
            [
                'name'          => 'cash-in-hand-statement-report',
                'display_name'  => 'Cash In Hand Statement Report',
                'description'   => 'Cash In Hand Statement Report'
            ],
            [
                'name'          => 'bank-statement-report',
                'display_name'  => 'Bank Statement Report',
                'description'   => 'Bank Statement Report'
            ],
            [
                'name'          => 'expense-report',
                'display_name'  => 'Expense Report',
                'description'   => 'Expense Report'
            ],
            [
                'name'          => 'income-report',
                'display_name'  => 'Income Report',
                'description'   => 'Income Report'
            ],
            [
                'name'          => 'investment-report',
                'display_name'  => 'Investment Report',
                'description'   => 'Investment Report'
            ],
            [
                'name'          => 'balance-report',
                'display_name'  => 'Balance Report',
                'description'   => 'Balance Report'
            ],
            [
                'name'          => 'overhead-report',
                'display_name'  => 'Over Head Report',
                'description'   => 'Over Head Report'
            ],
            [
                'name'          => 'profit-report',
                'display_name'  => 'Profit Report',
                'description'   => 'Profit Report'
            ],
            [
                'name'          => 'show-dashboard-figures',
                'display_name'  => 'Show dashboard figures',
                'description'   => 'Show dashboard figures'
            ],
            
        ];

        foreach ($permissions as $key=>$value)
        {
            Permission::create($value);
        }

    }


}

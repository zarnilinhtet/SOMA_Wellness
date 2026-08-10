<?php

namespace App\Http\Controllers;

use App\Models\UserType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserTypeController extends Controller
{
    private $permissionGroups = [
        'Dashboard' => [
            'dashboard_view'
        ],
        'Sale Modules' => [
            'pos_view',
            'pos_register',
            'pos_edit',
            'pos_print', // pos_receipt ကိုဖယ်ပြီး pos_print သာ ထားပါသည်
            'invoice_view',
            'invoice_register',
            'invoice_edit',
            'invoice_delete',
            'invoice_print',
            'invoice_next_payment',
            'invoice_next_payment_delete',
            'invoice_next_payment_history',
            'invoice_return',
            'customer_view',
            'customer_register',
            'customer_edit',
            'customer_delete'
        ],
        'Inventory Modules' => [
            'product_view',
            'product_register',
            'product_edit',
            'product_delete',
            'product_excel_export',
            'product_excel_import',
            'stock_adjust',
            'stock_register',
            'stock_delete'
        ],
        'Purchase Modules' => [
            'purchase_view',
            'purchase_register',
            'purchase_edit',
            'purchase_delete',
            'purchase_print',
            'supplier_view',
            'supplier_register',
            'supplier_edit',
            'supplier_delete'
        ],
        'Expense Modules' => [
            'expense_category_view',
            'expense_category_register',
            'expense_category_edit',
            'expense_category_delete',
            'expense_view',
            'expense_register',
            'expense_edit',
            'expense_delete'
        ],
        'Reports' => [
            'report_invoice',
            'report_pos',
            'report_purchase',
            'report_expense',
            'report_sale_product',
            'report_product_category',
            'report_profit_loss'
        ],
        'Master Data' => [
            'category_view',
            'category_edit',
            'category_delete',
            'unit_view',
            'unit_edit',
            'unit_delete',
            'payment_view',
            'payment_method_register',
            'payment_method_edit',
            'payment_method_delete',
            'user_type_view',
            'user_type_register',
            'user_type_edit',
            'user_type_delete',
            'user_view',
            'user_register',
            'user_edit',
            'user_delete'
        ]
    ];
    public function index()
    {
        $userTypes = UserType::latest()->get();
        $permissionGroups = $this->permissionGroups;
        return view('user_type.usertype_index', compact('userTypes', 'permissionGroups'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'nullable|array'
        ]);

        UserType::create([
            'name' => $request->name,
            'permissions' => $request->permissions ?? [],
        ]);

        return redirect()->back()->with('success', 'User Type created successfully!');
    }

    public function edit($id)
    {
        $userType = UserType::findOrFail($id);
        $permissionGroups = $this->permissionGroups;
        return view('user_type.usertype_edit', compact('userType', 'permissionGroups'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'nullable|array'
        ]);

        $userType = UserType::findOrFail($id);
        $userType->update([
            'name' => $request->name,
            'permissions' => $request->permissions ?? [],
        ]);

        return redirect()->route('user_types.index')->with('success', 'User Type updated successfully!');
    }

    public function destroy($id)
    {
        UserType::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'User Type deleted successfully!');
    }
}

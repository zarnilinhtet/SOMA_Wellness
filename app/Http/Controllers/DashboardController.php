<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Order;          // POS Model
use App\Models\InvoiceOrder;   // Invoice Model
use App\Models\Expense;        // Expense Model
use App\Models\Purchase;       // Purchase Model
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\InvoiceOrderItem;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }
}

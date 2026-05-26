<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PurchaseOrder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesExport;
use App\Exports\TopDishesExport;

class ReportController extends Controller
{
    public function index()
    {
        return view('content.reports.index');
    }

    public function sales(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to   = $request->input('to', now()->toDateString());

        $orders = Order::with('mesa')
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->where('status', 'pagado')
            ->orderByDesc('created_at')
            ->get();

        $total = $orders->sum('total');

        if ($request->input('export') === 'excel') {
            return Excel::download(new SalesExport($from, $to), "ventas_{$from}_{$to}.xlsx");
        }
        if ($request->input('export') === 'pdf') {
            $pdf = Pdf::loadView('content.reports.sales-pdf', compact('orders', 'total', 'from', 'to'));
            return $pdf->download("ventas_{$from}_{$to}.pdf");
        }

        return view('content.reports.sales', compact('orders', 'total', 'from', 'to'));
    }

    public function topDishes(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to   = $request->input('to', now()->toDateString());

        $dishes = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
            ->whereBetween('orders.created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->where('orders.status', 'pagado')
            ->select('menu_items.name', DB::raw('SUM(order_items.quantity) as total_qty'), DB::raw('SUM(order_items.subtotal) as total_revenue'))
            ->groupBy('menu_items.id', 'menu_items.name')
            ->orderByDesc('total_qty')
            ->get();

        if ($request->input('export') === 'excel') {
            return Excel::download(new TopDishesExport($from, $to), "platillos_{$from}_{$to}.xlsx");
        }
        if ($request->input('export') === 'pdf') {
            $pdf = Pdf::loadView('content.reports.top-dishes-pdf', compact('dishes', 'from', 'to'));
            return $pdf->download("platillos_{$from}_{$to}.pdf");
        }

        return view('content.reports.top-dishes', compact('dishes', 'from', 'to'));
    }

    public function lowStock()
    {
        $items = InventoryItem::where('is_active', true)
            ->whereColumn('current_stock', '<', 'minimum_stock')
            ->with('supplier')
            ->orderBy('name')
            ->get();

        return view('content.reports.low-stock', compact('items'));
    }

    public function supplierDelays()
    {
        $orders = PurchaseOrder::with('supplier')
            ->whereIn('status', ['pendiente', 'enviado'])
            ->whereNotNull('delivery_date')
            ->where('delivery_date', '<', now())
            ->orderBy('delivery_date')
            ->get();

        return view('content.reports.supplier-delays', compact('orders'));
    }
}

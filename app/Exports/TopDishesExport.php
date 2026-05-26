<?php

namespace App\Exports;

use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TopDishesExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private string $from, private string $to) {}

    public function collection()
    {
        return OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
            ->whereBetween('orders.created_at', [$this->from . ' 00:00:00', $this->to . ' 23:59:59'])
            ->where('orders.status', 'pagado')
            ->select('menu_items.name', DB::raw('SUM(order_items.quantity) as total_qty'), DB::raw('SUM(order_items.subtotal) as total_revenue'))
            ->groupBy('menu_items.id', 'menu_items.name')
            ->orderByDesc('total_qty')
            ->get();
    }

    public function headings(): array
    {
        return ['Platillo', 'Cantidad', 'Ingresos'];
    }

    public function map($row): array
    {
        return [
            $row->name,
            $row->total_qty,
            number_format($row->total_revenue, 2),
        ];
    }
}

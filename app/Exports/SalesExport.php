<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalesExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private string $from, private string $to) {}

    public function collection()
    {
        return Order::with('mesa')
            ->whereBetween('created_at', [$this->from . ' 00:00:00', $this->to . ' 23:59:59'])
            ->where('status', 'pagado')
            ->orderByDesc('created_at')
            ->get();
    }

    public function headings(): array
    {
        return ['#', 'Mesa', 'Total', 'Fecha'];
    }

    public function map($order): array
    {
        return [
            $order->id,
            $order->mesa?->numero ?? '—',
            number_format($order->total, 2),
            $order->created_at->format('d/m/Y H:i'),
        ];
    }
}

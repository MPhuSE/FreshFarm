<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrdersExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Order::with('user')->orderBy('id', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Mã Đơn Hàng',
            'Khách Hàng',
            'Số Điện Thoại',
            'Tổng Tiền',
            'Trạng Thái',
            'Thanh Toán',
            'Ngày Đặt',
        ];
    }

    public function map($order): array
    {
        return [
            $order->id,
            $order->order_code,
            $order->user ? $order->user->name : 'Khách vãng lai',
            $order->phone,
            $order->total_amount,
            $order->status,
            $order->payment_status,
            $order->created_at->format('d/m/Y H:i'),
        ];
    }
}

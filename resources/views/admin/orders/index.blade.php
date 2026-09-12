<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Quản lý đơn hàng</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 text-slate-800">

    <main class="mx-auto max-w-7xl px-6 py-10">

        <!-- Tiêu đề -->
        <div class="mb-8">

            <p class="text-sm font-medium text-emerald-600">
                Admin Orders
            </p>

            <h1 class="mt-1 text-3xl font-bold">
                Quản lý đơn hàng
            </h1>

            <p class="mt-2 text-slate-500">
                Danh sách đơn hàng, trạng thái và thanh toán
            </p>

        </div>


        <!-- Danh sách đơn hàng -->
        <section class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-slate-200">

            <div class="border-b border-slate-200 px-6 py-4">

                <h2 class="font-semibold">
                    Danh sách đơn hàng
                </h2>

            </div>


            <div class="overflow-x-auto">

                <table class="w-full min-w-[900px] text-left text-sm">

                    <thead class="bg-slate-50 text-xs uppercase text-slate-500">

                        <tr>

                            <th class="px-6 py-4">
                                Mã đơn hàng
                            </th>

                            <th class="px-6 py-4">
                                Khách hàng
                            </th>

                            <th class="px-6 py-4">
                                Tổng tiền
                            </th>

                            <th class="px-6 py-4">
                                Trạng thái
                            </th>

                            <th class="px-6 py-4">
                                Thanh toán
                            </th>

                            <th class="px-6 py-4">
                                Chi tiết
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-slate-100">

                        @forelse ($orders ?? [] as $order)

                            <tr class="hover:bg-slate-50">

                                <!-- Mã đơn -->
                                <td class="px-6 py-4 font-medium">
                                    #{{ $order->id }}
                                </td>


                                <!-- Khách hàng -->
                                <td class="px-6 py-4">
                                    {{ $order->user->name ?? 'Khách hàng' }}
                                </td>


                                <!-- Tổng tiền -->
                                <td class="px-6 py-4 font-medium">
                                    {{ number_format($order->total_amount ?? 0, 0, ',', '.') }}đ
                                </td>


                                <!-- Trạng thái -->
                                <td class="px-6 py-4">

                                    @if (($order->status ?? '') === 'pending')

                                        <span class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-medium text-yellow-700">
                                            Chờ xác nhận
                                        </span>

                                    @elseif (($order->status ?? '') === 'confirmed')

                                        <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-700">
                                            Đã xác nhận
                                        </span>

                                    @elseif (($order->status ?? '') === 'shipping')

                                        <span class="rounded-full bg-purple-100 px-3 py-1 text-xs font-medium text-purple-700">
                                            Đang giao
                                        </span>

                                    @elseif (($order->status ?? '') === 'delivered')

                                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-medium text-emerald-700">
                                            Đã giao
                                        </span>

                                    @elseif (($order->status ?? '') === 'cancelled')

                                        <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-700">
                                            Đã hủy
                                        </span>

                                    @else

                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">
                                            {{ $order->status ?? 'Chưa xác định' }}
                                        </span>

                                    @endif

                                </td>


                                <!-- Thanh toán -->
                                <td class="px-6 py-4">

                                    @if (($order->payment_status ?? '') === 'paid')

                                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-medium text-emerald-700">
                                            Đã thanh toán
                                        </span>

                                    @else

                                        <span class="rounded-full bg-orange-100 px-3 py-1 text-xs font-medium text-orange-700">
                                            Chưa thanh toán
                                        </span>

                                    @endif

                                </td>


                                <!-- Chi tiết -->
                                <td class="px-6 py-4">

                                    <a href="#"
                                        class="font-medium text-emerald-600 hover:text-emerald-700">
                                        Chi tiết đơn hàng
                                    </a>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="6"
                                    class="px-6 py-12 text-center text-slate-500">

                                    Chưa có đơn hàng nào.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </section>

    </main>

</body>

</html>
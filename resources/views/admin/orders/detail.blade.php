<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Chi tiết đơn hàng</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 text-slate-800">

    <main class="mx-auto max-w-6xl px-6 py-10">

        <!-- Tiêu đề -->
        <div class="mb-8">

            <p class="text-sm font-medium text-emerald-600">
                Admin Orders
            </p>

            <h1 class="mt-1 text-3xl font-bold">
                Chi tiết đơn hàng
            </h1>

        </div>


        <!-- Thông tin đơn hàng -->
        <section class="mb-6 rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">

            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">

                <!-- Mã đơn -->
                <div>
                    <p class="text-sm text-slate-500">
                        Mã đơn hàng
                    </p>

                    <p class="mt-1 font-semibold">
                        #{{ $order->id ?? '' }}
                    </p>
                </div>


                <!-- Khách hàng -->
                <div>
                    <p class="text-sm text-slate-500">
                        Khách hàng
                    </p>

                    <p class="mt-1 font-semibold">
                        {{ $order->user->name ?? 'Khách hàng' }}
                    </p>
                </div>


                <!-- Tổng tiền -->
                <div>
                    <p class="text-sm text-slate-500">
                        Tổng tiền
                    </p>

                    <p class="mt-1 font-semibold text-emerald-600">
                        {{ number_format($order->total_amount ?? 0, 0, ',', '.') }}đ
                    </p>
                </div>


                <!-- Thanh toán -->
                <div>
                    <p class="text-sm text-slate-500">
                        Thanh toán
                    </p>

                    @if (($order->payment_status ?? '') === 'paid')

                        <span class="mt-1 inline-block rounded-full bg-emerald-100 px-3 py-1 text-xs font-medium text-emerald-700">
                            Đã thanh toán
                        </span>

                    @else

                        <span class="mt-1 inline-block rounded-full bg-orange-100 px-3 py-1 text-xs font-medium text-orange-700">
                            Chưa thanh toán
                        </span>

                    @endif

                </div>

            </div>

        </section>


        <!-- Trạng thái đơn hàng -->
        <section class="mb-6 rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">

            <h2 class="mb-4 text-lg font-semibold">
                Trạng thái đơn hàng
            </h2>

            <div class="flex flex-col gap-3 sm:flex-row">

                <select id="orderStatus"
                        class="rounded-lg border border-slate-300 bg-white px-4 py-2.5 outline-none focus:border-emerald-500">

                    <option value="pending">
                        Chờ xác nhận
                    </option>

                    <option value="confirmed">
                        Đã xác nhận
                    </option>

                    <option value="shipping">
                        Đang giao
                    </option>

                    <option value="delivered">
                        Đã giao
                    </option>

                    <option value="cancelled">
                        Đã hủy
                    </option>

                </select>


                <button
        
                    id="updateStatus"
                    type="button"
                    class="rounded-lg bg-emerald-600 px-5 py-2.5 font-medium text-white hover:bg-emerald-700">

                    Cập nhật trạng thái

                </button>

            </div>

        </section>


        <!-- Sản phẩm trong đơn -->
        <section class="mb-6 overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-slate-200">

            <div class="border-b border-slate-200 px-6 py-4">

                <h2 class="font-semibold">
                    Sản phẩm trong đơn hàng
                </h2>

            </div>


            <div class="overflow-x-auto">

                <table class="w-full min-w-[700px] text-left text-sm">

                    <thead class="bg-slate-50 text-xs uppercase text-slate-500">

                        <tr>

                            <th class="px-6 py-4">
                                Sản phẩm
                            </th>

                            <th class="px-6 py-4">
                                Số lượng
                            </th>

                            <th class="px-6 py-4">
                                Đơn giá
                            </th>

                            <th class="px-6 py-4">
                                Thành tiền
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-slate-100">

                        @forelse ($order->items ?? [] as $item)

                            <tr class="hover:bg-slate-50">

                                <td class="px-6 py-4 font-medium">
                                    {{ $item->product->name ?? 'Sản phẩm' }}
                                </td>

                                <td class="px-6 py-4">
                                    {{ $item->quantity ?? 0 }}
                                </td>

                                <td class="px-6 py-4">
                                    {{ number_format($item->unit_price ?? 0, 0, ',', '.') }}đ
                                </td>

                                <td class="px-6 py-4 font-medium">
                                    {{ number_format($item->subtotal ?? 0, 0, ',', '.') }}đ
                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="4"
                                    class="px-6 py-12 text-center text-slate-500">

                                    Không có sản phẩm trong đơn hàng.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </section>


        <!-- Tổng thanh toán -->
        <section class="mb-6 rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">

            <div class="ml-auto max-w-sm space-y-3">

                <div class="flex justify-between">

                    <span class="text-slate-500">
                        Tạm tính
                    </span>

                    <span>
                        {{ number_format($order->subtotal ?? 0, 0, ',', '.') }}đ
                    </span>

                </div>


                <div class="flex justify-between">

                    <span class="text-slate-500">
                        Phí giao hàng
                    </span>

                    <span>
                        {{ number_format($order->shipping_fee ?? 0, 0, ',', '.') }}đ
                    </span>

                </div>


                <div class="flex justify-between border-t border-slate-200 pt-3 text-lg font-bold">

                    <span>
                        Tổng thanh toán
                    </span>

                    <span class="text-emerald-600">
                        {{ number_format($order->total_amount ?? 0, 0, ',', '.') }}đ
                    </span>

                </div>

            </div>

        </section>


        <!-- Quay lại -->
        <a href="{{ url('/admin/orders') }}"
            class="inline-block rounded-lg border border-slate-300 bg-white px-5 py-2.5 font-medium hover:bg-slate-50">

            Quay lại danh sách đơn hàng

        </a>

    </main>

    <script>
        document.getElementById('updateStatus')?.addEventListener('click', function () {
            alert('Nút cập nhật trạng thái đã hoạt động.');
        });
    </script>
</body>

</html>
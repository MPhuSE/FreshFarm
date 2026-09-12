<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Báo cáo hệ thống</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 text-slate-800">

    <main class="mx-auto max-w-7xl px-6 py-10">

        <!-- Tiêu đề -->
        <div class="mb-8">

            <p class="text-sm font-medium text-emerald-600">
                Admin System
            </p>

            <h1 class="mt-1 text-3xl font-bold">
                Báo cáo hệ thống
            </h1>

            <p class="mt-2 text-slate-500">
                Theo dõi tình hình hoạt động và doanh thu của hệ thống
            </p>

        </div>


        <!-- Bộ lọc thời gian -->
        <section class="mb-6 rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">

            <h2 class="mb-4 font-semibold">
                Thời gian báo cáo
            </h2>

            <div class="grid gap-4 md:grid-cols-3">

                <div>

                    <label class="mb-2 block text-sm font-medium">
                        Từ ngày
                    </label>

                    <input
                        type="date"
                        class="w-full rounded-lg border border-slate-300 px-4 py-2.5">

                </div>


                <div>

                    <label class="mb-2 block text-sm font-medium">
                        Đến ngày
                    </label>

                    <input
                        type="date"
                        class="w-full rounded-lg border border-slate-300 px-4 py-2.5">

                </div>


                <div class="flex items-end">

                    <button
                        type="button"
                        class="w-full rounded-lg bg-emerald-600 px-5 py-2.5 font-medium text-white hover:bg-emerald-700">
                        Xem báo cáo
                    </button>

                </div>

            </div>

        </section>


        <!-- Thống kê -->
        <section class="grid gap-6 md:grid-cols-3">

            <!-- Tổng đơn hàng -->
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">

                <p class="text-sm text-slate-500">
                    Tổng đơn hàng
                </p>

                <p class="mt-2 text-3xl font-bold">
                    {{ $totalOrders ?? 0 }}
                </p>

            </div>


            <!-- Tổng sản phẩm -->
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">

                <p class="text-sm text-slate-500">
                    Tổng sản phẩm
                </p>

                <p class="mt-2 text-3xl font-bold">
                    {{ $totalProducts ?? 0 }}
                </p>

            </div>


            <!-- Doanh thu -->
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">

                <p class="text-sm text-slate-500">
                    Tổng doanh thu
                </p>

                <p class="mt-2 text-3xl font-bold text-emerald-600">
                    {{ number_format($revenue ?? 0, 0, ',', '.') }}đ
                </p>

            </div>

        </section>


        <!-- Báo cáo doanh thu -->
        <section class="mt-6 rounded-xl bg-white shadow-sm ring-1 ring-slate-200">

            <div class="border-b border-slate-200 px-6 py-4">

                <h2 class="font-semibold">
                    Báo cáo doanh thu
                </h2>

            </div>


            <div class="overflow-x-auto">

                <table class="w-full text-left text-sm">

                    <thead class="bg-slate-50 text-xs uppercase text-slate-500">

                        <tr>

                            <th class="px-6 py-4">
                                Thời gian
                            </th>

                            <th class="px-6 py-4">
                                Số đơn hàng
                            </th>

                            <th class="px-6 py-4">
                                Doanh thu
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-slate-100">

                        @forelse ($reports ?? [] as $report)

                            <tr class="hover:bg-slate-50">

                                <td class="px-6 py-4">
                                    {{ $report->date ?? '' }}
                                </td>

                                <td class="px-6 py-4">
                                    {{ $report->order_count ?? 0 }}
                                </td>

                                <td class="px-6 py-4 font-medium">
                                    {{ number_format($report->revenue ?? 0, 0, ',', '.') }}đ
                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="3"
                                    class="px-6 py-12 text-center text-slate-500">

                                    Chưa có dữ liệu báo cáo.

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
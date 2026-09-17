@extends('layouts.admin')

@section('title', 'Báo cáo')

@section('page-header')
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">
            Báo cáo tổng hợp
        </h1>
        <p class="text-gray-500 mt-1">
            Thống kê doanh thu và đơn hàng
        </p>
    </div>
@endsection

@section('content')


    <!-- Bộ lọc ngày -->
    <div class="bg-white p-5 rounded-lg shadow mb-6">

        <div class="flex flex-wrap items-end gap-4">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Từ ngày
                </label>

                <input
                    type="date"
                    id="fromDate"
                    value="2026-09-01"
                    class="border rounded px-3 py-2"
                >
            </div>


            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Đến ngày
                </label>

                <input
                    type="date"
                    id="toDate"
                    value="2026-09-30"
                    class="border rounded px-3 py-2"
                >
            </div>


            <button
                onclick="loadReport()"
                class="bg-green-600 text-white px-5 py-2 rounded hover:bg-green-700"
            >
                Xem báo cáo
            </button>

        </div>

    </div>


    <!-- KPI -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">

        <!-- Doanh thu -->
        <div class="bg-white p-5 rounded-lg shadow">
            <p class="text-gray-500 text-sm">
                Tổng doanh thu
            </p>

            <h2
                id="revenue"
                class="text-2xl font-bold text-green-600 mt-2"
            >
                0 ₫
            </h2>
        </div>


        <!-- Đơn hàng -->
        <div class="bg-white p-5 rounded-lg shadow">
            <p class="text-gray-500 text-sm">
                Tổng đơn hàng
            </p>

            <h2
                id="orders"
                class="text-2xl font-bold text-blue-600 mt-2"
            >
                0
            </h2>
        </div>


        <!-- Giá trị đơn trung bình -->
        <div class="bg-white p-5 rounded-lg shadow">
            <p class="text-gray-500 text-sm">
                Tổng người dùng 
            </p>

            <h2
                id="totalUsers"
                class="text-2xl font-bold text-purple-600 mt-2"
            >
                
            </h2>
        </div>

    </div>


    <!-- Doanh thu theo ngày -->
    <div class="bg-white rounded-lg shadow mb-6">

        <div class="p-5 border-b">
            <h2 class="text-lg font-semibold text-gray-800">
                Doanh thu theo ngày
            </h2>
        </div>

        <div class="overflow-x-auto">

            <table class="w-full">

                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-sm font-medium text-gray-600">
                            Ngày
                        </th>

                        <th class="px-5 py-3 text-left text-sm font-medium text-gray-600">
                            Doanh thu
                        </th>
                    </tr>
                </thead>

                <tbody id="revenueSeries">

                    <tr>
                        <td
                            colspan="2"
                            class="px-5 py-4 text-center text-gray-500"
                        >
                            Chưa có dữ liệu
                        </td>
                    </tr>

                </tbody>

            </table>

        </div>

    </div>


    <!-- Top sản phẩm -->
    <div class="bg-white rounded-lg shadow">

        <div class="p-5 border-b">
            <h2 class="text-lg font-semibold text-gray-800">
                Sản phẩm bán chạy
            </h2>
        </div>


        <div class="overflow-x-auto">

            <table class="w-full">

                <thead class="bg-gray-50">

                    <tr>

                        <th class="px-5 py-3 text-left text-sm font-medium text-gray-600">
                            ID
                        </th>

                        <th class="px-5 py-3 text-left text-sm font-medium text-gray-600">
                            Tên sản phẩm
                        </th>

                        <th class="px-5 py-3 text-left text-sm font-medium text-gray-600">
                            Số lượng bán
                        </th>

                        <th class="px-5 py-3 text-left text-sm font-medium text-gray-600">
                            Doanh thu
                        </th>

                    </tr>

                </thead>


                <tbody id="topProducts">

                    <tr>
                        <td
                            colspan="4"
                            class="px-5 py-4 text-center text-gray-500"
                        >
                            Chưa có dữ liệu
                        </td>
                    </tr>

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection

@push('scripts')
<script>

    const API_BASE_URL = '/api/v1';


    // Format tiền Việt Nam
    function formatMoney(value) {

        return new Intl.NumberFormat('vi-VN').format(value || 0) + ' ₫';

    }


    // Load báo cáo
    async function loadReport() {

        const from = document.getElementById('fromDate').value;
        const to = document.getElementById('toDate').value;


        // Kiểm tra ngày
        if (!from || !to) {

            alert('Vui lòng chọn đầy đủ ngày bắt đầu và ngày kết thúc');

            return;

        }


        // Ngày bắt đầu không được lớn hơn ngày kết thúc
        if (from > to) {

            alert('Ngày bắt đầu không được lớn hơn ngày kết thúc');

            return;

        }


        try {

            const response = await fetch(
                `${API_BASE_URL}/admin/reports/summary?from=${encodeURIComponent(from)}&to=${encodeURIComponent(to)}`,
                {
                    method: 'GET',

                    headers: {
                        'Accept': 'application/json',
                        ...AdminApi.headers()
                    }
                }
            );


            const result = await response.json();


            // Lấy error_code từ API
            const errorCode =
                result.errors?.error_code ||
                result.error_code;


            // Không có quyền
            if (errorCode === 'FORBIDDEN') {

                alert('Bạn không có quyền xem báo cáo');

                return;

            }


            // Khoảng ngày không hợp lệ
            if (errorCode === 'INVALID_DATE_RANGE') {

                alert('Khoảng thời gian không hợp lệ');

                return;

            }


            // Các lỗi khác
            if (!response.ok || result.success === false) {

                alert(result.message || 'Không thể tải báo cáo');

                return;

            }


            // =========================
            // KPI
            // =========================

            const data = result.data || {};

            document.getElementById('revenue').textContent =
                 formatMoney(data.total_revenue);

            document.getElementById('orders').textContent =
                 data.total_orders || 0;

            document.getElementById('totalUsers').textContent =
                  data.total_users || 0;


            // =========================
            // DOANH THU THEO NGÀY
            // =========================

            const revenueSeries =
                result.data?.revenue_series || [];


            const revenueTable =
                document.getElementById('revenueSeries');


            revenueTable.innerHTML = '';


            if (revenueSeries.length === 0) {

                revenueTable.innerHTML = `
                    <tr>
                        <td
                            colspan="2"
                            class="px-5 py-4 text-center text-gray-500"
                        >
                            Chưa có dữ liệu
                        </td>
                    </tr>
                `;

            } else {

                revenueSeries.forEach(item => {

                    revenueTable.innerHTML += `
                        <tr class="border-t">

                            <td class="px-5 py-3">
                                ${escapeHtml(item.date)}
                            </td>

                            <td class="px-5 py-3">
                                ${formatMoney(item.revenue)}
                            </td>

                        </tr>
                    `;

                });

            }


            // =========================
            // TOP SẢN PHẨM
            // =========================

            const topProducts =
                result.data?.top_products || [];


            const productTable =
                document.getElementById('topProducts');


            productTable.innerHTML = '';


            if (topProducts.length === 0) {

                productTable.innerHTML = `
                    <tr>
                        <td
                            colspan="4"
                            class="px-5 py-4 text-center text-gray-500"
                        >
                            Chưa có dữ liệu
                        </td>
                    </tr>
                `;

            } else {

                topProducts.forEach(product => {

                    productTable.innerHTML += `
                        <tr class="border-t">

                            <td class="px-5 py-3">
                                ${escapeHtml(product.product_id)}
                            </td>

                            <td class="px-5 py-3">
                                ${escapeHtml(product.name)}
                            </td>

                            <td class="px-5 py-3">
                                ${product.quantity_sold}
                            </td>

                            <td class="px-5 py-3">
                                ${formatMoney(product.revenue)}
                            </td>

                        </tr>
                    `;

                });

            }

        } catch (error) {

            console.error(error);

            alert('Không thể kết nối đến API');

        }

    }

    function escapeHtml(value) {
        const element = document.createElement('span');
        element.textContent = String(value ?? '');
        return element.innerHTML;
    }

    document.addEventListener('DOMContentLoaded', function () {

        loadReport();

    });

</script>
@endpush
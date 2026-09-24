@extends('layouts.admin')

@section('title', 'Tổng quan')

@section('page-header')
    <p class="text-sm font-medium text-emerald-600">Admin Dashboard</p>
    <h1 class="mt-1 text-3xl font-bold">Tổng quan hệ thống</h1>
    <p class="mt-2 text-slate-500">Xin chào, đây là báo cáo tổng quát về hoạt động kinh doanh.</p>
@endsection

@section('content')
    <!-- Thống kê chung -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8" id="dashboard-stats">
        
        <!-- Doanh thu -->
        <div class="bg-white rounded-xl p-6 shadow-sm ring-1 ring-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Doanh thu tổng</p>
                    <p class="text-2xl font-bold text-slate-900 mt-1" id="stat-revenue">Đang tải...</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i data-feather="dollar-sign"></i>
                </div>
            </div>
        </div>

        <!-- Đơn hàng -->
        <div class="bg-white rounded-xl p-6 shadow-sm ring-1 ring-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Tổng đơn hàng</p>
                    <p class="text-2xl font-bold text-slate-900 mt-1" id="stat-orders">Đang tải...</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center">
                    <i data-feather="shopping-cart"></i>
                </div>
            </div>
        </div>

        <!-- Khách hàng -->
        <div class="bg-white rounded-xl p-6 shadow-sm ring-1 ring-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Khách hàng</p>
                    <p class="text-2xl font-bold text-slate-900 mt-1" id="stat-users">Đang tải...</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center">
                    <i data-feather="users"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-8">
        <!-- Đơn hàng gần đây -->
        <div class="bg-white rounded-xl shadow-sm ring-1 ring-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-200 flex items-center justify-between">
                <h2 class="font-semibold text-slate-800">Đơn hàng gần đây</h2>
                <a href="{{ route('admin.orders.index') }}" class="text-sm font-medium text-emerald-600 hover:text-emerald-700">Xem tất cả</a>
            </div>
            <div class="p-0 overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-slate-50 text-slate-500">
                        <tr>
                            <th class="px-6 py-3 font-medium">Mã đơn</th>
                            <th class="px-6 py-3 font-medium">Khách hàng</th>
                            <th class="px-6 py-3 font-medium">Trạng thái</th>
                            <th class="px-6 py-3 font-medium text-right">Tổng tiền</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200" id="recent-orders-list">
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-slate-500">Đang tải dữ liệu...</td>
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

const getAuthHeaders = () => {
    return window.AdminAuth ? window.AdminAuth.getHeaders() : {
        'Authorization': 'Bearer ' + (sessionStorage.getItem('access_token') || localStorage.getItem('access_token')),
        'Accept': 'application/json'
    };
};

function formatMoney(value) {
    return Number(value || 0).toLocaleString('vi-VN') + 'đ';
}

function getOrderStatus(status) {
    const statusMap = {
        pending: ['Chờ xử lý', 'bg-yellow-100 text-yellow-700'],
        confirmed: ['Đã xác nhận', 'bg-blue-100 text-blue-700'],
        shipping: ['Đang giao', 'bg-purple-100 text-purple-700'],
        completed: ['Hoàn thành', 'bg-emerald-100 text-emerald-700'],
        cancelled: ['Đã hủy', 'bg-red-100 text-red-700'],
        delivered: ['Đã giao', 'bg-emerald-100 text-emerald-700'],
        returned: ['Hoàn trả', 'bg-orange-100 text-orange-700']
    };
    const item = statusMap[status] || ['Không xác định', 'bg-slate-100 text-slate-600'];
    return `<span class="inline-flex items-center gap-1 rounded-full px-2 py-1 text-xs font-medium ${item[1]} ring-1 ring-inset ring-current/20">${item[0]}</span>`;
}

async function loadDashboard() {
    try {
        // Load stats
        const statsRes = await fetch(`${API_BASE_URL}/admin/reports/summary`, {
            headers: getAuthHeaders()
        });
        const statsResult = await statsRes.json();
        
        if (statsRes.ok && statsResult.success) {
            document.getElementById('stat-revenue').textContent = formatMoney(statsResult.data.total_revenue);
            document.getElementById('stat-orders').textContent = statsResult.data.total_orders;
            document.getElementById('stat-users').textContent = statsResult.data.total_users;
        }

        // Load recent orders
        const ordersRes = await fetch(`${API_BASE_URL}/admin/orders?page=1&per_page=5`, {
            headers: getAuthHeaders()
        });
        const ordersResult = await ordersRes.json();

        if (ordersRes.ok && ordersResult.success) {
            const tbody = document.getElementById('recent-orders-list');
            const orders = ordersResult.data || [];
            
            if (orders.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="px-6 py-8 text-center text-slate-500">Chưa có đơn hàng nào</td></tr>';
            } else {
                tbody.innerHTML = orders.map(order => {
                    const customerName = order.user?.name || order.recipient_name || (order.user_id ? ('Khách hàng #' + order.user_id) : 'Khách vãng lai');
                    const customerPhone = order.phone || order.user?.phone || '';

                    return `
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 font-medium text-emerald-600">
                            <a href="/admin/orders/${order.id}" class="hover:underline">${escapeHtml(order.order_code || ('#' + order.id))}</a>
                        </td>
                        <td class="px-6 py-4 text-slate-800">
                            <div class="font-medium">${escapeHtml(customerName)}</div>
                            ${customerPhone ? `<div class="text-xs text-slate-400 font-normal">${escapeHtml(customerPhone)}</div>` : ''}
                        </td>
                        <td class="px-6 py-4">${getOrderStatus(order.status)}</td>
                        <td class="px-6 py-4 text-right font-medium">${formatMoney(order.grand_total)}</td>
                    </tr>
                    `;
                }).join('');
            }
        }
    } catch (error) {
        console.error('Failed to load dashboard:', error);
    }
}

function escapeHtml(unsafe) {
    return (unsafe || '').toString()
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

loadDashboard();
</script>
@endpush

@extends('layouts.admin')

@section('title', 'Quản lý mã giảm giá')

@section('page-header')
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold">
                Quản lý mã giảm giá
            </h1>
            <p class="mt-1 text-sm font-medium text-emerald-600">
                Admin Marketing
            </p>
        </div>
        <button onclick="openModal()" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
            + Thêm mã giảm giá
        </button>
    </div>
@endsection

@section('content')
    <div class="mb-6 flex gap-4">
        <input type="text" id="searchInput" placeholder="Tìm kiếm mã code..." class="w-64 rounded-lg border border-slate-300 px-3 py-2 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
        <select id="statusFilter" class="rounded-lg border border-slate-300 px-3 py-2 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
            <option value="">Tất cả trạng thái</option>
            <option value="active">Hoạt động</option>
            <option value="inactive">Tạm ẩn</option>
        </select>
        <button onclick="loadCoupons(1)" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
            Tìm kiếm
        </button>
    </div>

    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 text-slate-800 border-b border-slate-200">
                <tr>
                    <th class="px-5 py-4 font-semibold">Mã Code</th>
                    <th class="px-5 py-4 font-semibold">Loại</th>
                    <th class="px-5 py-4 font-semibold">Giá trị</th>
                    <th class="px-5 py-4 font-semibold">Hạn dùng</th>
                    <th class="px-5 py-4 font-semibold">Lượt dùng</th>
                    <th class="px-5 py-4 font-semibold">Trạng thái</th>
                    <th class="px-5 py-4 font-semibold">Thao tác</th>
                </tr>
            </thead>
            <tbody id="couponTableBody" class="divide-y divide-slate-100">
                <tr>
                    <td colspan="7" class="px-5 py-8 text-center text-slate-500">
                        Đang tải dữ liệu...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- Phân trang -->
    <div id="pagination" class="mt-6 flex items-center justify-between"></div>

    <!-- Modal Form -->
    <div id="couponModal" class="fixed inset-0 z-50 hidden bg-slate-900/50 flex items-center justify-center p-4">
        <div class="w-full max-w-lg rounded-xl bg-white p-6 shadow-xl max-h-[90vh] overflow-y-auto">
            <h2 id="modalTitle" class="mb-4 text-xl font-bold">Thêm mã giảm giá</h2>
            <form id="couponForm">
                <input type="hidden" id="couponId">
                
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Mã code *</label>
                        <input type="text" id="couponCode" required class="w-full rounded-lg border border-slate-300 px-3 py-2 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 uppercase">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Loại giảm giá *</label>
                        <select id="couponType" required class="w-full rounded-lg border border-slate-300 px-3 py-2 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                            <option value="fixed">Tiền mặt (VNĐ)</option>
                            <option value="percent">Phần trăm (%)</option>
                        </select>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label id="couponValueLabel" class="mb-1 block text-sm font-medium text-slate-700">Giá trị giảm (VNĐ) *</label>
                        <input type="number" id="couponValue" required min="0" step="any" placeholder="Ví dụ: 20000" class="w-full rounded-lg border border-slate-300 px-3 py-2 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                        <div id="couponValueHint" class="mt-1 text-xs text-emerald-600 font-medium"></div>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Đơn tối thiểu (VNĐ)</label>
                        <input type="number" id="couponMinOrder" value="0" min="0" step="any" placeholder="Ví dụ: 500000" class="w-full rounded-lg border border-slate-300 px-3 py-2 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                        <div id="couponMinOrderHint" class="mt-1 text-xs text-slate-500"></div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="mb-1 block text-sm font-medium text-slate-700">Số lượt sử dụng tối đa (Bỏ trống = Không giới hạn)</label>
                    <input type="number" id="couponUsageLimit" min="1" class="w-full rounded-lg border border-slate-300 px-3 py-2 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Ngày bắt đầu</label>
                        <input type="datetime-local" id="couponStartsAt" class="w-full rounded-lg border border-slate-300 px-3 py-2 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Ngày kết thúc</label>
                        <input type="datetime-local" id="couponEndsAt" class="w-full rounded-lg border border-slate-300 px-3 py-2 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                    </div>
                </div>
                
                <div class="mb-6">
                    <label class="mb-1 block text-sm font-medium text-slate-700">Trạng thái</label>
                    <select id="couponStatus" class="w-full rounded-lg border border-slate-300 px-3 py-2 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                        <option value="active">Hoạt động</option>
                        <option value="inactive">Tạm ẩn</option>
                    </select>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeModal()" class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">
                        Hủy
                    </button>
                    <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
                        Lưu
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const API_URL = '/api/v1/admin/coupons';
    let currentPage = 1;

    async function loadCoupons(page = 1) {
        currentPage = page;
        const q = document.getElementById('searchInput').value;
        const status = document.getElementById('statusFilter').value;
        
        try {
            const res = await fetch(`${API_URL}?page=${page}&q=${q}&status=${status}`, {
                headers: window.AdminApi.headers()
            });
            const result = await res.json();
            
            if (!res.ok) throw new Error(result.message || 'Lỗi tải dữ liệu');
            
            renderCoupons(result.data);
            renderPagination(result.meta);
        } catch (error) {
            console.error(error);
            document.getElementById('couponTableBody').innerHTML = `
                <tr><td colspan="7" class="px-5 py-8 text-center text-red-500">Lỗi tải dữ liệu</td></tr>
            `;
            document.getElementById('pagination').innerHTML = '';
        }
    }

    function renderCoupons(coupons) {
        const tbody = document.getElementById('couponTableBody');
        if (!coupons.length) {
            tbody.innerHTML = `<tr><td colspan="7" class="px-5 py-8 text-center text-slate-500">Chưa có mã giảm giá nào.</td></tr>`;
            return;
        }

        tbody.innerHTML = coupons.map(coupon => {
            const numVal = parseFloat(coupon.value) || 0;
            const val = coupon.type === 'percent' ? `${numVal}%` : `${new Intl.NumberFormat('vi-VN').format(numVal)} đ`;
            const minOrder = parseFloat(coupon.min_order_amount) || 0;
            
            return `
            <tr class="hover:bg-slate-50">
                <td class="px-5 py-3 font-bold text-slate-800">${escapeHtml(coupon.code)}</td>
                <td class="px-5 py-3">${coupon.type === 'percent' ? 'Phần trăm' : 'Tiền mặt'}</td>
                <td class="px-5 py-3">
                    <span class="font-medium text-emerald-600">${val}</span>
                    ${minOrder > 0 ? `<div class="text-xs text-slate-400">Đơn từ ${new Intl.NumberFormat('vi-VN').format(minOrder)}đ</div>` : ''}
                </td>
                <td class="px-5 py-3 text-sm text-slate-500">
                    ${coupon.starts_at ? new Date(coupon.starts_at).toLocaleDateString('vi-VN') : 'Bất kỳ'} 
                    &rarr; 
                    ${coupon.ends_at ? new Date(coupon.ends_at).toLocaleDateString('vi-VN') : 'Vô thời hạn'}
                </td>
                <td class="px-5 py-3 text-sm">
                    ${coupon.usage_limit ? `Max: ${coupon.usage_limit}` : 'Không giới hạn'}
                </td>
                <td class="px-5 py-3">
                    <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold ${
                        coupon.status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700'
                    }">
                        ${coupon.status === 'active' ? 'Hoạt động' : 'Tạm ẩn'}
                    </span>
                </td>
                <td class="px-5 py-3">
                    <div class="flex gap-2">
                        <button onclick='editCoupon(${JSON.stringify(coupon).replace(/'/g, "&#39;")})' class="text-blue-600 hover:underline">Sửa</button>
                        <button onclick="deleteCoupon(${coupon.id})" class="text-red-600 hover:underline">Xóa</button>
                    </div>
                </td>
            </tr>
            `;
        }).join('');
    }

    function renderPagination(meta) {
        if (!meta || meta.last_page <= 1) {
            document.getElementById('pagination').innerHTML = '';
            return;
        }
        
        let html = `<div class="text-sm text-slate-500">Hiển thị ${meta.from || 0} - ${meta.to || 0} trong ${meta.total || 0}</div><div class="flex gap-1">`;
        
        for (let i = 1; i <= meta.last_page; i++) {
            if (i === meta.current_page) {
                html += `<button class="rounded-lg bg-emerald-600 px-3 py-1 text-white">${i}</button>`;
            } else {
                html += `<button onclick="loadCoupons(${i})" class="rounded-lg bg-slate-100 px-3 py-1 text-slate-700 hover:bg-slate-200">${i}</button>`;
            }
        }
        html += `</div>`;
        document.getElementById('pagination').innerHTML = html;
    }

    function formatDateTimeLocal(dateStr) {
        if (!dateStr) return '';
        const d = new Date(dateStr);
        d.setMinutes(d.getMinutes() - d.getTimezoneOffset());
        return d.toISOString().slice(0, 16);
    }

    function updateCouponTypeUI() {
        const type = document.getElementById('couponType').value;
        const label = document.getElementById('couponValueLabel');
        const input = document.getElementById('couponValue');
        if (type === 'percent') {
            label.textContent = 'Giá trị giảm (%) *';
            input.placeholder = 'Ví dụ: 10 (từ 1 - 100)';
            input.max = 100;
        } else {
            label.textContent = 'Giá trị giảm (VNĐ) *';
            input.placeholder = 'Ví dụ: 20000';
            input.removeAttribute('max');
        }
        updateValueHint();
    }

    function updateValueHint() {
        const type = document.getElementById('couponType').value;
        const rawVal = document.getElementById('couponValue').value;
        const val = parseFloat(rawVal);
        const hint = document.getElementById('couponValueHint');
        if (isNaN(val) || val <= 0) {
            hint.textContent = '';
            return;
        }
        if (type === 'percent') {
            hint.textContent = `Xem trước: Giảm ${val}%`;
        } else {
            hint.textContent = `Xem trước: Giảm ${new Intl.NumberFormat('vi-VN').format(val)} đ`;
        }
    }

    function updateMinOrderHint() {
        const rawVal = document.getElementById('couponMinOrder').value;
        const val = parseFloat(rawVal);
        const hint = document.getElementById('couponMinOrderHint');
        if (isNaN(val) || val <= 0) {
            hint.textContent = 'Áp dụng cho mọi giá trị đơn';
            return;
        }
        hint.textContent = `Đơn tối thiểu: ${new Intl.NumberFormat('vi-VN').format(val)} đ`;
    }

    function openModal() {
        document.getElementById('couponId').value = '';
        document.getElementById('couponCode').value = '';
        document.getElementById('couponType').value = 'fixed';
        document.getElementById('couponValue').value = '';
        document.getElementById('couponMinOrder').value = '0';
        document.getElementById('couponUsageLimit').value = '';
        document.getElementById('couponStartsAt').value = '';
        document.getElementById('couponEndsAt').value = '';
        document.getElementById('couponStatus').value = 'active';
        document.getElementById('modalTitle').textContent = 'Thêm mã giảm giá';
        updateCouponTypeUI();
        updateMinOrderHint();
        document.getElementById('couponModal').classList.remove('hidden');
    }

    function editCoupon(coupon) {
        document.getElementById('couponId').value = coupon.id;
        document.getElementById('couponCode').value = coupon.code;
        document.getElementById('couponType').value = coupon.type;
        // Parse float to remove redundant trailing zeros like 20000.00
        document.getElementById('couponValue').value = parseFloat(coupon.value) || 0;
        document.getElementById('couponMinOrder').value = parseFloat(coupon.min_order_amount) || 0;
        document.getElementById('couponUsageLimit').value = coupon.usage_limit || '';
        document.getElementById('couponStartsAt').value = formatDateTimeLocal(coupon.starts_at);
        document.getElementById('couponEndsAt').value = formatDateTimeLocal(coupon.ends_at);
        document.getElementById('couponStatus').value = coupon.status;
        document.getElementById('modalTitle').textContent = 'Sửa mã giảm giá';
        updateCouponTypeUI();
        updateMinOrderHint();
        document.getElementById('couponModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('couponModal').classList.add('hidden');
    }

    document.getElementById('couponForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const id = document.getElementById('couponId').value;
        const payload = {
            code: document.getElementById('couponCode').value.toUpperCase(),
            type: document.getElementById('couponType').value,
            value: document.getElementById('couponValue').value,
            min_order_amount: document.getElementById('couponMinOrder').value || 0,
            usage_limit: document.getElementById('couponUsageLimit').value || null,
            starts_at: document.getElementById('couponStartsAt').value || null,
            ends_at: document.getElementById('couponEndsAt').value || null,
            status: document.getElementById('couponStatus').value
        };

        const method = id ? 'PUT' : 'POST';
        const url = id ? `${API_URL}/${id}` : API_URL;

        try {
            const res = await fetch(url, {
                method,
                headers: window.AdminApi.headers(true),
                body: JSON.stringify(payload)
            });
            const result = await res.json();
            
            if (!res.ok) {
                alert(result.message || 'Có lỗi xảy ra');
                return;
            }
            
            closeModal();
            loadCoupons(currentPage);
        } catch (error) {
            alert('Lỗi kết nối API');
        }
    });

    async function deleteCoupon(id) {
        if (!confirm('Bạn có chắc muốn xóa mã giảm giá này?')) return;
        
        try {
            const res = await fetch(`${API_URL}/${id}`, {
                method: 'DELETE',
                headers: window.AdminApi.headers()
            });
            const result = await res.json();
            
            if (!res.ok) {
                alert(result.message || 'Không thể xóa');
                return;
            }
            
            loadCoupons(currentPage);
        } catch (error) {
            alert('Lỗi kết nối API');
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

    document.addEventListener('DOMContentLoaded', () => {
        loadCoupons(1);
        document.getElementById('couponType')?.addEventListener('change', updateCouponTypeUI);
        document.getElementById('couponValue')?.addEventListener('input', updateValueHint);
        document.getElementById('couponMinOrder')?.addEventListener('input', updateMinOrderHint);
    });
</script>
@endpush

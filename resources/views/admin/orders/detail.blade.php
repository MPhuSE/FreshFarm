@extends('layouts.admin')

@section('title', 'Chi tiết đơn hàng')

@section('page-header')
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.orders.index') }}" class="text-slate-400 hover:text-emerald-600 transition-colors">
            <i data-feather="arrow-left" class="w-5 h-5"></i>
        </a>
        <div>
            <p class="text-sm font-medium text-emerald-600">Admin Order</p>
            <h1 class="mt-1 text-3xl font-bold text-slate-900">Chi tiết đơn hàng</h1>
        </div>
    </div>
@endsection

@section('content')
    <section class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <!-- Thông tin tổng quan đơn hàng -->
        <div class="grid gap-6 md:grid-cols-3">
            <div>
                <p class="text-xs uppercase tracking-wider font-semibold text-slate-500">Mã đơn hàng</p>
                <p id="orderCode" class="mt-1 text-xl font-bold text-slate-900">Đang tải...</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wider font-semibold text-slate-500">Ngày tạo</p>
                <p id="createdAt" class="mt-1 font-medium text-slate-800">-</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wider font-semibold text-slate-500">Trạng thái đơn</p>
                <div id="orderStatus" class="mt-1">-</div>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wider font-semibold text-slate-500">Trạng thái thanh toán</p>
                <div id="paymentStatus" class="mt-1">-</div>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wider font-semibold text-slate-500">Phương thức thanh toán</p>
                <p id="paymentMethod" class="mt-1 font-medium text-slate-800">-</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wider font-semibold text-slate-500">Mã giao dịch</p>
                <p id="transactionRef" class="mt-1 font-medium text-slate-800">-</p>
            </div>
        </div>

        <!-- Thông tin khách hàng & Giao hàng -->
        <div class="mt-8 border-t border-slate-200 pt-6">
            <h2 class="text-lg font-semibold text-slate-800 mb-4">Thông tin khách hàng & Giao hàng</h2>
            <div class="grid gap-6 md:grid-cols-2 rounded-xl bg-slate-50 p-5 border border-slate-200">
                <div>
                    <p class="text-xs uppercase tracking-wider font-semibold text-slate-500">Tài khoản đặt hàng</p>
                    <p id="customerName" class="mt-1 font-semibold text-slate-900">-</p>
                    <p id="customerEmail" class="text-sm text-slate-500">-</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wider font-semibold text-slate-500">Người nhận hàng</p>
                    <p id="recipientName" class="mt-1 font-semibold text-slate-900">-</p>
                    <p id="recipientPhone" class="text-sm text-slate-600">-</p>
                </div>
                <div class="md:col-span-2">
                    <p class="text-xs uppercase tracking-wider font-semibold text-slate-500">Địa chỉ giao hàng</p>
                    <p id="shippingAddress" class="mt-1 text-slate-800 font-medium">-</p>
                </div>
                <div class="md:col-span-2">
                    <p class="text-xs uppercase tracking-wider font-semibold text-slate-500">Ghi chú từ khách</p>
                    <p id="orderNote" class="mt-1 text-slate-600 italic">Không có ghi chú</p>
                </div>
            </div>
        </div>

        <!-- Sản phẩm trong đơn -->
        <div class="mt-8 border-t border-slate-200 pt-6">
            <h2 class="text-lg font-semibold text-slate-800 mb-4">Sản phẩm trong đơn</h2>
            <div class="overflow-x-auto rounded-lg border border-slate-200">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-slate-600 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Sản phẩm</th>
                            <th class="px-4 py-3 font-semibold">Đơn vị</th>
                            <th class="px-4 py-3 font-semibold">Số lượng</th>
                            <th class="px-4 py-3 font-semibold">Đơn giá</th>
                            <th class="px-4 py-3 font-semibold text-right">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody id="orderItems" class="divide-y divide-slate-100">
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-slate-500">Đang tải sản phẩm...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tính tiền -->
        <div class="mt-8 border-t border-slate-200 pt-6">
            <div class="ml-auto max-w-sm space-y-3">
                <div class="flex justify-between text-slate-600">
                    <span>Tạm tính</span>
                    <span id="subtotal" class="font-medium text-slate-800">0đ</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Giảm giá</span>
                    <span id="discount" class="font-medium text-emerald-600">-0đ</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Phí giao hàng</span>
                    <span id="shippingFee" class="font-medium text-slate-800">0đ</span>
                </div>
                <div class="flex justify-between border-t border-slate-200 pt-3 text-lg font-bold text-slate-900">
                    <span>Tổng cộng</span>
                    <span id="grandTotal" class="text-emerald-600">0đ</span>
                </div>
            </div>
        </div>

        <!-- Cập nhật đơn hàng -->
        <div class="mt-8 border-t border-slate-200 pt-6">
            <h2 class="text-lg font-semibold text-slate-800 mb-4">Cập nhật đơn hàng</h2>
            <div class="grid gap-4 md:grid-cols-2">
                <!-- Status -->
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Trạng thái đơn hàng</label>
                    <select id="newStatus" class="w-full rounded-lg border border-slate-300 px-3 py-2 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                    </select>
                    <div id="statusFlowHint" class="mt-2 text-xs leading-relaxed text-slate-600 bg-amber-50/70 p-2.5 rounded-lg border border-amber-200/80"></div>
                </div>

                <!-- Payment -->
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Trạng thái thanh toán</label>
                    <select id="newPaymentStatus" class="w-full rounded-lg border border-slate-300 px-3 py-2 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                        <option value="unpaid">Chưa thanh toán</option>
                        <option value="paid">Đã thanh toán</option>
                        <option value="refunded">Đã hoàn tiền</option>
                    </select>
                </div>

                <!-- Transaction Ref -->
                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-slate-700">Mã giao dịch thanh toán</label>
                    <input type="text" id="newTransactionRef" class="w-full rounded-lg border border-slate-300 px-3 py-2 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" placeholder="Ví dụ: VCB-20260902-001, VNPAY-123456 (nếu có)">
                    <p class="mt-1 text-xs text-slate-500">Nhập mã tham chiếu ngân hàng hoặc mã giao dịch từ cổng thanh toán trực tuyến.</p>
                </div>

                <!-- Note -->
                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-slate-700">Ghi chú cập nhật</label>
                    <textarea id="statusNote" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" placeholder="Nhập ghi chú khi thay đổi trạng thái (tùy chọn)..."></textarea>
                </div>
            </div>

            <div class="mt-6 flex flex-wrap gap-3">
                <button type="button" id="btnUpdateStatus" onclick="updateOrderStatus()" class="rounded-lg bg-emerald-600 px-4 py-2 font-medium text-white hover:bg-emerald-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    Cập nhật trạng thái đơn
                </button>
                <button type="button" id="btnUpdatePayment" onclick="updatePaymentStatus()" class="rounded-lg bg-blue-600 px-4 py-2 font-medium text-white hover:bg-blue-700 transition-colors">
                    Cập nhật thanh toán
                </button>
                <a href="{{ route('admin.orders.index') }}" class="rounded-lg border border-slate-300 bg-white px-4 py-2 font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                    Quay lại danh sách
                </a>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
const API_BASE_URL = '/api/v1';
const orderId = {{ $id ?? 'null' }};

function getAuthHeaders(isJson = false) {
    if (window.AdminApi) {
        return window.AdminApi.headers(isJson);
    }
    const token = sessionStorage.getItem('access_token') || localStorage.getItem('access_token');
    const headers = {
        'Accept': 'application/json',
        'Authorization': 'Bearer ' + token
    };
    if (isJson) headers['Content-Type'] = 'application/json';
    return headers;
}

async function loadOrderDetail() {
    if (!orderId) {
        alert('Không tìm thấy ID đơn hàng');
        return;
    }

    try {
        const response = await fetch(`${API_BASE_URL}/admin/orders/${orderId}`, {
            method: 'GET',
            headers: getAuthHeaders()
        });

        const result = await response.json();

        if (response.ok && result.success === true) {
            displayOrder(result.data);
            return;
        }

        const errorCode = result.errors?.error_code || result.error_code;
        if (errorCode === 'ORDER_NOT_FOUND') {
            document.getElementById('orderCode').textContent = 'Không tìm thấy đơn hàng';
            alert('Không tìm thấy đơn hàng #' + orderId);
            return;
        }

        alert('Không thể tải đơn hàng: ' + (result.message || errorCode || 'UNKNOWN_ERROR'));
    } catch (error) {
        console.error('Lỗi khi tải chi tiết đơn hàng:', error);
        alert('Không thể kết nối đến máy chủ.');
    }
}

function displayOrder(order) {
    document.getElementById('orderCode').textContent = order.order_code || ('#' + order.id);
    document.getElementById('createdAt').textContent = formatDate(order.created_at || order.placed_at);
    document.getElementById('orderStatus').innerHTML = getOrderStatus(order.status);
    document.getElementById('paymentStatus').innerHTML = getPaymentStatus(order.payment_status);
    document.getElementById('paymentMethod').textContent = getPaymentMethodName(order.payment_method);
    document.getElementById('transactionRef').textContent = order.transaction_ref || 'Chưa có';

    // Khách hàng & giao hàng
    const customerName = order.user?.name || order.recipient_name || (order.user_id ? ('Khách hàng #' + order.user_id) : 'Khách vãng lai');
    document.getElementById('customerName').textContent = customerName;
    document.getElementById('customerEmail').textContent = order.user?.email || 'Không có email';
    document.getElementById('recipientName').textContent = order.recipient_name || customerName;
    document.getElementById('recipientPhone').textContent = order.phone || order.user?.phone || '-';
    document.getElementById('shippingAddress').textContent = order.shipping_address || 'Nhận tại cửa hàng';
    document.getElementById('orderNote').textContent = order.note || 'Không có ghi chú';

    // Tiền tệ
    document.getElementById('subtotal').textContent = formatMoney(order.subtotal);
    const discountVal = parseFloat(order.discount_amount || order.discount || 0);
    document.getElementById('discount').textContent = discountVal > 0 ? ('-' + formatMoney(discountVal)) : '0đ';
    document.getElementById('shippingFee').textContent = formatMoney(order.shipping_fee || 0);
    document.getElementById('grandTotal').textContent = formatMoney(order.grand_total);

    // Form inputs
    renderStatusOptions(order.status);
    document.getElementById('newPaymentStatus').value = order.payment_status;
    document.getElementById('newTransactionRef').value = order.transaction_ref || '';

    displayItems(order.items || []);
}

function renderStatusOptions(currentStatus) {
    const select = document.getElementById('newStatus');
    const hint = document.getElementById('statusFlowHint');
    const btn = document.getElementById('btnUpdateStatus');

    const transitionMap = {
        pending: {
            allowed: [
                { value: 'confirmed', label: '1. Xác nhận đơn hàng (Đã xác nhận)' },
                { value: 'cancelled', label: 'Hủy đơn hàng' }
            ],
            hint: '💡 <b>Quy trình chuẩn:</b> Chờ xử lý ➔ Đã xác nhận ➔ Đang giao ➔ Đã giao hàng.<br>Đơn hiện tại đang <b>Chờ xử lý</b>. Vui lòng bấm <b>"Cập nhật trạng thái đơn"</b> để chuyển sang <b>Đã xác nhận</b> trước khi chuyển sang bước giao hàng.'
        },
        confirmed: {
            allowed: [
                { value: 'shipping', label: '2. Tiến hành giao hàng (Đang giao)' },
                { value: 'cancelled', label: 'Hủy đơn hàng' }
            ],
            hint: '💡 Đơn đã được xác nhận. Bước tiếp theo: Chọn <b>"Đang giao"</b> khi nhân viên hoặc shipper lấy hàng đi giao.'
        },
        shipping: {
            allowed: [
                { value: 'delivered', label: '3. Hoàn tất giao hàng (Đã giao hàng)' },
                { value: 'completed', label: 'Hoàn thành đơn hàng' },
                { value: 'cancelled', label: 'Hủy đơn hàng' }
            ],
            hint: '💡 Đơn hàng đang trên đường giao tới khách. Bước tiếp theo: Chọn <b>"Đã giao hàng"</b> khi khách đã nhận hàng.'
        },
        delivered: {
            allowed: [
                { value: 'returned', label: 'Khách yêu cầu trả hàng (Đã trả hàng)' }
            ],
            hint: '✅ Đơn hàng đã giao thành công.'
        },
        completed: {
            allowed: [
                { value: 'returned', label: 'Khách yêu cầu trả hàng (Đã trả hàng)' }
            ],
            hint: '✅ Đơn hàng đã hoàn thành trọn vẹn.'
        },
        cancelled: {
            allowed: [],
            hint: '❌ Đơn hàng này đã bị hủy, không thể thay đổi trạng thái nữa.'
        },
        returned: {
            allowed: [],
            hint: '↩️ Đơn hàng đã ghi nhận trả hàng thành công.'
        }
    };

    const config = transitionMap[currentStatus] || { allowed: [], hint: '' };
    
    let html = `<option value="" disabled selected>Trạng thái hiện tại: ${getOrderStatusLabel(currentStatus)}</option>`;
    config.allowed.forEach(opt => {
        html += `<option value="${opt.value}">${opt.label}</option>`;
    });

    select.innerHTML = html;
    if (config.allowed.length > 0) {
        select.value = config.allowed[0].value;
        select.disabled = false;
        btn.disabled = false;
    } else {
        select.disabled = true;
        btn.disabled = true;
    }

    hint.innerHTML = config.hint;
}

function getOrderStatusLabel(status) {
    const map = {
        pending: 'Chờ xử lý',
        confirmed: 'Đã xác nhận',
        shipping: 'Đang giao',
        delivered: 'Đã giao hàng',
        completed: 'Hoàn thành',
        cancelled: 'Đã hủy',
        returned: 'Đã trả hàng'
    };
    return map[status] || status;
}

function displayItems(items) {
    const tbody = document.getElementById('orderItems');
    if (!items || items.length === 0) {
        tbody.innerHTML = `<tr><td colspan="5" class="px-4 py-6 text-center text-slate-500">Đơn hàng không có sản phẩm nào.</td></tr>`;
        return;
    }

    tbody.innerHTML = items.map(item => {
        const pName = escapeHtml(item.product_name || item.product?.name || 'Sản phẩm');
        const unit = escapeHtml(item.unit || item.product?.unit || 'Phần');
        const qty = parseFloat(item.quantity) || 1;
        const uPrice = formatMoney(item.unit_price);
        const lTotal = formatMoney(item.line_total || (parseFloat(item.unit_price || 0) * qty));

        let imgUrl = '/tv4/assets/images/favicon.svg';
        if (item.product?.images?.length) {
            const primary = item.product.images.find(img => img.is_primary) || item.product.images[0];
            if (primary?.file_path) {
                imgUrl = primary.file_path.startsWith('http') || primary.file_path.startsWith('/') 
                    ? primary.file_path 
                    : ('/storage/' + primary.file_path);
            }
        }

        return `
        <tr class="hover:bg-slate-50">
            <td class="px-4 py-3">
                <div class="flex items-center gap-3">
                    <img src="${imgUrl}" alt="${pName}" class="w-10 h-10 rounded-lg object-cover border border-slate-200" onerror="this.src='/tv4/assets/images/favicon.svg'">
                    <span class="font-medium text-slate-900">${pName}</span>
                </div>
            </td>
            <td class="px-4 py-3 text-slate-600">${unit}</td>
            <td class="px-4 py-3 text-slate-800 font-medium">${qty}</td>
            <td class="px-4 py-3 text-slate-600">${uPrice}</td>
            <td class="px-4 py-3 font-semibold text-slate-900 text-right">${lTotal}</td>
        </tr>
        `;
    }).join('');
}

async function updateOrderStatus() {
    const status = document.getElementById('newStatus').value;
    const note = document.getElementById('statusNote').value.trim();

    try {
        const response = await fetch(`${API_BASE_URL}/admin/orders/${orderId}/status`, {
            method: 'PATCH',
            headers: getAuthHeaders(true),
            body: JSON.stringify({
                status: status,
                note: note || undefined
            })
        });

        const result = await response.json();

        if (response.ok && result.success === true) {
            alert('Cập nhật trạng thái đơn thành công.');
            loadOrderDetail();
            return;
        }

        alert('Lỗi cập nhật trạng thái: ' + (result.message || result.error_code || 'UNKNOWN_ERROR'));
    } catch (error) {
        console.error(error);
        alert('Không thể kết nối đến máy chủ.');
    }
}

async function updatePaymentStatus() {
    const paymentStatus = document.getElementById('newPaymentStatus').value;
    const transactionRef = document.getElementById('newTransactionRef').value.trim();

    try {
        const response = await fetch(`${API_BASE_URL}/admin/orders/${orderId}/payment-status`, {
            method: 'PATCH',
            headers: getAuthHeaders(true),
            body: JSON.stringify({
                payment_status: paymentStatus,
                transaction_ref: transactionRef ? transactionRef : null
            })
        });

        const result = await response.json();

        if (response.ok && result.success === true) {
            alert('Cập nhật thanh toán thành công.');
            loadOrderDetail();
            return;
        }

        alert('Lỗi cập nhật thanh toán: ' + (result.message || result.error_code || 'UNKNOWN_ERROR'));
    } catch (error) {
        console.error(error);
        alert('Không thể kết nối đến máy chủ.');
    }
}

function getOrderStatus(status) {
    const map = {
        pending: ['Chờ xử lý', 'bg-yellow-100 text-yellow-700 ring-yellow-600/20'],
        confirmed: ['Đã xác nhận', 'bg-blue-100 text-blue-700 ring-blue-600/20'],
        shipping: ['Đang giao', 'bg-purple-100 text-purple-700 ring-purple-600/20'],
        delivered: ['Đã giao hàng', 'bg-emerald-100 text-emerald-700 ring-emerald-600/20'],
        completed: ['Hoàn thành', 'bg-emerald-100 text-emerald-700 ring-emerald-600/20'],
        cancelled: ['Đã hủy', 'bg-red-100 text-red-700 ring-red-600/20'],
        returned: ['Trả hàng', 'bg-orange-100 text-orange-700 ring-orange-600/20']
    };

    const item = map[status] || ['Không xác định', 'bg-slate-100 text-slate-600 ring-slate-500/20'];
    return `<span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ${item[1]} ring-1 ring-inset">${item[0]}</span>`;
}

function getPaymentStatus(status) {
    if (status === 'paid') {
        return `<span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">Đã thanh toán</span>`;
    }
    if (status === 'refunded') {
        return `<span class="inline-flex items-center rounded-full bg-orange-100 px-2.5 py-1 text-xs font-medium text-orange-700 ring-1 ring-inset ring-orange-600/20">Đã hoàn tiền</span>`;
    }
    return `<span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600 ring-1 ring-inset ring-slate-500/20">Chưa thanh toán</span>`;
}

function getPaymentMethodName(method) {
    const map = {
        cod: 'Thanh toán khi nhận hàng (COD)',
        vnpay: 'VNPAY (Thanh toán online)',
        bank_transfer: 'Chuyển khoản ngân hàng'
    };
    return map[method] || (method ? method.toUpperCase() : '-');
}

function formatMoney(value) {
    return Number(value || 0).toLocaleString('vi-VN') + 'đ';
}

function formatDate(value) {
    if (!value) return '-';
    return new Date(value).toLocaleString('vi-VN');
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
    loadOrderDetail();
});
</script>
@endpush
@extends('layouts.admin')

@section('title', 'Chi tiết đơn hàng')

@section('page-header')
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.orders.index') }}" class="text-slate-400 hover:text-emerald-600">
            <i data-feather="arrow-left" class="w-5 h-5"></i>
        </a>
        <div>
            <p class="text-sm font-medium text-emerald-600">Admin Order</p>
            <h1 class="mt-1 text-3xl font-bold">Chi tiết đơn hàng</h1>
        </div>
    </div>
@endsection

@section('content')


    <section
        class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">


        <!-- Thông tin đơn hàng -->

        <div class="grid gap-6 md:grid-cols-2">


            <div>

                <p class="text-sm text-slate-500">
                    Mã đơn hàng
                </p>

                <p
                    id="orderCode"
                    class="mt-1 text-lg font-bold">

                    Đang tải...

                </p>

            </div>


            <div>

                <p class="text-sm text-slate-500">
                    Ngày tạo
                </p>

                <p
                    id="createdAt"
                    class="mt-1 font-medium">

                    -

                </p>

            </div>


            <div>

                <p class="text-sm text-slate-500">
                    Trạng thái đơn
                </p>

                <div
                    id="orderStatus"
                    class="mt-1">

                    -

                </div>

            </div>


            <div>

                <p class="text-sm text-slate-500">
                    Trạng thái thanh toán
                </p>

                <div
                    id="paymentStatus"
                    class="mt-1">

                    -

                </div>

            </div>


            <div>

                <p class="text-sm text-slate-500">
                    Phương thức thanh toán
                </p>

                <p
                    id="paymentMethod"
                    class="mt-1 font-medium">

                    -

                </p>

            </div>


            <div>

                <p class="text-sm text-slate-500">
                    Mã giao dịch
                </p>

                <p
                    id="transactionRef"
                    class="mt-1 font-medium">

                    -

                </p>

            </div>

        </div>


  

        <div class="mt-8 border-t border-slate-200 pt-6">

            <h2 class="text-lg font-semibold">
                Sản phẩm trong đơn
            </h2>


            <div class="mt-4 overflow-x-auto">

                <table class="w-full text-left text-sm">

                    <thead class="bg-slate-50">

                        <tr>

                            <th class="px-4 py-3">
                                Sản phẩm
                            </th>

                            <th class="px-4 py-3">
                                Đơn vị
                            </th>

                            <th class="px-4 py-3">
                                Số lượng
                            </th>

                            <th class="px-4 py-3">
                                Đơn giá
                            </th>

                            <th class="px-4 py-3">
                                Thành tiền
                            </th>

                        </tr>

                    </thead>


                    <tbody
                        id="orderItems"
                        class="divide-y divide-slate-100">

                    </tbody>

                </table>

            </div>

        </div>


      

        <div class="mt-8 border-t border-slate-200 pt-6">

            <div class="ml-auto max-w-sm space-y-3">

                <div class="flex justify-between">

                    <span>
                        Tạm tính
                    </span>

                    <span id="subtotal">
                        0đ
                    </span>

                </div>


                <div class="flex justify-between">

                    <span>
                        Giảm giá
                    </span>

                    <span id="discount">
                        0đ
                    </span>

                </div>


                <div class="flex justify-between">

                    <span>
                        Phí giao hàng
                    </span>

                    <span id="shippingFee">
                        0đ
                    </span>

                </div>


                <div
                    class="flex justify-between border-t border-slate-200 pt-3 text-lg font-bold">

                    <span>
                        Tổng cộng
                    </span>

                    <span
                        id="grandTotal"
                        class="text-emerald-600">

                        0đ

                    </span>

                </div>

            </div>

        </div>


        <!-- Cập nhật trạng thái -->

        <div class="mt-8 border-t border-slate-200 pt-6">

            <h2 class="text-lg font-semibold">
                Cập nhật đơn hàng
            </h2>


            <div class="mt-4 grid gap-4 md:grid-cols-2">


                <!-- Status -->

                <div>

                    <label class="mb-1 block text-sm font-medium">

                        Trạng thái đơn

                    </label>

                    <select
                        id="newStatus"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2">

                        <option value="pending">
                            Chờ xử lý
                        </option>

                        <option value="confirmed">
                            Đã xác nhận
                        </option>

                        <option value="shipping">
                            Đang giao
                        </option>

                        <option value="completed">
                            Hoàn thành
                        </option>

                        <option value="cancelled">
                            Đã hủy
                        </option>

                    </select>

                </div>


                <!-- Payment -->

                <div>

                    <label class="mb-1 block text-sm font-medium">

                        Trạng thái thanh toán

                    </label>

                    <select
                        id="newPaymentStatus"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2">

                        <option value="unpaid">
                            Chưa thanh toán
                        </option>

                        <option value="paid">
                            Đã thanh toán
                        </option>

                    </select>

                </div>


                <!-- Note -->

                <div class="md:col-span-2">

                    <label class="mb-1 block text-sm font-medium">

                        Ghi chú

                    </label>

                    <textarea
                        id="statusNote"
                        rows="3"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2"
                        placeholder="Ghi chú cập nhật đơn hàng..."></textarea>

                </div>

            </div>


            <div class="mt-4 flex gap-3">

                <button
                    type="button"
                    onclick="updateOrderStatus()"
                    class="rounded-lg bg-emerald-600 px-4 py-2 font-medium text-white hover:bg-emerald-700">

                    Cập nhật trạng thái

                </button>


                <button
                    type="button"
                    onclick="updatePaymentStatus()"
                    class="rounded-lg bg-blue-600 px-4 py-2 font-medium text-white hover:bg-blue-700">

                    Cập nhật thanh toán

                </button>


                <a
                    href="{{ route('admin.orders.index') }}"
                    class="rounded-lg border border-slate-300 bg-white px-4 py-2 font-medium">

                    Quay lại

                </a>

            </div>

        </div>

    </section>

@endsection

@push('scripts')
<script>

const API_BASE_URL = '/api/v1';

const orderId = {{ $id ?? 'null' }};




async function loadOrderDetail() {

    try {

        const response = await fetch(

            `${API_BASE_URL}/admin/orders/${orderId}`,

            {

                method: 'GET',

                headers: {
                    'Authorization': 'Bearer ' + (sessionStorage.getItem('access_token') || localStorage.getItem('access_token')),
                    'Accept': 'application/json'
                }

            }

        );


        const result = await response.json();


        if (response.ok && result.success === true) {

            displayOrder(result.data);

            return;

        }


        const errorCode =
            result.errors?.error_code ||
            result.error_code;


        if (errorCode === 'ORDER_NOT_FOUND') {

            alert(
                'ORDER_NOT_FOUND: Không tìm thấy đơn hàng.'
            );

            return;

        }


        alert(
            'Không thể tải đơn hàng: ' +
            (errorCode || 'UNKNOWN_ERROR')
        );


    } catch (error) {

        console.error(error);

        alert(
            'Không thể kết nối đến API.'
        );

    }

}


// ===============================
// HIỂN THỊ ORDER
// ===============================

function displayOrder(order) {

    document.getElementById('orderCode').textContent =
        order.order_code;


    document.getElementById('createdAt').textContent =
        formatDate(order.created_at);


    document.getElementById('orderStatus').innerHTML =
        getOrderStatus(order.status);


    document.getElementById('paymentStatus').innerHTML =
        getPaymentStatus(order.payment_status);


    document.getElementById('paymentMethod').textContent =
        order.payment_method || '-';


    document.getElementById('transactionRef').textContent =
        order.transaction_ref || '-';


    document.getElementById('subtotal').textContent =
        formatMoney(order.subtotal);


    document.getElementById('discount').textContent =
        formatMoney(order.discount);


    document.getElementById('shippingFee').textContent =
        formatMoney(order.shipping_fee);


    document.getElementById('grandTotal').textContent =
        formatMoney(order.grand_total);


    document.getElementById('newStatus').value =
        order.status;


    document.getElementById('newPaymentStatus').value =
        order.payment_status;


    displayItems(order.items || []);

}



function displayItems(items) {

    const tbody =
        document.getElementById('orderItems');


    if (items.length === 0) {

        tbody.innerHTML = `

            <tr>

                <td
                    colspan="5"
                    class="px-4 py-6 text-center text-slate-500">

                    Không có sản phẩm.

                </td>

            </tr>

        `;

        return;

    }


    tbody.innerHTML = '';

items.forEach(function(item) {

    const row = document.createElement('tr');

    const productName = document.createElement('td');
    productName.className = 'px-4 py-3 font-medium';
    productName.textContent = item.product_name ?? '';

    const unit = document.createElement('td');
    unit.className = 'px-4 py-3';
    unit.textContent = item.unit ?? '';

    const quantity = document.createElement('td');
    quantity.className = 'px-4 py-3';
    quantity.textContent = item.quantity ?? '';

    const unitPrice = document.createElement('td');
    unitPrice.className = 'px-4 py-3';
    unitPrice.textContent = formatMoney(item.unit_price);

    const lineTotal = document.createElement('td');
    lineTotal.className = 'px-4 py-3 font-medium';
    lineTotal.textContent = formatMoney(item.line_total);

    row.appendChild(productName);
    row.appendChild(unit);
    row.appendChild(quantity);
    row.appendChild(unitPrice);
    row.appendChild(lineTotal);

    tbody.appendChild(row);
});

async function updateOrderStatus() {

    const status =
        document.getElementById('newStatus').value;


    const note =
        document.getElementById('statusNote').value.trim();


    try {

        const response = await fetch(

            `${API_BASE_URL}/admin/orders/${orderId}/status`,

            {

                method: 'PATCH',

                headers: {

                    'Accept': 'application/json',

                    'Content-Type': 'application/json',

                    'Authorization': 'Bearer ' + (sessionStorage.getItem('access_token') || localStorage.getItem('access_token'))

                },

                body: JSON.stringify({

                    status: status,

                    note: note

                })

            }

        );


        const result = await response.json();


        if (response.ok && result.success === true) {

            alert(
                'Cập nhật trạng thái đơn thành công.'
            );

            location.reload();

            return;

        }


        const errorCode =
            result.errors?.error_code ||
            result.error_code;


        if (errorCode === 'INVALID_ORDER_TRANSITION') {

            alert(
                'INVALID_ORDER_TRANSITION: Không thể chuyển sang trạng thái này.'
            );

            return;

        }


        alert(
            'Lỗi cập nhật trạng thái: ' +
            (errorCode || 'UNKNOWN_ERROR')
        );


    } catch (error) {

        console.error(error);

        alert(
            'Không thể kết nối đến API.'
        );

    }

}



async function updatePaymentStatus() {

    const paymentStatus =
        document.getElementById('newPaymentStatus').value;


    const transactionRef =
        prompt(
            'Nhập mã giao dịch (có thể bỏ trống):'
        );


    try {

        const response = await fetch(

            `${API_BASE_URL}/admin/orders/${orderId}/payment-status`,

            {

                method: 'PATCH',

                headers: {

                    'Accept': 'application/json',

                    'Content-Type': 'application/json',

                    'Authorization': 'Bearer ' + (sessionStorage.getItem('access_token') || localStorage.getItem('access_token'))

                },

                body: JSON.stringify({

                    payment_status: paymentStatus,

                    transaction_ref:
                        transactionRef || null

                })

            }

        );


        const result = await response.json();


        if (response.ok && result.success === true) {

            alert(
                'Cập nhật thanh toán thành công.'
            );

            location.reload();

            return;

        }


        const errorCode =
            result.errors?.error_code ||
            result.error_code;


        if (errorCode === 'INVALID_PAYMENT_STATE') {

            alert(
                'INVALID_PAYMENT_STATE: Trạng thái thanh toán không hợp lệ.'
            );

            return;

        }


        alert(
            'Lỗi cập nhật thanh toán: ' +
            (errorCode || 'UNKNOWN_ERROR')
        );


    } catch (error) {

        console.error(error);

        alert(
            'Không thể kết nối đến API.'
        );

    }

}




function getOrderStatus(status) {

    const map = {

        pending:
            ['Chờ xử lý', 'bg-yellow-100 text-yellow-700'],

        confirmed:
            ['Đã xác nhận', 'bg-blue-100 text-blue-700'],

        shipping:
            ['Đang giao', 'bg-purple-100 text-purple-700'],

        completed:
            ['Hoàn thành', 'bg-emerald-100 text-emerald-700'],

        cancelled:
            ['Đã hủy', 'bg-red-100 text-red-700']

    };


    const item =
        map[status] ||
        ['Không xác định', 'bg-slate-100 text-slate-600'];


    return `

        <span class="rounded-full px-3 py-1 text-xs font-medium ${item[1]}">

            ${item[0]}

        </span>

    `;

}



function getPaymentStatus(status) {

    if (status === 'paid') {

        return `

            <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-medium text-emerald-700">

                Đã thanh toán

            </span>

        `;

    }


    return `

        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">

            Chưa thanh toán

        </span>

    `;

}



function formatMoney(value) {

    return Number(value || 0)
        .toLocaleString('vi-VN') + 'đ';

}



function formatDate(value) {

    if (!value) {
        return '-';
    }

    return new Date(value)
        .toLocaleString('vi-VN');

}




loadOrderDetail();

</script>
@endpush
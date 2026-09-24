@extends('layouts.admin')

@section('title', 'Quản lý đơn hàng')

@section('page-header')
    <div class="flex justify-between items-start">
        <div>
            <p class="text-sm font-medium text-emerald-600">Admin Order</p>
            <h1 class="mt-1 text-3xl font-bold">Quản lý đơn hàng</h1>
            <p class="mt-2 text-slate-500">Quản lý danh sách đơn hàng trong hệ thống</p>
        </div>
        <button type="button" onclick="downloadExport()" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
            <i data-feather="download" class="w-4 h-4"></i>
            Xuất Excel
        </button>
    </div>
@endsection

@section('content')
 
    <section class="mb-6 rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">

        <h2 class="mb-4 font-semibold">
            Tìm kiếm và lọc đơn hàng
        </h2>

        <div class="grid gap-4 md:grid-cols-4">

        
            <div>
                <label class="mb-1 block text-sm font-medium">
                    Tìm kiếm
                </label>

                <input
                    type="text"
                    id="searchOrder"
                    placeholder="Mã đơn hàng..."
                    class="w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>


            <div>
                <label class="mb-1 block text-sm font-medium">
                    Trạng thái
                </label>

                <select
                    id="statusFilter"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2">

                    <option value="">
                        Tất cả
                    </option>

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

            <div>
                <label class="mb-1 block text-sm font-medium">
                    Thanh toán
                </label>

                <select
                    id="paymentStatusFilter"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2">

                    <option value="">
                        Tất cả
                    </option>

                    <option value="unpaid">
                        Chưa thanh toán
                    </option>

                    <option value="paid">
                        Đã thanh toán
                    </option>

                </select>
            </div>

            <div class="flex items-end">

                <button
                    type="button"
                    onclick="loadOrders()"
                    class="w-full rounded-lg bg-emerald-600 px-4 py-2 font-medium text-white hover:bg-emerald-700">

                    Tìm kiếm

                </button>

            </div>

        </div>

    </section>


  
    <section class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-slate-200">

        <div class="border-b border-slate-200 px-6 py-4">

            <h2 class="font-semibold">
                Danh sách đơn hàng
            </h2>

        </div>


        <div class="overflow-x-auto">

            <table class="w-full min-w-[1000px] text-left text-sm">

                <thead class="bg-slate-50 text-xs uppercase text-slate-500">

                    <tr>

                        <th class="px-6 py-4">
                            Mã đơn
                        </th>

                        <th class="px-6 py-4">
                            Trạng thái
                        </th>

                        <th class="px-6 py-4">
                            Thanh toán
                        </th>

                        <th class="px-6 py-4">
                            Phương thức
                        </th>

                        <th class="px-6 py-4">
                            Tổng tiền
                        </th>

                        <th class="px-6 py-4">
                            Ngày tạo
                        </th>

                        <th class="px-6 py-4">
                            Chi tiết
                        </th>

                    </tr>

                </thead>


                <tbody
                    id="orderTableBody"
                    class="divide-y divide-slate-100">

                    <tr>

                        <td
                            colspan="7"
                            class="px-6 py-12 text-center text-slate-500">

                            Đang tải đơn hàng...

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </section>



    <div
        id="pagination"
        class="mt-6 flex justify-center gap-2">
    </div>

@endsection

@push('scripts')
<script>

const API_BASE_URL = '/api/v1';

async function loadOrders(page = 1) {

    const q =
        document.getElementById('searchOrder').value.trim();

    const status =
        document.getElementById('statusFilter').value;

    const paymentStatus =
        document.getElementById('paymentStatusFilter').value;


    const params = new URLSearchParams();

    params.append('page', page);

    params.append('per_page', 20);


    if (q !== '') {
        params.append('q', q);
    }

    if (status !== '') {
        params.append('status', status);
    }

    if (paymentStatus !== '') {
        params.append(
            'payment_status',
            paymentStatus
        );
    }


    try {

        const response = await fetch(
            `${API_BASE_URL}/admin/orders?${params.toString()}`,
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

            displayOrders(result.data || []);

            displayPagination(result.meta);

            return;
        }



        const errorCode =
            result.errors?.error_code ||
            result.error_code;


        if (errorCode === 'FORBIDDEN') {

            alert(
                'FORBIDDEN: Bạn không có quyền Staff/Admin.'
            );

            return;
        }


        if (errorCode === 'INVALID_DATE_RANGE') {

            alert(
                'INVALID_DATE_RANGE: Khoảng thời gian không hợp lệ.'
            );

            return;
        }


        if (response.status === 401) {

            alert(
                'Bạn chưa đăng nhập hoặc token đã hết hạn.'
            );

            return;
        }


        alert(
            'Không thể tải danh sách đơn hàng: ' +
            (errorCode || 'UNKNOWN_ERROR')
        );


    } catch (error) {

        console.error(error);

        alert(
            'Không thể kết nối đến API.'
        );

    }

}


function displayOrders(orders) {

    const tbody =
        document.getElementById('orderTableBody');


    if (!orders || orders.length === 0) {

        tbody.innerHTML = `

            <tr>

                <td
                    colspan="7"
                    class="px-6 py-12 text-center text-slate-500">

                    Chưa có đơn hàng nào.

                </td>

            </tr>

        `;

        return;
    }


    tbody.innerHTML = '';


    orders.forEach(function(order) {

        const row = document.createElement('tr');

        row.className = 'hover:bg-slate-50';

const orderInfo = document.createElement('td');
orderInfo.className = 'px-6 py-4';

const orderCode = document.createElement('p');
orderCode.className = 'font-medium';
orderCode.textContent = order.order_code ?? '';

const orderId = document.createElement('p');
orderId.className = 'text-xs text-slate-500';
orderId.textContent = `ID: ${order.id}`;

const customerName = document.createElement('p');
customerName.className = 'text-xs text-slate-700 font-medium mt-1';
const cName = order.user?.name || order.recipient_name || (order.user_id ? ('Khách hàng #' + order.user_id) : 'Khách vãng lai');
customerName.textContent = cName;

orderInfo.appendChild(orderCode);
orderInfo.appendChild(orderId);
orderInfo.appendChild(customerName);


const statusCell = document.createElement('td');
statusCell.className = 'px-6 py-4';
statusCell.innerHTML = getOrderStatus(order.status);


const paymentStatusCell = document.createElement('td');
paymentStatusCell.className = 'px-6 py-4';
paymentStatusCell.innerHTML = getPaymentStatus(order.payment_status);


const paymentMethod = document.createElement('td');
paymentMethod.className = 'px-6 py-4';
paymentMethod.textContent = order.payment_method ?? '-';


const grandTotal = document.createElement('td');
grandTotal.className = 'px-6 py-4 font-medium';
grandTotal.textContent = formatMoney(order.grand_total);


const createdAt = document.createElement('td');
createdAt.className = 'px-6 py-4';
createdAt.textContent = formatDate(order.created_at);


const actionCell = document.createElement('td');
actionCell.className = 'px-6 py-4';

const viewButton = document.createElement('button');
viewButton.type = 'button';
viewButton.className = 'font-medium text-emerald-600 hover:text-emerald-700';
viewButton.textContent = 'Xem chi tiết';
viewButton.onclick = function () {
    viewOrder(order.id);
};

actionCell.appendChild(viewButton);


row.appendChild(orderInfo);
row.appendChild(statusCell);
row.appendChild(paymentStatusCell);
row.appendChild(paymentMethod);
row.appendChild(grandTotal);
row.appendChild(createdAt);
row.appendChild(actionCell);

tbody.appendChild(row);
    });
}

function viewOrder(id) {

    window.location.href =
        `/admin/orders/${id}`;

}



function getOrderStatus(status) {

    const statusMap = {

        pending:
            ['Chờ xử lý', 'bg-yellow-100 text-yellow-700'],

        confirmed:
            ['Đã xác nhận', 'bg-blue-100 text-blue-700'],

        shipping:
            ['Đang giao', 'bg-purple-100 text-purple-700'],
            
        delivered:
            ['Đã giao', 'bg-emerald-100 text-emerald-700'],

        completed:
            ['Hoàn thành', 'bg-emerald-100 text-emerald-700'],

        cancelled:
            ['Đã hủy', 'bg-red-100 text-red-700'],
            
        returned:
            ['Hoàn trả', 'bg-orange-100 text-orange-700']

    };


    const item =
        statusMap[status] ||
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



function displayPagination(meta) {

    const pagination =
        document.getElementById('pagination');


    if (!meta || meta.last_page <= 1) {

        pagination.innerHTML = '';

        return;
    }


    pagination.innerHTML = '';


    for (
        let page = 1;
        page <= meta.last_page;
        page++
    ) {

        const button =
            document.createElement('button');


        button.type = 'button';

        button.textContent = page;

        button.className =
            'rounded-lg border px-3 py-2';


        if (page === meta.current_page) {

            button.className +=
                ' bg-emerald-600 text-white';

        }


        button.onclick = function() {

            loadOrders(page);

        };


        pagination.appendChild(button);

    }

}

async function downloadExport() {
    try {
        const response = await fetch(`${API_BASE_URL}/admin/orders/export`, {
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + (sessionStorage.getItem('access_token') || localStorage.getItem('access_token')),
                'Accept': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            }
        });

        if (!response.ok) {
            alert('Lỗi xuất Excel. Vui lòng thử lại!');
            return;
        }

        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `orders_export_${new Date().toISOString().replace(/[:.]/g, '')}.xlsx`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        a.remove();
    } catch (error) {
        console.error('Export error:', error);
        alert('Lỗi xuất Excel. Vui lòng thử lại!');
    }
}

loadOrders();

</script>
@endpush
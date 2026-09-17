@extends('layouts.admin')

@section('title', 'Quản lý Tồn kho')

@section('page-header')
    <p class="text-sm font-medium text-emerald-600">Admin Inventory</p>
    <h1 class="mt-1 text-3xl font-bold">Quản lý Tồn kho</h1>
    <p class="mt-2 text-slate-500">Xem và quản lý số lượng tồn kho của các sản phẩm.</p>
@endsection

@section('content')
  
    <section class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
        <div class="border-b border-slate-200 px-6 py-4 flex justify-between items-center">
            <h2 class="font-semibold">Danh sách tồn kho</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[800px] text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                    <tr>
                        <th class="px-6 py-4">Mã Sản phẩm</th>
                        <th class="px-6 py-4">Tên Sản phẩm</th>
                        <th class="px-6 py-4">Tồn khả dụng</th>
                        <th class="px-6 py-4">Tồn đang giữ (Reserved)</th>
                        <th class="px-6 py-4">Cập nhật lần cuối</th>
                        <th class="px-6 py-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody id="inventoryTableBody" class="divide-y divide-slate-100">
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500">Đang tải dữ liệu...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <div id="pagination" class="mt-6 flex justify-center gap-2"></div>
@endsection

@push('scripts')
<script>
const API_BASE_URL = '/api/v1';

async function loadInventory(page = 1) {
    try {
        const response = await fetch(`${API_BASE_URL}/admin/inventory?page=${page}`, {
            headers: {
                'Authorization': 'Bearer ' + (sessionStorage.getItem('access_token') || localStorage.getItem('access_token')),
                'Accept': 'application/json'
            }
        });

        const result = await response.json();

        if (response.ok) {
            displayInventory(result.data || []);
            displayPagination(result);
            return;
        }

        alert('Không thể tải danh sách tồn kho.');

    } catch (error) {
        console.error(error);
        alert('Không thể kết nối đến API.');
    }
}

function displayInventory(inventories) {
    const tbody = document.getElementById('inventoryTableBody');

    if (!inventories || inventories.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6" class="px-6 py-12 text-center text-slate-500">Chưa có dữ liệu tồn kho.</td></tr>`;
        return;
    }

    tbody.innerHTML = '';

    inventories.forEach(function(item) {
        const row = document.createElement('tr');
        row.className = 'hover:bg-slate-50';

        const idCell = document.createElement('td');
        idCell.className = 'px-6 py-4';
        idCell.textContent = item.product_id;

        const productCell = document.createElement('td');
        productCell.className = 'px-6 py-4';
        productCell.innerHTML = `<a href="/products/${item.product?.slug}" target="_blank" class="text-emerald-600 font-medium hover:underline">${item.product?.name || 'Sản phẩm đã xóa'}</a>`;

        const availableCell = document.createElement('td');
        availableCell.className = 'px-6 py-4 font-bold text-lg';
        availableCell.textContent = item.quantity_on_hand;

        const reservedCell = document.createElement('td');
        reservedCell.className = 'px-6 py-4 text-slate-500';
        reservedCell.textContent = item.quantity_reserved;

        const dateCell = document.createElement('td');
        dateCell.className = 'px-6 py-4';
        dateCell.textContent = new Date(item.updated_at).toLocaleString('vi-VN');

        const actionCell = document.createElement('td');
        actionCell.className = 'px-6 py-4 text-right';
        
        const editBtn = document.createElement('button');
        editBtn.className = 'text-emerald-600 font-medium hover:underline';
        editBtn.textContent = 'Cập nhật số lượng';
        editBtn.onclick = () => updateQuantity(item.product_id, item.quantity_on_hand);
        
        actionCell.appendChild(editBtn);

        row.append(idCell, productCell, availableCell, reservedCell, dateCell, actionCell);
        tbody.appendChild(row);
    });
}

async function updateQuantity(productId, currentQty) {
    const newQty = prompt('Nhập số lượng tồn kho khả dụng mới:', currentQty);
    if (newQty === null || newQty.trim() === '') return;
    
    const qtyNum = Number(newQty);
    if (isNaN(qtyNum) || qtyNum < 0) {
        alert('Số lượng không hợp lệ.');
        return;
    }

    try {
        const response = await fetch(`${API_BASE_URL}/admin/inventory/${productId}`, {
            method: 'PATCH',
            headers: {
                'Authorization': 'Bearer ' + (sessionStorage.getItem('access_token') || localStorage.getItem('access_token')),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ quantity_on_hand: qtyNum })
        });
        if(response.ok) {
            loadInventory();
        } else {
            alert('Lỗi cập nhật');
        }
    } catch (e) { console.error(e); }
}

function displayPagination(meta) {
    const pagination = document.getElementById('pagination');
    if (!meta || meta.last_page <= 1) {
        pagination.innerHTML = '';
        return;
    }

    pagination.innerHTML = '';
    for (let page = 1; page <= meta.last_page; page++) {
        const button = document.createElement('button');
        button.type = 'button';
        button.textContent = page;
        button.className = 'rounded-lg border px-3 py-2';
        if (page === meta.current_page) button.className += ' bg-emerald-600 text-white';
        button.onclick = () => loadInventory(page);
        pagination.appendChild(button);
    }
}

loadInventory();
</script>
@endpush

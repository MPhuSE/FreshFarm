@extends('layouts.admin')

@section('title', 'Quản lý Đánh giá')

@section('page-header')
    <p class="text-sm font-medium text-emerald-600">Admin Reviews</p>
    <h1 class="mt-1 text-3xl font-bold">Quản lý Đánh giá</h1>
    <p class="mt-2 text-slate-500">Xem và quản lý các đánh giá sản phẩm của khách hàng.</p>
@endsection

@section('content')
  
    <section class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
        <div class="border-b border-slate-200 px-6 py-4 flex justify-between items-center">
            <h2 class="font-semibold">Danh sách đánh giá</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[1000px] text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                    <tr>
                        <th class="px-6 py-4">Khách hàng</th>
                        <th class="px-6 py-4">Sản phẩm</th>
                        <th class="px-6 py-4">Đánh giá</th>
                        <th class="px-6 py-4">Trạng thái</th>
                        <th class="px-6 py-4">Ngày tạo</th>
                        <th class="px-6 py-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody id="reviewTableBody" class="divide-y divide-slate-100">
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500">Đang tải đánh giá...</td>
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

async function loadReviews(page = 1) {
    try {
        const response = await fetch(`${API_BASE_URL}/admin/reviews?page=${page}`, {
            headers: window.AdminAuth ? window.AdminAuth.getHeaders() : {
                'Authorization': 'Bearer ' + (sessionStorage.getItem('access_token') || localStorage.getItem('access_token')),
                'Accept': 'application/json'
            }
        });

        if (response.status === 401) {
            window.location.href = '/login';
            return;
        }

        const result = await response.json();

        if (response.ok) {
            displayReviews(result.data || []);
            displayPagination(result); // Support standard laravel paginate format
            return;
        }

        displayReviews([]);

    } catch (error) {
        console.error(error);
        displayReviews([]);
    }
}

function displayReviews(reviews) {
    const tbody = document.getElementById('reviewTableBody');

    if (!reviews || reviews.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6" class="px-6 py-12 text-center text-slate-500">Chưa có đánh giá nào.</td></tr>`;
        return;
    }

    tbody.innerHTML = '';

    reviews.forEach(function(review) {
        const row = document.createElement('tr');
        row.className = 'hover:bg-slate-50';

        const customerCell = document.createElement('td');
        customerCell.className = 'px-6 py-4';
        customerCell.innerHTML = `<strong>${review.user?.full_name || review.user?.name || 'Vô danh'}</strong><br><span class="text-xs text-slate-500">${review.user?.email || ''}</span>`;

        const productCell = document.createElement('td');
        productCell.className = 'px-6 py-4';
        productCell.innerHTML = `<a href="/products/${review.product?.slug}" target="_blank" class="text-emerald-600 font-medium hover:underline">${review.product?.name || 'Sản phẩm đã xóa'}</a>`;

        const reviewCell = document.createElement('td');
        reviewCell.className = 'px-6 py-4 max-w-xs truncate';
        reviewCell.innerHTML = `<strong>${review.rating} ⭐</strong><br><span class="text-slate-600 text-sm whitespace-normal" title="${review.comment}">${review.comment || ''}</span>`;

        const statusCell = document.createElement('td');
        statusCell.className = 'px-6 py-4';
        statusCell.innerHTML = getReviewStatus(review.status);

        const dateCell = document.createElement('td');
        dateCell.className = 'px-6 py-4';
        dateCell.textContent = new Date(review.created_at).toLocaleDateString('vi-VN');

        const actionCell = document.createElement('td');
        actionCell.className = 'px-6 py-4 text-right flex flex-col gap-2 justify-end';
        
        if (review.status === 'pending' || review.status === 'hidden') {
            const approveBtn = document.createElement('button');
            approveBtn.className = 'text-emerald-600 font-medium hover:underline text-right';
            approveBtn.textContent = 'Duyệt (Hiển thị)';
            approveBtn.onclick = () => updateStatus(review.id, 'approved');
            actionCell.appendChild(approveBtn);
        }
        if (review.status === 'pending' || review.status === 'approved') {
            const hideBtn = document.createElement('button');
            hideBtn.className = 'text-yellow-600 font-medium hover:underline text-right';
            hideBtn.textContent = 'Ẩn';
            hideBtn.onclick = () => updateStatus(review.id, 'hidden');
            actionCell.appendChild(hideBtn);
        }

        const deleteBtn = document.createElement('button');
        deleteBtn.className = 'text-red-600 font-medium hover:underline text-right';
        deleteBtn.textContent = 'Xóa vĩnh viễn';
        deleteBtn.onclick = () => deleteReview(review.id);
        actionCell.appendChild(deleteBtn);

        row.append(customerCell, productCell, reviewCell, statusCell, dateCell, actionCell);
        tbody.appendChild(row);
    });
}

async function updateStatus(id, status) {
    if(!confirm(`Xác nhận chuyển sang trạng thái: ${status}?`)) return;
    try {
        const response = await fetch(`${API_BASE_URL}/admin/reviews/${id}`, {
            method: 'PATCH',
            headers: window.AdminAuth ? window.AdminAuth.getHeaders(true) : {
                'Authorization': 'Bearer ' + (sessionStorage.getItem('access_token') || localStorage.getItem('access_token')),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status })
        });
        if(response.ok) {
            loadReviews();
        } else {
            alert('Lỗi cập nhật');
        }
    } catch (e) { console.error(e); }
}

async function deleteReview(id) {
    if(!confirm('Xác nhận xóa vĩnh viễn?')) return;
    try {
        const response = await fetch(`${API_BASE_URL}/admin/reviews/${id}`, {
            method: 'DELETE',
            headers: window.AdminAuth ? window.AdminAuth.getHeaders() : {
                'Authorization': 'Bearer ' + (sessionStorage.getItem('access_token') || localStorage.getItem('access_token')),
                'Accept': 'application/json'
            }
        });
        if(response.ok) {
            loadReviews();
        } else {
            alert('Lỗi khi xóa');
        }
    } catch (e) { console.error(e); }
}

function getReviewStatus(status) {
    if (status === 'approved') return '<span class="rounded-full px-3 py-1 text-xs font-medium bg-emerald-100 text-emerald-700">Đã duyệt</span>';
    if (status === 'hidden') return '<span class="rounded-full px-3 py-1 text-xs font-medium bg-red-100 text-red-700">Đang ẩn</span>';
    return '<span class="rounded-full px-3 py-1 text-xs font-medium bg-yellow-100 text-yellow-700">Chờ duyệt</span>';
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
        button.onclick = () => loadReviews(page);
        pagination.appendChild(button);
    }
}

loadReviews();
</script>
@endpush

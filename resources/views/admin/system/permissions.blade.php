@extends('layouts.admin')

@section('title', 'Phân quyền người dùng')

@section('page-header')
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-emerald-600">Admin System</p>
            <h1 class="mt-1 text-3xl font-bold">Phân quyền người dùng</h1>
            <p class="mt-2 text-slate-500">Quản lý vai trò (Admin, Staff, Customer) và quyền hạn tài khoản trong hệ thống.</p>
        </div>
    </div>
@endsection

@section('content')
    <!-- THẺ CẬP NHẬT NHANH VAI TRÒ -->
    <section class="mb-8 rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <h2 class="mb-4 text-lg font-semibold text-slate-800">Cập nhật quyền người dùng</h2>
        <form id="quickRoleForm" onsubmit="handleQuickRoleUpdate(event)" class="grid gap-4 md:grid-cols-3 items-end">
            <div>
                <label for="quickUserSelect" class="block text-sm font-medium text-slate-700 mb-1">Chọn người dùng</label>
                <select id="quickUserSelect" required class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                    <option value="">-- Đang tải người dùng... --</option>
                </select>
            </div>
            <div>
                <label for="quickRoleSelect" class="block text-sm font-medium text-slate-700 mb-1">Gán vai trò mới</label>
                <select id="quickRoleSelect" required class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                    <option value="customer">Customer (Khách hàng)</option>
                    <option value="staff">Staff (Nhân viên)</option>
                    <option value="admin">Admin (Quản trị viên)</option>
                </select>
            </div>
            <div>
                <button type="submit" id="btnQuickSubmit" class="w-full rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 transition">
                    Cập nhật vai trò
                </button>
            </div>
        </form>
    </section>

    <!-- BỘ LỌC & TÌM KIẾM -->
    <div class="mb-6 rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
        <div class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label for="searchQuery" class="block text-sm font-medium text-slate-700 mb-1">Tìm kiếm</label>
                <input
                    type="text"
                    id="searchQuery"
                    placeholder="Tên, email, số điện thoại..."
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"
                >
            </div>

            <div class="w-44">
                <label for="roleFilter" class="block text-sm font-medium text-slate-700 mb-1">Vai trò</label>
                <select id="roleFilter" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                    <option value="">Tất cả vai trò</option>
                    <option value="admin">Admin</option>
                    <option value="staff">Staff</option>
                    <option value="customer">Customer</option>
                </select>
            </div>

            <div class="w-44">
                <label for="statusFilter" class="block text-sm font-medium text-slate-700 mb-1">Trạng thái</label>
                <select id="statusFilter" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                    <option value="">Tất cả</option>
                    <option value="active">Hoạt động (Active)</option>
                    <option value="locked">Bị khóa (Locked)</option>
                </select>
            </div>

            <button
                type="button"
                onclick="loadUsers(1)"
                class="rounded-lg bg-emerald-600 px-5 py-2 text-sm font-medium text-white hover:bg-emerald-700 transition"
            >
                Lọc dữ liệu
            </button>
        </div>
    </div>

    <!-- DANH SÁCH PHÂN QUYỀN -->
    <section class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
        <div class="border-b border-slate-200 px-6 py-4 flex items-center justify-between">
            <h2 class="font-semibold text-slate-800">Danh sách phân quyền tài khoản</h2>
            <span id="userCount" class="text-xs text-slate-500"></span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[850px] text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                    <tr>
                        <th class="px-6 py-4">ID</th>
                        <th class="px-6 py-4">Người dùng</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4">Vai trò</th>
                        <th class="px-6 py-4">Trạng thái</th>
                        <th class="px-6 py-4 text-right">Thay đổi vai trò</th>
                    </tr>
                </thead>
                <tbody id="userTableBody" class="divide-y divide-slate-100">
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500">Đang tải danh sách người dùng...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Phân trang -->
    <div id="pagination" class="mt-6 flex justify-center gap-2"></div>
@endsection

@push('scripts')
<script>
let currentPage = 1;
let loadedUsers = [];

function escapeHtml(str) {
    if (!str) return '';
    return String(str).replace(/[&<>"']/g, function(m) {
        return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[m];
    });
}

function getRoleBadge(role) {
    switch (role) {
        case 'admin':
            return '<span class="inline-flex items-center rounded-full bg-rose-100 px-2.5 py-1 text-xs font-semibold text-rose-700">Admin</span>';
        case 'staff':
            return '<span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-700">Staff</span>';
        default:
            return '<span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">Customer</span>';
    }
}

function getStatusBadge(status) {
    if (status === 'locked') {
        return '<span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">Bị khóa</span>';
    }
    return '<span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">Hoạt động</span>';
}

async function loadUsers(page = 1) {
    currentPage = page;
    const q = document.getElementById('searchQuery').value.trim();
    const role = document.getElementById('roleFilter').value;
    const status = document.getElementById('statusFilter').value;

    const params = new URLSearchParams();
    params.append('page', page);
    if (q) params.append('q', q);
    if (role) params.append('role', role);
    if (status) params.append('status', status);

    const tbody = document.getElementById('userTableBody');
    tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-8 text-center text-slate-500">Đang tải danh sách...</td></tr>';

    try {
        const response = await fetch(`/api/v1/admin/users?${params.toString()}`, {
            headers: window.AdminAuth ? window.AdminAuth.getHeaders() : { 'Accept': 'application/json' }
        });

        if (response.status === 401) {
            window.location.href = '/login';
            return;
        }

        const result = await response.json();
        if (response.ok && result.success) {
            const users = result.data || [];
            loadedUsers = users;
            renderUsers(users);
            updateQuickUserSelect(users);
            renderPagination(result.meta || {});
        } else {
            tbody.innerHTML = `<tr><td colspan="6" class="px-6 py-8 text-center text-rose-500">${escapeHtml(result.message || 'Lỗi khi tải dữ liệu')}</td></tr>`;
        }
    } catch (e) {
        console.error(e);
        tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-8 text-center text-rose-500">Không thể kết nối đến máy chủ.</td></tr>';
    }
}

function updateQuickUserSelect(users) {
    const select = document.getElementById('quickUserSelect');
    if (!select) return;
    if (!users || users.length === 0) {
        select.innerHTML = '<option value="">-- Không có người dùng --</option>';
        return;
    }
    select.innerHTML = '<option value="">-- Chọn người dùng --</option>' + users.map(u => `
        <option value="${u.id}">${escapeHtml(u.name)} (${escapeHtml(u.email)}) - [${u.role || 'customer'}]</option>
    `).join('');
}

function renderUsers(users) {
    const tbody = document.getElementById('userTableBody');
    if (!users || users.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-12 text-center text-slate-500">Không tìm thấy tài khoản nào.</td></tr>';
        return;
    }

    tbody.innerHTML = users.map(user => `
        <tr class="hover:bg-slate-50 transition-colors">
            <td class="px-6 py-4 font-mono text-xs text-slate-500">#${user.id}</td>
            <td class="px-6 py-4 font-medium text-slate-900">${escapeHtml(user.name)}</td>
            <td class="px-6 py-4 text-slate-600">${escapeHtml(user.email)}</td>
            <td class="px-6 py-4">${getRoleBadge(user.role)}</td>
            <td class="px-6 py-4">${getStatusBadge(user.status)}</td>
            <td class="px-6 py-4 text-right">
                <div class="inline-flex items-center gap-2">
                    <select id="role-select-${user.id}" class="rounded border border-slate-300 bg-white px-2.5 py-1 text-xs outline-none focus:border-emerald-500">
                        <option value="customer" ${user.role === 'customer' ? 'selected' : ''}>Customer</option>
                        <option value="staff" ${user.role === 'staff' ? 'selected' : ''}>Staff</option>
                        <option value="admin" ${user.role === 'admin' ? 'selected' : ''}>Admin</option>
                    </select>
                    <button onclick="changeUserRole(${user.id})" class="rounded bg-emerald-600 px-3 py-1 text-xs font-medium text-white hover:bg-emerald-700 transition">
                        Lưu
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

function renderPagination(meta) {
    const pagination = document.getElementById('pagination');
    if (!meta || !meta.last_page || meta.last_page <= 1) {
        pagination.innerHTML = '';
        return;
    }

    let html = '';
    for (let p = 1; p <= meta.last_page; p++) {
        const isActive = p === meta.current_page;
        html += `
            <button
                type="button"
                onclick="loadUsers(${p})"
                class="rounded-lg border px-3 py-1.5 text-sm font-medium transition ${
                    isActive ? 'bg-emerald-600 text-white border-emerald-600' : 'border-slate-300 text-slate-700 hover:bg-slate-50'
                }"
            >${p}</button>
        `;
    }
    pagination.innerHTML = html;
}

async function changeUserRole(userId) {
    const select = document.getElementById(`role-select-${userId}`);
    if (!select) return;
    const newRole = select.value;

    try {
        const response = await fetch(`/api/v1/admin/users/${userId}/role`, {
            method: 'PATCH',
            headers: window.AdminAuth ? window.AdminAuth.getHeaders(true) : { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ role: newRole })
        });

        const result = await response.json();
        if (response.ok && result.success) {
            alert('Cập nhật vai trò thành công!');
            loadUsers(currentPage);
        } else {
            alert(result.message || 'Không thể cập nhật vai trò.');
        }
    } catch (e) {
        console.error(e);
        alert('Lỗi kết nối máy chủ.');
    }
}

async function handleQuickRoleUpdate(e) {
    e.preventDefault();
    const userId = document.getElementById('quickUserSelect').value;
    const newRole = document.getElementById('quickRoleSelect').value;

    if (!userId) {
        alert('Vui lòng chọn người dùng.');
        return;
    }

    const btn = document.getElementById('btnQuickSubmit');
    btn.disabled = true;
    btn.textContent = 'Đang lưu...';

    try {
        const response = await fetch(`/api/v1/admin/users/${userId}/role`, {
            method: 'PATCH',
            headers: window.AdminAuth ? window.AdminAuth.getHeaders(true) : { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ role: newRole })
        });

        const result = await response.json();
        if (response.ok && result.success) {
            alert('Cập nhật vai trò thành công!');
            loadUsers(currentPage);
        } else {
            alert(result.message || 'Không thể cập nhật vai trò.');
        }
    } catch (err) {
        console.error(err);
        alert('Lỗi kết nối máy chủ.');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Cập nhật vai trò';
    }
}

// Khởi chạy khi tải trang
loadUsers(1);
</script>
@endpush

@extends('layouts.admin')

@section('title', 'Nhật ký Hoạt động')

@section('page-header')
    <p class="text-sm font-medium text-emerald-600">Admin System</p>
    <h1 class="mt-1 text-3xl font-bold">Nhật ký Hoạt động (Audit Logs)</h1>
    <p class="mt-2 text-slate-500">Xem lại lịch sử hoạt động và các thay đổi của người quản trị trong hệ thống.</p>
@endsection

@section('content')
  
    <section class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
        <div class="border-b border-slate-200 px-6 py-4 flex justify-between items-center">
            <h2 class="font-semibold">Danh sách Logs</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[1000px] text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                    <tr>
                        <th class="px-6 py-4">ID</th>
                        <th class="px-6 py-4">Hành động</th>
                        <th class="px-6 py-4">Tài khoản</th>
                        <th class="px-6 py-4">Đối tượng</th>
                        <th class="px-6 py-4">ID Đối tượng</th>
                        <th class="px-6 py-4">Thời gian</th>
                    </tr>
                </thead>
                <tbody id="logsTableBody" class="divide-y divide-slate-100">
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500">Đang tải nhật ký...</td>
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

async function loadLogs(page = 1) {
    try {
        const response = await fetch(`${API_BASE_URL}/admin/audit-logs?page=${page}`, {
            headers: {
                'Authorization': 'Bearer ' + (sessionStorage.getItem('access_token') || localStorage.getItem('access_token')),
                'Accept': 'application/json'
            }
        });

        const result = await response.json();

        if (response.ok) {
            displayLogs(result.data || []);
            displayPagination(result);
            return;
        }

        alert('Không thể tải nhật ký.');

    } catch (error) {
        console.error(error);
        alert('Không thể kết nối đến API.');
    }
}

function displayLogs(logs) {
    const tbody = document.getElementById('logsTableBody');

    if (!logs || logs.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6" class="px-6 py-12 text-center text-slate-500">Chưa có nhật ký hoạt động nào.</td></tr>`;
        return;
    }

    tbody.innerHTML = '';

    logs.forEach(function(log) {
        const row = document.createElement('tr');
        row.className = 'hover:bg-slate-50';

        const idCell = document.createElement('td');
        idCell.className = 'px-6 py-4 text-slate-500';
        idCell.textContent = log.id;

        const actionCell = document.createElement('td');
        actionCell.className = 'px-6 py-4 font-medium';
        actionCell.textContent = log.action;

        const userCell = document.createElement('td');
        userCell.className = 'px-6 py-4';
        userCell.innerHTML = log.user ? `<strong>${log.user.name || ''}</strong><br><span class="text-xs text-slate-500">${log.user.email}</span>` : 'Hệ thống';

        const modelCell = document.createElement('td');
        modelCell.className = 'px-6 py-4 text-slate-600';
        modelCell.textContent = log.model_type || '-';

        const modelIdCell = document.createElement('td');
        modelIdCell.className = 'px-6 py-4 font-mono text-slate-500';
        modelIdCell.textContent = log.model_id || '-';

        const dateCell = document.createElement('td');
        dateCell.className = 'px-6 py-4 whitespace-nowrap text-slate-500 text-sm';
        dateCell.textContent = new Date(log.created_at).toLocaleString('vi-VN');

        row.append(idCell, actionCell, userCell, modelCell, modelIdCell, dateCell);
        tbody.appendChild(row);
    });
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
        button.onclick = () => loadLogs(page);
        pagination.appendChild(button);
    }
}

loadLogs();
</script>
@endpush

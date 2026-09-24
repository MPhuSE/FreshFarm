@extends('layouts.admin')

@section('title', 'Quản lý Trang tĩnh')

@section('page-header')
    <div class="flex justify-between items-center">
        <div>
            <p class="text-sm font-medium text-emerald-600">Admin Pages</p>
            <h1 class="mt-1 text-3xl font-bold">Quản lý Trang tĩnh</h1>
            <p class="mt-2 text-slate-500">Quản lý các trang thông tin tĩnh (Giới thiệu, Liên hệ, Chính sách...)</p>
        </div>
        <button onclick="openPageModal()" class="rounded-lg bg-emerald-600 px-4 py-2 font-medium text-white hover:bg-emerald-700">
            Thêm trang tĩnh
        </button>
    </div>
@endsection

@section('content')
  
    <section class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
        <div class="border-b border-slate-200 px-6 py-4">
            <h2 class="font-semibold">Danh sách trang tĩnh</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[800px] text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                    <tr>
                        <th class="px-6 py-4">ID</th>
                        <th class="px-6 py-4">Tiêu đề</th>
                        <th class="px-6 py-4">Đường dẫn (Slug)</th>
                        <th class="px-6 py-4">Trạng thái</th>
                        <th class="px-6 py-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody id="pagesTableBody" class="divide-y divide-slate-100">
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-500">Đang tải dữ liệu...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <div id="pagination" class="mt-6 flex justify-center gap-2"></div>

    <!-- Modal -->
    <div id="pageModal" class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm overflow-y-auto">
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="w-full max-w-2xl rounded-2xl bg-white shadow-xl relative">
                <div class="flex items-center justify-between border-b px-6 py-4">
                    <h3 class="text-lg font-semibold" id="modalTitle">Thêm trang tĩnh</h3>
                    <button onclick="closePageModal()" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>
                
                <form id="pageForm" onsubmit="savePage(event)" class="p-6 space-y-4">
                    <input type="hidden" id="pageId">
                    
                    <div>
                        <label class="block text-sm font-medium mb-1">Tiêu đề trang</label>
                        <input type="text" id="pageTitle" required class="w-full rounded-lg border border-slate-300 px-3 py-2">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium mb-1">Nội dung HTML</label>
                        <textarea id="pageContent" rows="10" class="w-full rounded-lg border border-slate-300 px-3 py-2 font-mono text-sm"></textarea>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="pagePublished" checked class="rounded border-slate-300 text-emerald-600">
                        <label for="pagePublished" class="text-sm font-medium">Xuất bản</label>
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" onclick="closePageModal()" class="rounded-lg px-4 py-2 text-slate-600 hover:bg-slate-100 font-medium">Hủy</button>
                        <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 font-medium text-white hover:bg-emerald-700">Lưu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
const API_BASE_URL = '/api/v1';

async function loadPages(page = 1) {
    try {
        const response = await fetch(`${API_BASE_URL}/admin/pages?page=${page}`, {
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
            displayPages(result.data || []);
            displayPagination(result);
        }
    } catch (error) { console.error(error); }
}

function displayPages(pages) {
    const tbody = document.getElementById('pagesTableBody');
    if (!pages || pages.length === 0) {
        tbody.innerHTML = `<tr><td colspan="5" class="px-6 py-12 text-center text-slate-500">Chưa có trang nào.</td></tr>`;
        return;
    }
    tbody.innerHTML = '';
    pages.forEach(p => {
        const row = document.createElement('tr');
        row.className = 'hover:bg-slate-50';
        
        row.innerHTML = `
            <td class="px-6 py-4">${p.id}</td>
            <td class="px-6 py-4 font-medium">${p.title}</td>
            <td class="px-6 py-4 text-slate-500">/${p.slug}</td>
            <td class="px-6 py-4">
                ${p.is_published ? '<span class="rounded-full bg-emerald-100 text-emerald-700 px-2 py-1 text-xs">Đã xuất bản</span>' : '<span class="rounded-full bg-slate-100 text-slate-700 px-2 py-1 text-xs">Bản nháp</span>'}
            </td>
            <td class="px-6 py-4 text-right">
                <a href="/p/${p.slug}" target="_blank" class="text-blue-600 hover:underline mr-3">Xem</a>
                <button onclick='editPage(${JSON.stringify(p).replace(/'/g, "&#39;")})' class="text-emerald-600 hover:underline mr-3">Sửa</button>
                <button onclick="deletePage(${p.id})" class="text-red-600 hover:underline">Xóa</button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

function openPageModal() {
    document.getElementById('pageForm').reset();
    document.getElementById('pageId').value = '';
    document.getElementById('modalTitle').textContent = 'Thêm trang tĩnh';
    document.getElementById('pageModal').classList.remove('hidden');
}

function closePageModal() {
    document.getElementById('pageModal').classList.add('hidden');
}

function editPage(page) {
    document.getElementById('pageId').value = page.id;
    document.getElementById('pageTitle').value = page.title;
    document.getElementById('pageContent').value = page.content;
    document.getElementById('pagePublished').checked = page.is_published;
    document.getElementById('modalTitle').textContent = 'Sửa trang tĩnh';
    document.getElementById('pageModal').classList.remove('hidden');
}

async function savePage(e) {
    e.preventDefault();
    const id = document.getElementById('pageId').value;
    const data = {
        title: document.getElementById('pageTitle').value,
        content: document.getElementById('pageContent').value,
        is_published: document.getElementById('pagePublished').checked
    };

    const url = id ? `${API_BASE_URL}/admin/pages/${id}` : `${API_BASE_URL}/admin/pages`;
    const method = id ? 'PUT' : 'POST';

    try {
        const response = await fetch(url, {
            method,
            headers: window.AdminAuth ? window.AdminAuth.getHeaders(true) : {
                'Authorization': 'Bearer ' + (sessionStorage.getItem('access_token') || localStorage.getItem('access_token')),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });
        if(response.ok) {
            closePageModal();
            loadPages();
        } else alert('Lỗi lưu trang');
    } catch(err) { console.error(err); }
}

async function deletePage(id) {
    if(!confirm('Xác nhận xóa?')) return;
    try {
        const response = await fetch(`${API_BASE_URL}/admin/pages/${id}`, {
            method: 'DELETE',
            headers: window.AdminAuth ? window.AdminAuth.getHeaders() : { 'Authorization': 'Bearer ' + (sessionStorage.getItem('access_token') || localStorage.getItem('access_token')), 'Accept': 'application/json' }
        });
        if(response.ok) loadPages();
    } catch(err) { console.error(err); }
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
        button.onclick = () => loadPages(page);
        pagination.appendChild(button);
    }
}

loadPages();
</script>
@endpush

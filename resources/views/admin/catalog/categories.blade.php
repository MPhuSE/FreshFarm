@extends('layouts.admin')

@section('title', 'Quản lý danh mục')

@section('page-header')
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold">
                Quản lý danh mục
            </h1>
            <p class="mt-1 text-sm font-medium text-emerald-600">
                Admin Catalog
            </p>
        </div>
        <button onclick="openModal()" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
            + Thêm danh mục
        </button>
    </div>
@endsection

@section('content')
    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 text-slate-800 border-b border-slate-200">
                <tr>
                    <th class="px-5 py-4 font-semibold">ID</th>
                    <th class="px-5 py-4 font-semibold">Tên danh mục</th>
                    <th class="px-5 py-4 font-semibold">Hình ảnh</th>
                    <th class="px-5 py-4 font-semibold">Đường dẫn (Slug)</th>
                    <th class="px-5 py-4 font-semibold">Trạng thái</th>
                    <th class="px-5 py-4 font-semibold">Thao tác</th>
                </tr>
            </thead>
            <tbody id="categoryTableBody" class="divide-y divide-slate-100">
                <tr>
                    <td colspan="5" class="px-5 py-8 text-center text-slate-500">
                        Đang tải dữ liệu...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Modal Form -->
    <div id="categoryModal" class="fixed inset-0 z-50 hidden bg-slate-900/50 flex items-center justify-center p-4">
        <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
            <h2 id="modalTitle" class="mb-4 text-xl font-bold">Thêm danh mục</h2>
            <form id="categoryForm">
                <input type="hidden" id="categoryId">
                
                <div class="mb-4">
                    <label class="mb-1 block text-sm font-medium text-slate-700">Tên danh mục *</label>
                    <input type="text" id="categoryName" required class="w-full rounded-lg border border-slate-300 px-3 py-2 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                </div>
                
                <div class="mb-4">
                    <label class="mb-1 block text-sm font-medium text-slate-700">Mô tả</label>
                    <textarea id="categoryDescription" name="description" class="w-full rounded-lg border border-slate-300 px-3 py-2 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"></textarea>
                </div>
                
                <div class="mb-4">
                    <label class="mb-1 block text-sm font-medium text-slate-700">Hình ảnh</label>
                    <input type="file" id="categoryImage" name="image" accept="image/*" class="w-full rounded-lg border border-slate-300 px-3 py-2 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                    <img id="categoryImagePreview" src="" class="hidden mt-2 max-h-32 rounded-lg object-contain" />
                </div>
                
                <div class="mb-4">
                    <label class="mb-1 block text-sm font-medium text-slate-700">Sắp xếp (thứ tự)</label>
                    <input type="number" id="categorySortOrder" value="0" min="0" class="w-full rounded-lg border border-slate-300 px-3 py-2 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                </div>
                
                <div class="mb-6">
                    <label class="mb-1 block text-sm font-medium text-slate-700">Trạng thái</label>
                    <select id="categoryStatus" class="w-full rounded-lg border border-slate-300 px-3 py-2 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
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
    const API_URL = '/api/v1/admin/categories';

    async function loadCategories() {
        try {
            const res = await fetch(API_URL, {
                headers: window.AdminApi.headers()
            });
            const result = await res.json();
            
            if (!res.ok) throw new Error(result.message || 'Lỗi tải dữ liệu');
            
            renderCategories(result.data);
        } catch (error) {
            console.error(error);
            document.getElementById('categoryTableBody').innerHTML = `
                <tr><td colspan="5" class="px-5 py-8 text-center text-red-500">Lỗi tải dữ liệu</td></tr>
            `;
        }
    }

    function renderCategories(categories) {
        const tbody = document.getElementById('categoryTableBody');
        if (!categories.length) {
            tbody.innerHTML = `<tr><td colspan="5" class="px-5 py-8 text-center text-slate-500">Chưa có danh mục nào.</td></tr>`;
            return;
        }

        tbody.innerHTML = categories.map(cat => `
            <tr class="hover:bg-slate-50">
                <td class="px-5 py-3">${cat.id}</td>
                <td class="px-5 py-3 font-medium">${escapeHtml(cat.name)}</td>
                <td class="px-5 py-3">
                    ${cat.image_path ? `<img src="${cat.image_path}" class="h-10 w-10 rounded-lg object-cover" />` : '<span class="text-slate-400">Không có</span>'}
                </td>
                <td class="px-5 py-3 text-slate-500">${escapeHtml(cat.slug || '')}</td>
                <td class="px-5 py-3">
                    <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold ${
                        cat.status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700'
                    }">
                        ${cat.status === 'active' ? 'Hoạt động' : 'Tạm ẩn'}
                    </span>
                </td>
                <td class="px-5 py-3">
                    <div class="flex gap-2">
                        <button onclick="editCategory(${cat.id}, '${escapeString(cat.name)}', '${escapeString(cat.description || '')}', ${cat.sort_order}, '${cat.status}', '${cat.image_path || ''}')" class="text-blue-600 hover:underline">Sửa</button>
                        <button onclick="deleteCategory(${cat.id})" class="text-red-600 hover:underline">Xóa</button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    function openModal() {
        document.getElementById('categoryId').value = '';
        document.getElementById('categoryName').value = '';
        document.getElementById('categoryDescription').value = '';
        document.getElementById('categorySortOrder').value = '0';
        document.getElementById('categoryStatus').value = 'active';
        document.getElementById('categoryImage').value = '';
        document.getElementById('categoryImagePreview').classList.add('hidden');
        document.getElementById('modalTitle').textContent = 'Thêm danh mục';
        document.getElementById('categoryModal').classList.remove('hidden');
    }

    function editCategory(id, name, description, sortOrder, status, imagePath) {
        document.getElementById('categoryId').value = id;
        document.getElementById('categoryName').value = name;
        document.getElementById('categoryDescription').value = description;
        document.getElementById('categorySortOrder').value = sortOrder || 0;
        document.getElementById('categoryStatus').value = status;
        document.getElementById('categoryImage').value = '';
        
        const preview = document.getElementById('categoryImagePreview');
        if (imagePath) {
            preview.src = imagePath;
            preview.classList.remove('hidden');
        } else {
            preview.classList.add('hidden');
        }

        document.getElementById('modalTitle').textContent = 'Sửa danh mục';
        document.getElementById('categoryModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('categoryModal').classList.add('hidden');
    }

    document.getElementById('categoryForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const id = document.getElementById('categoryId').value;
        
        const formData = new FormData();
        formData.append('name', document.getElementById('categoryName').value);
        formData.append('description', document.getElementById('categoryDescription').value);
        formData.append('sort_order', document.getElementById('categorySortOrder').value);
        formData.append('status', document.getElementById('categoryStatus').value);
        
        if (id) {
            // Laravel needs _method for PUT with FormData
            formData.append('_method', 'PUT');
        }

        const imageFile = document.getElementById('categoryImage').files[0];
        if (imageFile) {
            formData.append('image', imageFile);
        }

        // Must use POST when sending FormData with file even for updates in Laravel (using _method)
        const method = 'POST';
        const url = id ? `${API_URL}/${id}` : API_URL;
        
        const headers = window.AdminApi.headers();
        // Remove Content-Type to let browser set it with boundary for FormData
        delete headers['Content-Type'];

        try {
            const res = await fetch(url, {
                method,
                headers,
                body: formData
            });
            const result = await res.json();
            
            if (!res.ok) {
                alert(result.message || 'Có lỗi xảy ra');
                return;
            }
            
            closeModal();
            loadCategories();
        } catch (error) {
            alert('Lỗi kết nối API');
        }
    });

    async function deleteCategory(id) {
        if (!confirm('Bạn có chắc muốn xóa danh mục này?')) return;
        
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
            
            loadCategories();
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
    
    function escapeString(str) {
        return (str || '').toString().replace(/'/g, "\\'").replace(/"/g, '\\"');
    }

    document.addEventListener('DOMContentLoaded', loadCategories);
</script>
@endpush
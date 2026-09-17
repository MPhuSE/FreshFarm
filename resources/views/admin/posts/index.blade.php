@extends('layouts.admin')

@section('title', 'Quản lý Bài viết')

@section('page-header')
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold">Bài viết</h1>
            <p class="mt-1 text-sm font-medium text-emerald-600">Quản lý tin tức và bài đăng</p>
        </div>
        <a href="{{ route('admin.posts.create') }}" class="flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 font-medium text-white transition-colors hover:bg-emerald-700">
            <i data-feather="plus" class="w-5 h-5"></i>
            Thêm bài viết mới
        </a>
    </div>
@endsection

@section('content')
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-800">
                    <tr>
                        <th class="border-b border-slate-200 px-6 py-4 font-semibold">ID</th>
                        <th class="border-b border-slate-200 px-6 py-4 font-semibold">Hình ảnh</th>
                        <th class="border-b border-slate-200 px-6 py-4 font-semibold">Tiêu đề</th>
                        <th class="border-b border-slate-200 px-6 py-4 font-semibold">Trạng thái</th>
                        <th class="border-b border-slate-200 px-6 py-4 font-semibold">Ngày đăng</th>
                        <th class="border-b border-slate-200 px-6 py-4 font-semibold text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody id="postsList" class="divide-y divide-slate-100">
                    <tr><td colspan="6" class="px-6 py-4 text-center">Đang tải...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    async function fetchPosts() {
        try {
            const res = await fetch('/api/v1/admin/posts', {
                headers: window.AdminApi.headers()
            });
            const result = await res.json();
            
            const tbody = document.getElementById('postsList');
            if (res.ok && result.data && result.data.length > 0) {
                tbody.innerHTML = result.data.map(post => `
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 font-medium text-slate-900">#${post.id}</td>
                        <td class="px-6 py-4">
                            ${post.thumbnail_path ? `<img src="${post.thumbnail_path}" class="w-16 h-12 object-cover rounded shadow-sm">` : '<div class="w-16 h-12 bg-slate-100 rounded flex items-center justify-center text-xs text-slate-400">No Img</div>'}
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-slate-900 line-clamp-1">${post.title}</div>
                            <div class="text-xs text-slate-500 line-clamp-1">${post.slug}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold ${
                                post.is_published ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700'
                            }">
                                ${post.is_published ? 'Đã đăng' : 'Bản nháp'}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-slate-500">${new Date(post.created_at).toLocaleDateString('vi-VN')}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="/admin/posts/${post.id}/edit" class="p-2 text-slate-400 hover:text-emerald-600 transition-colors rounded-lg hover:bg-emerald-50" title="Sửa">
                                    <i data-feather="edit-2" class="w-4 h-4"></i>
                                </a>
                                <button onclick="deletePost(${post.id})" class="p-2 text-slate-400 hover:text-rose-600 transition-colors rounded-lg hover:bg-rose-50" title="Xóa">
                                    <i data-feather="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `).join('');
                if (window.feather) feather.replace();
            } else {
                tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-8 text-center text-slate-500">Chưa có bài viết nào</td></tr>';
            }
        } catch (e) {
            document.getElementById('postsList').innerHTML = '<tr><td colspan="6" class="px-6 py-4 text-center text-red-500">Lỗi khi tải danh sách bài viết</td></tr>';
        }
    }

    async function deletePost(id) {
        if (!confirm('Bạn có chắc chắn muốn xóa bài viết này không?')) return;
        try {
            const res = await fetch(`/api/v1/admin/posts/${id}`, {
                method: 'DELETE',
                headers: window.AdminApi.headers()
            });
            if (res.ok) {
                alert('Đã xóa thành công');
                fetchPosts();
            } else {
                const data = await res.json();
                alert(data.message || 'Xóa thất bại');
            }
        } catch (e) {
            alert('Lỗi kết nối');
        }
    }

    document.addEventListener('DOMContentLoaded', fetchPosts);
</script>
@endpush

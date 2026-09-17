@extends('layouts.admin')

@section('title', isset($post) ? 'Sửa bài viết' : 'Thêm mới bài viết')

@push('styles')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
@endpush

@section('page-header')
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold">
                {{ isset($post) ? 'Sửa bài viết' : 'Thêm mới bài viết' }}
            </h1>
            <p class="mt-1 text-sm font-medium text-emerald-600">
                Admin Posts
            </p>
        </div>
        <a href="{{ route('admin.posts.index') }}" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
            &larr; Quay lại
        </a>
    </div>
@endsection

@section('content')
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm max-w-4xl">
        <form id="postForm" class="space-y-6">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <!-- Tiêu đề -->
                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-slate-700">Tiêu đề bài viết <span class="text-red-500">*</span></label>
                    <input type="text" id="title" required class="w-full rounded-lg border border-slate-300 px-3 py-2 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                </div>

                <!-- Slug & Trạng thái -->
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Đường dẫn tĩnh (Slug)</label>
                    <input type="text" id="slug" class="w-full rounded-lg border border-slate-300 px-3 py-2 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" placeholder="Để trống sẽ tự động tạo từ tiêu đề">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Trạng thái <span class="text-red-500">*</span></label>
                    <select id="status" required class="w-full rounded-lg border border-slate-300 px-3 py-2 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                        <option value="draft">Bản nháp</option>
                        <option value="published">Đã đăng</option>
                    </select>
                </div>
                
                <!-- Tóm tắt -->
                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-slate-700">Đoạn trích (Tóm tắt)</label>
                    <textarea id="excerpt" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"></textarea>
                </div>

                <!-- Nội dung chính (Editor) -->
                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-slate-700">Nội dung chi tiết</label>
                    <div id="content_editor" style="height: 350px;" class="w-full rounded-b-lg border border-slate-300"></div>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3 pt-6 border-t border-slate-200">
                <a href="{{ route('admin.posts.index') }}" class="rounded-lg border border-slate-300 px-6 py-2.5 font-medium text-slate-700 hover:bg-slate-50">
                    Hủy
                </a>
                <button type="submit" class="rounded-lg bg-emerald-600 px-6 py-2.5 font-medium text-white hover:bg-emerald-700">
                    Lưu bài viết
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script>
    const postId = "{{ $post->id ?? '' }}";
    
    var quill = new Quill('#content_editor', {
        theme: 'snow',
        placeholder: 'Soạn thảo nội dung bài viết...',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'color': [] }, { 'background': [] }],
                ['link', 'image', 'video'],
                ['clean']
            ]
        }
    });

    async function loadPostData() {
        if (!postId) return;
        
        try {
            // Note: Currently no GET /api/v1/admin/posts/{id} exists.
            // We pass $post from the view. Let's hydrate it via Blade:
            const p = @json($post ?? null);
            if (p) {
                document.getElementById('title').value = p.title || '';
                document.getElementById('slug').value = p.slug || '';
                document.getElementById('status').value = p.status || 'draft';
                document.getElementById('excerpt').value = p.excerpt || '';
                
                if (p.content) {
                    quill.root.innerHTML = p.content;
                }
            }
        } catch (e) { console.error('Lỗi lấy bài viết', e); }
    }

    document.getElementById('postForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const payload = {
            title: document.getElementById('title').value,
            status: document.getElementById('status').value,
            excerpt: document.getElementById('excerpt').value,
        };
        
        const slug = document.getElementById('slug').value;
        if (slug) payload.slug = slug;
        
        const contentHtml = quill.root.innerHTML;
        if (contentHtml && contentHtml !== '<p><br></p>') {
            payload.content = contentHtml;
        }

        try {
            const url = postId ? `/api/v1/admin/posts/${postId}` : '/api/v1/admin/posts';
            const method = postId ? 'PATCH' : 'POST';
            
            const res = await fetch(url, {
                method: method,
                headers: window.AdminApi.headers(true),
                body: JSON.stringify(payload)
            });
            
            const result = await res.json();
            if (res.ok) {
                alert(result.message || 'Lưu thành công');
                window.location.href = '/admin/posts';
            } else {
                alert(result.message || 'Lưu thất bại');
            }
        } catch (error) {
            alert('Lỗi kết nối');
        }
    });

    document.addEventListener('DOMContentLoaded', async () => {
        if (postId) {
            await loadPostData();
        }
    });
</script>
@endpush

@extends('layouts.admin')

@section('title', isset($product) ? 'Sửa sản phẩm' : 'Thêm mới sản phẩm')

@push('styles')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
@endpush

@section('page-header')
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 id="pageTitle" class="text-3xl font-bold">
                {{ isset($product) ? 'Sửa sản phẩm' : 'Thêm mới sản phẩm' }}
            </h1>
            <p class="mt-1 text-sm font-medium text-emerald-600">
                Admin Catalog
            </p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
            &larr; Quay lại
        </a>
    </div>
@endsection

@section('content')
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm max-w-4xl">
        <form id="productForm" class="space-y-6">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <!-- Tên sản phẩm -->
                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-slate-700">Tên sản phẩm <span class="text-red-500">*</span></label>
                    <input type="text" id="name" required class="w-full rounded-lg border border-slate-300 px-3 py-2 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                </div>

                <!-- SKU & Danh mục -->
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">SKU <span class="text-red-500">*</span></label>
                    <input type="text" id="sku" required class="w-full rounded-lg border border-slate-300 px-3 py-2 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Danh mục <span class="text-red-500">*</span></label>
                    <select id="category_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                        <option value="">Chọn danh mục...</option>
                    </select>
                </div>

                <!-- Giá -->
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Giá bán (VND) <span class="text-red-500">*</span></label>
                    <input type="number" id="price" required min="0" class="w-full rounded-lg border border-slate-300 px-3 py-2 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Giá gốc / So sánh (VND)</label>
                    <input type="number" id="compare_at_price" min="0" class="w-full rounded-lg border border-slate-300 px-3 py-2 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                </div>

                <!-- Đơn vị & Tồn kho -->
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Đơn vị tính <span class="text-red-500">*</span> (vd: kg, hộp)</label>
                    <input type="text" id="unit" required class="w-full rounded-lg border border-slate-300 px-3 py-2 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Số lượng tồn kho ban đầu</label>
                    <input type="number" id="quantity_on_hand" min="0" class="w-full rounded-lg border border-slate-300 px-3 py-2 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                    <p class="mt-1 text-xs text-slate-500">Chỉ áp dụng khi tạo mới, sẽ bỏ qua nếu đang sửa.</p>
                </div>

                <!-- Xuất xứ & Trạng thái -->
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Xuất xứ</label>
                    <input type="text" id="origin" class="w-full rounded-lg border border-slate-300 px-3 py-2 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Trạng thái</label>
                    <select id="status" class="w-full rounded-lg border border-slate-300 px-3 py-2 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                        <option value="active">Đang bán</option>
                        <option value="draft">Bản nháp</option>
                        <option value="inactive">Ngừng bán</option>
                    </select>
                </div>
                
                <!-- Tóm tắt & Mô tả -->
                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-slate-700">Mô tả ngắn</label>
                    <textarea id="short_description" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"></textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-slate-700">Chi tiết sản phẩm</label>
                    <div id="description_html_editor" style="height: 250px;" class="w-full rounded-b-lg border border-slate-300"></div>
                    <input type="hidden" id="description_html">
                </div>
                
                <!-- Hình ảnh sản phẩm -->
                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-slate-700">Hình ảnh sản phẩm (chọn nhiều ảnh)</label>
                    <input type="file" id="images" multiple accept="image/*" class="w-full rounded-lg border border-slate-300 px-3 py-2 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                    <p class="mt-1 text-xs text-slate-500">Bạn có thể chọn nhiều ảnh cùng lúc.</p>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3 pt-6 border-t border-slate-200">
                <a href="{{ route('admin.products.index') }}" class="rounded-lg border border-slate-300 px-6 py-2.5 font-medium text-slate-700 hover:bg-slate-50">
                    Hủy
                </a>
                <button type="submit" class="rounded-lg bg-emerald-600 px-6 py-2.5 font-medium text-white hover:bg-emerald-700">
                    Lưu sản phẩm
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script>
    const productId = "{{ $product->id ?? '' }}";
    
    var quill = new Quill('#description_html_editor', {
        theme: 'snow',
        placeholder: 'Nhập chi tiết sản phẩm...',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['link', 'image'],
                ['clean']
            ]
        }
    });
    
    async function loadCategories() {
        try {
            const res = await fetch('/api/v1/admin/categories', {
                headers: window.AdminApi.headers()
            });
            const result = await res.json();
            if (res.ok) {
                const select = document.getElementById('category_id');
                result.data.forEach(cat => {
                    const option = document.createElement('option');
                    option.value = cat.id;
                    option.textContent = cat.name;
                    select.appendChild(option);
                });
            }
        } catch (e) { console.error('Lỗi lấy danh mục', e); }
    }

    async function loadProductData() {
        if (!productId) return;
        
        try {
            const res = await fetch(`/api/v1/admin/products/${productId}`, {
                headers: window.AdminApi.headers()
            });
            const result = await res.json();
            if (res.ok && result.data) {
                const p = result.data;
                document.getElementById('name').value = p.name || '';
                document.getElementById('sku').value = p.sku || '';
                document.getElementById('category_id').value = p.category?.id || '';
                document.getElementById('price').value = p.price || '';
                document.getElementById('compare_at_price').value = p.compare_at_price || '';
                document.getElementById('unit').value = p.unit || '';
                document.getElementById('origin').value = p.origin || '';
                document.getElementById('status').value = p.status || 'active';
                document.getElementById('short_description').value = p.short_description || '';
                
                if (p.description_html) {
                    quill.root.innerHTML = p.description_html;
                    document.getElementById('description_html').value = p.description_html;
                }
                
                // Không điền quantity_on_hand vì api chi tiết có thể k trả về đúng định dạng hoặc ta không update kho ở form này
            }
        } catch (e) { console.error('Lỗi lấy SP', e); }
    }

    document.getElementById('productForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const payload = {
            name: document.getElementById('name').value,
            sku: document.getElementById('sku').value,
            category_id: parseInt(document.getElementById('category_id').value),
            price: parseFloat(document.getElementById('price').value),
            unit: document.getElementById('unit').value,
            status: document.getElementById('status').value
        };
        
        const compareAt = document.getElementById('compare_at_price').value;
        if (compareAt) payload.compare_at_price = parseFloat(compareAt);
        
        const origin = document.getElementById('origin').value;
        if (origin) payload.origin = origin;
        
        const shortDesc = document.getElementById('short_description').value;
        if (shortDesc) payload.short_description = shortDesc;
        
        const descHtml = quill.root.innerHTML;
        if (descHtml && descHtml !== '<p><br></p>') {
            payload.description_html = descHtml;
        }
        
        if (!productId) {
            const qty = document.getElementById('quantity_on_hand').value;
            if (qty) payload.quantity_on_hand = parseFloat(qty);
        }

        try {
            const url = productId ? `/api/v1/admin/products/${productId}` : '/api/v1/admin/products';
            const method = productId ? 'PATCH' : 'POST';
            
            const res = await fetch(url, {
                method: method,
                headers: window.AdminApi.headers(true),
                body: JSON.stringify(payload)
            });
            
            const result = await res.json();
            if (res.ok) {
                // Xử lý upload ảnh nếu có
                const imageInput = document.getElementById('images');
                if (imageInput && imageInput.files.length > 0) {
                    const finalProductId = productId ? productId : result.data.id;
                    const formData = new FormData();
                    Array.from(imageInput.files).forEach(file => {
                        formData.append('images[]', file);
                    });
                    
                    try {
                        const imgHeaders = window.AdminApi.headers();
                        delete imgHeaders['Content-Type']; // Xóa Content-Type để browser tự sinh boundary
                        
                        await fetch(`/api/v1/admin/products/${finalProductId}/images`, {
                            method: 'POST',
                            headers: imgHeaders,
                            body: formData
                        });
                    } catch (imgError) {
                        console.error('Lỗi upload ảnh:', imgError);
                        alert('Đã lưu sản phẩm nhưng có lỗi khi tải ảnh lên.');
                    }
                }

                alert(result.message || 'Lưu thành công');
                window.location.href = '/admin/products';
            } else {
                alert(result.message || 'Lưu thất bại');
            }
        } catch (error) {
            alert('Lỗi kết nối');
        }
    });

    document.addEventListener('DOMContentLoaded', async () => {
        await loadCategories();
        if (productId) {
            await loadProductData();
        }
    });
</script>
@endpush
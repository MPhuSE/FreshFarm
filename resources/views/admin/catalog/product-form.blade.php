<!DOCTYPE html>
@php
    $editing = isset($product);
@endphp
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $editing ? 'Chỉnh sửa sản phẩm' : 'Thêm sản phẩm' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 text-slate-800">
    <main class="mx-auto max-w-5xl px-6 py-10">
        <div class="mb-8 flex items-start justify-between gap-4">
            <div>
                <p class="text-sm font-medium text-emerald-600">Admin Catalog</p>
                <h1 class="mt-1 text-3xl font-bold">
                    {{ $editing ? 'Chỉnh sửa sản phẩm' : 'Thêm sản phẩm' }}
                </h1>
                <p class="mt-2 text-slate-500">
                    {{ $editing ? 'Cập nhật thông tin sản phẩm trong hệ thống.' : 'Tạo sản phẩm mới trong hệ thống.' }}
                </p>
            </div>

            <a href="{{ $editing ? route('admin.products.show', $product) : route('admin.products.index') }}"
                class="rounded-lg border border-slate-300 bg-white px-4 py-2 font-medium text-slate-700 hover:bg-slate-50">
                Hủy
            </a>
        </div>

        <div id="message" class="mb-6 hidden rounded-lg p-4 text-sm" role="alert"></div>

        <form id="productForm" class="space-y-6 rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            @csrf

            <div class="grid gap-6 md:grid-cols-2">
                <div>
                    <label for="category_id" class="mb-2 block text-sm font-medium">Danh mục <span class="text-red-600">*</span></label>
                    <select id="category_id" name="category_id" required
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                        <option value="">Đang tải danh mục...</option>
                    </select>
                </div>

                <div>
                    <label for="sku" class="mb-2 block text-sm font-medium">SKU <span class="text-red-600">*</span></label>
                    <input id="sku" name="sku" type="text" required maxlength="64"
                        value="{{ old('sku', $product->sku ?? '') }}"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2.5 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                </div>

                <div class="md:col-span-2">
                    <label for="name" class="mb-2 block text-sm font-medium">Tên sản phẩm <span class="text-red-600">*</span></label>
                    <input id="name" name="name" type="text" required maxlength="180"
                        value="{{ old('name', $product->name ?? '') }}"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2.5 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                </div>

                <div>
                    <label for="unit" class="mb-2 block text-sm font-medium">Đơn vị <span class="text-red-600">*</span></label>
                    <input id="unit" name="unit" type="text" required maxlength="30"
                        value="{{ old('unit', $product->unit ?? '') }}"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2.5 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                </div>

                <div>
                    <label for="origin" class="mb-2 block text-sm font-medium">Nguồn gốc</label>
                    <input id="origin" name="origin" type="text" maxlength="150"
                        value="{{ old('origin', $product->origin ?? '') }}"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2.5 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                </div>

                <div>
                    <label for="price" class="mb-2 block text-sm font-medium">Giá <span class="text-red-600">*</span></label>
                    <input id="price" name="price" type="number" required min="0" step="0.01"
                        value="{{ old('price', $product->price ?? '') }}"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2.5 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                </div>

                <div>
                    <label for="compare_at_price" class="mb-2 block text-sm font-medium">Giá niêm yết</label>
                    <input id="compare_at_price" name="compare_at_price" type="number" min="0" step="0.01"
                        value="{{ old('compare_at_price', $product->compare_at_price ?? '') }}"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2.5 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                </div>

                @if (! $editing)
                    <div>
                        <label for="quantity_on_hand" class="mb-2 block text-sm font-medium">Tồn kho ban đầu</label>
                        <input id="quantity_on_hand" name="quantity_on_hand" type="number" min="0" step="1" value="0"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2.5 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                    </div>
                @endif

                <div>
                    <label for="status" class="mb-2 block text-sm font-medium">Trạng thái</label>
                    <select id="status" name="status"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                        @foreach (['draft' => 'Bản nháp', 'active' => 'Đang bán', 'inactive' => 'Không hoạt động'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', $product->status ?? 'draft') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label for="short_description" class="mb-2 block text-sm font-medium">Mô tả ngắn</label>
                <textarea id="short_description" name="short_description" rows="3" maxlength="500"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2.5 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">{{ old('short_description', $product->short_description ?? '') }}</textarea>
            </div>

            <div>
                <label for="description_html" class="mb-2 block text-sm font-medium">Mô tả chi tiết</label>
                <textarea id="description_html" name="description_html" rows="7"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2.5 font-mono text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">{{ old('description_html', $product->description_html ?? '') }}</textarea>
            </div>

            <label class="flex items-center gap-3 text-sm font-medium">
                <input id="featured" name="featured" type="checkbox" value="1"
                    @checked(old('featured', $product->featured ?? false))
                    class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                Sản phẩm nổi bật
            </label>

            <div class="flex items-center justify-end gap-3 border-t border-slate-200 pt-6">
                <a href="{{ $editing ? route('admin.products.show', $product) : route('admin.products.index') }}"
                    class="rounded-lg border border-slate-300 bg-white px-5 py-2.5 font-medium text-slate-700 hover:bg-slate-50">
                    Hủy
                </a>
                <button id="submitButton" type="submit"
                    class="rounded-lg bg-emerald-600 px-5 py-2.5 font-medium text-white hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-60">
                    {{ $editing ? 'Lưu thay đổi' : 'Tạo sản phẩm' }}
                </button>
            </div>
        </form>
    </main>

    <script>
        const isEditing = @json($editing);
        const productId = @json($product->id ?? null);
        const currentCategoryId = @json($product->category_id ?? null);
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const productForm = document.getElementById('productForm');
        const messageBox = document.getElementById('message');
        const submitButton = document.getElementById('submitButton');
        const categorySelect = document.getElementById('category_id');

        function showMessage(message, success = false) {
            messageBox.textContent = message;
            messageBox.className = `mb-6 rounded-lg p-4 text-sm ${success ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700'}`;
            messageBox.classList.remove('hidden');
        }

        function apiHeaders() {
            const headers = {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            };
            const token = localStorage.getItem('access_token') || localStorage.getItem('token');

            if (token) {
                headers.Authorization = `Bearer ${token}`;
            }

            return headers;
        }

        async function loadCategories() {
            const response = await fetch('/api/v1/categories', {
                headers: { Accept: 'application/json' },
            });
            const result = await response.json();

            if (!response.ok || !Array.isArray(result.data)) {
                throw new Error(result.message || 'Không tải được danh sách danh mục.');
            }

            categorySelect.innerHTML = '<option value="">Chọn danh mục</option>';
            result.data.forEach((category) => {
                const option = new Option(category.name, category.id, false, String(category.id) === String(currentCategoryId));
                categorySelect.add(option);
            });
        }

        function formPayload() {
            const formData = new FormData(productForm);
            const payload = {
                category_id: Number(formData.get('category_id')),
                sku: formData.get('sku').trim(),
                name: formData.get('name').trim(),
                unit: formData.get('unit').trim(),
                origin: formData.get('origin').trim() || null,
                price: Number(formData.get('price')),
                compare_at_price: formData.get('compare_at_price') ? Number(formData.get('compare_at_price')) : null,
                short_description: formData.get('short_description').trim() || null,
                description_html: formData.get('description_html').trim() || null,
                status: formData.get('status'),
                featured: formData.get('featured') === '1',
            };

            if (!isEditing) {
                payload.quantity_on_hand = Number(formData.get('quantity_on_hand') || 0);
            }

            return payload;
        }

        productForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            submitButton.disabled = true;
            messageBox.classList.add('hidden');

            try {
                const response = await fetch(isEditing ? `/api/v1/admin/products/${productId}` : '/api/v1/admin/products', {
                    method: isEditing ? 'PATCH' : 'POST',
                    headers: apiHeaders(),
                    body: JSON.stringify(formPayload()),
                });
                const result = await response.json();

                if (!response.ok) {
                    const validationErrors = result.errors ? Object.values(result.errors).flat().join(' ') : '';
                    throw new Error(validationErrors || result.message || 'Không thể lưu sản phẩm.');
                }

                const savedProductId = result.data?.id || productId;
                window.location.href = `/admin/products/${savedProductId}`;
            } catch (error) {
                showMessage(error.message || 'Không thể kết nối tới API.');
                submitButton.disabled = false;
            }
        });

        loadCategories().catch((error) => {
            categorySelect.innerHTML = '<option value="">Không tải được danh mục</option>';
            showMessage(error.message);
        });
    </script>
</body>

</html>

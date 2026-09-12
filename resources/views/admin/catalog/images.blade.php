<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Quản lý hình ảnh sản phẩm</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 text-slate-800">
<main class="mx-auto max-w-6xl px-6 py-10">

    <div class="mb-8">
        <p class="text-sm font-medium text-emerald-600">Admin Catalog</p>
        <h1 class="mt-1 text-3xl font-bold">Quản lý hình ảnh sản phẩm</h1>
    </div>

    @if (!isset($product))
        <div class="rounded-xl bg-red-50 p-4 text-red-700">
            Không tìm thấy sản phẩm.
        </div>
    @else
        <section class="mb-6 rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <h2 class="text-xl font-semibold">
                {{ $product->name ?? 'Sản phẩm' }}
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Mã sản phẩm: {{ $product->id }}
            </p>
        </section>

        <div id="message" class="mb-6 hidden rounded-lg p-4 text-sm"></div>

        <section class="mb-6 rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <h2 class="mb-4 text-lg font-semibold">Tải ảnh lên</h2>

            <form id="uploadForm" enctype="multipart/form-data">
                <input
                    id="images"
                    type="file"
                    name="images[]"
                    multiple
                    required
                    accept="image/jpeg,image/png,image/webp"
                    class="block w-full rounded-lg border border-slate-300 px-3 py-2">

                <p class="mt-2 text-xs text-slate-500">
                    JPG, PNG hoặc WEBP. Tối đa 5MB mỗi ảnh.
                </p>

                <button
                    type="submit"
                    class="mt-4 rounded-lg bg-emerald-600 px-5 py-2.5 font-medium text-white hover:bg-emerald-700">
                    Tải ảnh lên
                </button>
            </form>
        </section>

        <section class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="mb-6 flex items-center justify-between">
                <h2 class="text-lg font-semibold">Danh sách ảnh</h2>

                <button
                    id="saveOrder"
                    type="button"
                    class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                    Lưu thứ tự
                </button>
            </div>

            <div id="imageList" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($product->images ?? [] as $image)
                    <article
                        class="image-item rounded-lg border border-slate-200 p-4"
                        data-id="{{ $image->id }}"
                        data-sort-order="{{ $image->sort_order }}">

                        <img
                            src="{{ \Illuminate\Support\Facades\Storage::url($image->file_path) }}"
                            alt="{{ $image->alt_text ?? 'Ảnh sản phẩm' }}"
                            class="mb-4 h-48 w-full rounded-lg object-cover">

                        <div class="flex items-center justify-between gap-2">
                            <span class="text-sm text-slate-500">
                                Thứ tự: {{ $image->sort_order }}
                            </span>

                            <label class="flex items-center gap-2 text-sm">
                                <input
                                    type="radio"
                                    name="primary_image"
                                    value="{{ $image->id }}"
                                    {{ $image->is_primary ? 'checked' : '' }}>
                                Ảnh chính
                            </label>
                        </div>

                        <div class="mt-4 flex gap-2">
                            <button
                                type="button"
                                class="move-up rounded border px-3 py-1 text-sm">
                                ↑
                            </button>

                            <button
                                type="button"
                                class="move-down rounded border px-3 py-1 text-sm">
                                ↓
                            </button>
                        </div>
                    </article>
                @empty
                    <p class="text-sm text-slate-500">
                        Sản phẩm chưa có hình ảnh.
                    </p>
                @endforelse
            </div>
        </section>
    @endif
</main>

@if (isset($product))
<script>
const productId = @json($product->id);
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
const apiUrl = `/api/v1/admin/products/${productId}/images`;

function showMessage(message, success = true) {
    const box = document.getElementById('message');
    box.textContent = message;
    box.className = `mb-6 rounded-lg p-4 text-sm ${
        success
            ? 'bg-emerald-50 text-emerald-700'
            : 'bg-red-50 text-red-700'
    }`;
}

document.getElementById('uploadForm')?.addEventListener('submit', async function (event) {
    event.preventDefault();

    const formData = new FormData(this);

    const response = await fetch(apiUrl, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: formData
    });

    const result = await response.json();

    if (!response.ok) {
        showMessage(result.message || 'Tải ảnh thất bại.', false);
        return;
    }

    showMessage(result.message || 'Tải ảnh thành công.');
    setTimeout(() => window.location.reload(), 800);
});

document.querySelectorAll('.move-up').forEach(button => {
    button.addEventListener('click', () => {
        const item = button.closest('.image-item');
        const previous = item.previousElementSibling;

        if (previous) {
            item.parentNode.insertBefore(item, previous);
        }
    });
});

document.querySelectorAll('.move-down').forEach(button => {
    button.addEventListener('click', () => {
        const item = button.closest('.image-item');
        const next = item.nextElementSibling;

        if (next) {
            item.parentNode.insertBefore(next, item);
        }
    });
});

document.getElementById('saveOrder')?.addEventListener('click', async function () {
    const items = [...document.querySelectorAll('.image-item')];
    const primaryId = document.querySelector('input[name="primary_image"]:checked')?.value;

    const images = items.map((item, index) => ({
        id: Number(item.dataset.id),
        sort_order: index + 1,
        is_primary: item.dataset.id === primaryId
    }));

    const response = await fetch(`${apiUrl}/reorder`, {
        method: 'PATCH',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ images })
    });

    const result = await response.json();

    showMessage(
        result.message || (response.ok ? 'Đã lưu thứ tự ảnh.' : 'Lưu thất bại.'),
        response.ok
    );
});
</script>
@endif

</body>
</html>
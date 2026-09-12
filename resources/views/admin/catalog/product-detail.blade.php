<!DOCTYPE html>
@php
    use Illuminate\Support\Facades\Storage;
@endphp
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Chi tiết sản phẩm</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 text-slate-800">

    <main class="mx-auto max-w-5xl px-6 py-10">

        <!-- Tiêu đề -->
        <div class="mb-8">

            <p class="text-sm font-medium text-emerald-600">
                Admin Catalog
            </p>

            <h1 class="mt-1 text-3xl font-bold">
                Chi tiết sản phẩm
            </h1>

        </div>


        @if (isset($product))

            <!-- Thông tin sản phẩm -->
            <section class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">

                <div class="grid gap-8 md:grid-cols-2">

                    <!-- Hình ảnh -->
                    <div>

                        @if ($product->primaryImage)

                            <img
                                src="{{ Storage::url($product->primaryImage->file_path) }}"
                                alt="{{ $product->name }}"
                                class="h-80 w-full rounded-xl object-cover">

                        @else

                            <div class="flex h-80 items-center justify-center rounded-xl bg-slate-100 text-slate-400">
                                Chưa có hình ảnh
                            </div>

                        @endif

                    </div>


                    <!-- Thông tin -->
                    <div>

                        <h2 class="text-2xl font-bold">
                            {{ $product->name }}
                        </h2>

                        <p class="mt-2 text-sm text-slate-500">
                            SKU: {{ $product->sku }}
                        </p>


                        <div class="mt-6 space-y-4">

                            <div>
                                <p class="text-sm text-slate-500">
                                    Danh mục
                                </p>

                                <p class="font-medium">
                                    {{ $product->category->name ?? 'Chưa phân loại' }}
                                </p>
                            </div>


                            <div>
                                <p class="text-sm text-slate-500">
                                    Đơn vị
                                </p>

                                <p class="font-medium">
                                    {{ $product->unit }}
                                </p>
                            </div>


                            <div>
                                <p class="text-sm text-slate-500">
                                    Nguồn gốc
                                </p>

                                <p class="font-medium">
                                    {{ $product->origin }}
                                </p>
                            </div>


                            <div>
                                <p class="text-sm text-slate-500">
                                    Giá
                                </p>

                                <p class="text-xl font-bold text-emerald-600">
                                    {{ number_format($product->price, 0, ',', '.') }}đ
                                </p>
                            </div>


                            <div>
                                <p class="text-sm text-slate-500">
                                    Tồn kho
                                </p>

                                <p class="font-medium">
                                    {{ $product->inventory->quantity_on_hand ?? 0 }}
                                </p>
                            </div>


                            <div>
                                <p class="text-sm text-slate-500">
                                    Trạng thái
                                </p>

                                @if (($product->status ?? '') === 'active')

                                    <span class="inline-block rounded-full bg-emerald-100 px-3 py-1 text-xs font-medium text-emerald-700">
                                        Đang bán
                                    </span>

                                @else

                                    <span class="inline-block rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">
                                        Không hoạt động
                                    </span>

                                @endif

                            </div>

                        </div>

                    </div>

                </div>


                <!-- Mô tả -->
                <div class="mt-8 border-t border-slate-200 pt-6">

                    <h3 class="text-lg font-semibold">
                        Mô tả sản phẩm
                    </h3>

                    <div class="mt-3 text-slate-600">
                        {!! $product->description_html ?? 'Chưa có mô tả sản phẩm.' !!}
                    </div>

                </div>


                <!-- Nút -->
                <div class="mt-8 flex gap-3 border-t border-slate-200 pt-6">

                          <a href="{{ route('admin.products.edit', $product) }}"
                       class="rounded-lg bg-emerald-600 px-4 py-2 font-medium text-white hover:bg-emerald-700">
                       Chỉnh sửa
                    </a>

                    <a href="{{ url('/admin/catalog/products/' . $product->id . '/images') }}"
                       class="rounded-lg border border-slate-300 bg-white px-4 py-2 font-medium text-slate-700 hover:bg-slate-50">
                       Quản lý ảnh
                    </a>

                          <a href="{{ route('admin.products.index') }}"
                       class="rounded-lg border border-slate-300 bg-white px-4 py-2 font-medium text-slate-700 hover:bg-slate-50">
                       Quay lại
                    </a>

                </div>

            </section>

        @else

            <!-- Không có sản phẩm -->
            <section class="rounded-xl bg-white p-10 text-center shadow-sm ring-1 ring-slate-200">

                <p class="text-slate-500">
                    Không tìm thấy sản phẩm.
                </p>

            </section>

        @endif

    </main>

</body>

</html>
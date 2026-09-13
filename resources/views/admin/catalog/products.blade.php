<!DOCTYPE html>

@php
    use Illuminate\Support\Facades\Storage;
@endphp

<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Quản lý sản phẩm</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body class="min-h-screen bg-slate-100 text-slate-800">

    <main class="w-full px-5 py-10">


        <div class="mb-8 flex items-center justify-between">

            <div>

                <p class="text-sm font-medium text-emerald-600">
                    Admin Catalog
                </p>

                <h1 class="mt-1 text-3xl font-bold">
                    Quản lý sản phẩm
                </h1>

                <p class="mt-2 text-slate-500">
                    Quản lý thông tin sản phẩm trong hệ thống
                </p>

            </div>

            <a
                href="{{ route('admin.products.create') }}"
                class="rounded-lg bg-emerald-600 px-4 py-2 font-medium text-white hover:bg-emerald-700"
            >
                + Thêm sản phẩm
            </a>

        </div>

        <section class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-slate-200">

            <div class="border-b border-slate-200 px-6 py-4">

                <h2 class="font-semibold">
                    Danh sách sản phẩm
                </h2>

            </div>


            <div class="overflow-x-auto">

                <table class="w-full min-w-[900px] text-left text-sm">

                    <thead class="bg-slate-50 text-xs uppercase text-slate-500">

                        <tr>

                            <th class="px-6 py-4">
                                Sản phẩm
                            </th>

                            <th class="px-6 py-4">
                                Danh mục
                            </th>

                            <th class="px-6 py-4">
                                Giá
                            </th>

                            <th class="px-6 py-4">
                                Tồn kho
                            </th>

                            <th class="px-6 py-4">
                                Trạng thái
                            </th>

                            <th class="px-6 py-4">
                                Chi tiết
                            </th>

                        </tr>

                    </thead>

                    <tbody class="divide-y divide-slate-100">

                        @forelse ($products ?? [] as $product)

                            <tr class="hover:bg-slate-50">

                                <td class="px-6 py-4">

                                    <div class="flex items-center gap-3">

                                        @if ($product->primaryImage)

                                            <img
                                                src="{{ Storage::url($product->primaryImage->file_path) }}"
                                                alt="{{ $product->name }}"
                                                class="h-12 w-12 rounded-lg object-cover"
                                            >

                                        @else

                                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-slate-100 text-xs text-slate-400">
                                                No image
                                            </div>

                                        @endif


                                        <div>

                                            <p class="font-medium">
                                                {{ $product->name }}
                                            </p>

                                            <p class="text-xs text-slate-500">
                                                {{ $product->sku }}
                                            </p>

                                        </div>

                                    </div>

                                </td>

                                <td class="px-6 py-4">

                                    {{ $product->category->name ?? 'Chưa phân loại' }}

                                </td>


                                <td class="px-6 py-4 font-medium">

                                    {{ number_format($product->price, 0, ',', '.') }}đ

                                </td>


                                <td class="px-6 py-4">

                                    {{ $product->inventory->quantity_on_hand ?? 0 }}

                                </td>


                                <td class="px-6 py-4">

                                    @if (($product->status ?? '') === 'active')

                                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-medium text-emerald-700">
                                            Đang bán
                                        </span>

                                    @else

                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">
                                            Không hoạt động
                                        </span>

                                    @endif

                                </td>

                                <td class="px-6 py-4">

                                    <div class="flex gap-3">

                                        <a
                                            href="{{ route('admin.products.show', $product) }}"
                                            class="font-medium text-emerald-600 hover:text-emerald-700"
                                        >
                                            Xem chi tiết
                                        </a>


                                        <a
                                            href="#"
                                            onclick="deleteProduct({{ $product->id }}); return false;"
                                            class="font-medium text-red-600 hover:text-red-700"
                                        >
                                            Xóa
                                        </a>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="6"
                                    class="px-6 py-12 text-center text-slate-500"
                                >
                                    Chưa có sản phẩm nào.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </section>

    </main>

    <script>

        const API_BASE_URL = 'http://api.nongsanxanh.local';

        const token = localStorage.getItem('access_token');


        async function deleteProduct(id) {



            if (!confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?')) {
                return;
            }


            try {

               
                const response = await fetch(
                    `${API_BASE_URL}/api/v1/admin/products/${id}`,
                    {
                        method: 'DELETE',

                        headers: {
                            'Accept': 'application/json',
                            'Authorization': 'Bearer ' + token
                        }
                    }
                );


                

                const result = await response.json();


                if (response.ok && result.success === true) {

                    alert(result.message);


                    location.reload();

                    return;
                }


                

                const errorCode =
                    result.errors?.error_code ||
                    result.error_code;


                if (errorCode === 'PRODUCT_IN_ACTIVE_ORDER') {

                    alert(
                        'Không thể xóa sản phẩm vì sản phẩm đang nằm trong đơn hàng đang hoạt động.'
                    );

                    return;
                }


                // Lỗi khác

                alert(
                    result.message || 'Không thể xóa sản phẩm.'
                );


            } catch (error) {

                console.error(error);

                alert('Không thể kết nối đến API.');
            }
        }

    </script>

</body>

</html>
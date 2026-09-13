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

    <style>
        * {
            box-sizing: border-box;
        }

        body {
               margin: 0;
               width: 100%;
               min-height: 100vh;
               font-family: Arial, sans-serif;
               background: #e2eae8 !important;
               color: #333;
}
        main {
            width: 100% !important;
            max-width: none !important;
            margin: 0 !important;
            padding: 35px 25px 60px !important;
        }


        main > div:first-child {
            margin-bottom: 25px !important;
            padding: 5px;
        }

        main > div:first-child p:first-child {
            color: rgb(51, 101, 110) !important;
            font-size: 15px !important;
            font-weight: bold !important;
        }

        h1 {
            color: rgb(44, 74, 80) !important;
            font-size: 30px !important;
            font-weight: bold !important;
        }

        main > div:first-child p:last-child {
            color: #64748b !important;
            font-size: 15px !important;
        }


        main > div:first-child > a {
            background: rgb(51, 101, 110) !important;
            color: white !important;

            border-radius: 8px !important;

            padding: 12px 22px !important;

            font-size: 15px !important;
            font-weight: 600 !important;

            text-decoration: none !important;

            transition: 0.2s ease !important;
        }

        main > div:first-child > a:hover {
            background: rgb(43, 88, 96) !important;

            transform: translateY(-1px);

            box-shadow:
                0 5px 12px rgba(51, 101, 110, 0.20) !important;
        }


        section {
            width: 100% !important;
            max-width: none !important;

            background: white !important;

            border: 1px solid #dfe7e8 !important;
            border-radius: 12px !important;

            box-shadow:
                0 4px 15px rgba(0, 0, 0, 0.06) !important;

            overflow: hidden !important;
        }

       

        section > div:first-child {
            padding: 20px 24px !important;

            background: #f8faf9 !important;

            border-bottom: 1px solid #dfe7e8 !important;
        }

        section > div:first-child h2 {
            color: rgb(44, 74, 80) !important;

            font-size: 20px !important;
            font-weight: bold !important;
        }

     

        table {
            width: 100% !important;
            min-width: 1000px !important;

            border-collapse: collapse !important;
        }

        thead {
            background: #eaf1f2 !important;
        }

        thead th {
            padding: 18px 22px !important;

            color: rgb(44, 74, 80) !important;

            font-size: 13px !important;
            font-weight: bold !important;

            text-transform: uppercase;

            white-space: nowrap;
        }

        tbody tr {
            transition: 0.15s ease !important;
        }

        tbody tr:hover {
            background: #f8fbfb !important;
        }

        tbody td {
            padding: 18px 22px !important;

            border-bottom: 1px solid #edf1f2 !important;

            font-size: 15px !important;

            vertical-align: middle !important;
        }

        /* =========================
           ẢNH SẢN PHẨM
        ========================= */

        tbody td img,
        tbody td .h-12 {
            width: 60px !important;
            height: 60px !important;

            border-radius: 8px !important;

            object-fit: cover !important;
        }

        /* =========================
        

        tbody td p.font-medium {
            color: rgb(44, 74, 80) !important;

            font-size: 16px !important;
            font-weight: 600 !important;
        }

        tbody td p.text-xs {
            color: #7b8790 !important;

            font-size: 13px !important;

            margin-top: 4px !important;
        }

        /* =========================
           GIÁ
        ========================= */

        tbody td.font-medium {
            color: rgb(51, 101, 110) !important;

            font-size: 16px !important;
            font-weight: bold !important;
        }

        

        tbody td span {
            display: inline-block;

            padding: 7px 13px !important;

            border-radius: 20px !important;

            font-size: 13px !important;
            font-weight: 600 !important;

            white-space: nowrap;
        }

        tbody td span.bg-emerald-100 {
            background: #e7f5ea !important;

            color: #237a43 !important;

            border: 1px solid #bde3c8 !important;
        }

        tbody td span.bg-slate-100 {
            background: #eef1f2 !important;

            color: #64748b !important;

            border: 1px solid #dce3e5 !important;
        }

        /* =========================
           NÚT XEM + XÓA
        ========================= */

        tbody td a {
            font-size: 14px !important;

            font-weight: 600 !important;

            text-decoration: none !important;

            transition: 0.2s ease !important;
        }

        tbody td a.text-emerald-600 {
            color: rgb(51, 101, 110) !important;
        }

        tbody td a.text-emerald-600:hover {
            color: rgb(43, 88, 96) !important;
        }

        tbody td a.text-red-600 {
            color: #dc2626 !important;
        }

        tbody td a.text-red-600:hover {
            color: #b91c1c !important;
        }

        /* =========================
           KHUNG INPUT / SELECT
           Dùng nếu trang có form
        ========================= */

        input,
        select,
        textarea {
            width: 100% !important;

            min-height: 48px !important;

            padding: 11px 15px !important;

            background: #ffffff !important;

            border: 1.5px solid #ccdadd !important;

            border-radius: 8px !important;

            color: rgb(44, 74, 80) !important;

            font-size: 16px !important;

            outline: none !important;

            transition: all 0.2s ease !important;
        }

        input:hover,
        select:hover,
        textarea:hover {
            border-color: #9db5b9 !important;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: rgb(51, 101, 110) !important;

            box-shadow:
                0 0 0 3px rgba(51, 101, 110, 0.10) !important;
        }

        input::placeholder,
        textarea::placeholder {
            color: #9aa9ad !important;
        }

        label {
            display: block;

            margin-bottom: 8px !important;

            color: rgb(44, 74, 80) !important;

            font-size: 16px !important;

            font-weight: 600 !important;
        }

       
        @media (max-width: 768px) {

            main {
                padding: 20px 12px 40px !important;
            }

            main > div:first-child {
                flex-direction: column;

                align-items: stretch !important;

                gap: 15px;
            }

            main > div:first-child > a {
                text-align: center;
            }

            h1 {
                font-size: 25px !important;
            }

            section > div:first-child {
                padding: 16px !important;
            }

            thead th,
            tbody td {
                padding: 14px !important;
            }
        }
    </style>
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
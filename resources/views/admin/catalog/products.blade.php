@extends('layouts.admin')

@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Facades\Route;
@endphp

@section('title', 'Quản lý sản phẩm')

@section('page-header')
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-emerald-600">Admin Catalog</p>
            <h1 class="mt-1 text-3xl font-bold">Quản lý sản phẩm</h1>
            <p class="mt-2 text-slate-500">Quản lý thông tin sản phẩm trong hệ thống</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="rounded-lg bg-emerald-600 px-4 py-2 font-medium text-white hover:bg-emerald-700">
            + Thêm sản phẩm
        </a>
    </div>
@endsection

@section('content')
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

                    <tbody
                        id="productList"
                        class="divide-y divide-slate-100"
                    >

                        <tr>

                            <td
                                colspan="6"
                                class="px-6 py-12 text-center text-slate-500"
                            >
                                Đang tải danh sách sản phẩm...
                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </section>

@endsection

@push('scripts')
<script>
    const API_BASE_URL = '/api/v1';



    function getAuthHeaders(includeContentType = false) {
        const token = sessionStorage.getItem('access_token') || localStorage.getItem('access_token');

        const headers = {
            'Accept': 'application/json'
        };

        if (includeContentType) {
            headers['Content-Type'] = 'application/json';
        }

        if (token) {
            headers['Authorization'] = `Bearer ${token}`;
        }

        return headers;
    }

    function getErrorCode(result) {
        return result?.errors?.error_code ||
               result?.error_code ||
               'UNKNOWN_ERROR';
    }

    function showApiError(result, defaultMessage = 'Có lỗi xảy ra.') {
        const errorCode = getErrorCode(result);

        if (errorCode === 'FORBIDDEN') {
            alert('Bạn không có quyền thực hiện thao tác này.');
            return;
        }

        if (errorCode === 'PRODUCT_NOT_FOUND') {
            alert('Không tìm thấy sản phẩm.');
            return;
        }

        if (errorCode === 'PRODUCT_IN_ACTIVE_ORDER') {
            alert(
                'Không thể xóa sản phẩm vì sản phẩm đang nằm trong đơn hàng đang hoạt động.'
            );
            return;
        }

        if (errorCode === 'VALIDATION_ERROR') {
            alert('Dữ liệu không hợp lệ.');
            return;
        }

        if (errorCode === 'DUPLICATE_VALUE') {
            alert('Dữ liệu sản phẩm bị trùng.');
            return;
        }

        alert(result?.message || defaultMessage);
    }



    async function loadProducts() {
        const productList = document.getElementById('productList');

        try {
            const response = await fetch(
                `${API_BASE_URL}/admin/products`,
                {
                    method: 'GET',
                    headers: getAuthHeaders()
                }
            );

            const result = await response.json();

            if (!response.ok || result.success !== true) {
                console.error('API error:', getErrorCode(result));

                showApiError(
                    result,
                    'Không thể tải danh sách sản phẩm.'
                );

                productList.innerHTML = '';

                const row = document.createElement('tr');
                const cell = document.createElement('td');

                cell.colSpan = 6;
                cell.className =
                    'px-6 py-12 text-center text-red-500';
                cell.textContent =
                    'Không thể tải danh sách sản phẩm.';

                row.appendChild(cell);
                productList.appendChild(row);

                return;
            }

            const products = Array.isArray(result.data)
                ? result.data
                : [];

            displayProducts(products);

        } catch (error) {
            console.error('Load products error:', error);

            productList.innerHTML = '';

            const row = document.createElement('tr');
            const cell = document.createElement('td');

            cell.colSpan = 6;
            cell.className =
                'px-6 py-12 text-center text-red-500';
            cell.textContent =
                'Không thể kết nối đến API.';

            row.appendChild(cell);
            productList.appendChild(row);
        }
    }


    function displayProducts(products) {
        const productList = document.getElementById('productList');

        productList.innerHTML = '';

        if (!products || products.length === 0) {
            const row = document.createElement('tr');
            const cell = document.createElement('td');

            cell.colSpan = 6;
            cell.className =
                'px-6 py-12 text-center text-slate-500';
            cell.textContent =
                'Chưa có sản phẩm nào.';

            row.appendChild(cell);
            productList.appendChild(row);

            return;
        }

        products.forEach(function (product) {
            const id = product.id;

            const name =
                product.name ?? 'Không có tên';

            const sku =
                product.sku ?? '';

            const category =
                product.category?.name ??
                'Chưa phân loại';

            const price =
                Number(product.price ?? 0)
                    .toLocaleString('vi-VN') + 'đ';

            const stock =
                product.available_quantity ?? 0;

            const status =
                product.status ?? '';

            const row = document.createElement('tr');
            row.className = 'hover:bg-slate-50';


            // =========================
            // SẢN PHẨM
            // =========================

            const productCell = document.createElement('td');
            productCell.className = 'px-6 py-4';

            const productWrapper =
                document.createElement('div');

            productWrapper.className =
                'flex items-center gap-3';

            const imageBox =
                document.createElement('div');

            imageBox.className =
                'flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-slate-100 text-xs text-slate-400';

            if (product.primary_image_url) {
                const image = document.createElement('img');
                image.src = product.primary_image_url;
                image.alt = name;
                image.className = 'h-full w-full object-cover';
                imageBox.appendChild(image);
            } else {
                imageBox.textContent = 'No image';
            }


            const productInfo =
                document.createElement('div');

            const nameElement =
                document.createElement('p');

            nameElement.className = 'font-medium';
            nameElement.textContent = name;

            const skuElement =
                document.createElement('p');

            skuElement.className =
                'text-xs text-slate-500';
            skuElement.textContent = sku;

            productInfo.appendChild(nameElement);
            productInfo.appendChild(skuElement);

            productWrapper.appendChild(imageBox);
            productWrapper.appendChild(productInfo);

            productCell.appendChild(productWrapper);


            // =========================
            // DANH MỤC
            // =========================

            const categoryCell =
                document.createElement('td');

            categoryCell.className =
                'px-6 py-4';

            categoryCell.textContent = category;


            // =========================
            // GIÁ
            // =========================

            const priceCell =
                document.createElement('td');

            priceCell.className =
                'px-6 py-4 font-medium';

            priceCell.textContent = price;


            // =========================
            // TỒN KHO
            // =========================

            const stockCell =
                document.createElement('td');

            stockCell.className =
                'px-6 py-4';

            stockCell.textContent = stock;


            // =========================
            // TRẠNG THÁI
            // =========================

            const statusCell =
                document.createElement('td');

            statusCell.className =
                'px-6 py-4';

            const statusElement =
                document.createElement('span');

            statusElement.className =
                status === 'active'
                    ? 'rounded-full bg-emerald-100 px-3 py-1 text-xs font-medium text-emerald-700'
                    : 'rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600';

            statusElement.textContent =
                status === 'active'
                    ? 'Đang bán'
                    : 'Không hoạt động';

            statusCell.appendChild(statusElement);


            // =========================
            // CHI TIẾT
            // =========================

            const actionCell =
                document.createElement('td');

            actionCell.className =
                'px-6 py-4';

            const actionWrapper =
                document.createElement('div');

            actionWrapper.className =
                'flex gap-3';

            const detailLink =
                document.createElement('a');

            detailLink.href =
                `/admin/products/${encodeURIComponent(id)}`;

            detailLink.className =
                'font-medium text-emerald-600 hover:text-emerald-700';

            detailLink.textContent =
                'Xem chi tiết';


            const deleteLink =
                document.createElement('a');

            deleteLink.href = '#';

            deleteLink.className =
                'font-medium text-red-600 hover:text-red-700';

            deleteLink.textContent = 'Xóa';

            deleteLink.addEventListener(
                'click',
                function (event) {
                    event.preventDefault();
                    deleteProduct(id);
                }
            );

            actionWrapper.appendChild(detailLink);
            actionWrapper.appendChild(deleteLink);

            actionCell.appendChild(actionWrapper);


            // =========================
            // GHÉP ROW
            // =========================

            row.appendChild(productCell);
            row.appendChild(categoryCell);
            row.appendChild(priceCell);
            row.appendChild(stockCell);
            row.appendChild(statusCell);
            row.appendChild(actionCell);

            productList.appendChild(row);
        });
    }


    // =========================
    // DELETE PRODUCT
    // DELETE /api/v1/admin/products/{id}
    // =========================

    async function deleteProduct(id) {
        if (
            !confirm(
                'Bạn có chắc chắn muốn xóa sản phẩm này không?'
            )
        ) {
            return;
        }

        try {
            const response = await fetch(
                `${API_BASE_URL}/admin/products/${id}`,
                {
                    method: 'DELETE',
                    headers: getAuthHeaders()
                }
            );

            const result = response.status === 204
                ? { success: true }
                : await response.json();

            if (response.ok && result.success === true) {
                alert(
                    result.message ||
                    'Xóa sản phẩm thành công.'
                );

                loadProducts();
                return;
            }

            console.error(
                'Delete product error:',
                getErrorCode(result)
            );

            showApiError(
                result,
                'Không thể xóa sản phẩm.'
            );

        } catch (error) {
            console.error(
                'Delete product error:',
                error
            );

            alert('Không thể kết nối đến API.');
        }
    }


    // =========================
    // INIT
    // =========================

    document.addEventListener(
        'DOMContentLoaded',
        function () {
            loadProducts();
        }
    );
</script>
@endpush
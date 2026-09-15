<!DOCTYPE html>

<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Chi tiết sản phẩm</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-white text-slate-800 font-sans antialiased">

    <main class="w-full px-5 py-10">

        {{-- ================= TIÊU ĐỀ ================= --}}
        <div class="mb-8 flex items-center justify-between gap-4">

            <div>
                <h1 class="text-3xl font-extrabold tracking-tight">
                    Chi tiết sản phẩm
                </h1>

                <p class="mt-2 text-sm text-slate-500">
                    Thông tin chi tiết và quản lý sản phẩm
                </p>
            </div>

            <a
                href="{{ route('admin.products.index') }}"
                class="inline-flex items-center rounded-lg px-5 py-3 text-sm font-semibold text-white"
                style="background: rgb(8, 17, 19);"
            >
                ← Quay lại
            </a>

        </div>


        {{-- ================= THÔNG TIN SẢN PHẨM ================= --}}
        <section class="overflow-hidden">

            <div class="grid grid-cols-1 gap-8 p-6 lg:grid-cols-3">

                {{-- ================= HÌNH ẢNH CHÍNH ================= --}}
                <div class="lg:col-span-1">

                    <div
                        id="mainImageContainer"
                        class="flex min-h-[400px] items-center justify-center rounded-xl bg-slate-50 p-5"
                    >
                        <div class="text-center text-slate-400">
                            Đang tải hình ảnh...
                        </div>
                    </div>

                </div>


                {{-- ================= THÔNG TIN ================= --}}
                <div class="lg:col-span-2">

                    <div
                        class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between"
                    >

                        <div>

                            <h2
                                id="productName"
                                class="text-3xl font-bold"
                            >
                                Đang tải...
                            </h2>

                            <p
                                id="productSku"
                                class="mt-2 text-sm text-slate-500"
                            >
                                SKU: —
                            </p>

                        </div>

                        <span
                            id="productStatus"
                            class="inline-flex w-fit rounded-full px-4 py-2 text-sm font-semibold"
                        >
                            Đang tải...
                        </span>

                    </div>


                    {{-- ================= GIÁ ================= --}}
                    <div class="mb-6 rounded-xl bg-slate-50 p-5">

                        <p class="mb-2 text-sm text-slate-500">
                            Giá bán
                        </p>

                        <p
                            id="productPrice"
                            class="text-3xl font-bold"
                            style="color: rgb(4, 14, 16);"
                        >
                            0 đ
                        </p>

                    </div>


                    {{-- ================= THÔNG TIN CƠ BẢN ================= --}}
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                        <div>

                            <p class="mb-1 text-sm text-slate-500">
                                Danh mục
                            </p>

                            <p
                                id="productCategory"
                                class="font-semibold"
                            >
                                —
                            </p>

                        </div>


                        <div>

                            <p class="mb-1 text-sm text-slate-500">
                                Đơn vị
                            </p>

                            <p
                                id="productUnit"
                                class="font-semibold"
                            >
                                —
                            </p>

                        </div>


                        <div>

                            <p class="mb-1 text-sm text-slate-500">
                                Xuất xứ
                            </p>

                            <p
                                id="productOrigin"
                                class="font-semibold"
                            >
                                —
                            </p>

                        </div>


                        <div>

                            <p class="mb-1 text-sm text-slate-500">
                                Tồn kho
                            </p>

                            <p
                                id="productQuantity"
                                class="font-semibold"
                            >
                                0
                            </p>

                        </div>

                    </div>


                    {{-- ================= MÔ TẢ ================= --}}
                    <div class="mt-8">

                        <h3 class="mb-3 text-xl font-bold">
                            Mô tả sản phẩm
                        </h3>

                        <div
                            id="productDescription"
                            class="rounded-xl bg-slate-50 p-5 leading-7 text-slate-600"
                        >
                            Chưa có mô tả.
                        </div>

                    </div>


                    {{-- ================= NÚT CHỨC NĂNG ================= --}}
                    <div class="mt-8 flex flex-wrap gap-3">

                        <button
                            type="button"
                            onclick="openEditModal()"
                            class="rounded-lg px-5 py-3 text-sm font-semibold text-white"
                            style="background: rgb(51, 101, 110);"
                        >
                            Sửa sản phẩm
                        </button>


                        <button
                            type="button"
                            onclick="openImageModal()"
                            class="rounded-lg border px-5 py-3 text-sm font-semibold"
                            style="border-color: rgb(51, 101, 110); color: rgb(51, 101, 110);"
                        >
                            Quản lý hình ảnh
                        </button>

                    </div>

                </div>

            </div>

        </section>

    </main>



    {{-- ========================================================= --}}
    {{-- ====================== EDIT MODAL ======================= --}}
    {{-- ========================================================= --}}

    <div
        id="editModal"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 px-4"
    >

        <div class="w-full max-w-lg rounded-xl bg-white p-6 shadow-xl">

            <div class="mb-6 flex items-center justify-between">

                <h2 class="text-2xl font-bold">
                    Sửa sản phẩm
                </h2>

                <button
                    type="button"
                    onclick="closeEditModal()"
                    class="text-2xl text-slate-400 hover:text-slate-700"
                >
                    ×
                </button>

            </div>


            <div class="space-y-5">

                {{-- Tên --}}
                <div>

                    <label class="mb-2 block text-sm font-semibold">
                        Tên sản phẩm
                    </label>

                    <input
                        id="editName"
                        type="text"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 outline-none"
                    >

                </div>


                {{-- Giá --}}
                <div>

                    <label class="mb-2 block text-sm font-semibold">
                        Giá
                    </label>

                    <input
                        id="editPrice"
                        type="number"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 outline-none"
                    >

                </div>


                {{-- Xuất xứ --}}
                <div>

                    <label class="mb-2 block text-sm font-semibold">
                        Xuất xứ
                    </label>

                    <input
                        id="editOrigin"
                        type="text"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 outline-none"
                    >

                </div>


                {{-- Trạng thái --}}
                <div>

                    <label class="mb-2 block text-sm font-semibold">
                        Trạng thái
                    </label>

                    <select
                        id="editStatus"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 outline-none"
                    >

                        <option value="active">
                            Đang bán
                        </option>

                        <option value="inactive">
                            Ngừng bán
                        </option>

                    </select>

                </div>

            </div>


            <div class="mt-7 flex justify-end gap-3">

                <button
                    type="button"
                    onclick="closeEditModal()"
                    class="rounded-lg border border-slate-300 px-5 py-3 font-semibold"
                >
                    Hủy
                </button>


                <button
                    type="button"
                    onclick="updateProduct()"
                    class="rounded-lg px-5 py-3 font-semibold text-white"
                    style="background: rgb(51, 101, 110);"
                >
                    Lưu thay đổi
                </button>

            </div>

        </div>

    </div>



    {{-- ========================================================= --}}
    {{-- ====================== IMAGE MODAL ====================== --}}
    {{-- ========================================================= --}}

    <div
        id="imageModal"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 px-4"
    >

        <div class="w-full max-w-4xl rounded-xl bg-white p-6 shadow-xl">

            <div class="mb-6 flex items-center justify-between">

                <h2 class="text-2xl font-bold">
                    Quản lý hình ảnh
                </h2>

                <button
                    type="button"
                    onclick="closeImageModal()"
                    class="text-2xl text-slate-400 hover:text-slate-700"
                >
                    ×
                </button>

            </div>


            {{-- ================= UPLOAD ================= --}}
            <div class="rounded-xl border border-dashed border-slate-300 p-5">

                <label class="mb-3 block text-sm font-semibold">
                    Thêm hình ảnh
                </label>

                <input
                    id="imageFiles"
                    type="file"
                    multiple
                    accept="image/*"
                    class="w-full rounded-lg border border-slate-300 px-4 py-3"
                >


                {{-- Preview ảnh --}}
                <div
                    id="imagePreview"
                    class="mt-5 grid grid-cols-2 gap-4 sm:grid-cols-4"
                >
                </div>


                <button
                    type="button"
                    onclick="uploadImages()"
                    class="mt-5 rounded-lg px-5 py-3 font-semibold text-white"
                    style="background: rgb(51, 101, 110);"
                >
                    Tải ảnh lên
                </button>

            </div>


            {{-- ================= SẮP XẾP ẢNH ================= --}}
            <div class="mt-7">

                <div class="mb-4 flex items-center justify-between gap-3">

                    <div>

                        <h3 class="text-lg font-bold">
                            Hình ảnh hiện tại
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            Dùng nút ↑ ↓ để thay đổi thứ tự. Chọn ảnh chính nếu cần.
                        </p>

                    </div>


                    <button
                        type="button"
                        onclick="saveImageOrder()"
                        class="rounded-lg px-4 py-2 text-sm font-semibold text-white"
                        style="background: rgb(51, 101, 110);"
                    >
                        Lưu thứ tự
                    </button>

                </div>


                <div
                    id="currentImages"
                    class="grid grid-cols-2 gap-4 sm:grid-cols-4"
                >
                </div>

            </div>


            {{-- Đóng --}}
            <div class="mt-7 flex justify-end">

                <button
                    type="button"
                    onclick="closeImageModal()"
                    class="rounded-lg border border-slate-300 px-5 py-3 font-semibold"
                >
                    Đóng
                </button>

            </div>

        </div>

    </div>



    <script>

        // =========================================================
        // PRODUCT ID
        // =========================================================

        const productId = "{{ $id ?? '' }}";


        // =========================================================
        // API BASE URL
        // =========================================================

        const API_BASE_URL = '/api/v1';


        // =========================================================
        // DANH SÁCH ẢNH HIỆN TẠI
        // =========================================================

        let currentImages = [];


        // =========================================================
        // API HELPER
        // =========================================================

        function getAuthHeaders(includeContentType = false) {

            const headers = {
                'Accept': 'application/json'
            };

            if (includeContentType) {
                headers['Content-Type'] =
                    'application/json';
            }

            return headers;
        }


        function getErrorCode(result) {

            return result?.errors?.error_code ||
                   result?.error_code ||
                   'UNKNOWN_ERROR';
        }


        function showApiError(
            result,
            defaultMessage = 'Có lỗi xảy ra.'
        ) {

            const errorCode =
                getErrorCode(result);


            if (errorCode === 'FORBIDDEN') {

                alert(
                    'Bạn không có quyền thực hiện thao tác này.'
                );

                return;
            }


            if (errorCode === 'PRODUCT_NOT_FOUND') {

                alert(
                    'Không tìm thấy sản phẩm.'
                );

                return;
            }


            if (errorCode === 'DUPLICATE_VALUE') {

                alert(
                    'Dữ liệu sản phẩm bị trùng.'
                );

                return;
            }


            if (errorCode === 'VALIDATION_ERROR') {

                alert(
                    'Dữ liệu không hợp lệ.'
                );

                return;
            }


            if (errorCode === 'INVALID_IMAGE_ORDER') {

                alert(
                    'Thứ tự hình ảnh không hợp lệ.'
                );

                return;
            }


            if (errorCode === 'FILE_TOO_LARGE') {

                alert(
                    'File ảnh quá lớn.'
                );

                return;
            }


            if (errorCode === 'UNSUPPORTED_MEDIA') {

                alert(
                    'Định dạng ảnh không được hỗ trợ.'
                );

                return;
            }


            alert(
                result?.message ||
                defaultMessage
            );
        }


        // =========================================================
        // LOAD PRODUCT DETAIL
        // GET /api/v1/admin/products/{id}
        // =========================================================

        async function loadProductDetail() {

            try {

                const response =
                    await fetch(
                        `${API_BASE_URL}/admin/products/${productId}`,
                        {
                            method: 'GET',
                            credentials: 'same-origin',
                            headers: getAuthHeaders()
                        }
                    );


                const result =
                    await response.json();


                if (
                    !response.ok ||
                    result.success !== true
                ) {

                    console.error(
                        'Load product detail error:',
                        getErrorCode(result)
                    );

                    showApiError(
                        result,
                        'Không thể lấy thông tin sản phẩm.'
                    );

                    return;
                }


                const product =
                    result.data;


                console.log(
                    'Chi tiết sản phẩm:',
                    product
                );


                // Tên
                document.getElementById(
                    'productName'
                ).textContent =
                    product.name || '—';


                // SKU
                document.getElementById(
                    'productSku'
                ).textContent =
                    `SKU: ${product.sku || '—'}`;


                // Giá
                document.getElementById(
                    'productPrice'
                ).textContent =
                    Number(
                        product.price || 0
                    ).toLocaleString('vi-VN') + ' đ';


                // Danh mục
                document.getElementById(
                    'productCategory'
                ).textContent =
                    product.category?.name ||
                    'Chưa có danh mục';


                // Đơn vị
                document.getElementById(
                    'productUnit'
                ).textContent =
                    product.unit || '—';


                // Xuất xứ
                document.getElementById(
                    'productOrigin'
                ).textContent =
                    product.origin || '—';


                // Tồn kho
                document.getElementById(
                    'productQuantity'
                ).textContent =
                    product.available_quantity ?? 0;


                // Trạng thái
                const statusElement =
                    document.getElementById(
                        'productStatus'
                    );


                if (product.status === 'active') {

                    statusElement.textContent =
                        'Đang bán';

                    statusElement.className =
                        'inline-flex w-fit rounded-full bg-green-100 px-4 py-2 text-sm font-semibold text-green-700';

                } else {

                    statusElement.textContent =
                        'Ngừng bán';

                    statusElement.className =
                        'inline-flex w-fit rounded-full bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-600';
                }


                // Mô tả
                document.getElementById(
                    'productDescription'
                ).textContent =
                    product.description_html ||
                    'Chưa có mô tả.';


                // Form sửa
                document.getElementById(
                    'editName'
                ).value =
                    product.name || '';


                document.getElementById(
                    'editPrice'
                ).value =
                    product.price || 0;


                document.getElementById(
                    'editOrigin'
                ).value =
                    product.origin || '';


                document.getElementById(
                    'editStatus'
                ).value =
                    product.status || 'active';


                // Hình ảnh
                renderMainImage(product);

                renderImages(
                    product.images || []
                );

            }

            catch (error) {

                console.error(
                    'Load product detail error:',
                    error
                );

                alert(
                    'Không thể kết nối đến API.'
                );
            }
        }


        // =========================================================
        // RENDER MAIN IMAGE
        // =========================================================

        function renderMainImage(product) {

            const container =
                document.getElementById(
                    'mainImageContainer'
                );


            let imageUrl =
                product.primary_image_url;


            if (
                !imageUrl &&
                product.images &&
                product.images.length > 0
            ) {

                imageUrl =
                    product.images[0].url;
            }


            container.innerHTML = '';


            if (!imageUrl) {

                const message =
                    document.createElement('div');

                message.className =
                    'text-center text-slate-400';

                message.textContent =
                    'Không có hình ảnh';

                container.appendChild(
                    message
                );

                return;
            }


            // Nếu API trả về đường dẫn tương đối
            // thì dùng domain hiện tại.
            if (
                !imageUrl.startsWith('http')
            ) {

                imageUrl =
                    window.location.origin +
                    imageUrl;
            }


            const image =
                document.createElement('img');

            image.src =
                imageUrl;

            image.alt =
                product.name ||
                'Sản phẩm';

            image.className =
                'max-h-[420px] w-full rounded-lg object-contain';


            container.appendChild(
                image
            );
        }


        // =========================================================
        // RENDER IMAGES
        // =========================================================

        function renderImages(images) {

            currentImages =
                [...images];


            const container =
                document.getElementById(
                    'currentImages'
                );


            container.innerHTML = '';


            if (!currentImages.length) {

                const message =
                    document.createElement('p');

                message.className =
                    'col-span-full text-sm text-slate-400';

                message.textContent =
                    'Chưa có hình ảnh.';

                container.appendChild(
                    message
                );

                return;
            }


            currentImages.forEach(
                function (image, index) {

                    let imageUrl =
                        image.url || '';


                    if (
                        imageUrl &&
                        !imageUrl.startsWith('http')
                    ) {

                        imageUrl =
                            window.location.origin +
                            imageUrl;
                    }


                    const div =
                        document.createElement('div');

                    div.className =
                        'rounded-lg border border-slate-200 bg-slate-50 p-3';


                    // =========================
                    // ẢNH
                    // =========================

                    const img =
                        document.createElement('img');

                    img.src =
                        imageUrl;

                    img.alt =
                        image.alt_text ||
                        'Hình ảnh sản phẩm';

                    img.className =
                        'h-32 w-full rounded object-cover';


                    div.appendChild(img);


                    // =========================
                    // THỨ TỰ
                    // =========================

                    const orderText =
                        document.createElement('p');

                    orderText.className =
                        'mt-2 text-center text-sm font-semibold';

                    orderText.textContent =
                        `Thứ tự: ${index + 1}`;


                    div.appendChild(
                        orderText
                    );


                    // =========================
                    // ẢNH CHÍNH
                    // =========================

                    if (image.is_primary) {

                        const primaryText =
                            document.createElement('p');

                        primaryText.className =
                            'mt-1 text-center text-xs font-semibold text-green-600';

                        primaryText.textContent =
                            'Ảnh chính';


                        div.appendChild(
                            primaryText
                        );
                    }


                    // =========================
                    // RADIO ẢNH CHÍNH
                    // =========================

                    const label =
                        document.createElement('label');

                    label.className =
                        'mt-3 flex items-center justify-center gap-2 text-sm';


                    const radio =
                        document.createElement('input');

                    radio.type =
                        'radio';

                    radio.name =
                        'primaryImage';

                    radio.value =
                        image.id;

                    radio.checked =
                        Boolean(
                            image.is_primary
                        );


                    label.appendChild(
                        radio
                    );


                    const labelText =
                        document.createTextNode(
                            'Ảnh chính'
                        );


                    label.appendChild(
                        labelText
                    );


                    div.appendChild(
                        label
                    );


                    // =========================
                    // NÚT ↑ ↓
                    // =========================

                    const buttonWrapper =
                        document.createElement('div');

                    buttonWrapper.className =
                        'mt-3 flex justify-center gap-2';


                    // Nút lên
                    const upButton =
                        document.createElement('button');

                    upButton.type =
                        'button';

                    upButton.className =
                        'rounded border border-slate-300 px-3 py-1 text-sm hover:bg-slate-100';

                    upButton.textContent =
                        '↑';

                    upButton.disabled =
                        index === 0;


                    upButton.addEventListener(
                        'click',
                        function () {
                            moveImageUp(index);
                        }
                    );


                    // Nút xuống
                    const downButton =
                        document.createElement('button');

                    downButton.type =
                        'button';

                    downButton.className =
                        'rounded border border-slate-300 px-3 py-1 text-sm hover:bg-slate-100';

                    downButton.textContent =
                        '↓';

                    downButton.disabled =
                        index ===
                        currentImages.length - 1;


                    downButton.addEventListener(
                        'click',
                        function () {
                            moveImageDown(index);
                        }
                    );


                    buttonWrapper.appendChild(
                        upButton
                    );

                    buttonWrapper.appendChild(
                        downButton
                    );


                    div.appendChild(
                        buttonWrapper
                    );


                    container.appendChild(
                        div
                    );
                }
            );
        }


        // =========================================================
        // MOVE IMAGE UP
        // =========================================================

        function moveImageUp(index) {

            if (index <= 0) {
                return;
            }


            const temp =
                currentImages[index - 1];


            currentImages[index - 1] =
                currentImages[index];


            currentImages[index] =
                temp;


            renderImages(
                currentImages
            );
        }


        // =========================================================
        // MOVE IMAGE DOWN
        // =========================================================

        function moveImageDown(index) {

            if (
                index >=
                currentImages.length - 1
            ) {
                return;
            }


            const temp =
                currentImages[index + 1];


            currentImages[index + 1] =
                currentImages[index];


            currentImages[index] =
                temp;


            renderImages(
                currentImages
            );
        }


        // =========================================================
        // SAVE IMAGE ORDER
        // PATCH /api/v1/admin/products/{id}/images/reorder
        // =========================================================

        async function saveImageOrder() {

            if (!currentImages.length) {

                alert(
                    'Sản phẩm chưa có hình ảnh.'
                );

                return;
            }


            try {

                const primaryId =
                    document.querySelector(
                        'input[name="primaryImage"]:checked'
                    )?.value;


                const images =
                    currentImages.map(
                        function (image, index) {

                            return {
                                id: image.id,

                                sort_order:
                                    index + 1,

                                is_primary:
                                    String(image.id) ===
                                    String(primaryId)
                            };
                        }
                    );


                console.log(
                    'Dữ liệu reorder gửi lên:',
                    images
                );


                const response =
                    await fetch(
                        `${API_BASE_URL}/admin/products/${productId}/images/reorder`,
                        {
                            method: 'PATCH',
                            credentials: 'same-origin',

                            headers:
                                getAuthHeaders(true),

                            body:
                                JSON.stringify({
                                    images: images
                                })
                        }
                    );


                const result =
                    await response.json();


                if (
                    !response.ok ||
                    result.success !== true
                ) {

                    console.error(
                        'Reorder image error:',
                        getErrorCode(result)
                    );

                    showApiError(
                        result,
                        'Không thể sắp xếp hình ảnh.'
                    );

                    return;
                }


                alert(
                    'Cập nhật thứ tự hình ảnh thành công.'
                );


                if (
                    Array.isArray(
                        result.data
                    )
                ) {

                    renderImages(
                        result.data
                    );
                }

            }

            catch (error) {

                console.error(
                    'Reorder image error:',
                    error
                );

                alert(
                    'Không thể kết nối đến API.'
                );
            }
        }


        // =========================================================
        // OPEN EDIT MODAL
        // =========================================================

        function openEditModal() {

            const modal =
                document.getElementById(
                    'editModal'
                );


            modal.classList.remove(
                'hidden'
            );

            modal.classList.add(
                'flex'
            );
        }


        // =========================================================
        // CLOSE EDIT MODAL
        // =========================================================

        function closeEditModal() {

            const modal =
                document.getElementById(
                    'editModal'
                );


            modal.classList.add(
                'hidden'
            );

            modal.classList.remove(
                'flex'
            );
        }


        // =========================================================
        // UPDATE PRODUCT
        // PATCH /api/v1/admin/products/{id}
        // =========================================================

        async function updateProduct() {

            const name =
                document.getElementById(
                    'editName'
                ).value.trim();


            const price =
                document.getElementById(
                    'editPrice'
                ).value;


            const origin =
                document.getElementById(
                    'editOrigin'
                ).value.trim();


            const status =
                document.getElementById(
                    'editStatus'
                ).value;


            if (!name) {

                alert(
                    'Vui lòng nhập tên sản phẩm.'
                );

                return;
            }


            if (
                !price ||
                Number(price) < 0
            ) {

                alert(
                    'Giá sản phẩm không hợp lệ.'
                );

                return;
            }


            try {

                const response =
                    await fetch(
                        `${API_BASE_URL}/admin/products/${productId}`,
                        {
                            method: 'PATCH',
                            credentials: 'same-origin',

                            headers:
                                getAuthHeaders(true),

                            body:
                                JSON.stringify({
                                    name: name,

                                    price:
                                        Number(price),

                                    origin:
                                        origin,

                                    status:
                                        status
                                })
                        }
                    );


                const result =
                    await response.json();


                if (
                    !response.ok ||
                    result.success !== true
                ) {

                    console.error(
                        'Update product error:',
                        getErrorCode(result)
                    );

                    showApiError(
                        result,
                        'Không thể cập nhật sản phẩm.'
                    );

                    return;
                }


                alert(
                    'Cập nhật sản phẩm thành công.'
                );


                closeEditModal();


                await loadProductDetail();
            }

            catch (error) {

                console.error(
                    'Update product error:',
                    error
                );

                alert(
                    'Không thể kết nối đến API.'
                );
            }
        }


        // =========================================================
        // OPEN IMAGE MODAL
        // =========================================================

        function openImageModal() {

            const modal =
                document.getElementById(
                    'imageModal'
                );


            modal.classList.remove(
                'hidden'
            );

            modal.classList.add(
                'flex'
            );
        }


        // =========================================================
        // CLOSE IMAGE MODAL
        // =========================================================

        function closeImageModal() {

            const modal =
                document.getElementById(
                    'imageModal'
                );


            modal.classList.add(
                'hidden'
            );

            modal.classList.remove(
                'flex'
            );
        }


        // =========================================================
        // IMAGE PREVIEW
        // =========================================================

        document
            .getElementById('imageFiles')
            .addEventListener(
                'change',
                function (event) {

                    const preview =
                        document.getElementById(
                            'imagePreview'
                        );


                    preview.innerHTML = '';


                    Array
                        .from(event.target.files)
                        .forEach(
                            function (file) {

                                const reader =
                                    new FileReader();


                                reader.onload =
                                    function (e) {

                                        const div =
                                            document.createElement(
                                                'div'
                                            );


                                        div.className =
                                            'overflow-hidden rounded-lg border border-slate-200';


                                        const image =
                                            document.createElement(
                                                'img'
                                            );


                                        image.src =
                                            e.target.result;

                                        image.alt =
                                            file.name;

                                        image.className =
                                            'h-32 w-full object-cover';


                                        div.appendChild(
                                            image
                                        );


                                        preview.appendChild(
                                            div
                                        );
                                    };


                                reader.readAsDataURL(
                                    file
                                );
                            }
                        );
                }
            );


        // =========================================================
        // UPLOAD IMAGES
        // POST /api/v1/admin/products/{id}/images
        // =========================================================

        async function uploadImages() {

            const input =
                document.getElementById(
                    'imageFiles'
                );


            if (!input.files.length) {

                alert(
                    'Vui lòng chọn hình ảnh.'
                );

                return;
            }


            const formData =
                new FormData();


            Array
                .from(input.files)
                .forEach(
                    function (file) {

                        formData.append(
                            'images[]',
                            file
                        );

                        formData.append(
                            'alt_text[]',
                            file.name
                        );
                    }
                );


            try {

                const response =
                    await fetch(
                        `${API_BASE_URL}/admin/products/${productId}/images`,
                        {
                            method: 'POST',
                            credentials: 'same-origin',

                            headers:
                                getAuthHeaders(),

                            body:
                                formData
                        }
                    );


                const result =
                    await response.json();


                if (
                    !response.ok ||
                    result.success !== true
                ) {

                    console.error(
                        'Upload image error:',
                        getErrorCode(result)
                    );

                    showApiError(
                        result,
                        'Không thể tải ảnh lên.'
                    );

                    return;
                }


                alert(
                    'Tải ảnh lên thành công.'
                );


                input.value = '';


                document.getElementById(
                    'imagePreview'
                ).innerHTML = '';


                await loadProductDetail();
            }

            catch (error) {

                console.error(
                    'Upload image error:',
                    error
                );

                alert(
                    'Không thể kết nối đến API.'
                );
            }
        }


        // =========================================================
        // INIT
        // =========================================================

        loadProductDetail();

    </script>

</body>

</html>

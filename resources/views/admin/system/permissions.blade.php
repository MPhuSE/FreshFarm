@extends('layouts.admin')

@section('title', 'Quản lý bài viết')

@section('page-header')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">
                Quản lý bài viết
            </h1>
            <p class="text-gray-500 mt-1">
                Quản lý danh sách và nội dung bài viết
            </p>
        </div>

        <button
            onclick="openCreateModal()"
            class="bg-green-600 text-white px-5 py-2 rounded-lg hover:bg-green-700"
        >

            + Thêm bài viết

        </button>

    </div>



    <!-- TÌM KIẾM + LỌC -->

    <div class="bg-white p-4 rounded-lg shadow mb-6">

        <div class="flex flex-wrap gap-4 items-end">

            <!-- q -->

            <div>

                <label class="block text-sm font-medium mb-1">

                    Tìm kiếm

                </label>

                <input
                    type="text"
                    id="search"
                    placeholder="Tên bài viết..."
                    class="border rounded-lg px-3 py-2 w-64"
                >

            </div>



            <!-- status -->

            <div>

                <label class="block text-sm font-medium mb-1">

                    Trạng thái

                </label>

                <select
                    id="statusFilter"
                    class="border rounded-lg px-3 py-2"
                >

                    <option value="">

                        Tất cả

                    </option>

                    <option value="published">

                        Published

                    </option>

                    <option value="draft">

                        Draft

                    </option>

                </select>

            </div>



            <button
                onclick="loadPosts(1)"
                class="bg-green-600 text-white px-5 py-2 rounded-lg"
            >

                Tìm kiếm

            </button>

        </div>

    </div>



    <!-- DANH SÁCH -->

    <div class="bg-white rounded-lg shadow overflow-hidden">

        <div class="p-5 border-b">

            <h2 class="text-lg font-bold">

                Danh sách bài viết

            </h2>

        </div>



        <div class="overflow-x-auto">

            <table class="w-full">

                <thead class="bg-gray-50">

                    <tr>

                        <th class="text-left px-5 py-3">

                            ID

                        </th>

                        <th class="text-left px-5 py-3">

                            Tiêu đề

                        </th>

                        <th class="text-left px-5 py-3">

                            Slug

                        </th>

                        <th class="text-left px-5 py-3">

                            Mô tả

                        </th>

                        <th class="text-left px-5 py-3">

                            Trạng thái

                        </th>

                        <th class="text-left px-5 py-3">

                            Ngày đăng

                        </th>

                        <th class="text-center px-5 py-3">

                            Thao tác

                        </th>

                    </tr>

                </thead>



                <tbody id="postsTable">

                    <tr>

                        <td
                            colspan="7"
                            class="text-center py-6 text-gray-500"
                        >

                            Đang tải dữ liệu...

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>



        <!-- PHÂN TRANG -->

        <div
            id="pagination"
            class="p-4 border-t flex justify-between items-center"
        ></div>

    </div>

</div>



<div
    id="postModal"
    class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center"
>

    <div class="bg-white rounded-lg p-6 w-full max-w-2xl">

        <h2
            id="modalTitle"
            class="text-xl font-bold mb-5"
        >

            Thêm bài viết

        </h2>



        <input
            type="hidden"
            id="postId"
        >



        <!-- TITLE -->

        <div class="mb-4">

            <label class="block text-sm font-medium mb-1">

                Tiêu đề

            </label>

            <input
                type="text"
                id="postTitle"
                class="border rounded-lg px-3 py-2 w-full"
            >

        </div>



        <div class="mb-4">

            <label class="block text-sm font-medium mb-1">

                Mô tả ngắn

            </label>

            <textarea
                id="postExcerpt"
                rows="3"
                class="border rounded-lg px-3 py-2 w-full"
            ></textarea>

        </div>



        <div class="mb-4">

            <label class="block text-sm font-medium mb-1">

                Nội dung

            </label>

            <textarea
                id="postContent"
                rows="8"
                class="border rounded-lg px-3 py-2 w-full"
                placeholder="<p>Nội dung bài viết...</p>"
            ></textarea>

            <p class="text-xs text-gray-500 mt-1">

                Nội dung HTML phải được server sanitize trước khi lưu.

            </p>

        </div>



        <div class="mb-4">

            <label class="block text-sm font-medium mb-1">

                Thumbnail

            </label>

            <input
                type="text"
                id="postThumbnail"
                placeholder="URL thumbnail"
                class="border rounded-lg px-3 py-2 w-full"
            >

        </div>



        <div class="mb-5">

            <label class="block text-sm font-medium mb-1">

                Trạng thái

            </label>

            <select
                id="postStatus"
                class="border rounded-lg px-3 py-2 w-full"
            >

                <option value="draft">

                    Draft

                </option>

                <option value="published">

                    Published

                </option>

            </select>

        </div>



        <div class="flex justify-end gap-2">

            <button
                onclick="closeModal()"
                class="px-4 py-2 border rounded-lg"
            >

                Hủy

            </button>

            <button
                onclick="savePost()"
                class="px-4 py-2 bg-green-600 text-white rounded-lg"
            >

                Lưu

            </button>

        </div>

    </div>

@endsection

@push('scripts')
<script>

const API_BASE_URL = '/api/v1';

let currentPage = 1;



async function loadPosts(page = 1) {

    currentPage = page;

    const q =
        document.getElementById('search').value.trim();

    const status =
        document.getElementById('statusFilter').value;



    let url =
        `${API_BASE_URL}/admin/posts?page=${page}`;



    if (q) {

        url +=
            `&q=${encodeURIComponent(q)}`;

    }



    if (status) {

        url +=
            `&status=${encodeURIComponent(status)}`;

    }



    try {

        const response = await fetch(url, {

            method: 'GET',

            headers: {

                'Accept':
                    'application/json',

                ...AdminApi.headers()

            }

        });



        const result =
            await response.json();



        if (!response.ok) {

            const errorCode =
                result.errors?.error_code ||
                result.error_code;



            if (errorCode === 'FORBIDDEN') {

                alert(
                    'Bạn không có quyền xem danh sách bài viết'
                );

            } else {

                alert(
                    'Không thể tải danh sách bài viết'
                );

            }

            return;

        }



        renderPosts(result.data || []);

        renderPagination(result.meta);



    } catch (error) {

        console.error(error);

        alert(
            'Không thể kết nối đến API'
        );

    }

}



function renderPosts(posts) {

    const table =
        document.getElementById('postsTable');



    table.innerHTML = '';



    if (!posts.length) {

        table.innerHTML = `

            <tr>

                <td
                    colspan="7"
                    class="text-center py-6 text-gray-500"
                >

                    Không có bài viết

                </td>

            </tr>

        `;

        return;

    }



    posts.forEach(post => {

        table.innerHTML += `

            <tr class="border-t">

                <td class="px-5 py-3">

                    ${post.id}

                </td>

                <td class="px-5 py-3 font-medium">

                    ${escapeHtml(post.title)}

                </td>

                <td class="px-5 py-3">

                    ${escapeHtml(post.slug)}

                </td>

                <td class="px-5 py-3">

                    ${escapeHtml(post.excerpt || '')}

                </td>

                <td class="px-5 py-3">

                    ${
                        post.status === 'published'

                        ? `

                            <span class="px-2 py-1 rounded bg-green-100 text-green-700">

                                Published

                            </span>

                        `

                        : `

                            <span class="px-2 py-1 rounded bg-gray-100 text-gray-700">

                                Draft

                            </span>

                        `

                    }

                </td>

                <td class="px-5 py-3">

                    ${formatDate(post.published_at)}

                </td>

                <td class="px-5 py-3 text-center">

                    <button
                        onclick="editPost(${post.id})"
                        class="text-blue-600 hover:underline"
                    >

                        Sửa

                    </button>

                </td>

            </tr>

        `;

    });

}



function renderPagination(meta) {

    const pagination =
        document.getElementById('pagination');



    if (!meta || meta.last_page <= 1) {

        pagination.innerHTML = '';

        return;

    }



    pagination.innerHTML = `

        <div class="text-sm text-gray-500">

            Hiển thị

            ${meta.from || 0}

            -

            ${meta.to || 0}

            / ${meta.total || 0} bài viết

        </div>



        <div class="flex gap-2">

            <button
                onclick="loadPosts(${meta.current_page - 1})"
                ${meta.current_page <= 1 ? 'disabled' : ''}
                class="px-3 py-1 border rounded"
            >

                Trước

            </button>



            <span class="px-3 py-1">

                Trang

                ${meta.current_page}

                /

                ${meta.last_page}

            </span>



            <button
                onclick="loadPosts(${meta.current_page + 1})"
                ${meta.current_page >= meta.last_page ? 'disabled' : ''}
                class="px-3 py-1 border rounded"
            >

                Sau

            </button>

        </div>

    `;

}



function openCreateModal() {

    document.getElementById('modalTitle').textContent =
        'Thêm bài viết';

    document.getElementById('postId').value = '';

    document.getElementById('postTitle').value = '';

    document.getElementById('postExcerpt').value = '';

    document.getElementById('postContent').value = '';

    document.getElementById('postThumbnail').value = '';

    document.getElementById('postStatus').value =
        'draft';



    document
        .getElementById('postModal')
        .classList
        .remove('hidden');

}



// =================================================
// ĐÓNG MODAL
// =================================================

function closeModal() {

    document
        .getElementById('postModal')
        .classList
        .add('hidden');

}



async function editPost(id) {

    try {

        /*
         * API hiện tại không có:
         * GET /api/v1/admin/posts/{id}
         *
         * Vì vậy lấy lại danh sách của trang hiện tại
         * rồi tìm bài viết theo id.
         */

        const response = await fetch(

            `${API_BASE_URL}/admin/posts?page=${currentPage}`,

            {

                method: 'GET',

                headers: {

                    'Accept': 'application/json',

                    ...AdminApi.headers(true)

                }

            }

        );



        const result =
            await response.json();



        if (!response.ok) {

            const errorCode =
                result.errors?.error_code ||
                result.error_code;



            if (errorCode === 'FORBIDDEN') {

                alert(
                    'Bạn không có quyền xem danh sách bài viết'
                );

            } else {

                alert(
                    'Không thể tải dữ liệu bài viết'
                );

            }

            return;

        }



        const post =
            (result.data || []).find(
                item => item.id == id
            );



        if (!post) {

            alert(
                'Không tìm thấy bài viết'
            );

            return;

        }



        document.getElementById('modalTitle').textContent =
            'Cập nhật bài viết';



        document.getElementById('postId').value =
            post.id;



        document.getElementById('postTitle').value =
            post.title || '';



        document.getElementById('postExcerpt').value =
            post.excerpt || '';



        /*
         * API GET danh sách hiện tại chỉ trả:
         * id, title, slug, excerpt, status, published_at
         *
         * Không có content_html và thumbnail
         * nên không tự đoán dữ liệu.
         */

        document.getElementById('postContent').value = '';

        document.getElementById('postThumbnail').value = '';



        document.getElementById('postStatus').value =
            post.status || 'draft';



        document
            .getElementById('postModal')
            .classList
            .remove('hidden');



    } catch (error) {

        console.error(error);

        alert(
            'Không thể kết nối đến API'
        );

    }

}



async function savePost() {

    const id =
        document.getElementById('postId').value;



    const title =
        document.getElementById('postTitle').value.trim();



    const excerpt =
        document.getElementById('postExcerpt').value.trim();



    const contentHtml =
        document.getElementById('postContent').value.trim();



    const thumbnail =
        document.getElementById('postThumbnail').value.trim();



    const status =
        document.getElementById('postStatus').value;



    if (!title) {

        alert('Vui lòng nhập tiêu đề');

        return;

    }



    if (!id && !contentHtml) {

        alert('Vui lòng nhập nội dung bài viết');

        return;

    }



    try {



        if (!id) {

            const body = {

                title: title,

                content_html: contentHtml,

                status: status

            };



            if (excerpt) {

                body.excerpt = excerpt;

            }



            if (thumbnail) {

                body.thumbnail = thumbnail;

            }



            const response = await fetch(

                `${API_BASE_URL}/admin/posts`,

                {

                    method: 'POST',

                    headers: {

                        'Accept':
                            'application/json',

                        'Content-Type':
                            'application/json',

                        ...AdminApi.headers(true)

                    },

                    body:
                        JSON.stringify(body)

                }

            );



            const result =
                await response.json();



            if (!response.ok) {

                const errorCode =
                    result.errors?.error_code ||
                    result.error_code;



                if (
                    errorCode ===
                    'SLUG_EXISTS'
                ) {

                    alert(
                        'Slug bài viết đã tồn tại'
                    );

                }

                else if (
                    errorCode ===
                    'VALIDATION_ERROR'
                ) {

                    alert(
                        'Dữ liệu bài viết không hợp lệ'
                    );

                }

                else {

                    alert(
                        'Không thể tạo bài viết'
                    );

                }

                return;

            }



            alert(
                'Tạo bài viết thành công'
            );

        }



        else {

            const body = {};



            if (title) {

                body.title = title;

            }



            if (excerpt) {

                body.excerpt = excerpt;

            }



            if (contentHtml) {

                body.content_html =
                    contentHtml;

            }



            if (thumbnail) {

                body.thumbnail =
                    thumbnail;

            }



            if (status) {

                body.status =
                    status;

            }



            const response = await fetch(

                `${API_BASE_URL}/admin/posts/${id}`,

                {

                    method: 'PATCH',

                    headers: {

                        'Accept':
                            'application/json',

                        'Content-Type':
                            'application/json',

                        'Authorization':
                            ...AdminApi.headers(true)

                    },

                    body:
                        JSON.stringify(body)

                }

            );



            const result =
                await response.json();



            if (!response.ok) {

                const errorCode =
                    result.errors?.error_code ||
                    result.error_code;



                if (
                    errorCode ===
                    'POST_NOT_FOUND'
                ) {

                    alert(
                        'Không tìm thấy bài viết'
                    );

                }

                else if (
                    errorCode ===
                    'VALIDATION_ERROR'
                ) {

                    alert(
                        'Dữ liệu bài viết không hợp lệ'
                    );

                }

                else {

                    alert(
                        'Không thể cập nhật bài viết'
                    );

                }

                return;

            }



            alert(
                'Cập nhật bài viết thành công'
            );

        }



        closeModal();

        loadPosts(currentPage);



    } catch (error) {

        console.error(error);

        alert(
            'Không thể kết nối đến API'
        );

    }

}



function escapeHtml(value) {

    return String(value || '')

        .replace(/&/g, '&amp;')

        .replace(/</g, '&lt;')

        .replace(/>/g, '&gt;')

        .replace(/"/g, '&quot;')

        .replace(/'/g, '&#039;');

}



function formatDate(date) {

    if (!date) {

        return '';

    }



    return new Date(date)

        .toLocaleDateString('vi-VN');

}



document.addEventListener(

    'DOMContentLoaded',

    function () {

        loadPosts(1);

    }

);

</script>
@endpush
@extends('layouts.admin')

@section('title', 'Quản lý người dùng')

@section('page-header')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">
                Quản lý người dùng
            </h1>
            <p class="text-gray-500 mt-1">
                Quản lý tài khoản, trạng thái và vai trò người dùng
            </p>
        </div>
    </div>
@endsection

@section('content')


  
    <div class="bg-white p-4 rounded-lg shadow mb-6">

        <div class="flex flex-wrap gap-4 items-end">

            <div>
                <label class="block text-sm font-medium mb-1">
                    Tìm kiếm
                </label>

                <input
                    type="text"
                    id="search"
                    placeholder="Tên, email, số điện thoại"
                    class="border rounded-lg px-3 py-2 w-64"
                >
            </div>


       
            <div>
                <label class="block text-sm font-medium mb-1">
                    Vai trò
                </label>

                <select
                    id="role"
                    class="border rounded-lg px-3 py-2"
                >
                    <option value="">Tất cả</option>
                    <option value="customer">Customer</option>
                    <option value="staff">Staff</option>
                    <option value="admin">Admin</option>
                </select>
            </div>


            <div>
                <label class="block text-sm font-medium mb-1">
                    Trạng thái
                </label>

                <select
                    id="status"
                    class="border rounded-lg px-3 py-2"
                >
                    <option value="">Tất cả</option>
                    <option value="active">Active</option>
                    <option value="locked">Locked</option>
                </select>
            </div>


            <button
                onclick="loadUsers(1)"
                class="bg-green-600 text-white px-5 py-2 rounded-lg hover:bg-green-700"
            >
                Tìm kiếm
            </button>

        </div>

    </div>


    <div class="bg-white rounded-lg shadow overflow-hidden">

        <div class="p-5 border-b">
            <h2 class="text-lg font-bold">
                Danh sách người dùng
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
                            Họ tên
                        </th>

                        <th class="text-left px-5 py-3">
                            Email
                        </th>

                        <th class="text-left px-5 py-3">
                            Số điện thoại
                        </th>

                        <th class="text-left px-5 py-3">
                            Vai trò
                        </th>

                        <th class="text-left px-5 py-3">
                            Trạng thái
                        </th>

                        <th class="text-left px-5 py-3">
                            Ngày tạo
                        </th>

                        <th class="text-center px-5 py-3">
                            Thao tác
                        </th>

                    </tr>

                </thead>


                <tbody id="usersTable">

                    <tr>
                        <td
                            colspan="8"
                            class="text-center py-6 text-gray-500"
                        >
                            Đang tải dữ liệu...
                        </td>
                    </tr>

                </tbody>

            </table>

        </div>


        <div
            id="pagination"
            class="p-4 border-t flex justify-between items-center"
        ></div>

    </div>

</div>


<div
    id="userModal"
    class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center"
>

    <div class="bg-white rounded-lg p-6 w-96">

        <h2 class="text-xl font-bold mb-5">
            Cập nhật người dùng
        </h2>

        <input
            type="hidden"
            id="userId"
        >


 
        <div class="mb-4">

            <label class="block text-sm font-medium mb-1">
                Họ tên
            </label>

            <input
                type="text"
                id="userName"
                readonly
                class="border rounded-lg px-3 py-2 w-full bg-gray-100"
            >

        </div>


      
        <div class="mb-4">

            <label class="block text-sm font-medium mb-1">
                Trạng thái
            </label>

            <select
                id="userStatus"
                class="border rounded-lg px-3 py-2 w-full"
            >
                <option value="active">
                    Active
                </option>

                <option value="locked">
                    Locked
                </option>
            </select>

        </div>


        <div class="mb-5">

            <label class="block text-sm font-medium mb-1">
                Vai trò
            </label>

            <select
                id="userRole"
                class="border rounded-lg px-3 py-2 w-full"
            >
                <option value="customer">
                    Customer
                </option>

                <option value="staff">
                    Staff
                </option>

                <option value="admin">
                    Admin
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
                onclick="updateUser()"
                class="px-4 py-2 bg-green-600 text-white rounded-lg"
            >
                Lưu
            </button>

        </div>

    </div>

</div>

@endsection

@push('scripts')
<script>

const API_BASE_URL = '/api/v1';

let currentPage = 1;




async function loadUsers(page = 1) {

    currentPage = page;

    const q =
        document.getElementById('search').value.trim();

    const role =
        document.getElementById('role').value;

    const status =
        document.getElementById('status').value;


    let url =
        `${API_BASE_URL}/admin/users?page=${page}`;


    // q
    if (q) {
        url += `&q=${encodeURIComponent(q)}`;
    }


    // role
    if (role) {
        url += `&role=${role}`;
    }


    // status
    if (status) {
        url += `&status=${status}`;
    }


    try {

        const response = await fetch(url, {

            method: 'GET',

            headers: {
                'Accept': 'application/json',
                ...AdminApi.headers()
            }

        });


        const result = await response.json();


        // ==========================================
        // XỬ LÝ LỖI THEO error_code
        // ==========================================

        if (!response.ok) {

            const errorCode =
                result.errors?.error_code ||
                result.error_code;


            if (errorCode === 'FORBIDDEN') {

                alert('Bạn không có quyền xem danh sách người dùng');

            } else {

                alert('Không thể tải danh sách người dùng');

            }

            return;
        }


    

        renderUsers(result.data);

        renderPagination(result.meta);


    } catch (error) {

        console.error(error);

        alert('Không thể kết nối đến API');

    }

}



function renderUsers(users) {

    const table =
        document.getElementById('usersTable');

    table.innerHTML = '';


    if (!users || users.length === 0) {

        table.innerHTML = `
            <tr>

                <td
                    colspan="8"
                    class="text-center py-6 text-gray-500"
                >
                    Không có người dùng
                </td>

            </tr>
        `;

        return;
    }


    users.forEach(user => {

        const row = document.createElement('tr');
        row.className = 'border-t';
        row.innerHTML = `

            <tr class="border-t">

                <td class="px-5 py-3">
                    ${escapeHtml(user.id)}
                </td>

                <td class="px-5 py-3 font-medium">
                    ${escapeHtml(user.name)}
                </td>

                <td class="px-5 py-3">
                    ${escapeHtml(user.email)}
                </td>

                <td class="px-5 py-3">
                    ${escapeHtml(user.phone || '')}
                </td>

                <td class="px-5 py-3">
                    ${escapeHtml(user.role)}
                </td>

                <td class="px-5 py-3">

                    ${
                        user.status === 'active'

                        ? `
                            <span class="px-2 py-1 rounded bg-green-100 text-green-700">
                                Active
                            </span>
                        `

                        : `
                            <span class="px-2 py-1 rounded bg-red-100 text-red-700">
                                Locked
                            </span>
                        `
                    }

                </td>

                <td class="px-5 py-3">
                    ${escapeHtml(formatDate(user.created_at))}
                </td>

                <td class="px-5 py-3 text-center">

                    <button
                        onclick="openModal(
                            ${user.id},
                            '${escapeString(user.name)}',
                            '${user.status}',
                            '${user.role}'
                        )"
                        class="text-blue-600 hover:underline"
                    >
                        Sửa
                    </button>

                </td>

            </tr>

        `;
        table.appendChild(row);

    });

}



function formatDate(date) {

    if (!date) {
        return '';
    }

    return new Date(date).toLocaleDateString('vi-VN');

}

function escapeHtml(value) {
    const element = document.createElement('span');
    element.textContent = String(value ?? '');
    return element.innerHTML;
}




function escapeString(value) {

    return String(value || '')
        .replace(/\\/g, '\\\\')
        .replace(/'/g, "\\'");

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

            / ${meta.total || 0} người dùng

        </div>


        <div class="flex gap-2">

            <button
                onclick="loadUsers(${meta.current_page - 1})"
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
                onclick="loadUsers(${meta.current_page + 1})"
                ${meta.current_page >= meta.last_page ? 'disabled' : ''}
                class="px-3 py-1 border rounded"
            >
                Sau
            </button>

        </div>

    `;

}



function openModal(id, name, status, role) {

    document.getElementById('userId').value = id;

    document.getElementById('userName').value = name;

    document.getElementById('userStatus').value = status;

    document.getElementById('userRole').value = role;


    document
        .getElementById('userModal')
        .classList
        .remove('hidden');

}


function closeModal() {

    document
        .getElementById('userModal')
        .classList
        .add('hidden');

}




async function updateUser() {

    const id =
        document.getElementById('userId').value;

    const status =
        document.getElementById('userStatus').value;

    const role =
        document.getElementById('userRole').value;


    try {

     

        const statusResponse = await fetch(

            `${API_BASE_URL}/admin/users/${id}/status`,

            {

                method: 'PATCH',

                headers: {

                    'Accept': 'application/json',

                    'Content-Type': 'application/json',

                    ...AdminApi.headers(true)

                },

                body: JSON.stringify({
                    status: status
                })

            }

        );


        const statusResult =
            await statusResponse.json();



        if (!statusResponse.ok) {

            const errorCode =
                statusResult.errors?.error_code ||
                statusResult.error_code;


            if (
                errorCode ===
                'CANNOT_LOCK_SELF'
            ) {

                alert(
                    'Không thể khóa tài khoản của chính mình'
                );

            } else {

                alert(
                    'Không thể cập nhật trạng thái'
                );

            }

            return;
        }


        const roleResponse = await fetch(

            `${API_BASE_URL}/admin/users/${id}/role`,

            {

                method: 'PATCH',

                headers: {

                    'Accept': 'application/json',

                    'Content-Type':
                        'application/json',

                    ...AdminApi.headers(true)

                },

                body: JSON.stringify({
                    role: role
                })

            }

        );


        const roleResult =
            await roleResponse.json();



        if (!roleResponse.ok) {

            const errorCode =
                roleResult.errors?.error_code ||
                roleResult.error_code;


            if (
                errorCode ===
                'CANNOT_CHANGE_SELF_ROLE'
            ) {

                alert(
                    'Không thể thay đổi vai trò của chính mình'
                );

            }

            else if (
                errorCode ===
                'VALIDATION_ERROR'
            ) {

                alert(
                    'Vai trò không hợp lệ'
                );

            }

            else {

                alert(
                    'Không thể cập nhật vai trò'
                );

            }

            return;
        }


        alert(
            'Cập nhật người dùng thành công'
        );

        closeModal();

        loadUsers(currentPage);


    } catch (error) {

        console.error(error);

        alert('Không thể kết nối đến API');

    }

}


document.addEventListener(
    'DOMContentLoaded',
    function () {

        loadUsers(1);

    }
);

</script>
@endpush
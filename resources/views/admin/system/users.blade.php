<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Quản lý người dùng</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 text-slate-800">

    <main class="mx-auto max-w-7xl px-6 py-10">

        <!-- Tiêu đề -->
        <div class="mb-8">
            <p class="text-sm font-medium text-emerald-600">
                Admin System
            </p>

            <h1 class="mt-1 text-3xl font-bold">
                Quản lý người dùng
            </h1>

            <p class="mt-2 text-slate-500">
                Quản lý tài khoản người dùng trong hệ thống
            </p>
        </div>


        <!-- Danh sách người dùng -->
        <section class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-slate-200">

            <div class="border-b border-slate-200 px-6 py-4">
                <h2 class="font-semibold">
                    Danh sách người dùng
                </h2>
            </div>


            <div class="overflow-x-auto">

                <table class="w-full min-w-[800px] text-left text-sm">

                    <thead class="bg-slate-50 text-xs uppercase text-slate-500">

                        <tr>

                            <th class="px-6 py-4">
                                Họ tên
                            </th>

                            <th class="px-6 py-4">
                                Email
                            </th>

                            <th class="px-6 py-4">
                                Quyền
                            </th>

                            <th class="px-6 py-4">
                                Trạng thái
                            </th>

                            <th class="px-6 py-4">
                                Khóa tài khoản
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-slate-100">

                        @forelse ($users ?? [] as $user)

                            <tr class="hover:bg-slate-50">

                                <!-- Họ tên -->
                                <td class="px-6 py-4 font-medium">
                                    {{ $user->name }}
                                </td>


                                <!-- Email -->
                                <td class="px-6 py-4">
                                    {{ $user->email }}
                                </td>


                                <!-- Quyền -->
                                <td class="px-6 py-4">
                                    {{ $user->role ?? 'Chưa phân quyền' }}
                                </td>


                                <!-- Trạng thái -->
                                <td class="px-6 py-4">

                                    @if (($user->status ?? 'active') === 'active')

                                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-medium text-emerald-700">
                                            Đang hoạt động
                                        </span>

                                    @else

                                        <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-700">
                                            Đã khóa
                                        </span>

                                    @endif

                                </td>


                                <!-- Khóa tài khoản -->
                                <td class="px-6 py-4">

                                    <button
                                        type="button"
                                        class="rounded-lg border border-red-300 px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50">
                                        Khóa tài khoản
                                    </button>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="5"
                                    class="px-6 py-12 text-center text-slate-500">

                                    Chưa có người dùng nào.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </section>

    </main>

</body>

</html>
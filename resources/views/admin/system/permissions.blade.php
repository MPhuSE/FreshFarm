<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Phân quyền người dùng</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 text-slate-800">

    <main class="mx-auto max-w-6xl px-6 py-10">

        <!-- Tiêu đề -->
        <div class="mb-8">

            <p class="text-sm font-medium text-emerald-600">
                Admin System
            </p>

            <h1 class="mt-1 text-3xl font-bold">
                Phân quyền người dùng
            </h1>

            <p class="mt-2 text-slate-500">
                Quản lý quyền truy cập của người dùng trong hệ thống
            </p>

        </div>


        <!-- Phân quyền -->
        <section class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">

            <h2 class="mb-6 text-lg font-semibold">
                Cập nhật quyền người dùng
            </h2>


            <div class="grid gap-6 md:grid-cols-2">

                <!-- Chọn người dùng -->
                <div>

                    <label class="mb-2 block text-sm font-medium">
                        Người dùng
                    </label>

                    <select
                        class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 outline-none focus:border-emerald-500">

                        <option value="">
                            Chọn người dùng
                        </option>

                        @foreach ($users ?? [] as $user)

                            <option value="{{ $user->id }}">
                                {{ $user->name }} - {{ $user->email }}
                            </option>

                        @endforeach

                    </select>

                </div>


                <!-- Chọn quyền -->
                <div>

                    <label class="mb-2 block text-sm font-medium">
                        Quyền
                    </label>

                    <select
                        class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 outline-none focus:border-emerald-500">

                        <option value="">
                            Chọn quyền
                        </option>

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

            </div>


            <!-- Nút cập nhật -->
            <div class="mt-6">

                <button
                    type="button"
                    class="rounded-lg bg-emerald-600 px-5 py-2.5 font-medium text-white hover:bg-emerald-700">
                    Cập nhật quyền
                </button>

            </div>

        </section>


        <!-- Danh sách quyền -->
        <section class="mt-6 overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-slate-200">

            <div class="border-b border-slate-200 px-6 py-4">

                <h2 class="font-semibold">
                    Danh sách phân quyền
                </h2>

            </div>


            <div class="overflow-x-auto">

                <table class="w-full min-w-[700px] text-left text-sm">

                    <thead class="bg-slate-50 text-xs uppercase text-slate-500">

                        <tr>

                            <th class="px-6 py-4">
                                Người dùng
                            </th>

                            <th class="px-6 py-4">
                                Email
                            </th>

                            <th class="px-6 py-4">
                                Quyền hiện tại
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-slate-100">

                        @forelse ($users ?? [] as $user)

                            <tr class="hover:bg-slate-50">

                                <td class="px-6 py-4 font-medium">
                                    {{ $user->name }}
                                </td>

                                <td class="px-6 py-4">
                                    {{ $user->email }}
                                </td>

                                <td class="px-6 py-4">

                                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-medium text-emerald-700">
                                        {{ $user->role ?? 'Chưa phân quyền' }}
                                    </span>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="3"
                                    class="px-6 py-12 text-center text-slate-500">

                                    Chưa có dữ liệu phân quyền.

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
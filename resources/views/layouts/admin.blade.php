<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - Nông Sản Xanh</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Feather Icons -->
    <script src="https://unpkg.com/feather-icons"></script>
    
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-900 font-sans antialiased min-h-screen flex">

    <!-- Sidebar overlay for mobile -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-slate-900/50 z-40 hidden lg:hidden" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside id="adminSidebar" class="w-64 bg-emerald-800 text-emerald-50 flex flex-col flex-shrink-0 transition-transform duration-300 fixed inset-y-0 left-0 z-50 -translate-x-full lg:relative lg:translate-x-0">
        <div class="h-16 flex items-center justify-between px-6 border-b border-emerald-700/50">
            <span class="text-xl font-bold tracking-tight text-white flex items-center gap-2">
                <i data-feather="leaf" class="w-6 h-6 text-emerald-400"></i>
                NSX Admin
            </span>
            <button onclick="toggleSidebar()" class="lg:hidden text-emerald-100 hover:text-white">
                <i data-feather="x" class="w-6 h-6"></i>
            </button>
        </div>
        
        <nav class="flex-1 overflow-y-auto py-6 px-3 space-y-1">
            <a href="{{ route('admin.dashboard.index') ?? '#' }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors hover:bg-emerald-700 {{ request()->is('admin') ? 'bg-emerald-700 text-white shadow-inner' : 'text-emerald-100' }}">
                <i data-feather="pie-chart" class="w-5 h-5"></i>
                Tổng quan
            </a>
            
            <div class="pt-4 pb-2">
                <p class="px-3 text-xs font-semibold text-emerald-400 uppercase tracking-wider">Cửa hàng</p>
            </div>
            
            <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors hover:bg-emerald-700 {{ request()->is('admin/orders*') ? 'bg-emerald-700 text-white shadow-inner' : 'text-emerald-100' }}">
                <i data-feather="shopping-bag" class="w-5 h-5"></i>
                Đơn hàng
            </a>
            
            <a href="{{ route('admin.products.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors hover:bg-emerald-700 {{ request()->is('admin/products*') ? 'bg-emerald-700 text-white shadow-inner' : 'text-emerald-100' }}">
                <i data-feather="box" class="w-5 h-5"></i>
                Sản phẩm
            </a>
            
            <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors hover:bg-emerald-700 {{ request()->is('admin/categories*') ? 'bg-emerald-700 text-white shadow-inner' : 'text-emerald-100' }}">
                <i data-feather="grid" class="w-5 h-5"></i>
                Danh mục
            </a>

            <a href="{{ route('admin.coupons.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors hover:bg-emerald-700 {{ request()->is('admin/coupons*') ? 'bg-emerald-700 text-white shadow-inner' : 'text-emerald-100' }}">
                <i data-feather="tag" class="w-5 h-5"></i>
                Khuyến mãi
            </a>

            <a href="{{ route('admin.reviews.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors hover:bg-emerald-700 {{ request()->is('admin/reviews*') ? 'bg-emerald-700 text-white shadow-inner' : 'text-emerald-100' }}">
                <i data-feather="star" class="w-5 h-5"></i>
                Đánh giá
            </a>
            
            <a href="{{ route('admin.posts.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors hover:bg-emerald-700 {{ request()->is('admin/posts*') ? 'bg-emerald-700 text-white shadow-inner' : 'text-emerald-100' }}">
                <i data-feather="file-text" class="w-5 h-5"></i>
                Bài viết
            </a>
            
            <a href="{{ route('admin.inventory.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors hover:bg-emerald-700 {{ request()->is('admin/inventory*') ? 'bg-emerald-700 text-white shadow-inner' : 'text-emerald-100' }}">
                <i data-feather="package" class="w-5 h-5"></i>
                Tồn kho
            </a>
            
            <div class="pt-4 pb-2">
                <p class="px-3 text-xs font-semibold text-emerald-400 uppercase tracking-wider">Hệ thống</p>
            </div>
            
            <a href="{{ route('admin.system.users') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors hover:bg-emerald-700 {{ request()->is('admin/system/users*') ? 'bg-emerald-700 text-white shadow-inner' : 'text-emerald-100' }}">
                <i data-feather="users" class="w-5 h-5"></i>
                Khách hàng
            </a>
            
            <a href="{{ route('admin.system.reports') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors hover:bg-emerald-700 {{ request()->is('admin/system/reports*') ? 'bg-emerald-700 text-white shadow-inner' : 'text-emerald-100' }}">
                <i data-feather="bar-chart-2" class="w-5 h-5"></i>
                Báo cáo
            </a>

            <a href="{{ route('admin.system.permissions') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors hover:bg-emerald-700 {{ request()->is('admin/system/permissions*') ? 'bg-emerald-700 text-white shadow-inner' : 'text-emerald-100' }}">
                <i data-feather="shield" class="w-5 h-5"></i>
                Phân quyền
            </a>

            <a href="{{ route('admin.system.pages') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors hover:bg-emerald-700 {{ request()->is('admin/system/pages*') ? 'bg-emerald-700 text-white shadow-inner' : 'text-emerald-100' }}">
                <i data-feather="layout" class="w-5 h-5"></i>
                Trang tĩnh
            </a>

            <a href="{{ route('admin.system.audit-logs') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors hover:bg-emerald-700 {{ request()->is('admin/system/audit-logs*') ? 'bg-emerald-700 text-white shadow-inner' : 'text-emerald-100' }}">
                <i data-feather="activity" class="w-5 h-5"></i>
                Nhật ký
            </a>

            <a href="{{ route('admin.system.settings') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors hover:bg-emerald-700 {{ request()->is('admin/system/settings*') ? 'bg-emerald-700 text-white shadow-inner' : 'text-emerald-100' }}">
                <i data-feather="settings" class="w-5 h-5"></i>
                Cài đặt
            </a>
        </nav>
        
        <div class="p-4 border-t border-emerald-700/50">
            <a href="{{ url('/') }}" target="_blank" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-emerald-200 transition-colors hover:bg-emerald-700 hover:text-white">
                <i data-feather="external-link" class="w-5 h-5"></i>
                Xem cửa hàng
            </a>
        </div>
    </aside>

    <!-- Main wrapper -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        <!-- Topbar -->
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 lg:px-8 z-10 sticky top-0">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="lg:hidden p-2 -ml-2 text-slate-500 hover:bg-slate-100 rounded-lg">
                    <i data-feather="menu" class="w-6 h-6"></i>
                </button>
            </div>
            
            <div class="flex items-center gap-4">
                <button class="relative p-2 text-slate-400 hover:text-slate-500 transition-colors">
                    <span class="sr-only">Thông báo</span>
                    <i data-feather="bell" class="w-5 h-5"></i>
                    <span class="absolute top-1.5 right-1.5 block h-2 w-2 rounded-full bg-rose-500 ring-2 ring-white"></span>
                </button>
                
                <div class="h-6 w-px bg-slate-200 mx-2"></div>
                
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm">
                        A
                    </div>
                    <div class="hidden md:block text-sm">
                        <p class="font-medium text-slate-700">Quản trị viên</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main content -->
        <main class="flex-1 overflow-y-auto p-6 md:p-8">
            <div class="max-w-7xl mx-auto">
                <!-- Tiêu đề trang -->
                @hasSection('page-header')
                    <div class="mb-8">
                        @yield('page-header')
                    </div>
                @endif
                
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Init Feather Icons -->
    <script>
        feather.replace();

        function toggleSidebar() {
            const sidebar = document.getElementById('adminSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }
    </script>
    
    @stack('scripts')
</body>
</html>

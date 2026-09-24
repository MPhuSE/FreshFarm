@extends('layouts.admin')

@section('title', 'Test Hiệu năng')

@section('page-header')
    <p class="text-sm font-medium text-emerald-600">Performance Dashboard</p>
    <h1 class="mt-1 text-3xl font-bold">Kiểm tra hiệu năng (Benchmark)</h1>
    <p class="mt-2 text-slate-500">So sánh tốc độ và tài nguyên tiêu thụ giữa các chiến lược code khác nhau.</p>
@endsection

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        
        <!-- N+1 vs Eager Loading -->
        <div class="bg-white rounded-xl p-6 shadow-sm ring-1 ring-slate-200">
            <h2 class="text-lg font-bold text-slate-900 mb-2">1. Eloquent N+1 Query vs Eager Loading</h2>
            <p class="text-sm text-slate-500 mb-4">So sánh số lượng câu truy vấn (queries) khi lấy danh sách sản phẩm kèm danh mục.</p>
            
            <div class="flex gap-4 mb-4">
                <button onclick="runTest('n_plus_one', 'result1')" class="px-4 py-2 bg-red-100 text-red-700 font-medium rounded-lg hover:bg-red-200 transition">Test N+1 (Chậm)</button>
                <button onclick="runTest('eager_loading', 'result1')" class="px-4 py-2 bg-emerald-100 text-emerald-700 font-medium rounded-lg hover:bg-emerald-200 transition">Test Eager Loading</button>
            </div>
            
            <div class="bg-slate-50 rounded p-4 text-sm font-mono border border-slate-200 min-h-[140px]" id="result1">
                Chờ chạy test...
            </div>
        </div>

        <!-- No Cache vs Cache -->
        <div class="bg-white rounded-xl p-6 shadow-sm ring-1 ring-slate-200">
            <h2 class="text-lg font-bold text-slate-900 mb-2">2. Truy vấn trực tiếp vs Caching</h2>
            <p class="text-sm text-slate-500 mb-4">So sánh thời gian phản hồi khi gọi cùng 1 câu truy vấn 50 lần liên tục có Cache và không Cache.</p>
            
            <div class="flex gap-4 mb-4">
                <button onclick="runTest('no_cache', 'result2')" class="px-4 py-2 bg-yellow-100 text-yellow-700 font-medium rounded-lg hover:bg-yellow-200 transition">Test Không Cache</button>
                <button onclick="runTest('with_cache', 'result2')" class="px-4 py-2 bg-blue-100 text-blue-700 font-medium rounded-lg hover:bg-blue-200 transition">Test Có Cache</button>
            </div>
            
            <div class="bg-slate-50 rounded p-4 text-sm font-mono border border-slate-200 min-h-[140px]" id="result2">
                Chờ chạy test...
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    async function runTest(type, resultElementId) {
        const resultDiv = document.getElementById(resultElementId);
        resultDiv.innerHTML = '<div class="text-slate-500 flex items-center gap-2"><svg class="animate-spin h-4 w-4 text-emerald-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Đang chạy bài test...</div>';
        
        try {
            const response = await fetch('{{ route("admin.performance.run") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ type: type })
            });

            if (!response.ok) {
                throw new Error('Lỗi kết nối hoặc bài test thất bại');
            }

            const data = await response.json();
            
            resultDiv.innerHTML = `
                <div class="space-y-2">
                    <p><span class="font-bold text-slate-700">Kịch bản:</span> <span class="text-blue-600 font-semibold">${type}</span></p>
                    <p><span class="font-bold text-slate-700">Thời gian thực thi:</span> <span class="text-emerald-600 font-bold">${data.execution_time_ms} ms</span></p>
                    <p><span class="font-bold text-slate-700">Số lượng Queries:</span> <span class="text-purple-600 font-bold">${data.queries_count}</span></p>
                    <p><span class="font-bold text-slate-700">RAM tiêu thụ:</span> <span class="text-orange-600 font-bold">${data.memory_used_kb} KB</span></p>
                </div>
            `;
        } catch (error) {
            resultDiv.innerHTML = `<span class="text-red-500 font-medium">Lỗi: ${error.message}</span>`;
        }
    }
</script>
@endpush

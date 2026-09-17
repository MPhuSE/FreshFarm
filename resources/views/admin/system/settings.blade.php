@extends('layouts.admin')

@section('title', 'Cài đặt Hệ thống')

@section('page-header')
    <p class="text-sm font-medium text-emerald-600">Admin Settings</p>
    <h1 class="mt-1 text-3xl font-bold">Cài đặt Hệ thống</h1>
    <p class="mt-2 text-slate-500">Cấu hình các tham số hoạt động của website.</p>
@endsection

@section('content')
  
    <section class="mb-6 rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200 max-w-3xl">
        <h2 class="mb-4 font-semibold text-xl">Cấu hình chung</h2>
        
        <form id="settingsForm" onsubmit="saveSettings(event)" class="space-y-6">
            
            <div id="settingsContainer" class="space-y-4">
                <p class="text-slate-500">Đang tải cài đặt...</p>
            </div>

            <div class="pt-4 border-t border-slate-200 flex justify-end">
                <button type="submit" class="rounded-lg bg-emerald-600 px-6 py-2.5 font-medium text-white hover:bg-emerald-700">
                    Lưu cài đặt
                </button>
            </div>
        </form>
    </section>
@endsection

@push('scripts')
<script>
const API_BASE_URL = '/api/v1';

async function loadSettings() {
    try {
        const response = await fetch(`${API_BASE_URL}/admin/settings`, {
            headers: {
                'Authorization': 'Bearer ' + (sessionStorage.getItem('access_token') || localStorage.getItem('access_token')),
                'Accept': 'application/json'
            }
        });

        const settings = await response.json();
        renderSettings(settings || []);

    } catch (error) {
        console.error(error);
        alert('Không thể tải cài đặt.');
    }
}

// Basic seed list if DB is empty
const defaultFields = [
    { key: 'site_name', type: 'string', description: 'Tên Website', value: 'Nông Sản Xanh' },
    { key: 'contact_email', type: 'string', description: 'Email liên hệ', value: 'contact@nongsanhxanh.com' },
    { key: 'contact_phone', type: 'string', description: 'Số điện thoại', value: '0123456789' },
    { key: 'free_shipping_threshold', type: 'integer', description: 'Đơn tối thiểu để freeship (VNĐ)', value: '500000' }
];

function renderSettings(settings) {
    const container = document.getElementById('settingsContainer');
    container.innerHTML = '';
    
    // Merge DB settings with defaults
    const combinedSettings = [...defaultFields];
    
    settings.forEach(dbSetting => {
        const index = combinedSettings.findIndex(s => s.key === dbSetting.key);
        if (index > -1) {
            combinedSettings[index].value = dbSetting.value;
        } else {
            combinedSettings.push(dbSetting);
        }
    });

    combinedSettings.forEach(setting => {
        const wrap = document.createElement('div');
        wrap.className = 'grid grid-cols-3 gap-4 items-center';
        
        const label = document.createElement('label');
        label.className = 'block text-sm font-medium text-slate-700';
        label.textContent = setting.description || setting.key;
        
        let input;
        if (setting.type === 'boolean') {
            input = document.createElement('input');
            input.type = 'checkbox';
            input.checked = setting.value === 'true' || setting.value === '1';
            input.className = 'rounded border-slate-300 text-emerald-600 focus:ring-emerald-500';
        } else {
            input = document.createElement('input');
            input.type = setting.type === 'integer' ? 'number' : 'text';
            input.value = setting.value || '';
            input.className = 'col-span-2 w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-emerald-500 focus:ring-emerald-500';
        }
        
        input.dataset.key = setting.key;
        input.dataset.type = setting.type || 'string';
        input.name = 'setting_' + setting.key;
        
        wrap.appendChild(label);
        
        if(setting.type === 'boolean') {
            const innerWrap = document.createElement('div');
            innerWrap.className = 'col-span-2';
            innerWrap.appendChild(input);
            wrap.appendChild(innerWrap);
        } else {
            wrap.appendChild(input);
        }
        
        container.appendChild(wrap);
    });
}

async function saveSettings(e) {
    e.preventDefault();
    const inputs = document.querySelectorAll('#settingsContainer input');
    
    const settingsPayload = Array.from(inputs).map(input => {
        return {
            key: input.dataset.key,
            value: input.type === 'checkbox' ? (input.checked ? '1' : '0') : input.value
        };
    });

    try {
        const response = await fetch(`${API_BASE_URL}/admin/settings`, {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + (sessionStorage.getItem('access_token') || localStorage.getItem('access_token')),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ settings: settingsPayload })
        });

        if (response.ok) {
            alert('Lưu cài đặt thành công!');
        } else {
            alert('Lỗi lưu cài đặt');
        }
    } catch (e) {
        console.error(e);
        alert('Lỗi kết nối');
    }
}

loadSettings();
</script>
@endpush

import {
    $,
    esc,
    url,
    token,
    request,
    errorBox,
    showError,
    heading,
    message,
    input,
    onForm
} from './api.core.js';


/* =============================================
   ACCOUNT / PROFILE PAGE
   ============================================= */

async function account() {
    const main = $('#live-main');
    if (!main) return;

    if (!token()) {
        location.href = url('login');
        return;
    }

    try {
        const response = await request('/me');
        const user = response.data;

        // Save sanitized user to storage for header
        if (window.FF_AUTH) {
            window.FF_AUTH.setUser(user);
        } else {
            localStorage.setItem('current_user', JSON.stringify({ name: user.full_name || user.name, role: user.role }));
        }

        // Fill the existing Blade form fields
        const nameField = document.getElementById('full_name');
        const phoneField = document.getElementById('phone');
        const emailField = document.getElementById('email');
        const avatarEl = main.querySelector('.avatar');
        const nameDisplay = main.querySelector('.profile-top strong');
        const infoDisplay = main.querySelector('.profile-top p');
        const statusBadge = main.querySelector('.status-badge');

        if (nameField) nameField.value = user.full_name || user.name || '';
        if (phoneField) phoneField.value = user.phone || '';
        if (emailField) emailField.value = user.email || '';

        if (avatarEl) {
            const name = user.full_name || user.name || '';
            const parts = name.trim().split(/\s+/);
            avatarEl.textContent = parts.length >= 2
                ? (parts[0][0] + parts[parts.length - 1][0]).toUpperCase()
                : (parts[0]?.[0] || '?').toUpperCase();
        }

        if (nameDisplay) nameDisplay.textContent = user.full_name || user.name || 'Người dùng';
        if (infoDisplay) infoDisplay.textContent = `Thành viên · ${user.email || ''}`;

        if (statusBadge) {
            statusBadge.className = 'status-badge status-badge--success';
            statusBadge.textContent = user.status === 'active' ? 'Đang hoạt động' : (user.status || 'Hoạt động');
        }

        // Handle profile form submit
        const form = main.querySelector('[data-save-form]');
        if (form) {
            onForm(form, async (data) => {
                const body = {
                    full_name: data.get('full_name') || document.getElementById('full_name')?.value,
                    phone: data.get('phone') || document.getElementById('phone')?.value
                };

                // Try both field names
                if (!body.full_name && nameField) body.full_name = nameField.value;
                if (!body.phone && phoneField) body.phone = phoneField.value;

                await request('/user/profile', {
                    method: 'PUT',
                    body
                });

                // Update user storage
                user.full_name = body.full_name;
                user.phone = body.phone;
                if (window.FF_AUTH) {
                    window.FF_AUTH.setUser(user);
                } else {
                    localStorage.setItem('current_user', JSON.stringify({ name: user.full_name, role: user.role }));
                }

                if (typeof toast === 'function') {
                    toast('Đã cập nhật thông tin tài khoản');
                }
            });
        }

    } catch (error) {
        showError(error, main);
    }
}


/* =============================================
   ADDRESSES PAGE
   ============================================= */

async function addresses() {
    const main = $('#live-main');
    if (!main) return;

    if (!token()) {
        location.href = url('login');
        return;
    }

    const addressList = document.getElementById('address-list');
    const emptyState = document.getElementById('addresses-empty');
    const modal = document.getElementById('address-modal');
    const form = document.querySelector('[data-address-form]');

    async function load() {
        try {
            const response = await request('/user/addresses');
            const list = response.data || [];

            if (!addressList) return;

            if (list.length === 0) {
                addressList.innerHTML = '';
                addressList.hidden = true;
                if (emptyState) emptyState.hidden = false;
                return;
            }

            if (emptyState) emptyState.hidden = true;
            addressList.hidden = false;

            addressList.innerHTML = list.map((addr) => `
                <article
                    class="address-card ${addr.is_default ? 'is-default' : ''}"
                    data-address-id="${esc(addr.id)}"
                    style="animation: fadeInUp .4s cubic-bezier(.16,1,.3,1) both"
                >
                    <div>
                        ${addr.is_default
                            ? '<span class="status-badge status-badge--success">Mặc định</span>'
                            : ''
                        }
                        <h3>${esc(addr.recipient_name)}</h3>
                        <p>${esc(addr.phone)}</p>
                        <p>${esc(addr.address)}</p>
                    </div>

                    <div>
                        ${!addr.is_default
                            ? `<button
                                    class="link-button"
                                    data-set-default="${esc(addr.id)}"
                                >Đặt mặc định</button>`
                            : ''
                        }

                        <button
                            class="link-button"
                            data-edit-address="${esc(addr.id)}"
                            data-name="${esc(addr.recipient_name)}"
                            data-phone="${esc(addr.phone)}"
                            data-address="${esc(addr.address)}"
                            data-default="${addr.is_default ? '1' : '0'}"
                        >Chỉnh sửa</button>

                        <button
                            class="link-button link-button--danger"
                            data-delete-address="${esc(addr.id)}"
                        >Xóa</button>
                    </div>
                </article>
            `).join('');

            bindAddressActions();

            if (typeof refreshIcons === 'function') refreshIcons();

        } catch (error) {
            showError(error, main);
        }
    }


    function bindAddressActions() {
        // Set default
        main.querySelectorAll('[data-set-default]').forEach((btn) => {
            btn.onclick = async () => {
                btn.disabled = true;
                btn.textContent = 'Đang lưu...';
                try {
                    await request('/user/addresses/' + btn.dataset.setDefault, {
                        method: 'PUT',
                        body: { is_default: true }
                    });
                    if (typeof toast === 'function') toast('Đã đặt làm địa chỉ mặc định');
                    await load();
                } catch (error) {
                    showError(error, main);
                }
            };
        });

        // Edit
        main.querySelectorAll('[data-edit-address]').forEach((btn) => {
            btn.onclick = () => {
                if (!modal || !form) return;

                const idField = form.querySelector('#address-id');
                const nameField = form.querySelector('[name="recipient_name"]');
                const phoneField = form.querySelector('[name="phone"]');
                const addrField = form.querySelector('[name="address"]');
                const defaultField = form.querySelector('[name="is_default"]');
                const title = document.getElementById('address-modal-title');
                const eyebrow = document.getElementById('address-modal-eyebrow');

                if (idField) idField.value = btn.dataset.editAddress;
                if (nameField) nameField.value = btn.dataset.name || '';
                if (phoneField) phoneField.value = btn.dataset.phone || '';
                if (addrField) addrField.value = btn.dataset.address || '';
                if (defaultField) defaultField.checked = btn.dataset.default === '1';
                if (title) title.textContent = 'Chỉnh sửa địa chỉ';
                if (eyebrow) eyebrow.textContent = 'Cập nhật';

                modal.showModal();
            };
        });

        // Delete
        main.querySelectorAll('[data-delete-address]').forEach((btn) => {
            btn.onclick = async () => {
                if (!confirm('Bạn có chắc muốn xóa địa chỉ này?')) return;

                btn.disabled = true;
                btn.textContent = 'Đang xóa...';

                try {
                    await request('/user/addresses/' + btn.dataset.deleteAddress, {
                        method: 'DELETE'
                    });
                    if (typeof toast === 'function') toast('Đã xóa địa chỉ');
                    await load();
                } catch (error) {
                    showError(error, main);
                }
            };
        });
    }


    // Open modal for new address
    main.querySelectorAll('[data-open-address]').forEach((btn) => {
        btn.addEventListener('click', () => {
            if (!modal || !form) return;

            form.reset();
            const idField = form.querySelector('#address-id');
            if (idField) idField.value = '';

            const title = document.getElementById('address-modal-title');
            const eyebrow = document.getElementById('address-modal-eyebrow');
            if (title) title.textContent = 'Thông tin nhận hàng';
            if (eyebrow) eyebrow.textContent = 'Địa chỉ mới';

            modal.showModal();
        });
    });


    // Handle form submit (create or update)
    if (form) {
        // Handle click outside to close modal
        if (modal) {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) modal.close();
            });
        }

        form.addEventListener('submit', async (e) => {
            if (e.submitter && e.submitter.value === 'cancel') {
                return; // Native dialog handles closing
            }

            e.preventDefault();

            const errorBox = form.querySelector('[data-form-error]');
            if (errorBox) errorBox.hidden = true;

            const submitBtn = form.querySelector('[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Đang lưu...';
            }

            const data = new FormData(form);
            const addressId = data.get('address_id');

            const body = {
                recipient_name: data.get('recipient_name'),
                phone: data.get('phone'),
                address: data.get('address'),
                is_default: data.has('is_default') && form.querySelector('[name="is_default"]')?.checked
            };

            try {
                if (addressId) {
                    // Update
                    await request('/user/addresses/' + addressId, {
                        method: 'PUT',
                        body
                    });
                    if (typeof toast === 'function') toast('Đã cập nhật địa chỉ');
                } else {
                    // Create
                    await request('/user/addresses', {
                        method: 'POST',
                        body
                    });
                    if (typeof toast === 'function') toast('Đã thêm địa chỉ mới');
                }

                modal.close();
                await load();

            } catch (error) {
                if (errorBox) {
                    errorBox.hidden = false;
                    errorBox.innerHTML = `<strong>${error.message || 'Có lỗi xảy ra'}</strong>`;
                }
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Lưu địa chỉ';
                }
            }
        });
    }

    await load();
}


export {
    account,
    addresses
};
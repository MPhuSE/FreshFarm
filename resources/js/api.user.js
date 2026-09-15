import {
    $,
    esc,
    url,
    request,
    errorBox,
    showError,
    heading,
    message,
    input,
    onForm
} from './api.core.js';

async function account() {
    const main =
        $('#live-main');

    if (!main) {
        return;
    }

    try {
        const response =
            await request('/me');

        const user =
            response.data;

        main.innerHTML =
            heading(
                'Tài khoản của tôi'
            ) +

            `
                <div class="account-layout">

                    <section class="panel">

                        <h2>
                            Thông tin cá nhân
                        </h2>

                        <form
                            class="live-form"
                            id="profile-form"
                        >

                            ${input(
                                'Họ và tên',
                                'name',
                                'text',
                                user.name || ''
                            )}

                            ${input(
                                'Email',
                                'email',
                                'email',
                                user.email || ''
                            )}

                            ${input(
                                'Số điện thoại',
                                'phone',
                                'tel',
                                user.phone || '',
                                false
                            )}

                            <button
                                type="submit"
                                class="btn btn--primary"
                            >
                                Lưu thay đổi
                            </button>

                            <div
                                data-form-error
                                class="alert alert--error"
                                hidden
                            ></div>

                        </form>

                    </section>

                    <section class="panel">

                        <h2>
                            Quản lý tài khoản
                        </h2>

                        <div class="live-actions">

                            <a
                                class="btn btn--outline"
                                href="${url('addresses')}"
                            >
                                Địa chỉ giao hàng
                            </a>

                            <a
                                class="btn btn--outline"
                                href="${url('orders')}"
                            >
                                Đơn hàng của tôi
                            </a>

                        </div>

                    </section>

                </div>
            `;

        onForm(
            $('#profile-form'),
            async (data) => {

                const body = {
                    name:
                        data.get('name'),

                    phone:
                        data.get('phone')
                };

                await request(
                    '/user/profile',
                    {
                        method: 'PUT',
                        body
                    }
                );

                main.innerHTML +=
                    message(
                        'Đã cập nhật thông tin tài khoản.'
                    );
            }
        );

    } catch (error) {
        if (error.status === 401) {
            location.href =
                url('login');

            return;
        }

        showError(
            error,
            main
        );
    }
}

// =========================
// ADDRESSES
// =========================

async function addresses() {
    const main =
        $('#live-main');

    if (!main) {
        return;
    }

    async function load() {

        try {

            const response =
                await request(
                    '/user/addresses'
                );

            const list =
                response.data || [];

            main.innerHTML =
                heading(
                    'Địa chỉ giao hàng'
                ) +

                `
                    <div
                        class="address-layout"
                    >

                        <section
                            class="panel"
                        >

                            <h2>
                                Địa chỉ đã lưu
                            </h2>

                            <div
                                id="address-list"
                                class="live-stack"
                            >

                                ${
                                    list.length

                                        ? list
                                            .map(
                                                (address) => `
                                                    <article
                                                        class="panel live-row"
                                                        data-address-id="${esc(
                                                            address.id
                                                        )}"
                                                    >

                                                        <div>

                                                            <strong>
                                                                ${esc(
                                                                    address.recipient_name
                                                                )}
                                                            </strong>

                                                            <p>
                                                                ${esc(
                                                                    address.phone
                                                                )}
                                                            </p>

                                                            <p>
                                                                ${esc(
                                                                    address.address
                                                                )}
                                                            </p>

                                                            ${
                                                                address.is_default
                                                                    ? `
                                                                        <span
                                                                            class="status-badge"
                                                                        >
                                                                            Mặc định
                                                                        </span>
                                                                    `
                                                                    : ''
                                                            }

                                                        </div>

                                                        <div
                                                            class="live-actions"
                                                        >

                                                            ${
                                                                !address.is_default
                                                                    ? `
                                                                        <button
                                                                            type="button"
                                                                            class="btn btn--outline"
                                                                            data-set-default="${esc(
                                                                                address.id
                                                                            )}"
                                                                        >
                                                                            Đặt mặc định
                                                                        </button>
                                                                    `
                                                                    : ''
                                                            }

                                                            <button
                                                                type="button"
                                                                class="btn btn--danger"
                                                                data-delete-address="${esc(
                                                                    address.id
                                                                )}"
                                                            >
                                                                Xóa
                                                            </button>

                                                        </div>

                                                    </article>
                                                `
                                            )
                                            .join('')

                                        : message(
                                            'Bạn chưa có địa chỉ giao hàng.'
                                        )
                                }

                            </div>

                        </section>

                        <section
                            class="panel"
                        >

                            <h2>
                                Thêm địa chỉ
                            </h2>

                            <form
                                class="live-form"
                                id="address-form"
                            >

                                ${input(
                                    'Họ và tên người nhận',
                                    'recipient_name',
                                    'text'
                                )}

                                ${input(
                                    'Số điện thoại',
                                    'phone',
                                    'tel'
                                )}

                                ${input(
                                    'Địa chỉ',
                                    'address',
                                    'text'
                                )}

                                <label
                                    class="check-inline"
                                >

                                    <input
                                        type="checkbox"
                                        name="is_default"
                                        value="1"
                                    >

                                    Đặt làm địa chỉ mặc định

                                </label>

                                <button
                                    type="submit"
                                    class="btn btn--primary"
                                >
                                    Thêm địa chỉ
                                </button>

                                <div
                                    data-form-error
                                    class="alert alert--error"
                                    hidden
                                ></div>

                            </form>

                        </section>

                    </div>
                `;

            // =========================
            // ADD ADDRESS
            // =========================

            onForm(
                $('#address-form'),
                async (data) => {

                    await request(
                        '/user/addresses',
                        {
                            method: 'POST',
                            body: {
                                recipient_name:
                                    data.get(
                                        'recipient_name'
                                    ),

                                phone:
                                    data.get(
                                        'phone'
                                    ),

                                address:
                                    data.get(
                                        'address'
                                    ),

                                is_default:
                                    data.has(
                                        'is_default'
                                    )
                            }
                        }
                    );

                    await load();
                }
            );

            // =========================
            // SET DEFAULT
            // =========================

            main
                .querySelectorAll(
                    '[data-set-default]'
                )
                .forEach(
                    (button) => {

                        button.onclick =
                            async () => {

                                button.disabled =
                                    true;

                                const id =
                                    button.dataset
                                        .setDefault;

                                try {

                                    await request(
                                        '/user/addresses/' +
                                        encodeURIComponent(
                                            id
                                        ),
                                        {
                                            method:
                                                'PUT',

                                            body: {
                                                is_default:
                                                    true
                                            }
                                        }
                                    );

                                    await load();

                                } catch (error) {

                                    showError(
                                        error,
                                        main
                                    );

                                    button.disabled =
                                        false;
                                }
                            };
                    }
                );

            // =========================
            // DELETE ADDRESS
            // =========================

            main
                .querySelectorAll(
                    '[data-delete-address]'
                )
                .forEach(
                    (button) => {

                        button.onclick =
                            async () => {

                                if (
                                    !confirm(
                                        'Bạn có chắc muốn xóa địa chỉ này?'
                                    )
                                ) {
                                    return;
                                }

                                button.disabled =
                                    true;

                                const id =
                                    button.dataset
                                        .deleteAddress;

                                try {

                                    await request(
                                        '/user/addresses/' +
                                        encodeURIComponent(
                                            id
                                        ),
                                        {
                                            method:
                                                'DELETE'
                                        }
                                    );

                                    await load();

                                } catch (error) {

                                    showError(
                                        error,
                                        main
                                    );

                                    button.disabled =
                                        false;
                                }
                            };
                    }
                );

        } catch (error) {
            if (error.status === 401) {
                location.href =
                    url('login');

                return;
            }

            showError(
                error,
                main
            );
        }
    }

    await load();
}

export {
    account,
    addresses
};

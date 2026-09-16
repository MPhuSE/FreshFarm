'use strict';

export const $ = (selector) => {
    return document.querySelector(selector);
};



export const esc = (value) => {
    return String(value ?? '').replace(
        /[&<>"']/g,
        (char) => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;'
        })[char]
    );
};



export const cash = (value) => {
    return new Intl.NumberFormat(
        'vi-VN',
        {
            style: 'currency',
            currency: 'VND'
        }
    ).format(Number(value) || 0);
};


export const url = FF.url;

export const token = () => {
    return (
        sessionStorage.getItem(
            'access_token'
        ) ||
        localStorage.getItem(
            'access_token'
        )
    );
};


export const statusLabel = (status) => {
    return {
        pending: 'Chờ xác nhận',
        confirmed: 'Đã xác nhận',
        processing: 'Đang chuẩn bị',
        shipping: 'Đang giao',
        delivered: 'Đã giao',
        completed: 'Hoàn tất',
        cancelled: 'Đã hủy',
        paid: 'Đã thanh toán',
        unpaid: 'Chưa thanh toán'
    }[status] || status || '—';
};


export const safeImage = (value) => {
    if (
        typeof value === 'string' &&
        value.trim()
    ) {
        try {
            const imageUrl =
                new URL(
                    value,
                    location.origin
                );

            if (
                ['http:', 'https:'].includes(
                    imageUrl.protocol
                )
            ) {
                return esc(
                    imageUrl.href
                );
            }

        } catch {
            // Ignore invalid image URL
        }
    }

    return FF.asset(
        'images/favicon.svg'
    );
};

export async function request(
    path,
    {
        method = 'GET',
        body,
        headers = {}
    } = {}
) {
    const controller =
        new AbortController();

    const timer =
        setTimeout(() => {
            controller.abort();
        }, 15000);

    try {
        const response =
            await fetch(
                FF.base +
                '/api/v1' +
                path,
                {
                    method,
                    signal:
                        controller.signal,

                    headers: {
                        Accept:
                            'application/json',

                        ...(body
                            ? {
                                'Content-Type':
                                    'application/json'
                            }
                            : {}),

                        ...(token()
                            ? {
                                Authorization:
                                    'Bearer ' +
                                    token()
                            }
                            : {}),

                        ...headers
                    },

                    ...(body
                        ? {
                            body:
                                JSON.stringify(
                                    body
                                )
                        }
                        : {})
                }
            );

        let result;

        try {
            result =
                await response.json();

        } catch {
            throw new Error(
                'Máy chủ không trả JSON hợp lệ. ' +
                'Kiểm tra API Laravel.'
            );
        }

        if (
            !response.ok ||
            result.success === false
        ) {
            const error =
                new Error(
                    result.message ||
                    'Không thể hoàn tất yêu cầu.'
                );

            error.status =
                response.status;

            error.fields =
                result.errors;

            error.code =
                result.error_code ||
                result.errors_code;

            if (
                response.status ===
                401
            ) {
                sessionStorage.removeItem(
                    'access_token'
                );

                localStorage.removeItem(
                    'access_token'
                );

                localStorage.removeItem(
                    'current_user'
                );
            }

            throw error;
        }

        return result;

    } catch (error) {

        if (
            error.name ===
            'AbortError'
        ) {
            throw new Error(
                'Yêu cầu quá thời gian. ' +
                'Vui lòng thử lại.'
            );
        }

        throw error;

    } finally {
        clearTimeout(timer);
    }
}

export function errorBox(error) {
    return `
        <div
            class="alert alert--error live-message"
            role="alert"
        >

            <strong>
                ${
                    error.status
                        ? `Lỗi ${error.status}`
                        : 'Không thể tải dữ liệu'
                }
            </strong>

            <span>
                ${esc(error.message)}
            </span>

            ${
                error.fields
                    ? Object.entries(
                        error.fields
                    )
                        .map(
                            ([key, value]) => `
                                <span>
                                    ${esc(key)}:
                                    ${esc(
                                        Array.isArray(value)
                                            ? value.join(' ')
                                            : value
                                    )}
                                </span>
                            `
                        )
                        .join('')
                    : ''
            }

            ${
                error.status === 401
                    ? `
                        <a
                            class="btn btn--outline"
                            href="${url('login')}"
                        >
                            Đăng nhập
                        </a>
                    `
                    : ''
            }

        </div>
    `;
}
export function showError(
    error,
    container = $('#live-main')
) {
    if (!container) {
        return;
    }

    container.innerHTML = `
        ${errorBox(error)}

        <button
            class="btn btn--outline"
            type="button"
            data-retry
        >
            Tải lại
        </button>
    `;

    container
        .querySelector(
            '[data-retry]'
        )
        ?.addEventListener(
            'click',
            () => location.reload()
        );
}


export function heading(
    title,
    sub = ''
) {
    return `
        <div class="live-heading">

            <div>

                <h1>
                    ${esc(title)}
                </h1>

                ${
                    sub
                        ? `<p>${esc(sub)}</p>`
                        : ''
                }

            </div>

        </div>
    `;
}


export function message(text) {
    return `
        <div class="alert alert--info">
            ${esc(text)}
        </div>
    `;
}


export function input(
    label,
    name,
    type = 'text',
    value = '',
    required = true
) {
    return `
        <label>

            ${esc(label)}

            <input
                name="${name}"
                type="${type}"
                value="${esc(value)}"
                ${required ? 'required' : ''}
            >

        </label>
    `;
}


export function onForm(
    form,
    handler
) {
    form.addEventListener(
        'submit',
        async (event) => {

            event.preventDefault();

            if (form.dataset.busy) {
                return;
            }

            form.dataset.busy =
                '1';

            const data =
                new FormData(form);

            const controls =
                [...form.elements]
                    .map(
                        (element) => [
                            element,
                            element.disabled
                        ]
                    );

            controls.forEach(
                ([element]) => {
                    element.disabled =
                        true;
                }
            );

            let box =
                form.querySelector(
                    '[data-feedback]'
                );

            if (!box) {
                box =
                    document.createElement(
                        'div'
                    );

                box.dataset.feedback =
                    '';

                form.prepend(box);
            }

            box.innerHTML = '';

            try {
                await handler(
                    data,
                    form
                );

            } catch (error) {
                box.innerHTML =
                    errorBox(error);

            } finally {
                delete form.dataset.busy;

                controls.forEach(
                    ([element, disabled]) => {
                        element.disabled =
                            disabled;
                    }
                );
            }
        }
    );
}

export function updateCount(cart) {
    const count =
        (cart?.items || []).length;

    document
        .querySelectorAll(
            '[data-cart-count]'
        )
        .forEach((element) => {
            element.textContent =
                count;
        });
}
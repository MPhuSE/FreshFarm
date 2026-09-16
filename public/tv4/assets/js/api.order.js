import {
    $,
    esc,
    cash,
    url,
    request,
    errorBox,
    showError,
    heading,
    message,
    safeImage,
    statusLabel,
    token
} from './api.core.js';

import {
    totals
} from './api.cart.js';


async function orders() {
    const main =
        $('#live-main');

    if (!main) {
        return;
    }

    if (!token()) {
        location.href = url('login');
        return;
    }

    main.innerHTML =
        `
        <div class="container section">
        `

        +

        heading(
            'Đơn hàng của tôi'
        ) +

        `
            <form
                id="order-filter-form"
                class="panel live-filter"
            >

                <label>
                    Trạng thái

                    <select name="status">

                        <option value="">
                            Tất cả
                        </option>

                        ${
                            [
                                'pending',
                                'confirmed',
                                'shipping',
                                'delivered',
                                'cancelled'
                            ]
                                .map(
                                    (status) => `
                                        <option
                                            value="${status}"
                                        >
                                            ${statusLabel(
                                                status
                                            )}
                                        </option>
                                    `
                                )
                                .join('')
                        }

                    </select>
                </label>

                <button
                    class="btn btn--primary"
                    type="submit"
                >
                    Lọc đơn hàng
                </button>

            </form>

            <div
                id="orders-result"
            ></div>
        </div>
        `;

    let sequence = 0;

    async function load(
        page = 1
    ) {
        const requestId =
            ++sequence;

        const target =
            $('#orders-result');

        if (!target) {
            return;
        }

        target.innerHTML =
            message(
                'Đang tải đơn hàng…'
            );

        const params =
            new URLSearchParams(
                new FormData(
                    $('#order-filter-form')
                )
            );

        if (
            !params.get('status')
        ) {
            params.delete(
                'status'
            );
        }

        params.set(
            'page',
            page
        );

        try {

            const response =
                await request(
                    '/orders?' +
                    params
                );

            if (
                requestId !==
                sequence
            ) {
                return;
            }

            const orderList =
                response.data || [];

            target.innerHTML =
                `
                    <div
                        class="live-stack"
                    >

                        ${
                            orderList
                                .length

                                ? orderList
                                    .map(
                                        (order) => `
                                            <article
                                                class="panel live-row"
                                            >

                                                <div>

                                                    <a
                                                        class="text-link"
                                                        href="${url(
                                                            'order-detail',
                                                            order.order_code
                                                        )}"
                                                    >
                                                        ${esc(
                                                            order.order_code
                                                        )}
                                                    </a>

                                                    <p>
                                                        ${esc(
                                                            order.created_at
                                                        )}
                                                    </p>

                                                </div>

                                                <span
                                                    class="status-badge status-badge--muted"
                                                >
                                                    ${esc(
                                                        statusLabel(
                                                            order.status
                                                        )
                                                    )}
                                                </span>

                                                <strong>
                                                    ${cash(
                                                        order.grand_total
                                                    )}
                                                </strong>

                                                <a
                                                    class="btn btn--outline"
                                                    href="${url(
                                                        'order-detail',
                                                        order.order_code
                                                    )}"
                                                >
                                                    Xem đơn
                                                </a>

                                            </article>
                                        `
                                    )
                                    .join('')

                                : message(
                                    'Chưa có đơn hàng.'
                                )
                        }

                    </div>
                `;

            if (
                response.meta
                    ?.last_page > 1
            ) {

                target.innerHTML += `
                    <nav
                        class="live-pagination"
                    >

                        <button
                            data-prev
                            class="btn btn--outline"
                            ${
                                page <= 1
                                    ? 'disabled'
                                    : ''
                            }
                        >
                            Trước
                        </button>

                        <span>
                            ${page}
                            /
                            ${esc(
                                response.meta
                                    .last_page
                            )}
                        </span>

                        <button
                            data-next
                            class="btn btn--outline"
                            ${
                                page >=
                                response.meta
                                    .last_page
                                    ? 'disabled'
                                    : ''
                            }
                        >
                            Sau
                        </button>

                    </nav>
                `;

                target.querySelector(
                    '[data-prev]'
                ).onclick =
                    () => {
                        load(
                            page - 1
                        );
                    };

                target.querySelector(
                    '[data-next]'
                ).onclick =
                    () => {
                        load(
                            page + 1
                        );
                    };
            }

        } catch (error) {

            if (
                requestId ===
                sequence
            ) {
                showError(
                    error,
                    target
                );
            }
        }
    }

    $('#order-filter-form')
        .onsubmit =
        (event) => {

            event.preventDefault();

            load();
        };

    await load();
}


async function order() {
    if (!token()) {
        location.href = url('login');
        return;
    }

    const response =
        await request(
            '/orders/' +
            encodeURIComponent(
                FF.orderCode
            )
        );

    const orderData =
        response.data?.order ||
        response.data;

    const main =
        $('#live-main');

    if (!main) {
        return;
    }

    main.innerHTML =
        `
        <div class="container section">
        `

        +

        heading(
            'Đơn ' +
            orderData.order_code,
            statusLabel(
                orderData.status
            )
        ) +

        `
            <div class="detail-layout">

                <div class="live-stack">

                    <section class="panel">

                        <h2>
                            Sản phẩm
                        </h2>

                        ${
                            (
                                orderData.items ||
                                []
                            )
                                .map(
                                    (item) => `
                                        <article
                                            class="order-line"
                                        >

                                            <img
                                                src="${safeImage(
                                                    item
                                                        .product
                                                        ?.primary_image_url
                                                )}"
                                                alt=""
                                            >

                                            <div>

                                                <strong>
                                                    ${esc(
                                                        item
                                                            .product
                                                            ?.name ||
                                                        item.product_name
                                                    )}
                                                </strong>

                                                <small>
                                                    ${esc(
                                                        item.quantity
                                                    )}
                                                    ×
                                                    ${cash(
                                                        item.unit_price
                                                    )}
                                                </small>

                                            </div>

                                            <strong>
                                                ${cash(
                                                    item.line_total
                                                )}
                                            </strong>

                                            ${
                                                [
                                                    'delivered',
                                                    'completed'
                                                ].includes(
                                                    orderData
                                                        .status
                                                )
                                                    ? `
                                                        <a
                                                            class="text-link"
                                                            href="${url(
                                                                'review'
                                                            )}?order_item_id=${encodeURIComponent(
                                                                item.id
                                                            )}"
                                                        >
                                                            Đánh giá
                                                        </a>
                                                    `
                                                    : ''
                                            }

                                        </article>
                                    `
                                )
                                .join('')
                        }

                    </section>

                    <section class="panel">

                        <h2>
                            Giao hàng
                        </h2>

                        <p>
                            ${esc(
                                orderData
                                    .address
                                    ?.full_address ||

                                orderData
                                    .shipping_address
                                    ?.full_address ||

                                orderData
                                    .shipping_address ||

                                'Đang cập nhật'
                            )}
                        </p>

                        <p>
                            ${esc(
                                orderData.note
                            )}
                        </p>

                    </section>

                </div>

                <aside
                    class="panel order-summary"
                >

                    <h2>
                        Thanh toán
                    </h2>

                    ${totals(
                        orderData.summary ||
                        orderData
                    )}

                    ${
                        [
                            'pending',
                            'confirmed'
                        ].includes(
                            orderData.status
                        )
                            ? `
                                <button
                                    class="btn btn--outline"
                                    id="cancel-order"
                                    type="button"
                                >
                                    Hủy đơn
                                </button>
                            `
                            : ''
                    }

                    <div
                        data-feedback
                    ></div>

                </aside>

            </div>
        </div>
        `;


    $('#cancel-order')
        ?.addEventListener(
            'click',
            async (event) => {

                if (
                    !confirm(
                        'Bạn muốn hủy đơn hàng này?'
                    )
                ) {
                    return;
                }

                const button =
                    event.currentTarget;

                button.disabled =
                    true;

                try {

                    await request(
                        '/orders/' +
                        encodeURIComponent(
                            FF.orderCode
                        ) +
                        '/cancel',
                        {
                            method:
                                'POST',

                            body: {
                                reason:
                                    'Khách hàng yêu cầu hủy'
                            }
                        }
                    );

                    await order();

                } catch (error) {

                    main
                        .querySelector(
                            '[data-feedback]'
                        )
                        .innerHTML =
                        errorBox(error);

                } finally {

                    button.disabled =
                        false;
                }
            }
        );
}

export {
    orders,
    order
};
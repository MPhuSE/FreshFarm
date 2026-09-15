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
    updateCount
} from './api.core.js';


// =========================
// TOTALS
// =========================

function totals(summary = {}) {
    return `
        <dl>

            <div class="live-row">
                <dt>Tạm tính</dt>
                <dd>
                    ${cash(
                        summary.subtotal
                    )}
                </dd>
            </div>

            <div class="live-row">
                <dt>Giảm giá</dt>
                <dd>
                    ${cash(
                        summary.discount
                    )}
                </dd>
            </div>

            <div class="live-row">
                <dt>Phí giao hàng</dt>
                <dd>
                    ${cash(
                        summary.shipping_fee
                    )}
                </dd>
            </div>

            <div class="live-row order-total">
                <dt>Tổng cộng</dt>
                <dd>
                    ${cash(
                        summary.grand_total
                    )}
                </dd>
            </div>

        </dl>
    `;
}


// =========================
// CART
// =========================

async function cart() {
    const response =
        await request('/cart');

    const cartData =
        response.data;

    updateCount(cartData);

    const main =
        $('#live-main');

    if (!main) {
        return;
    }

    main.innerHTML =
        heading(
            'Giỏ hàng của bạn'
        )

        +

        (
            !(cartData.items || [])
                .length

                ? `
                    ${message(
                        'Giỏ hàng đang trống.'
                    )}

                    <div class="live-actions">

                        <a
                            class="btn btn--primary"
                            href="${url('shop')}"
                        >
                            Chọn sản phẩm
                        </a>

                    </div>
                `

                :

                `
                    <div data-feedback></div>

                    <div class="cart-layout">

                        <div class="live-stack">

                            ${
                                cartData.items
                                    .map(
                                        (item) => `
                                            <article
                                                class="panel live-row"
                                            >

                                                <img
                                                    src="${safeImage(
                                                        item
                                                            .product
                                                            ?.primary_image_url
                                                    )}"
                                                    alt="${esc(
                                                        item
                                                            .product
                                                            ?.name
                                                    )}"
                                                >

                                                <div>

                                                    <a
                                                        href="${url(
                                                            'product',
                                                            item
                                                                .product
                                                                ?.slug
                                                        )}"
                                                    >
                                                        <strong>
                                                            ${esc(
                                                                item
                                                                    .product
                                                                    ?.name
                                                            )}
                                                        </strong>
                                                    </a>

                                                    <p>
                                                        ${cash(
                                                            item
                                                                .unit_price
                                                        )}
                                                        /
                                                        ${esc(
                                                            item
                                                                .product
                                                                ?.unit
                                                        )}
                                                    </p>

                                                    <button
                                                        class="link-button link-button--danger"
                                                        data-remove="${esc(
                                                            item.id
                                                        )}"
                                                        type="button"
                                                    >
                                                        Xóa
                                                    </button>

                                                </div>

                                                <label>
                                                    Số lượng

                                                    <input
                                                        type="number"
                                                        min="1"
                                                        step="0.001"
                                                        value="${esc(
                                                            item.quantity
                                                        )}"
                                                        data-quantity="${esc(
                                                            item.id
                                                        )}"
                                                        aria-label="Số lượng ${esc(
                                                            item
                                                                .product
                                                                ?.name
                                                        )}"
                                                    >
                                                </label>

                                                <strong>
                                                    ${cash(
                                                        item.line_total
                                                    )}
                                                </strong>

                                            </article>
                                        `
                                    )
                                    .join('')
                            }

                        </div>

                        <aside
                            class="panel order-summary"
                        >

                            <h2>
                                Tổng giỏ hàng
                            </h2>

                            ${totals(
                                cartData.summary
                            )}

                            <a
                                class="btn btn--primary btn--block"
                                href="${url(
                                    'checkout'
                                )}"
                            >
                                Thanh toán
                            </a>

                        </aside>

                    </div>
                `
        );


    const mutate =
        async (
            button,
            path,
            options
        ) => {

            button.disabled =
                true;

            try {

                await request(
                    path,
                    options
                );

                await cart();

            } catch (error) {

                if (
                    error.status === 409
                ) {

                    try {
                        await cart();
                    } catch {
                        // Ignore reload error
                    }
                }

                const feedback =
                    $(
                        '#live-main [data-feedback]'
                    );

                if (feedback) {

                    feedback.innerHTML =
                        errorBox(error);

                } else {

                    showError(error);
                }

            } finally {

                button.disabled =
                    false;
            }
        };



    main
        .querySelectorAll(
            '[data-remove]'
        )
        .forEach(
            (button) => {

                button.onclick =
                    () =>
                        mutate(
                            button,

                            '/cart/items/' +
                            encodeURIComponent(
                                button.dataset
                                    .remove
                            ),

                            {
                                method: 'DELETE'
                            }
                        );
            }
        );

    main
        .querySelectorAll(
            '[data-quantity]'
        )
        .forEach(
            (inputElement) => {

                inputElement.onchange =
                    () => {

                        if (
                            !inputElement
                                .checkValidity()
                        ) {

                            inputElement
                                .reportValidity();

                            return;
                        }

                        mutate(

                            inputElement,

                            '/cart/items/' +
                            encodeURIComponent(
                                inputElement
                                    .dataset
                                    .quantity
                            ),

                            {
                                method: 'PATCH',

                                body: {
                                    quantity:
                                        Number(
                                            inputElement
                                                .value
                                        )
                                }
                            }
                        );
                    };
            }
        );
}

export {
    totals,
    cart
};
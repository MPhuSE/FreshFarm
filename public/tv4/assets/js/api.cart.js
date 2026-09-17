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
    updateCount,
    token
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
    if (!token()) {
        location.href = url('login');
        return;
    }

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
        `
        <div class="container section">
        `

        +

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
                                                        step="1"
                                                        value="${esc(
                                                            Math.floor(Number(item.quantity)) || 1
                                                        )}"
                                                        data-quantity="${esc(
                                                            item.id
                                                        )}"
                                                        onkeydown="if(['.', ',', 'e', 'E', '+', '-'].includes(event.key)) event.preventDefault();"
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

                            <div style="display:flex;gap:8px;margin-top:16px;margin-bottom:8px;">
                                <input id="cart-coupon-input" type="text" placeholder="M\u00e3 gi\u1ea3m gi\u00e1" style="flex:1;border:1px solid #d1d5db;border-radius:8px;padding:10px 14px;font-size:14px;outline:none;">
                                <button type="button" id="cart-coupon-btn" class="btn btn--outline" style="white-space:nowrap;">
                                    \u00c1p d\u1ee5ng
                                </button>
                            </div>
                            <div id="cart-coupon-msg" style="font-size:13px;margin-bottom:12px;display:none;"></div>

                            <a
                                class="btn btn--primary btn--block"
                                href="${url(
                                    'checkout'
                                )}"
                            >
                                Thanh to\u00e1n
                            </a>

                        </aside>

                    </div>
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
                                        Math.floor(Number(
                                            inputElement
                                                .value
                                        )) || 1
                                }
                            }
                        );
                    };
            }
        );

    // Coupon apply - validates via checkout preview endpoint
    const couponBtn = main.querySelector('#cart-coupon-btn');
    if (couponBtn) {
        couponBtn.onclick = async () => {
            const input = main.querySelector('#cart-coupon-input');
            const msg = main.querySelector('#cart-coupon-msg');
            const code = input?.value?.trim();
            if (!code) return;

            couponBtn.disabled = true;
            if (msg) { msg.style.display = 'none'; }

            try {
                // Use checkout preview to validate coupon
                const defaultAddr = null; // coupon validation doesn't need address
                const res = await request('/checkout/preview', {
                    method: 'POST',
                    body: { address_id: 1, payment_method: 'cod', coupon_code: code }
                });
                const discount = res.data?.summary?.discount || 0;
                if (msg) {
                    msg.textContent = discount > 0
                        ? `✅ Áp dụng thành công! Giảm ${new Intl.NumberFormat('vi-VN').format(discount)}đ`
                        : `✅ Mã hợp lệ nhưng không có giảm giá thêm.`;
                    msg.style.color = '#16a34a';
                    msg.style.display = 'block';
                }
                // Save coupon to sessionStorage for checkout page
                sessionStorage.setItem('applied_coupon', code);
            } catch (err) {
                if (msg) {
                    msg.textContent = `❌ Mã không hợp lệ: ${err.message || 'Không thể áp dụng'}`;
                    msg.style.color = '#dc2626';
                    msg.style.display = 'block';
                }
            } finally {
                couponBtn.disabled = false;
            }
        };
    }
}

export {
    totals,
    cart
};
import {
    $,
    esc,
    url,
    token,
    request,
    errorBox,
    heading,
    message,
    input,
    generateUUID
} from './api.core.js';

import {
    totals
} from './api.cart.js';

import {
    updateCount
} from './api.core.js';


async function checkout() {


    if (!token()) {
        location.href =
            url('login');

        return;
    }


    const cartData =
        (
            await request('/cart')
        ).data;

    updateCount(cartData);


    if (!cartData.items?.length) {

        $('#live-main').innerHTML =
            `
            <div class="container section" style="padding-top: 40px; padding-bottom: 80px;">
            `
            +
            heading(
                'Thanh toán'
            ) +

            message(
                'Giỏ hàng trống. ' +
                'Hãy thêm sản phẩm trước.'
            ) +
            `</div>`;

        return;
    }


    const addressResponse =
        await request(
            '/user/addresses'
        );

    const addresses =
        addressResponse.data || [];


    if (!addresses.length) {

        $('#live-main').innerHTML =
            `
            <div class="container section" style="padding-top: 40px; padding-bottom: 80px;">
            `
            +
            heading(
                'Thanh toán'
            ) +

            message(
                'Bạn chưa có địa chỉ giao hàng. ' +
                'Vui lòng thêm địa chỉ trước khi thanh toán.'
            ) +

            `
                <div class="live-actions" style="margin-top: 24px;">

                    <a
                        class="btn btn--primary"
                        href="${url('addresses')}"
                    >
                        Thêm địa chỉ
                    </a>

                </div>
            </div>
            `;

        return;
    }



    const defaultAddress =
        addresses.find(
            (address) =>
                Boolean(
                    address.is_default
                )
        );



    $('#live-main').innerHTML =
        `
        <div class="container section">
        `

        +

        heading(
            'Thanh toán'
        ) +

        `
            <div class="checkout-layout">

                <form
                    class="panel live-form"
                    id="checkout-live"
                >

                    <label>
                        Địa chỉ nhận hàng

                        <select
                            name="address_id"
                            required
                        >

                            <option value="">
                                -- Chọn địa chỉ --
                            </option>

                            ${
                                addresses
                                    .map(
                                        (address) => `
                                            <option
                                                value="${esc(
                                                    address.id
                                                )}"
                                                ${
                                                    defaultAddress &&
                                                    String(
                                                        defaultAddress.id
                                                    ) ===
                                                    String(
                                                        address.id
                                                    )
                                                        ? 'selected'
                                                        : ''
                                                }
                                            >
                                                ${esc(
                                                    address.recipient_name
                                                )}
                                                -
                                                ${esc(
                                                    address.phone
                                                )}
                                                -
                                                ${esc(
                                                    address.address
                                                )}
                                                ${
                                                    address.is_default
                                                        ? ' (Mặc định)'
                                                        : ''
                                                }
                                            </option>
                                        `
                                    )
                                    .join('')
                            }

                        </select>
                    </label>

                    <p class="text-link">

                        <a
                            href="${url('addresses')}"
                        >
                            Quản lý địa chỉ giao hàng
                        </a>

                    </p>

                    <label>
                        Phương thức thanh toán

                        <select
                            name="payment_method"
                        >

                            <option value="cod">
                                Thanh toán khi nhận hàng
                            </option>


                            <option value="vnpay">
                                Thanh toán qua VNPay
                            </option>

                        </select>

                    </label>

                    ${input(
                        'Mã giảm giá',
                        'coupon_code',
                        'text',
                        '',
                        false
                    )}

                    <label>
                        Ghi chú

                        <textarea
                            name="note"
                            rows="3"
                        ></textarea>

                    </label>

                    <button
                        type="submit"
                        class="btn btn--primary"
                    >
                        Kiểm tra đơn hàng
                    </button>

                </form>

                <aside
                    id="checkout-summary"
                    class="panel order-summary"
                >

                    <h2>
                        Tạm tính từ giỏ hàng
                    </h2>

                    ${totals(
                        cartData.summary
                    )}

                    <div
                        id="checkout-preview"
                    ></div>

                </aside>

            </div>
        </div>
        `;

    const form =
        $('#checkout-live');

    let key = null;
    let payload = null;

    form.oninput = () => {

        if (!form.dataset.busy) {

            $('#checkout-preview')
                .innerHTML = '';

            key = null;
            payload = null;
        }
    };

    // Auto-fill coupon code if applied from cart page
    const savedCoupon = sessionStorage.getItem('applied_coupon');
    if (savedCoupon) {
        const couponInput = form.querySelector('[name="coupon_code"]');
        if (couponInput) {
            couponInput.value = savedCoupon;
        }
    }


    form.addEventListener(
        'submit',
        async (event) => {

            event.preventDefault();

            if (form.dataset.busy) {
                return;
            }

            form.dataset.busy =
                '1';

            const button =
                form.querySelector(
                    'button[type="submit"]'
                );

            if (button) {
                button.disabled =
                    true;
            }

            const data =
                new FormData(form);

            try {

                const body =
                    Object.fromEntries(
                        data
                    );

                body.address_id =
                    Number(
                        body.address_id
                    );

                if (!body.coupon_code) {
                    delete body.coupon_code;
                }

                const response =
                    await request(
                        '/checkout/preview',
                        {
                            method: 'POST',
                            body
                        }
                    );

                payload = body;

                key =
                    generateUUID();

                const preview =
                    response.data;

                $('#checkout-summary')
                    .innerHTML =
                    `
                        <h2>
                            Xác nhận đơn hàng
                        </h2>

                        ${totals(
                            preview.summary ||
                            preview
                        )}

                        <div
                            id="checkout-preview"
                        >

                            <p>
                                ${esc(
                                    preview
                                        .address
                                        ?.full_address ||
                                    preview
                                        .address
                                        ?.address_line ||
                                    'Vui lòng kiểm tra thông tin địa chỉ.'
                                )}
                            </p>

                            <button
                                class="btn btn--primary btn--block"
                                id="place-order"
                                type="button"
                            >
                                Xác nhận đặt hàng
                            </button>

                            <div
                                data-feedback
                            ></div>

                        </div>
                    `;

            

                $('#place-order')
                    .onclick =
                    async (event) => {

                        const placeButton =
                            event.currentTarget;

                        placeButton.disabled =
                            true;

                        const controls =
                            [
                                ...form.elements
                            ];

                        controls.forEach(
                            (control) => {
                                control.disabled =
                                    true;
                            }
                        );

                        try {

                            const result =
                                await request(
                                    '/orders',
                                    {
                                        method:
                                            'POST',

                                        body: {
                                            ...payload,

                                            idempotency_key:
                                                key
                                        },

                                        headers: {
                                            'Idempotency-Key':
                                                key
                                        }
                                    }
                                );

                            const order =
                                result.data
                                    ?.order ||
                                result.data;

                            if (order?.payment_method === 'vnpay' && order?.order_code) {
                                try {
                                    const vnpayResult = await request('/payment/vnpay/' + encodeURIComponent(order.order_code), { method: 'POST' });
                                    const paymentUrl = vnpayResult?.data?.payment_url || vnpayResult?.payment_url;
                                    if (paymentUrl) {
                                        location.href = paymentUrl;
                                        return;
                                    }
                                } catch (vnpayError) {
                                    console.error('VNPay error:', vnpayError);
                                }
                            }

                            if (
                                order?.order_code
                            ) {

                                location.href =
                                    url(
                                        'order-detail',
                                        order.order_code
                                    );

                            } else {

                                location.href =
                                    url('orders');
                            }

                        } catch (error) {

                            const feedback =
                                $(
                                    '#checkout-preview [data-feedback]'
                                );

                            if (feedback) {

                                feedback.innerHTML =
                                    errorBox(
                                        error
                                    );
                            }

                            if (
                                error.status ===
                                409
                            ) {

                                placeButton.remove();

                                key = null;
                                payload = null;
                            }

                        } finally {

                            if (
                                document.body.contains(
                                    placeButton
                                )
                            ) {
                                placeButton.disabled =
                                    false;
                            }

                            controls.forEach(
                                (control) => {
                                    control.disabled =
                                        false;
                                }
                            );
                        }
                    };

            } catch (error) {

                const feedback =
                    $(
                        '#checkout-preview'
                    );

                if (feedback) {
                    feedback.innerHTML =
                        errorBox(error);
                }

            } finally {

                delete form.dataset.busy;

                if (button) {
                    button.disabled =
                        false;
                }
            }
        }
    );
}

export {
    checkout
};
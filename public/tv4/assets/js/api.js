import {
    auth
} from './api.auth.js';

import {
    catalog,
    product
} from './api.catalog.js';

import {
    cart
} from './api.cart.js';

import {
    checkout
} from './api.checkout.js';

import {
    orders,
    order
} from './api.order.js';

import {
    review
} from './api.review.js';

import {
    account,
    addresses
} from './api.user.js';

import {
    showError,
    request,
    esc,
    safeImage
} from './api.core.js';


// =========================
// PAGE HANDLERS
// =========================

const handlers = {

    home: () =>
        catalog(true),

    shop:
        catalog,

    product:
        product,

    cart:
        cart,

    checkout:
        checkout,

    orders:
        orders,

    'order-detail':
        order,

    login:
        () =>
            auth(false),

    register:
        () =>
            auth(true),

    account:
        account,

    review:
        review,

    addresses:
        addresses,

    'ui-states':
        async () => {

            const main =
                document.querySelector(
                    '#live-main'
                );

            if (!main) {
                return;
            }

            main.innerHTML = `
                <div class="live-heading">
                    <div>
                        <h1>
                            Trạng thái kết nối
                        </h1>
                    </div>
                </div>

                <div class="alert alert--info">
                    Các màn hình API hiển thị
                    trạng thái tải, rỗng và lỗi
                    theo phản hồi máy chủ.
                </div>
            `;
        }
};


// =========================
// START APPLICATION
// =========================

async function startTV4() {

    try {

        const page =
            document.body.dataset.page;

        const handler =
            handlers[page] ||
            handlers.shop;

        await handler();

        if (
            typeof refreshIcons ===
            'function'
        ) {
            refreshIcons();
        }

    } catch (error) {

        showError(error);
    }
}


// =========================
// DOM READY
// =========================

if (
    document.readyState ===
    'loading'
) {

    document.addEventListener(
        'DOMContentLoaded',
        startTV4
    );

} else {

    startTV4();
}


// =========================
// EXPOSE FOR TESTING
// =========================

window.TV4 = {
    startTV4,
    request,
    esc,
    safeImage
};
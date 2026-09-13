(() => {

    // =========================
    // FORMAT TIỀN
    // =========================
    const money = (n) =>
        new Intl.NumberFormat('vi-VN').format(n) + 'đ';
    // =========================
    // ICON
    // =========================
    const icon = (name) =>
        feather.icons[
            ({ cart: 'shopping-cart' }[name] || name)
        ].toSvg({
            class: 'ui-icon',
            'aria-hidden': 'true',
            focusable: 'false'
        });


    function refreshIcons() {
        feather.replace({
            class: 'ui-icon',
            'aria-hidden': 'true',
            focusable: 'false'
        });
    }


    // =========================
    // HEADER
    // =========================
    function header() {

        const page = document.body.dataset.page;

        document.getElementById('site-header').innerHTML = `
            <div class="topbar">

                <div class="container topbar__inner">

                    <span>
                        ${icon('map-pin')}
                        Thông tin giao hàng
                    </span>

                    <span>
                        Hotline hỗ trợ
                    </span>

                </div>

            </div>


            <div class="container nav">

                <a class="logo" href="${FF.url('index')}">

                    <span class="logo__mark">
                        ${icon('feather')}
                    </span>

                    <span>
                        Nông Sản Xanh
                    </span>

                </a>


                <nav
                    class="nav__links"
                    id="main-nav"
                    aria-label="Điều hướng chính">

                    <a
                        class="${page === 'home' ? 'is-active' : ''}"
                        href="${FF.url('index')}">
                        Trang chủ
                    </a>

                    <a
                        class="${['shop', 'product'].includes(page) ? 'is-active' : ''}"
                        href="${FF.url('shop')}">
                        Sản phẩm
                    </a>

                    <a
                        class="${['orders', 'order-detail', 'review'].includes(page) ? 'is-active' : ''}"
                        href="${FF.url('orders')}">
                        Đơn hàng
                    </a>

                    <a
                        class="${['account', 'addresses'].includes(page) ? 'is-active' : ''}"
                        href="${FF.url('account')}">
                        Tài khoản
                    </a>

                </nav>


                <div class="nav__actions">

                    <a
                        class="icon-btn"
                        aria-label="Đăng nhập"
                        href="${FF.url('login')}">

                        ${icon('user')}

                    </a>


                    <a
                        class="icon-btn"
                        aria-label="Giỏ hàng"
                        href="${FF.url('cart')}">

                        ${icon('cart')}

                        <span
                            class="cart-count"
                            data-cart-count>
                        </span>

                    </a>


                    <button
                        class="icon-btn mobile-toggle"
                        id="mobile-toggle"
                        aria-label="Mở menu"
                        aria-expanded="false">

                        ${icon('menu')}

                    </button>

                </div>

            </div>
        `;
    }


    // =========================
    // FOOTER
    // =========================
    function footer() {

        document.getElementById('site-footer').innerHTML = `

            <div class="site-footer">

                <div class="container">

                    <div class="footer-grid">

                        <div>

                            <a
                                class="logo"
                                href="${FF.url('index')}">

                                <span class="logo__mark">
                                    ${icon('feather')}
                                </span>

                                <span>
                                    Nông Sản Xanh
                                </span>

                            </a>


                            <p>
                                Nông sản Việt tươi sạch,
                                minh bạch nguồn gốc
                                và giao đến tận cửa nhà bạn.
                            </p>

                        </div>


                        <div>

                            <h3>Mua sắm</h3>

                            <a href="${FF.url('shop')}">
                                Tất cả sản phẩm
                            </a>

                            <a href="${FF.url('shop')}">
                                Rau củ theo mùa
                            </a>

                            <a href="${FF.url('cart')}">
                                Giỏ hàng
                            </a>

                        </div>


                        <div>

                            <h3>Tài khoản</h3>

                            <a href="${FF.url('login')}">
                                Đăng nhập
                            </a>

                            <a href="${FF.url('account')}">
                                Hồ sơ
                            </a>

                            <a href="${FF.url('orders')}">
                                Lịch sử đơn
                            </a>

                        </div>


                        <div>

                            <h3>Hỗ trợ</h3>

                            <a href="#">
                                Chính sách giao hàng
                            </a>

                            <a href="#">
                                Đổi trả sản phẩm
                            </a>

                            <a href="#">
                                Liên hệ
                            </a>

                        </div>

                    </div>


                    <div class="footer-bottom">

                        <span>
                            © 2026 Nông Sản Xanh
                        </span>

                        <span>
                            Đồ án Laravel
                        </span>

                    </div>

                </div>

            </div>
        `;
    }


    // =========================
    // THÔNG BÁO
    // =========================
    function toast(message) {

        const t = document.getElementById('toast');

        if (!t) return;

        t.textContent = message;

        t.classList.add('is-visible');

        clearTimeout(window.toastTimer);

        window.toastTimer = setTimeout(() => {
            t.classList.remove('is-visible');
        }, 2200);
    }


    // =========================
    // MENU TÀI KHOẢN
    // =========================
    function accountNav() {

        const target =
            document.querySelector('[data-account-nav]');

        if (!target) return;

        const page =
            document.body.dataset.page;

        target.className = 'account-nav';

        target.innerHTML = `

            <a
                class="${page === 'account' ? 'is-active' : ''}"
                href="${FF.url('account')}">

                ${icon('user')}
                Hồ sơ cá nhân

            </a>


            <a
                class="${page === 'addresses' ? 'is-active' : ''}"
                href="${FF.url('addresses')}">

                ${icon('map-pin')}
                Địa chỉ giao hàng

            </a>


            <a
                class="${['orders', 'order-detail', 'review'].includes(page) ? 'is-active' : ''}"
                href="${FF.url('orders')}">

                ${icon('package')}
                Lịch sử đơn

            </a>


            <a href="${FF.url('ui-states')}">

                ${icon('activity')}
                Trạng thái API

            </a>


            <a href="${FF.url('login')}">

                ${icon('log-out')}
                Đăng xuất

            </a>
        `;
    }


    // =========================
    // CHECKOUT CHOICE
    // =========================
    function setChoice(card) {

        const group =
            card.closest('.checkout-section');

        group
            ?.querySelectorAll('.choice-card')
            .forEach(x =>
                x.classList.remove('is-selected')
            );

        card.classList.add('is-selected');
    }


    // =========================
    // GALLERY
    // =========================
    function setupGallery() {

        document
            .querySelectorAll('.gallery__thumbs button')
            .forEach(button => {

                button.addEventListener('click', () => {

                    document
                        .querySelectorAll('.gallery__thumbs button')
                        .forEach(x =>
                            x.classList.remove('is-active')
                        );

                    button.classList.add('is-active');


                    const image =
                        button.querySelector('img');


                    const mainImage =
                        document.getElementById(
                            'main-product-image'
                        );


                    if (image && mainImage) {

                        mainImage.src =
                            image.src;

                    }

                });

            });
    }


    // =========================
    // TABS
    // =========================
    function setupTabs() {

        document
            .querySelectorAll('[data-tab]')
            .forEach(button => {

                button.addEventListener('click', () => {

                    document
                        .querySelectorAll('[data-tab]')
                        .forEach(x =>
                            x.classList.remove('is-active')
                        );


                    document
                        .querySelectorAll('[data-panel]')
                        .forEach(x =>
                            x.classList.remove('is-active')
                        );


                    button.classList.add('is-active');


                    document
                        .querySelector(
                            `[data-panel="${button.dataset.tab}"]`
                        )
                        ?.classList.add('is-active');

                });

            });
    }


    // =========================
    // FILTER TRẠNG THÁI ĐƠN
    // =========================
    function setupOrderFilter() {

        const filter =
            document.getElementById('order-filter');

        if (!filter) return;


        filter.addEventListener('change', e => {

            let shown = 0;


            document
                .querySelectorAll('[data-order-status]')
                .forEach(card => {

                    const visible =
                        e.target.value === 'all' ||
                        card.dataset.orderStatus ===
                        e.target.value;


                    card.hidden = !visible;


                    if (visible) {
                        shown++;
                    }

                });


            const empty =
                document.getElementById('orders-empty');


            if (empty) {
                empty.hidden = !!shown;
            }

        });

    }


    // =========================
    // FORM
    // =========================
    function setupForms() {

        // Form demo nếu có
        document
            .querySelectorAll('[data-demo-form]')
            .forEach(form => {

                form.addEventListener('submit', e => {

                    e.preventDefault();

                    if (!form.checkValidity()) {

                        form.reportValidity();

                        return;
                    }


                    toast(
                        'Biểu mẫu đã hợp lệ.'
                    );

                });

            });


        // Hồ sơ
        document
            .querySelector('[data-save-form]')
            ?.addEventListener('submit', e => {

                e.preventDefault();

                toast(
                    'Chưa có API cập nhật hồ sơ.'
                );

            });


        // Review
        document
            .querySelector('[data-review-form]')
            ?.addEventListener('submit', e => {

                if (!e.currentTarget.checkValidity()) {

                    e.preventDefault();

                    e.currentTarget.reportValidity();

                }

            });


        // Checkout
        document
            .getElementById('checkout-form')
            ?.addEventListener('submit', e => {

                if (!e.currentTarget.checkValidity()) {

                    e.preventDefault();

                    e.currentTarget.reportValidity();

                }

            });

    }


    // =========================
    // DOCUMENT READY
    // =========================
    document.addEventListener(
        'DOMContentLoaded',
        () => {

            header();

            footer();

            accountNav();

            setupGallery();

            setupTabs();

            setupOrderFilter();

            setupForms();

            refreshIcons();


            // Mobile menu
            document
                .getElementById('mobile-toggle')
                ?.addEventListener('click', e => {

                    const nav =
                        document.getElementById('main-nav');


                    if (!nav) return;


                    nav.classList.toggle('is-open');


                    e.currentTarget.setAttribute(
                        'aria-expanded',
                        nav.classList.contains('is-open')
                    );

                });
            // Thanh toán
            document
                .querySelectorAll('.choice-card input')
                .forEach(radio => {

                    radio.addEventListener(
                        'change',
                        () =>
                            setChoice(
                                radio.closest(
                                    '.choice-card'
                                )
                            )
                    );

                });


            // Hiện / ẩn mật khẩu
            document
                .querySelectorAll('[data-toggle-password]')
                .forEach(button => {

                    button.addEventListener(
                        'click',
                        () => {

                            const input =
                                button
                                    .closest('.password-field')
                                    ?.querySelector('input');


                            if (!input) return;


                            input.type =
                                input.type === 'password'
                                    ? 'text'
                                    : 'password';

                        }
                    );

                });


            // Các nút giao diện
            document.addEventListener(
                'click',
                e => {

                    if (
                        e.target.closest(
                            '[data-open-address]'
                        )
                    ) {

                        document
                            .getElementById('address-modal')
                            ?.showModal();

                    }


                    if (
                        e.target.closest(
                            '[data-cancel-order]'
                        )
                    ) {

                        document
                            .getElementById('cancel-modal')
                            ?.showModal();

                    }

                }
            );

        }
    );

})();
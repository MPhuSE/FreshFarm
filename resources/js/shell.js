'use strict';

const money = (n) => {
    return new Intl.NumberFormat('vi-VN').format(n) + 'đ';
};

const icon = (name) => {
    const iconName = {
        cart: 'shopping-cart'
    }[name] || name;

    return feather.icons[iconName].toSvg({
        class: 'ui-icon',
        'aria-hidden': 'true',
        focusable: 'false'
    });
};

function refreshIcons() {
    feather.replace({
        class: 'ui-icon',
        'aria-hidden': 'true',
        focusable: 'false'
    });
}


function header() {
    const page =
        document.body.dataset.page;

    const headerElement =
        document.getElementById(
            'site-header'
        );

    if (!headerElement) {
        return;
    }

    headerElement.innerHTML = `
        <div class="topbar">

            <div class="container topbar__inner">

                <span>
                    ${icon('map-pin')}
                    Giao hàng tại TP.HCM
                </span>

                <span>
                    Hotline: 0901 234 567 · 7:00–21:00
                </span>

            </div>

        </div>

        <div class="container nav">

            <a
                class="logo"
                href="${FF.url('index')}"
            >
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
                aria-label="Điều hướng chính"
            >

                <a
                    class="${page === 'home'
                        ? 'is-active'
                        : ''
                    }"
                    href="${FF.url('index')}"
                >
                    Trang chủ
                </a>

                <a
                    class="${['shop', 'product'].includes(page)
                        ? 'is-active'
                        : ''
                    }"
                    href="${FF.url('shop')}"
                >
                    Sản phẩm
                </a>

                <a
                    class="${[
                        'orders',
                        'order-detail',
                        'review'
                    ].includes(page)
                        ? 'is-active'
                        : ''
                    }"
                    href="${FF.url('orders')}"
                >
                    Đơn hàng
                </a>

                <a
                    class="${[
                        'account',
                        'addresses'
                    ].includes(page)
                        ? 'is-active'
                        : ''
                    }"
                    href="${FF.url('account')}"
                >
                    Tài khoản
                </a>

            </nav>

            <div class="nav__actions">

                <a
                    class="icon-btn"
                    aria-label="Đăng nhập"
                    href="${FF.url('account')}"
                >
                    ${icon('user')}
                </a>

                <a
                    class="icon-btn"
                    aria-label="Giỏ hàng"
                    href="${FF.url('cart')}"
                >
                    ${icon('cart')}

                    <span
                        class="cart-count"
                        data-cart-count
                    >
                        0
                    </span>
                </a>

                <button
                    class="icon-btn mobile-toggle"
                    id="mobile-toggle"
                    type="button"
                    aria-label="Mở menu"
                    aria-expanded="false"
                >
                    ${icon('menu')}
                </button>

            </div>

        </div>
    `;
}


function footer() {
    const footerElement =
        document.getElementById(
            'site-footer'
        );

    if (!footerElement) {
        return;
    }

    footerElement.innerHTML = `
        <div class="site-footer">

            <div class="container">

                <div class="footer-grid">

                    <div>

                        <a
                            class="logo"
                            href="${FF.url('index')}"
                        >
                            <span class="logo__mark">
                                ${icon('feather')}
                            </span>

                            <span>
                                Nông Sản Xanh
                            </span>
                        </a>

                        <p>
                            Nông sản Việt tươi sạch,
                            minh bạch nguồn gốc và
                            giao đến tận cửa nhà bạn.
                        </p>

                    </div>

                    <div>

                        <h3>
                            Mua sắm
                        </h3>

                        <a
                            href="${FF.url('shop')}"
                        >
                            Tất cả sản phẩm
                        </a>

                        <a
                            href="${FF.url('shop')}"
                        >
                            Rau củ theo mùa
                        </a>

                        <a
                            href="${FF.url('cart')}"
                        >
                            Giỏ hàng
                        </a>

                    </div>

                    <div>

                        <h3>
                            Tài khoản
                        </h3>

                        <a
                            href="${FF.url('login')}"
                        >
                            Đăng nhập
                        </a>

                        <a
                            href="${FF.url('account')}"
                        >
                            Hồ sơ
                        </a>

                        <a
                            href="${FF.url('orders')}"
                        >
                            Lịch sử đơn
                        </a>

                    </div>

                    <div>

                        <h3>
                            Hỗ trợ
                        </h3>

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
                        Đồ án Laravel ·

                        <a
                            href="${
                                FF.staticPreview
                                    ? 'LICENSE.txt'
                                    : FF.base +
                                      '/tv4/LICENSE.txt'
                            }"
                        >
                            MIT assets notice
                        </a>
                    </span>

                </div>

            </div>

        </div>
    `;
}



function toast(message) {
    const toastElement =
        document.getElementById('toast');

    if (!toastElement) {
        return;
    }

    toastElement.textContent =
        message;

    toastElement.classList.add(
        'is-visible'
    );

    clearTimeout(
        window.toastTimer
    );

    window.toastTimer =
        setTimeout(() => {
            toastElement.classList.remove(
                'is-visible'
            );
        }, 2200);
}


function accountNav() {
    const target =
        document.querySelector(
            '[data-account-nav]'
        );

    if (!target) {
        return;
    }

    const page =
        document.body.dataset.page;

    target.className =
        'account-nav';

    target.innerHTML = `
        <a
            class="${page === 'account'
                ? 'is-active'
                : ''
            }"
            href="${FF.url('account')}"
        >
            ${icon('user')}
            Hồ sơ cá nhân
        </a>

        <a
            class="${page === 'addresses'
                ? 'is-active'
                : ''
            }"
            href="${FF.url('addresses')}"
        >
            ${icon('map-pin')}
            Địa chỉ giao hàng
        </a>

        <a
            class="${[
                'orders',
                'order-detail',
                'review'
            ].includes(page)
                ? 'is-active'
                : ''
            }"
            href="${FF.url('orders')}"
        >
            ${icon('package')}
            Lịch sử đơn
        </a>

        <a
            href="${FF.url('ui-states')}"
        >
            ${icon('activity')}
            Trạng thái API
        </a>

        <a
            href="${FF.url('login')}"
            data-logout
        >
            ${icon('log-out')}
            Đăng xuất
        </a>
    `;
}


function initLogout() {
    document.addEventListener(
        'click',
        async (event) => {
            const link =
                event.target.closest(
                    '[data-logout]'
                );

            if (!link) {
                return;
            }

            event.preventDefault();

            try {
                await fetch(
                    FF.base +
                    '/api/v1/auth/logout',
                    {
                        method: 'POST',
                        credentials:
                            'same-origin',
                        headers: {
                            Accept:
                                'application/json'
                        }
                    }
                );

            } finally {
                sessionStorage.removeItem(
                    'access_token'
                );

                localStorage.removeItem(
                    'access_token'
                );

                localStorage.removeItem(
                    'current_user'
                );

                document.cookie =
                    'freshfarm_auth=; Max-Age=0; path=/; SameSite=Lax';

                location.href =
                    FF.url('login');
            }
        }
    );
}


function initMobileMenu() {
    const toggle =
        document.getElementById(
            'mobile-toggle'
        );

    const nav =
        document.getElementById(
            'main-nav'
        );

    if (!toggle || !nav) {
        return;
    }

    toggle.onclick = (event) => {
        nav.classList.toggle(
            'is-open'
        );

        const isOpen =
            nav.classList.contains(
                'is-open'
            );

        event.currentTarget.setAttribute(
            'aria-expanded',
            String(isOpen)
        );
    };
}
document.addEventListener(
    'DOMContentLoaded',
    () => {
        header();
        footer();
        accountNav();
        refreshIcons();
        initMobileMenu();
        initLogout();

        const cartCount =
            document.querySelector(
                '[data-cart-count]'
            );

        if (cartCount) {
            cartCount.textContent =
                '0';
        }
    }
);

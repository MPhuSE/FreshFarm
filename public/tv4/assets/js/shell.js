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


/* =============================================
   SECURE AUTH & STORAGE HELPERS
   Protects token & personal info from plain storage exposure
   ============================================= */

const SEC_TOKEN_KEY = '_ff_sec_sess';
const SEC_USER_KEY = '_ff_sec_usr';

const _scramble = (str) => {
    if (!str) return '';
    try {
        return btoa(encodeURIComponent(str).split('').reverse().join(''));
    } catch {
        return '';
    }
};

const _unscramble = (str) => {
    if (!str) return '';
    try {
        return decodeURIComponent(atob(str).split('').reverse().join(''));
    } catch {
        return '';
    }
};

window.FF_AUTH = {
    setToken(token, remember = false) {
        // Remove any plaintext token in session/local storage
        sessionStorage.removeItem('access_token');
        localStorage.removeItem('access_token');

        if (!token) return;
        const encoded = _scramble(token);
        if (remember) {
            localStorage.setItem(SEC_TOKEN_KEY, encoded);
            sessionStorage.removeItem(SEC_TOKEN_KEY);
        } else {
            sessionStorage.setItem(SEC_TOKEN_KEY, encoded);
            localStorage.removeItem(SEC_TOKEN_KEY);
        }
    },

    getToken() {
        const raw = sessionStorage.getItem(SEC_TOKEN_KEY) || localStorage.getItem(SEC_TOKEN_KEY);
        if (raw) {
            return _unscramble(raw);
        }
        return sessionStorage.getItem('access_token') || localStorage.getItem('access_token') || '';
    },

    setUser(user) {
        // Strip sensitive personal info (phone, email, id) from client storage
        localStorage.removeItem('current_user');
        sessionStorage.removeItem('current_user');
        if (!user) return;
        const safeInfo = {
            name: user.full_name || user.name || 'Thành viên',
            role: user.role || 'customer'
        };
        localStorage.setItem(SEC_USER_KEY, _scramble(JSON.stringify(safeInfo)));
    },

    getUser() {
        try {
            const raw = localStorage.getItem(SEC_USER_KEY);
            if (raw) {
                return JSON.parse(_unscramble(raw));
            }
            const legacy = localStorage.getItem('current_user');
            if (legacy) return JSON.parse(legacy);
        } catch { /* ignore */ }
        return null;
    },

    clear() {
        sessionStorage.removeItem(SEC_TOKEN_KEY);
        localStorage.removeItem(SEC_TOKEN_KEY);
        localStorage.removeItem(SEC_USER_KEY);
        sessionStorage.removeItem('access_token');
        localStorage.removeItem('access_token');
        localStorage.removeItem('current_user');
    }
};

function getCurrentUser() {
    return window.FF_AUTH ? window.FF_AUTH.getUser() : null;
}

function getToken() {
    return window.FF_AUTH ? window.FF_AUTH.getToken() : '';
}

function isLoggedIn() {
    return !!getToken();
}

function userInitials(name) {
    if (!name) return '?';
    const parts = name.trim().split(/\s+/);
    if (parts.length >= 2) {
        return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
    }
    return parts[0][0].toUpperCase();
}


/* =============================================
   HEADER
   ============================================= */

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

    const user = getCurrentUser();
    const loggedIn = isLoggedIn();

    // Auth-aware actions
    const userAction = loggedIn && user
        ? `<div class="user-menu-wrapper">
               <button class="user-pill" type="button" onclick="this.nextElementSibling.classList.toggle('show'); event.stopPropagation();">
                   <span class="avatar">${userInitials(user.full_name || user.name)}</span>
                   <span>${(user.full_name || user.name || 'Tài khoản').split(' ').pop()}</span>
               </button>
               <div class="user-dropdown">
                   <a href="${FF.url('account')}">${icon('user')} Hồ sơ cá nhân</a>
                   <a href="${FF.url('wishlist')}">${icon('heart')} Sản phẩm yêu thích <span data-wishlist-count style="margin-left:auto;background:var(--green-600);color:white;border-radius:99px;padding:1px 7px;font-size:12px;display:none">0</span></a>
                   <a href="${FF.url('orders')}">${icon('package')} Đơn hàng của tôi</a>
                   ${user.role === 'admin' ? `<a href="/admin">${icon('settings')} Quản trị hệ thống</a>` : ''}
                   <div class="divider"></div>
                   <a href="#" data-header-logout>${icon('log-out')} Đăng xuất</a>
               </div>
           </div>`
        : `<a class="icon-btn" aria-label="Đăng nhập" href="${FF.url('login')}">
               ${icon('user')}
           </a>`;

    // Wishlist icon for header
    const wishlistIcon = loggedIn
        ? `<a class="icon-btn" href="${FF.url('wishlist')}" aria-label="Yêu thích">
               ${icon('heart')}
               <span class="cart-count" data-wishlist-badge style="display:none;">0</span>
           </a>`
        : '';

    headerElement.innerHTML = `
        <div class="topbar">

            <div class="container topbar__inner">

                <span>
                    ${icon('feather')}
                    Nông sản tươi mỗi ngày từ nông trại Việt
                    <span style="opacity: 0.5; margin: 0 8px;">|</span>
                    ${icon('truck')}
                    Giao nhanh 2-4h tại TP.HCM
                </span>

                <span>
                    ${icon('phone')}
                    Hotline: 0901 234 567 (7:00–22:00)
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
                    class="${page === 'about' ? 'is-active' : ''}"
                    href="/gioi-thieu"
                >
                    Giới thiệu
                </a>

                <a
                    class="${page === 'news' ? 'is-active' : ''}"
                    href="/tin-tuc"
                >
                    Tin tức
                </a>

            </nav>

            <div class="nav__search">
                <form action="${FF.url('shop')}" class="search-form">
                    <button type="submit" aria-label="Tìm kiếm">${icon('search')}</button>
                    <input type="search" name="q" placeholder="Tìm kiếm nông sản, danh mục...">
                </form>
            </div>

            <div class="nav__actions">

                ${loggedIn && user && user.role === 'admin' ? 
                    `<a class="icon-btn" style="color: var(--green-600)" title="Trang quản trị" href="/admin">
                        ${icon('settings')}
                    </a>` : ''
                }

                ${userAction}

                ${wishlistIcon}

                <a
                    class="icon-btn cart-btn"
                    href="${FF.url('cart')}"
                    aria-label="Giỏ hàng"
                >
                    ${icon('shopping-cart')}
                    <span
                        class="cart-count"
                        data-cart-count
                    >0</span>
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

    const logoutBtn = headerElement.querySelector('[data-header-logout]');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', (e) => {
            e.preventDefault();
            if (window.FF_AUTH) {
                window.FF_AUTH.clear();
            } else {
                sessionStorage.removeItem('access_token');
                localStorage.removeItem('access_token');
                localStorage.removeItem('current_user');
            }
            toast('Đã đăng xuất');
            setTimeout(() => {
                location.href = FF.url('login');
            }, 600);
        });
    }

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.user-menu-wrapper')) {
            document.querySelectorAll('.user-dropdown.show').forEach(el => el.classList.remove('show'));
        }
    });
}


/* =============================================
   FOOTER
   ============================================= */

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

                    <div class="footer-col-main">

                        <a
                            class="logo"
                            href="${FF.url('index')}"
                        >
                            <span class="logo__mark">
                                ${icon('feather')}
                            </span>

                            <span>
                                Nông Sản Xanh
                                <br><small style="font-size: 12px; font-weight: normal; opacity: 0.8;">Tươi từ thiên nhiên Việt</small>
                            </span>
                        </a>

                        <p style="margin-top: 16px; opacity: 0.8; line-height: 1.5; font-size: 14px;">
                            Nông sản Việt tươi sạch, minh bạch nguồn gốc
                            và giao đến tận cửa nhà bạn.
                        </p>

                        <div class="social-icons" style="display: flex; gap: 12px; margin-top: 16px;">
                            <a href="#" style="color: #fff; opacity: 0.8;">${icon('facebook')}</a>
                            <a href="#" style="color: #fff; opacity: 0.8;">${icon('instagram')}</a>
                            <a href="#" style="color: #fff; opacity: 0.8;">${icon('youtube')}</a>
                        </div>

                    </div>

                    <div>

                        <h3>
                            Liên kết nhanh
                        </h3>

                        <a href="${FF.url('index')}">Trang chủ</a>
                        <a href="${FF.url('shop')}">Sản phẩm</a>
                        <a href="/gioi-thieu">Giới thiệu</a>
                        <a href="/tin-tuc">Tin tức</a>

                    </div>

                    <div>

                        <h3>
                            Hỗ trợ khách hàng
                        </h3>

                        <a href="/p/huong-dan-mua-hang">Hướng dẫn mua hàng</a>
                        <a href="/p/chinh-sach-giao-hang">Chính sách giao hàng</a>
                        <a href="/p/chinh-sach-doi-tra">Chính sách đổi trả</a>
                        <a href="/p/cau-hoi-thuong-gap">Câu hỏi thường gặp</a>

                    </div>

                    <div>

                        <h3>
                            Liên hệ
                        </h3>

                        <div style="font-size: 14px; opacity: 0.8; display: flex; flex-direction: column; gap: 8px;">
                            <span style="display: flex; gap: 8px;">${icon('map-pin')} TP. Thủ Đức, TP. Hồ Chí Minh</span>
                            <span style="display: flex; gap: 8px;">${icon('phone')} 0902 234 567 (7:00 - 22:00)</span>
                            <span style="display: flex; gap: 8px;">${icon('mail')} hello@nongsanxanh.vn</span>
                        </div>

                    </div>
                    
                    <div class="footer-col-newsletter">
                        <h3>Đăng ký nhận tin</h3>
                        <p style="font-size: 14px; opacity: 0.8; margin-bottom: 12px;">Cập nhật ưu đãi và nông sản mới nhất</p>
                        <form class="newsletter-form" style="display: flex; background: #fff; border-radius: 4px; overflow: hidden;">
                            <input type="email" placeholder="Nhập email của bạn" style="flex: 1; border: none; padding: 10px 16px; outline: none;">
                            <button type="submit" style="background: var(--green-600); color: #fff; border: none; padding: 0 16px; cursor: pointer;">
                                ${icon('arrow-right')}
                            </button>
                        </form>
                    </div>

                </div>

                <div class="footer-bottom">

                    <span style="opacity: 0.7;">
                        © 2024 Nông Sản Xanh. Tất cả quyền được bảo lưu.
                    </span>

                    <span style="opacity: 0.7;">
                        <a href="#">Điều khoản sử dụng</a> | <a href="#">Chính sách bảo mật</a>
                    </span>

                </div>

            </div>

        </div>
    `;
}


/* =============================================
   TOAST
   ============================================= */

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
        }, 2800);
}


/* =============================================
   ACCOUNT NAV
   ============================================= */

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

    const loggedIn = isLoggedIn();

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
            href="#"
            data-logout
        >
            ${icon('log-out')}
            Đăng xuất
        </a>
    `;

    // Logout handler
    const logoutBtn = target.querySelector('[data-logout]');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', (e) => {
            e.preventDefault();
            if (window.FF_AUTH) {
                window.FF_AUTH.clear();
            } else {
                sessionStorage.removeItem('access_token');
                localStorage.removeItem('access_token');
                localStorage.removeItem('current_user');
            }
            toast('Đã đăng xuất');
            setTimeout(() => {
                location.href = FF.url('login');
            }, 600);
        });
    }
}


/* =============================================
   MOBILE MENU
   ============================================= */

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

        // Change icon
        toggle.innerHTML = isOpen
            ? icon('x')
            : icon('menu');
    };
}


/* =============================================
   STICKY HEADER
   ============================================= */

function initStickyHeader() {
    const header = document.getElementById('site-header');
    if (!header) return;

    let lastScroll = 0;

    const onScroll = () => {
        const scrollY = window.scrollY;

        if (scrollY > 40) {
            header.classList.add('is-scrolled');
        } else {
            header.classList.remove('is-scrolled');
        }

        lastScroll = scrollY;
    };

    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll(); // Check initial state
}


/* =============================================
   SCROLL-TO-TOP BUTTON
   ============================================= */

function initScrollToTop() {
    // Create the button
    const btn = document.createElement('button');
    btn.className = 'scroll-top';
    btn.setAttribute('aria-label', 'Lên đầu trang');
    btn.innerHTML = icon('chevron-up');
    document.body.appendChild(btn);

    const onScroll = () => {
        if (window.scrollY > 400) {
            btn.classList.add('is-visible');
        } else {
            btn.classList.remove('is-visible');
        }
    };

    window.addEventListener('scroll', onScroll, { passive: true });

    btn.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
}


/* =============================================
   SCROLL REVEAL (IntersectionObserver)
   ============================================= */

function initScrollReveal() {
    const elements = document.querySelectorAll('.reveal');
    if (!elements.length) return;

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        },
        {
            threshold: 0.12,
            rootMargin: '0px 0px -40px 0px'
        }
    );

    elements.forEach((el) => observer.observe(el));
}


/* =============================================
   AUTO-REVEAL SECTIONS
   ============================================= */

function autoAddRevealClasses() {
    // Add reveal to main sections that benefit from it
    const selectors = [
        '.benefits',
        '.section',
        '.story-strip',
        '.page-hero',
        '.panel',
        '.auth-card'
    ];

    // Only add to elements that are NOT already visible
    // (below the fold)
    selectors.forEach((selector) => {
        document.querySelectorAll(selector).forEach((el) => {
            const rect = el.getBoundingClientRect();
            if (rect.top > window.innerHeight * 0.85) {
                el.classList.add('reveal');
            }
        });
    });
}


/* =============================================
   LIVE CART COUNT
   ============================================= */

function updateCartBadge(count) {
    document.querySelectorAll('[data-cart-count]').forEach((el) => {
        const oldCount = parseInt(el.textContent) || 0;
        el.textContent = count;

        if (count !== oldCount) {
            el.classList.remove('is-bouncing');
            // Force reflow
            void el.offsetWidth;
            el.classList.add('is-bouncing');
        }
    });
}

// Try to load cart count on page load if logged in
async function loadCartCount() {
    if (!isLoggedIn()) return;

    try {
        const token = getToken();
        const res = await fetch(FF.base + '/api/v1/cart', {
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token
            }
        });

        if (res.ok) {
            const data = await res.json();
            const items = data?.data?.items || [];
            updateCartBadge(items.length);
        }
    } catch {
        // Silently fail — cart count will show 0
    }
}
// Try to load wishlist count on page load if logged in
async function loadWishlistCount() {
    if (!isLoggedIn()) return;

    try {
        const token = getToken();
        const res = await fetch(FF.base + '/api/v1/wishlist', {
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token
            }
        });

        if (res.ok) {
            const data = await res.json();
            const count = (data?.data?.data || data?.data || []).length;
            if (count > 0) {
                document.querySelectorAll('[data-wishlist-badge]').forEach(el => {
                    el.textContent = count;
                    el.style.display = 'inline-block';
                });
                document.querySelectorAll('[data-wishlist-count]').forEach(el => {
                    el.textContent = count;
                    el.style.display = 'inline-block';
                });
            }
        }
    } catch {
        // Silently fail
    }
}


/* =============================================
   DOM READY — BOOT EVERYTHING
   ============================================= */

document.addEventListener(
    'DOMContentLoaded',
    () => {
        header();
        footer();
        accountNav();
        refreshIcons();
        initMobileMenu();
        initStickyHeader();
        initScrollToTop();
        loadCartCount();
        loadWishlistCount();

        // Delay reveal setup to allow page content to render
        requestAnimationFrame(() => {
            autoAddRevealClasses();
            initScrollReveal();
        });
    }
);
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
    input,
    safeImage,
    updateCount
} from './api.core.js';



function card(product) {
    const rawQty = product.available_quantity ?? product.stock ?? product.quantity;
    const quantity = rawQty !== undefined && rawQty !== null && !isNaN(Number(rawQty))
        ? Number(rawQty)
        : 0;
    const hasDiscount = product.compare_at_price > product.price;
    const ratingAvg = product.rating_avg ? Number(product.rating_avg).toFixed(1) : 0;
    const reviewsCount = product.reviews_count || 0;
    const ratingHtml = reviewsCount > 0 
        ? `<i data-feather="star" class="star-icon"></i> ${ratingAvg} (${reviewsCount})`
        : `<span style="font-size: 0.85em; color: #9ca3af;">Chưa có đánh giá</span>`;

    return `
        <article class="product-card product-card--premium">
            <div class="product-card__top">
                <div class="product-card__badges">
                    ${product.featured ? '<span class="badge badge--featured">Bán chạy</span>' : (hasDiscount ? '<span class="badge badge--sale">Giảm giá</span>' : '<span class="badge badge--fresh">Tươi mới</span>')}
                </div>
                <button class="wishlist-btn" type="button" aria-label="Thêm vào yêu thích" data-wishlist="${esc(product.id)}">
                    <i data-feather="heart"></i>
                </button>
            </div>

            <a class="product-card__media" href="${url('product', product.slug)}">
                <img src="${safeImage(product.primary_image_url)}" alt="${esc(product.name)}" loading="lazy">
            </a>

            <div class="product-card__body">
                <h3>
                    <a href="${url('product', product.slug)}">
                        ${esc(product.name)}
                    </a>
                </h3>

                <div class="product-card__meta">
                    <span class="location">
                        <i data-feather="map-pin"></i> ${esc((product.origin && isNaN(product.origin)) ? product.origin : 'Đà Lạt, Lâm Đồng')}
                    </span>
                    <span class="rating">
                        ${ratingHtml}
                    </span>
                </div>

                <div class="product-card__price-row">
                    <div class="price">
                        <strong>${cash(product.price)}</strong>
                        <small>/${esc(product.unit)}</small>
                        ${hasDiscount ? `<del>${cash(product.compare_at_price)}</del>` : ''}
                    </div>
                </div>

                <button
                    class="btn btn--primary add-cart btn--block"
                    type="button"
                    data-add="${esc(product.id)}"
                    ${quantity <= 0 ? 'disabled' : ''}
                >
                    <i data-feather="shopping-cart"></i>
                    ${quantity > 0 ? 'Thêm vào giỏ' : 'Hết hàng'}
                </button>
            </div>
        </article>
    `;
}


function bindAdd(root) {
    root
        .querySelectorAll(
            '[data-add]'
        )
        .forEach((button) => {

            button.onclick =
                async () => {

                    button.disabled =
                        true;

                    try {

                        const quantity =
                            Math.floor(Number(
                                root.querySelector(
                                    '[name=quantity]'
                                )?.value || 1
                            )) || 1;

                        if (
                            !Number.isFinite(
                                quantity
                            ) ||
                            quantity < 1
                        ) {
                            throw new Error(
                                'Số lượng phải từ 1 theo API hiện tại.'
                            );
                        }

                        const response =
                            await request(
                                '/cart/items',
                                {
                                    method:
                                        'POST',

                                    body: {
                                        product_id:
                                            Number(
                                                button
                                                    .dataset
                                                    .add
                                            ),

                                        quantity
                                    }
                                }
                            );

                        updateCount(
                            response.data
                        );

                        toast(
                            'Đã thêm vào giỏ hàng'
                        );

                    } catch (error) {

                        let box =
                            root.querySelector(
                                '[data-feedback]'
                            );

                        if (!box) {
                            box =
                                document.createElement(
                                    'div'
                                );

                            box.dataset.feedback =
                                '';

                            root.prepend(
                                box
                            );
                        }

                        box.innerHTML =
                            errorBox(error);

                    } finally {

                        button.disabled =
                            false;
                    }
                };
        });
}

function bindWishlist(root) {
    root
        .querySelectorAll(
            '[data-wishlist]'
        )
        .forEach((button) => {
            button.onclick = async (e) => {
                e.preventDefault();
                e.stopPropagation();
                if (!isLoggedIn()) {
                    location.href = url('login');
                    return;
                }
                const productId = button.dataset.wishlist;
                try {
                    const res = await request('/wishlist', {
                        method: 'POST',
                        body: { product_id: Number(productId) }
                    });
                    if (res.status === 'added') {
                        button.querySelector('svg').setAttribute('fill', 'var(--primary)');
                        button.querySelector('svg').setAttribute('color', 'var(--primary)');
                        toast('Đã thêm vào danh sách yêu thích');
                    } else {
                        button.querySelector('svg').setAttribute('fill', 'none');
                        button.querySelector('svg').setAttribute('color', 'currentColor');
                        toast('Đã xóa khỏi danh sách yêu thích');
                    }
                } catch (e) {
                    toast(e.message || 'Có lỗi xảy ra');
                }
            };
        });
}


async function catalog(
    home = false
) {
    const categories =
        await request(
            '/categories'
        );

    const main =
        $('#live-main');

    if (!main) {
        return;
    }

    if (home) {
        const catGrid = document.querySelector('.categories-grid');
        if (catGrid && categories.data) {
            const bgColors = ['#eef7eb', '#fff3e0', '#ffebee', '#fdf8e8', '#f0f4ec'];
            catGrid.innerHTML = categories.data.slice(0, 5).map((cat, i) => `
                <a href="${FF.url('shop')}?category_id=${cat.id}" class="cat-card" style="background: ${bgColors[i % bgColors.length]};">
                    <div class="cat-card__img"><img src="${cat.image_path ? cat.image_path : 'https://placehold.co/400x400/eef7eb/2d6a4f?text=' + encodeURIComponent(cat.name)}" alt="${esc(cat.name)}"></div>
                    <div class="cat-card__content">
                        <h3>${esc(cat.name)} <i data-feather="arrow-right"></i></h3>
                        <p>${esc(cat.description || 'Khám phá ngay')}</p>
                    </div>
                </a>
            `).join('');
            if (window.feather) feather.replace();
        }
    }

    if (!home) {
        main.innerHTML = `
            <div class="shop-hero" style="background: url('${FF.asset('images/farmer_banner.jpg')}') center/cover no-repeat; position: relative; color: white;">
                <div style="position: absolute; inset: 0; background: rgba(0, 0, 0, 0.4);"></div>
                <div class="container shop-hero__inner" style="position: relative; z-index: 2; padding: 60px 0;">
                    <nav class="breadcrumbs" style="color: rgba(255,255,255,0.8);">
                        <a href="${url('/')}" style="color: white;">Trang chủ</a>
                        <span>/</span>
                        <span style="color: white;">Sản phẩm</span>
                    </nav>
                    <h1 style="color: white; margin-top: 16px; font-size: 40px; font-weight: 700;">Cửa hàng</h1>
                    <p style="color: rgba(255,255,255,0.9); font-size: 18px;">Nông sản tươi sạch — từ vườn đến bàn ăn</p>
                </div>
            </div>
            
            <div class="container category-chips" style="margin-top: -30px; position: relative; z-index: 10;">
                <a href="javascript:void(0)" class="chip active" style="background: white; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                    <div class="chip__icon" style="background: #eef7eb;"><i data-feather="grid" style="width: 16px; height: 16px; color: var(--green-600);"></i></div>
                    Tất cả
                </a>
                ${
                    (categories.data || []).map(category => `
                        <a href="javascript:void(0)" class="chip" style="background: white; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                            <div class="chip__icon"><img src="${category.image_path ? category.image_path : 'https://placehold.co/100x100/eef7eb/2d6a4f?text=' + encodeURIComponent(category.name.substring(0,2))}" alt="${esc(category.name)}" style="object-fit: cover; width: 100%; height: 100%;"></div>
                            ${esc(category.name)}
                        </a>
                    `).join('')
                }
            </div>
        
            <div class="container section">
                <div class="shop-layout">
                    <aside class="filters">
                        <form id="catalog-filter">
                            <div class="filter-head">
                                <h2>Bộ lọc</h2>
                                <button type="reset" class="link-button">Xóa tất cả</button>
                            </div>

                            <div class="filter-group">
                                <h3>Tìm kiếm</h3>
                                ${input('Tên sản phẩm', 'q', 'search', '', false)}
                            </div>

                            <div class="filter-group filter-group--categories">
                                <h3>Danh mục</h3>
                                <div class="live-stack" style="gap:12px; margin-top:16px">
                                    <label class="check-inline check-inline--custom">
                                        <input type="radio" name="category_id" value="" checked>
                                        <span class="custom-checkbox"><i data-feather="check"></i></span>
                                        <span class="cat-name">Tất cả</span>
                                    </label>
                                    ${
                                        (categories.data || []).map(category => `
                                            <label class="check-inline check-inline--custom">
                                                <input type="radio" name="category_id" value="${esc(category.id)}">
                                                <span class="custom-checkbox"><i data-feather="check"></i></span>
                                                <span class="cat-name">${esc(category.name)}</span>
                                            </label>
                                        `).join('')
                                    }
                                </div>
                            </div>

                            <div class="filter-group">
                                <h3>Khoảng giá</h3>
                                <div class="price-slider-ui">
                                    <input type="range" min="0" max="500000" value="500000" class="range-slider">
                                    <div class="price-range-labels">
                                        <span>0đ</span>
                                        <span>500.000đ</span>
                                    </div>
                                </div>
                            </div>

                            <div class="filter-group filter-group--toggle">
                                <label class="switch-row">
                                    <span>
                                        <strong>Chỉ còn hàng</strong>
                                        <small>Ẩn sản phẩm hết hàng</small>
                                    </span>
                                    <input type="checkbox" name="in_stock" value="1">
                                    <i></i>
                                </label>
                            </div>

                            <button type="submit" class="btn btn--primary btn--block btn--apply-filter">Áp dụng</button>
                        </form>
                    </aside>

                    <div class="catalog">
                        <div class="catalog-toolbar catalog-toolbar--premium">
                            <h2 class="catalog-title">24 sản phẩm</h2>
                            <div class="catalog-actions">
                                <div class="view-toggles">
                                    <button class="view-btn active" data-view="grid" title="Xem dạng lưới" aria-label="Xem dạng lưới"><i data-feather="grid"></i></button>
                                    <button class="view-btn" data-view="list" title="Xem dạng danh sách" aria-label="Xem dạng danh sách"><i data-feather="list"></i></button>
                                </div>
                                <label class="inline-select inline-select--premium">
                                    Sắp xếp
                                    <select form="catalog-filter" name="sort">
                                        <option value="newest">Mới nhất</option>
                                        <option value="price_asc">Giá tăng dần</option>
                                        <option value="price_desc">Giá giảm dần</option>
                                        <option value="name_asc">Tên A–Z</option>
                                    </select>
                                </label>
                            </div>
                        </div>

                        <div id="catalog-results" aria-live="polite">
                            <div class="product-grid product-grid--catalog">
                                ${Array(6).fill(0).map(() => `
                                <div class="skeleton-product">
                                    <div class="skeleton-product__img"></div>
                                    <div class="skeleton-product__body">
                                        <div class="skeleton-line skeleton-line--short"></div>
                                        <div class="skeleton-line"></div>
                                        <div class="skeleton-line skeleton-line--mid"></div>
                                        <div class="skeleton-line skeleton-line--price"></div>
                                    </div>
                                </div>`).join('')}
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        `;
    }

    let currentViewMode = 'grid';

    if (!home) {
        const viewBtns = document.querySelectorAll('.view-btn');
        viewBtns.forEach(btn => {
            btn.onclick = (e) => {
                e.preventDefault();
                const mode = btn.dataset.view;
                if (!mode) return;
                currentViewMode = mode;
                viewBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                const targetGrid = $('#catalog-results .product-grid');
                if (targetGrid) {
                    if (currentViewMode === 'list') {
                        targetGrid.classList.add('product-grid--list');
                    } else {
                        targetGrid.classList.remove('product-grid--list');
                    }
                }
            };
        });
    }

    const form =
        $('#catalog-filter');

    if (!home && form) {
        const chips = document.querySelectorAll('.category-chips .chip');
        const radios = document.querySelectorAll('input[name="category_id"]');
        chips.forEach((chip, index) => {
            chip.onclick = () => {
                chips.forEach(c => c.classList.remove('active'));
                chip.classList.add('active');
                if (radios[index]) {
                    radios[index].checked = true;
                    if (typeof form.requestSubmit === 'function') {
                        form.requestSubmit();
                    } else {
                        form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
                    }
                }
            };
        });
    }

    let sequence = 0;

    async function load(
        page = 1
    ) {
        const seq =
            ++sequence;

        const target = home ? $('#home-catalog-results') : $('#catalog-results');
        
        if (!target) return;

        target.innerHTML =
            message(
                'Đang tải sản phẩm…'
            );

        const params =
            new URLSearchParams();

        if (form) {
            new FormData(form).forEach(
                (value, key) => {

                    if (value !== '') {
                        params.set(
                            key,
                            value
                        );
                    }
                }
            );
        }

        params.set(
            'page',
            page
        );

        params.set(
            'per_page',
            12
        );

        try {

            const response =
                await request(
                    '/products?' +
                    params
                );

            if (seq !== sequence) {
                return;
            }

            const items =
                response.data || [];

            if (!home) {
                const titleEl = document.querySelector('.catalog-title');
                if (titleEl) {
                    titleEl.textContent = `${response.meta?.total ?? items.length} sản phẩm`;
                }
            }

            target.innerHTML =
                items.length
                    ? `
                        <div class="product-grid ${home ? '' : 'product-grid--catalog'} ${!home && currentViewMode === 'list' ? 'product-grid--list' : ''}">
                            ${items
                                .map(card)
                                .join('')}
                        </div>
                    `
                    : message(
                        'Không tìm thấy sản phẩm phù hợp.'
                    );

            if (
                response.meta
                    ?.last_page > 1
            ) {

                target.innerHTML += `
                    <nav
                        class="live-pagination"
                    >

                        <button
                            class="btn btn--outline"
                            data-prev
                            ${
                                page <= 1
                                    ? 'disabled'
                                    : ''
                            }
                        >
                            Trước
                        </button>

                        <span>
                            Trang
                            ${page}
                            /
                            ${esc(
                                response.meta
                                    .last_page
                            )}
                        </span>

                        <button
                            class="btn btn--outline"
                            data-next
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
                    () =>
                        load(
                            page - 1
                        );

                target.querySelector(
                    '[data-next]'
                ).onclick =
                    () =>
                        load(
                            page + 1
                        );
            }

            bindAdd(
                target
            );
            bindWishlist(target);

        } catch (error) {

            if (seq === sequence) {
                showError(
                    error,
                    target
                );
            }
        }
    }

    if (form) {
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.forEach((value, key) => {
            const field = form.querySelector(`[name="${key}"]`);
            if (field) {
                if (field.type === 'checkbox' || field.type === 'radio') {
                    const el = form.querySelector(`[name="${key}"][value="${value}"]`);
                    if (el) el.checked = true;
                } else {
                    field.value = value;
                }
            }
        });
        
        // update chips UI
        const chips = document.querySelectorAll('.category-chips .chip');
        const radios = document.querySelectorAll('input[name="category_id"]');
        radios.forEach((radio, index) => {
            if (radio.checked && chips[index]) {
                chips.forEach(c => c.classList.remove('active'));
                chips[index].classList.add('active');
            }
        });

        form.addEventListener(
            'submit',
            (e) => {
                e.preventDefault();
                load(1);
            }
        );
    } await load();
}


async function product() {
    const response =
        await request(
            '/products/' +
            encodeURIComponent(
                FF.slug
            )
        );

    const productData =
        response.data;

    const main =
        $('#live-main');

    if (!main) {
        return;
    }

    main.innerHTML = `
        <div class="container section">
        <nav class="breadcrumbs">

            <a
                href="${url('shop')}"
            >
                Sản phẩm
            </a>

            <span>/</span>

            ${esc(
                productData.name
            )}

        </nav>

        <section class="live-product">

            <img
                id="main-product-image"
                src="${safeImage(
                    productData
                        .primary_image_url ||
                    productData
                        .images?.[0]?.url
                )}"
                alt="${esc(
                    productData.name
                )}"
            >

            <div>

                <span class="eyebrow">
                    ${esc(
                        productData
                            .category
                            ?.name
                    )}
                </span>

                <h1>
                    ${esc(
                        productData.name
                    )}
                </h1>

                <p class="product-price">

                    <strong>
                        ${cash(
                            productData.price
                        )}
                    </strong>

                    /
                    ${esc(
                        productData.unit
                    )}

                </p>

                <p>
                    ${esc(
                        productData
                            .short_description
                    )}
                </p>

                <p>
                    Nguồn gốc:
                    ${esc(
                        productData.origin ||
                        'Đang cập nhật'
                    )}
                </p>

                <p>
                    Còn
                    ${esc(
                        productData
                            .available_quantity
                    )}
                    ${esc(
                        productData.unit
                    )}
                </p>

                <div
                    class="live-actions"
                >

                    <label>
                        Số lượng

                        <input
                            name="quantity"
                            aria-label="Số lượng"
                            type="number"
                            min="1"
                            max="${esc(
                                Math.floor(Number(productData.available_quantity)) || 1
                            )}"
                            step="1"
                            value="1"
                            onkeydown="if(['.', ',', 'e', 'E', '+', '-'].includes(event.key)) event.preventDefault();"
                            oninput="this.value = this.value.replace(/[^0-9]/g, ''); if(parseInt(this.value, 10) < 1) this.value = '1';"
                        >
                    </label>

                    <button
                        class="btn btn--primary"
                        data-add="${esc(
                            productData.id
                        )}"
                        ${
                            Number(
                                productData
                                    .available_quantity
                            ) < 1
                                ? 'disabled'
                                : ''
                        }
                    >
                        ${icon(
                            'shopping-cart'
                        )}

                        Thêm vào giỏ
                    </button>

                    <button class="btn btn--outline wishlist-btn--lg" type="button" data-wishlist="${esc(productData.id)}" style="display:flex;align-items:center;gap:8px;">
                        ${icon('heart')} Yêu thích
                    </button>

                </div>

                <div data-feedback></div>

            </div>

        </section>

        <section class="section">

            <h2>
                Thông tin sản phẩm
            </h2>

            <div
                class="live-description"
                id="description"
            ></div>

        </section>

        <section>

            <h2>
                Đánh giá sản phẩm
            </h2>

            <div
                id="product-reviews"
            >
                Đang tải…
            </div>

        </section>
        </div>
    `;



    const doc =
        new DOMParser()
            .parseFromString(
                productData
                    .description_html ||
                productData.description ||
                '',
                'text/html'
            );

    doc
        .querySelectorAll(
            'script,style'
        )
        .forEach(
            (node) => {
                node.remove();
            }
        );

    $('#description')
        .textContent =
        doc.body.textContent;

    bindAdd(main);
    bindWishlist(main);



    if (
        productData.images?.length
    ) {

        const gallery =
            document.createElement(
                'div'
            );

        gallery.className =
            'gallery__thumbs';

        gallery.style.cssText =
            'flex-direction:row;' +
            'flex-wrap:wrap;' +
            'margin-top:16px';

        gallery.innerHTML =
            productData.images
                .map(
                    (image) => `
                        <button
                            type="button"
                            style="width:64px"
                            aria-label="Xem ảnh sản phẩm"
                        >

                            <img
                                src="${safeImage(
                                    image.url
                                )}"
                                alt="${esc(
                                    productData.name
                                )}"
                            >

                        </button>
                    `
                )
                .join('');

        main
            .querySelector(
                '.live-product>div'
            )
            .append(
                gallery
            );

        gallery
            .querySelectorAll(
                'button'
            )
            .forEach(
                (button) => {

                    button.onclick =
                        () => {

                            $(
                                '#main-product-image'
                            ).src =
                                button
                                    .querySelector(
                                        'img'
                                    )
                                    .src;
                        };
                }
            );
    }
    try {
        const reviews =
            await request(
                '/products/' +
                encodeURIComponent(
                    productData.id
                ) +
                '/reviews'
            );

        const reviewList = reviews.data || [];
        const avgRating = reviewList.length
            ? (reviewList.reduce((s, r) => s + r.rating, 0) / reviewList.length).toFixed(1)
            : null;

        const stars = (rating) => {
            let s = '';
            for (let i = 1; i <= 5; i++) {
                s += `<svg width="16" height="16" viewBox="0 0 24 24" fill="${i <= Math.round(rating) ? '#f59e0b' : 'none'}" stroke="#f59e0b" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>`;
            }
            return s;
        };

        $('#product-reviews').innerHTML = `
            ${avgRating ? `
            <div style="display:flex;align-items:center;gap:16px;background:#f9f9f9;border-radius:12px;padding:20px;margin-bottom:24px;">
                <div style="text-align:center;">
                    <div style="font-size:48px;font-weight:700;color:#1a3b2e;line-height:1;">${avgRating}</div>
                    <div style="margin:6px 0;">${stars(avgRating)}</div>
                    <div style="color:#666;font-size:14px;">${reviewList.length} đánh giá</div>
                </div>
            </div>` : ''}

            ${reviewList.map(r => `
                <article style="border-bottom:1px solid #eee;padding:20px 0;">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:8px;">
                        <div style="width:40px;height:40px;border-radius:50%;background:var(--green-100);display:flex;align-items:center;justify-content:center;font-weight:700;color:var(--green-700);font-size:16px;">
                            ${esc((r.user?.full_name || r.user?.name || 'K')[0])}
                        </div>
                        <div>
                            <div style="font-weight:600;">${esc(r.user?.full_name || r.user?.name || 'Khách hàng')}</div>
                            <div style="display:flex;gap:2px;margin-top:2px;">${stars(r.rating)}</div>
                        </div>
                        <span style="margin-left:auto;color:#999;font-size:13px;">${esc(r.created_at?.substring(0,10) || '')}</span>
                    </div>
                    <p style="margin:0;color:#444;line-height:1.6;">${esc(r.comment)}</p>
                </article>
            `).join('')}

            ${reviewList.length === 0 ? `<div style="text-align:center;padding:40px;color:#999;">
                <p style="font-size:16px;">Chưa có đánh giá nào. Hãy là người đầu tiên!</p>
            </div>` : ''}
        `;

    } catch (error) {

        $('#product-reviews')
            .innerHTML =
            errorBox(error);
    }
}

export {
    card,
    bindAdd,
    catalog,
    product
};
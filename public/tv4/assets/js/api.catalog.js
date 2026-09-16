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
    const quantity = Number(product.available_quantity);
    const hasDiscount = product.compare_at_price > product.price;

    return `
        <article class="product-card product-card--premium">
            <div class="product-card__top">
                <div class="product-card__badges">
                    ${product.featured ? '<span class="badge badge--featured">Bán chạy</span>' : (hasDiscount ? '<span class="badge badge--sale">Giảm giá</span>' : '<span class="badge badge--fresh">Tươi mới</span>')}
                </div>
                <button class="wishlist-btn" type="button" aria-label="Thêm vào yêu thích">
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
                        <i data-feather="map-pin"></i> ${esc(product.origin || 'Nông trại sạch')}
                    </span>
                    <span class="rating">
                        <i data-feather="star" class="star-icon"></i> 4.9 (88)
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
                            Number(
                                root.querySelector(
                                    '[name=quantity]'
                                )?.value || 1
                            );

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

    if (!home) {
        main.innerHTML = `
            <div class="shop-hero">
                <div class="container shop-hero__inner">
                    <nav class="breadcrumbs">
                        <a href="${url('/')}">Trang chủ</a>
                        <span>/</span>
                        <span>Sản phẩm</span>
                    </nav>
                    <h1>Cửa hàng</h1>
                    <p>Nông sản tươi sạch — từ vườn đến bàn ăn</p>
                </div>
            </div>
            
            <div class="container category-chips">
                <a href="javascript:void(0)" class="chip active">
                    <div class="chip__icon"><img src="${FF.asset('images/placeholder.svg')}" alt="Tất cả"></div>
                    Tất cả
                </a>
                ${
                    (categories.data || []).map(category => `
                        <a href="javascript:void(0)" class="chip">
                            <div class="chip__icon"><img src="${FF.asset('images/placeholder.svg')}" alt="${esc(category.name)}"></div>
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
                                        <span class="cat-count">(10)</span>
                                    </label>
                                    ${
                                        (categories.data || []).map(category => `
                                            <label class="check-inline check-inline--custom">
                                                <input type="radio" name="category_id" value="${esc(category.id)}">
                                                <span class="custom-checkbox"><i data-feather="check"></i></span>
                                                <span class="cat-name">${esc(category.name)}</span>
                                                <span class="cat-count">(${Math.floor(Math.random()*15 + 2)})</span>
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
                                    <button class="view-btn active"><i data-feather="grid"></i></button>
                                    <button class="view-btn"><i data-feather="list"></i></button>
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
                    form.dispatchEvent(new Event('submit'));
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
                        <div class="product-grid ${home ? '' : 'product-grid--catalog'}">
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
                                productData
                                    .available_quantity
                            )}"
                            step="0.001"
                            value="1"
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

        $('#product-reviews')
            .innerHTML =
            (reviews.data || [])
                .map(
                    (review) => `
                        <article class="panel">

                            <strong>
                                ${esc(
                                    review.user
                                        ?.full_name ||
                                    review.user
                                        ?.name ||
                                    'Khách hàng'
                                )}
                            </strong>

                            <p>
                                ${esc(
                                    review.rating
                                )}
                                / 5 sao
                            </p>

                            <p>
                                ${esc(
                                    review.comment
                                )}
                            </p>

                        </article>
                    `
                )
                .join('')

            ||

            message(
                'Chưa có đánh giá.'
            );

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
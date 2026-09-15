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
    const quantity =
        Number(
            product.available_quantity
        );

    return `
        <article class="product-card">

            <a
                class="product-card__media"
                href="${url(
                    'product',
                    product.slug
                )}"
            >
                <img
                    src="${safeImage(
                        product.primary_image_url
                    )}"
                    alt="${esc(
                        product.name
                    )}"
                    loading="lazy"
                >
            </a>

            <div class="product-card__body">

                <span class="product-card__cat">
                    ${esc(
                        product.category?.name
                    )}
                </span>

                <h3>
                    <a
                        href="${url(
                            'product',
                            product.slug
                        )}"
                    >
                        ${esc(
                            product.name
                        )}
                    </a>
                </h3>

                <div class="product-card__row">

                    <span class="price">
                        ${cash(
                            product.price
                        )}

                        <small>
                            /${esc(
                                product.unit
                            )}
                        </small>
                    </span>

                    <button
                        class="add-cart"
                        type="button"
                        data-add="${esc(
                            product.id
                        )}"
                        ${
                            quantity <= 0
                                ? 'disabled'
                                : ''
                        }
                        aria-label="Thêm ${esc(
                            product.name
                        )} vào giỏ"
                    >
                        ${icon('plus')}
                    </button>

                </div>

                <small>
                    ${
                        quantity > 0
                            ? `Còn ${esc(
                                quantity
                            )} ${esc(
                                product.unit
                            )}`
                            : 'Hết hàng'
                    }
                </small>

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

    main.innerHTML =
        (
            home
                ? `
                    <section class="hero">

                        <div class="hero__content">

                            <span class="eyebrow">
                                FreshFarm · Nông Sản Xanh
                            </span>

                            <h1>
                                Tươi từ vườn.
                                <br>

                                <span>
                                    Lành mỗi ngày.
                                </span>
                            </h1>

                            <p>
                                Nông sản tươi sạch
                                cho bữa ăn gia đình.
                            </p>

                            <a
                                class="btn btn--primary"
                                href="${url('shop')}"
                            >
                                Khám phá sản phẩm
                            </a>

                        </div>

                        <div class="hero__visual">

                            <img
                                src="${FF.asset(
                                    'images/hero.png'
                                )}"
                                alt="Nông sản tươi"
                            >

                        </div>

                    </section>

                    <div style="height:32px"></div>
                `
                : ''
        )

        +

        heading(
            home
                ? 'Nông sản hôm nay'
                : 'Sản phẩm'
        )

        +

        `
            <form
                class="panel live-filter"
                id="catalog-filter"
            >

                ${input(
                    'Tìm sản phẩm',
                    'q',
                    'search',
                    '',
                    false
                )}

                <label>
                    Danh mục

                    <select
                        name="category_id"
                    >

                        <option value="">
                            Tất cả
                        </option>

                        ${
                            (
                                categories.data ||
                                []
                            )
                                .map(
                                    (category) => `
                                        <option
                                            value="${esc(
                                                category.id
                                            )}"
                                        >
                                            ${esc(
                                                category.name
                                            )}
                                        </option>
                                    `
                                )
                                .join('')
                        }

                    </select>
                </label>

                <label>
                    Sắp xếp

                    <select
                        name="sort"
                    >

                        <option value="newest">
                            Mới nhất
                        </option>

                        <option value="price_asc">
                            Giá tăng dần
                        </option>

                        <option value="price_desc">
                            Giá giảm dần
                        </option>

                        <option value="name_asc">
                            Tên A–Z
                        </option>

                    </select>
                </label>

                ${input(
                    'Giá từ',
                    'min_price',
                    'number',
                    '',
                    false
                )}

                ${input(
                    'Giá đến',
                    'max_price',
                    'number',
                    '',
                    false
                )}

                <label>
                    Kho hàng

                    <select
                        name="in_stock"
                    >

                        <option value="">
                            Tất cả
                        </option>

                        <option value="1">
                            Còn hàng
                        </option>

                    </select>
                </label>

                <button
                    type="submit"
                    class="btn btn--primary"
                >
                    Áp dụng
                </button>

            </form>

            <div
                id="catalog-results"
                aria-live="polite"
            ></div>
        `;

    const form =
        $('#catalog-filter');

    let sequence = 0;

    async function load(
        page = 1
    ) {
        const seq =
            ++sequence;

        const target =
            $('#catalog-results');

        target.innerHTML =
            message(
                'Đang tải sản phẩm…'
            );

        const params =
            new URLSearchParams();

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

            target.innerHTML =
                items.length
                    ? `
                        <p>
                            ${esc(
                                response.meta
                                    ?.total ??
                                items.length
                            )}
                            sản phẩm
                        </p>

                        <div class="product-grid">
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

    form.onsubmit =
        (event) => {

            event.preventDefault();

            load();
        };

    await load();
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
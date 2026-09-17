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
    statusLabel,
    token
} from './api.core.js';

import {
    totals
} from './api.cart.js';


function renderOrderCard(order) {
    const items = order.items || [];
    const imagesHtml = items.slice(0, 3).map(item => `
        <img src="${safeImage(item.product?.primary_image_url)}" alt="${esc(item.product?.name || item.product_name || '')}" title="${esc(item.product?.name || item.product_name || '')}">
    `).join('');
    const moreCount = items.length > 3
        ? `<span>+${items.length - 3} sản phẩm khác</span>`
        : (items.length > 0 ? `<span>${items.length} sản phẩm</span>` : '');

    return `
        <article class="order-card">
            <div class="order-card__head">
                <div>
                    <span>Mã đơn hàng</span>
                    <a href="${url('order-detail', order.order_code)}">#${esc(order.order_code)}</a>
                </div>
                <div>
                    <span>Ngày đặt</span>
                    <strong>${esc(order.created_at)}</strong>
                </div>
                <span class="status-badge status-badge--${esc(order.status)}">
                    ${esc(statusLabel(order.status))}
                </span>
            </div>
            <div class="order-card__body">
                <div class="order-products">
                    ${imagesHtml}
                    ${moreCount}
                </div>
                <div>
                    <span>Tổng tiền</span>
                    <strong style="color: var(--green-700); font-size: 1.1rem;">${cash(order.grand_total)}</strong>
                </div>
                <a class="btn btn--outline" href="${url('order-detail', order.order_code)}">
                    Xem chi tiết
                </a>
            </div>
        </article>
    `;
}


async function orders() {
    const main = $('#live-main');
    if (!main) {
        return;
    }

    if (!token()) {
        location.href = url('login');
        return;
    }

    const orderListEl = document.getElementById('order-list');
    const filterSelect = document.getElementById('order-filter');
    const emptyEl = document.getElementById('orders-empty');

    let sequence = 0;

    async function load(page = 1) {
        const requestId = ++sequence;

        if (orderListEl) {
            orderListEl.innerHTML = `
                <div class="empty-state">
                    <p>Đang tải đơn hàng…</p>
                </div>
            `;
        }
        if (emptyEl) {
            emptyEl.hidden = true;
            emptyEl.setAttribute('hidden', '');
        }

        const status = filterSelect ? filterSelect.value : '';
        const params = new URLSearchParams();

        if (status && status !== 'all') {
            params.set('status', status);
        }
        params.set('page', page);

        try {
            const response = await request('/orders?' + params.toString());

            if (requestId !== sequence) {
                return;
            }

            const orderList = response.data || [];

            if (orderList.length === 0) {
                if (orderListEl) {
                    orderListEl.innerHTML = '';
                }
                if (emptyEl) {
                    emptyEl.removeAttribute('hidden');
                    emptyEl.hidden = false;
                }
                const oldPagination = document.querySelector('.live-pagination');
                if (oldPagination) {
                    oldPagination.remove();
                }
                return;
            }

            if (emptyEl) {
                emptyEl.setAttribute('hidden', '');
                emptyEl.hidden = true;
            }

            if (orderListEl) {
                orderListEl.innerHTML = orderList.map(renderOrderCard).join('');
            }

            // Pagination
            let paginationEl = document.querySelector('.live-pagination');
            if (paginationEl) {
                paginationEl.remove();
            }

            if (response.meta?.last_page > 1) {
                const nav = document.createElement('nav');
                nav.className = 'live-pagination';
                nav.innerHTML = `
                    <button data-prev class="btn btn--outline" ${page <= 1 ? 'disabled' : ''}>Trước</button>
                    <span>Trang ${page} / ${esc(response.meta.last_page)}</span>
                    <button data-next class="btn btn--outline" ${page >= response.meta.last_page ? 'disabled' : ''}>Sau</button>
                `;
                orderListEl.after(nav);

                nav.querySelector('[data-prev]')?.addEventListener('click', () => load(page - 1));
                nav.querySelector('[data-next]')?.addEventListener('click', () => load(page + 1));
            }

            if (window.feather) {
                feather.replace();
            }

        } catch (error) {
            if (requestId === sequence) {
                if (orderListEl) {
                    orderListEl.innerHTML = errorBox(error);
                }
            }
        }
    }

    if (filterSelect) {
        filterSelect.addEventListener('change', () => load(1));
    }

    await load();
}


async function order() {
    if (!token()) {
        location.href = url('login');
        return;
    }

    const orderCode = FF.orderCode || new URLSearchParams(window.location.search).get('order_code');
    if (!orderCode) {
        location.href = url('orders');
        return;
    }

    try {
        const response = await request('/orders/' + encodeURIComponent(orderCode));
        const orderData = response.data?.order || response.data;

        if (!orderData) {
            throw new Error('Không tìm thấy thông tin đơn hàng.');
        }

        // Show toast if redirected from payment gateway
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('payment') === 'success') {
            if (typeof toast === 'function') {
                toast('Thanh toán VNPay thành công! Đơn hàng đã được xác nhận.');
            }
        } else if (urlParams.get('payment') === 'failed') {
            if (typeof toast === 'function') {
                toast('Thanh toán chưa hoàn tất hoặc bị hủy.', 'error');
            }
        }

        // 1. Update Hero info
        const heroTitle = document.querySelector('.page-hero h1');
        if (heroTitle) {
            heroTitle.textContent = 'Đơn hàng #' + orderData.order_code;
        }

        const heroSub = document.querySelector('.page-hero p');
        if (heroSub) {
            heroSub.textContent = `Đặt ngày ${orderData.created_at || '—'}`;
        }

        const statusBadge = document.querySelector('.page-hero .status-badge');
        if (statusBadge) {
            statusBadge.className = `status-badge status-badge--${esc(orderData.status)}`;
            statusBadge.textContent = statusLabel(orderData.status);
        }

        // 2. Tracking progress
        const statusOrder = ['pending', 'confirmed', 'shipping', 'delivered'];
        const currentIdx = statusOrder.indexOf(orderData.status);

        const trackingItems = document.querySelectorAll('.tracking ol li');
        trackingItems.forEach((li, idx) => {
            li.classList.remove('is-done', 'is-current');
            if (orderData.status === 'cancelled') {
                // Cancelled status
                if (idx === 0) li.classList.add('is-done');
            } else if (currentIdx >= 0) {
                if (idx < currentIdx) {
                    li.classList.add('is-done');
                } else if (idx === currentIdx) {
                    li.classList.add('is-done', 'is-current');
                }
            }
        });

        // 3. Products list
        const itemsContainer = document.getElementById('order-items');
        if (itemsContainer) {
            itemsContainer.innerHTML = (orderData.items || []).map(item => `
                <article class="order-line">
                    <img src="${safeImage(item.product?.primary_image_url)}" alt="${esc(item.product?.name || item.product_name)}">
                    <div>
                        <strong>${esc(item.product?.name || item.product_name)}</strong>
                        <small>${esc(item.quantity)} × ${cash(item.unit_price)}</small>
                    </div>
                    <strong>${cash(item.line_total)}</strong>
                    ${['delivered', 'completed'].includes(orderData.status) ? `
                        <a class="text-link" href="${url('review')}?order_item_id=${encodeURIComponent(item.id)}">
                            Đánh giá
                        </a>
                    ` : ''}
                </article>
            `).join('');
        }

        // 4. Shipping info
        const shippingEl = document.getElementById('shipping-info');
        if (shippingEl) {
            const addr = orderData.address || orderData.shipping_address;
            const fullAddress = typeof addr === 'object'
                ? (addr?.full_address || [addr?.address, addr?.ward, addr?.district, addr?.province].filter(Boolean).join(', '))
                : (addr || 'Đang cập nhật');

            const receiverName = addr?.receiver_name || addr?.name || orderData.receiver_name || '';
            const receiverPhone = addr?.receiver_phone || addr?.phone || orderData.receiver_phone || '';

            shippingEl.innerHTML = `
                ${receiverName ? `<p><strong>${esc(receiverName)}</strong></p>` : ''}
                ${receiverPhone ? `<p>Điện thoại: ${esc(receiverPhone)}</p>` : ''}
                <p>Địa chỉ: ${esc(fullAddress)}</p>
                ${orderData.note ? `<p style="margin-top: 8px; color: var(--muted);"><em>Ghi chú: ${esc(orderData.note)}</em></p>` : ''}
            `;
        }

        // 5. Payment info
        const paymentMethodEl = document.getElementById('payment-method');
        if (paymentMethodEl) {
            const methodLabels = {
                cod: 'Thanh toán khi nhận hàng (COD)',
                vnpay: 'Thanh toán trực tuyến qua VNPay',
                bank_transfer: 'Chuyển khoản ngân hàng'
            };
            paymentMethodEl.textContent = methodLabels[orderData.payment_method] || orderData.payment_method || 'COD';
        }

        const paymentStatusBadge = document.querySelector('.info-panel .status-badge');
        if (paymentStatusBadge) {
            const isPaid = orderData.payment_status === 'paid';
            paymentStatusBadge.className = `status-badge ${isPaid ? 'status-badge--success' : 'status-badge--warning'}`;
            paymentStatusBadge.textContent = isPaid ? 'Đã thanh toán' : 'Chưa thanh toán';
        }

        // 6. Summary totals
        const subtotalEl = document.querySelector('[data-subtotal]');
        if (subtotalEl) subtotalEl.textContent = cash(orderData.subtotal);

        const discountEl = document.querySelector('[data-discount]');
        if (discountEl) discountEl.textContent = Number(orderData.discount_amount) > 0 ? '-' + cash(orderData.discount_amount) : '0đ';

        const shippingFeeEl = document.querySelector('[data-shipping-fee]');
        if (shippingFeeEl) shippingFeeEl.textContent = cash(orderData.shipping_fee || 0);

        const grandTotalEl = document.querySelector('[data-grand-total]');
        if (grandTotalEl) grandTotalEl.textContent = cash(orderData.grand_total);

        // 7. Cancel order button & modal
        const cancelBtn = document.querySelector('[data-cancel-order]');
        const cancelModal = document.getElementById('cancel-modal');
        const confirmCancelBtn = document.querySelector('[data-confirm-cancel]');

        if (cancelBtn) {
            if (['pending', 'confirmed'].includes(orderData.status)) {
                cancelBtn.removeAttribute('hidden');
                cancelBtn.style.display = '';

                cancelBtn.onclick = () => {
                    if (cancelModal && typeof cancelModal.showModal === 'function') {
                        cancelModal.showModal();
                    } else if (confirm('Bạn có chắc chắn muốn hủy đơn hàng này không?')) {
                        handleCancel('Khách hàng yêu cầu hủy');
                    }
                };
            } else {
                cancelBtn.style.display = 'none';
            }
        }

        async function handleCancel(reason) {
            try {
                if (cancelBtn) cancelBtn.disabled = true;
                if (confirmCancelBtn) confirmCancelBtn.disabled = true;

                await request('/orders/' + encodeURIComponent(orderCode) + '/cancel', {
                    method: 'POST',
                    body: { reason: reason || 'Khách hàng yêu cầu hủy' }
                });

                if (cancelModal && typeof cancelModal.close === 'function') {
                    cancelModal.close();
                }

                if (typeof toast === 'function') {
                    toast('Đã gửi yêu cầu hủy đơn');
                }

                await order();

            } catch (err) {
                alert(err.message || 'Không thể hủy đơn hàng');
            } finally {
                if (cancelBtn) cancelBtn.disabled = false;
                if (confirmCancelBtn) confirmCancelBtn.disabled = false;
            }
        }

        if (confirmCancelBtn) {
            confirmCancelBtn.onclick = (e) => {
                e.preventDefault();
                const reasonSelect = cancelModal?.querySelector('select');
                const noteInput = cancelModal?.querySelector('textarea');
                const reason = [reasonSelect?.value, noteInput?.value].filter(Boolean).join(' - ');
                handleCancel(reason);
            };
        }

        if (window.feather) {
            feather.replace();
        }

    } catch (error) {
        showError(error);
    }
}

export {
    orders,
    order
};
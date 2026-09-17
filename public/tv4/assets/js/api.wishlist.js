import { $, esc, cash, url, request, message, safeImage, token } from './api.core.js';
import { card, bindAdd } from './api.catalog.js';

export async function loadWishlist() {
    const target = $('#wishlist-results');
    if (!target) return;

    if (!token()) {
        location.href = url('login');
        return;
    }

    try {
        const response = await request('/wishlist');
        const rawItems = response.data?.data || response.data || [];
        const items = Array.isArray(rawItems) ? rawItems.filter(item => item && item.product) : [];

        target.innerHTML = items.length
            ? `<div class="product-grid">${items.map(item => card(item.product)).join('')}</div>`
            : message('Bạn chưa có sản phẩm yêu thích nào.');

        if (window.feather) {
            feather.replace();
        }

        // Rebind add to cart
        bindAdd(target);

        // Bind remove from wishlist logic
        target.querySelectorAll('[data-wishlist]').forEach((button) => {
            const svg = button.querySelector('svg');
            if (svg) {
                svg.setAttribute('fill', 'var(--primary)');
                svg.setAttribute('color', 'var(--primary)');
            }
            button.classList.add('is-active');

            button.onclick = async (e) => {
                e.preventDefault();
                e.stopPropagation();

                const productId = button.dataset.wishlist;
                try {
                    await request('/wishlist/' + encodeURIComponent(productId), { method: 'DELETE' });
                    // Remove card from UI
                    const article = button.closest('article');
                    if (article) article.remove();

                    // Show empty message if no more items
                    if (!target.querySelector('article')) {
                        target.innerHTML = message('Bạn chưa có sản phẩm yêu thích nào.');
                    }

                    if (typeof toast === 'function') {
                        toast('Đã xóa khỏi danh sách yêu thích');
                    }
                } catch (err) {
                    console.error(err);
                }
            };
        });

    } catch (error) {
        if (error.status === 401) {
            location.href = url('login');
            return;
        }
        console.error('Wishlist error:', error);
        target.innerHTML = message('Không thể tải danh sách yêu thích. Vui lòng thử lại sau.');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    if (document.body.dataset.page === 'wishlist') {
        loadWishlist();
    }
});

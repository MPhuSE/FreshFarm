import { $, esc, cash, url, request, message, safeImage } from './api.core.js';
import { card, bindAdd } from './api.catalog.js';

export async function loadWishlist() {
    const target = $('#wishlist-results');
    if (!target) return;

    try {
        const response = await request('/wishlist');
        const items = response.data?.data || response.data || [];

        target.innerHTML = items.length
            ? `<div class="product-grid">${items.map(item => card(item.product)).join('')}</div>`
            : message('Bạn chưa có sản phẩm yêu thích nào.');

        // Rebind add to cart & remove wishlist logic
        bindAdd(target);

        // Bind remove from wishlist logic
        target.querySelectorAll('[data-wishlist]').forEach((button) => {
            button.querySelector('svg').setAttribute('fill', 'var(--primary)');
            button.querySelector('svg').setAttribute('color', 'var(--primary)');

            button.onclick = async (e) => {
                e.preventDefault();
                e.stopPropagation();
                
                const productId = button.dataset.wishlist;
                try {
                    await request('/wishlist/' + productId, { method: 'DELETE' });
                    // Remove card from UI
                    const article = button.closest('article');
                    if (article) article.remove();
                    
                    // Show empty message if no more items
                    if (!target.querySelector('article')) {
                        target.innerHTML = message('Bạn chưa có sản phẩm yêu thích nào.');
                    }
                } catch (err) {
                    console.error(err);
                }
            };
        });

    } catch (error) {
        target.innerHTML = message('Lỗi khi tải danh sách yêu thích.');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    if (document.body.dataset.page === 'wishlist') {
        loadWishlist();
    }
});

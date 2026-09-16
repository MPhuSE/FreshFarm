import {
    $,
    esc,
    url,
    request,
    heading,
    message,
    input,
    onForm
} from './api.core.js';

export async function review() {
    const id =
        new URLSearchParams(
            location.search
        ).get(
            'order_item_id'
        ) || '';

    $('#live-main').innerHTML =
        heading(
            'Đánh giá sản phẩm'
        )

        +

        `
            <form
                class="panel live-form"
                id="review-live"
            >

                ${input(
                    'Mã dòng sản phẩm trong đơn hàng',
                    'order_item_id',
                    'number',
                    id
                )}

                <label>
                    Số sao

                    <select name="rating">

                        ${
                            [5, 4, 3, 2, 1]
                                .map(
                                    (number) => `
                                        <option
                                            value="${number}"
                                        >
                                            ${number} sao
                                        </option>
                                    `
                                )
                                .join('')
                        }

                    </select>
                </label>

                <label>
                    Nội dung

                    <textarea
                        name="comment"
                        required
                        maxlength="2000"
                        rows="5"
                    ></textarea>
                </label>

                <button
                    type="submit"
                    class="btn btn--primary"
                >
                    Gửi đánh giá
                </button>

            </form>
        `;

    $('#review-live')
        .elements
        .order_item_id
        .min = 1;

    onForm(
        $('#review-live'),

        async (data) => {
            await request(
                '/reviews',
                {
                    method: 'POST',

                    body: {
                        order_item_id:
                            Number(
                                data.get(
                                    'order_item_id'
                                )
                            ),

                        rating:
                            Number(
                                data.get(
                                    'rating'
                                )
                            ),

                        comment:
                            data.get(
                                'comment'
                            )
                    }
                }
            );

            $('#live-main')
                .innerHTML =
                heading(
                    'Đã gửi đánh giá'
                )

                +

                message(
                    'Hệ thống đã tiếp nhận ' +
                    'đánh giá của bạn.'
                );
        }
    );
}
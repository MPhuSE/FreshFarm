import {
    request,
    errorBox,
    url
} from './api.core.js';

async function auth(register = false) {
    // Redirect if already logged in
    if (localStorage.getItem('access_token') || sessionStorage.getItem('access_token')) {
        location.href = url('shop');
        return;
    }
    // Password toggle logic
    document.querySelectorAll('[data-toggle-password]').forEach(button => {
        button.addEventListener('click', () => {
            const container = button.closest('.password-field');
            const input = container ? container.querySelector('input') : null;
            if (input) {
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);
                
                // Update icon if feather is used
                const icon = button.querySelector('i');
                if (icon) {
                    icon.setAttribute('data-feather', type === 'password' ? 'eye' : 'eye-off');
                    if (window.feather) window.feather.replace();
                }
            }
        });
    });

    if (register) {

        const form =
            document.querySelector(
                '[data-register-form]'
            );

        if (!form) {
            throw new Error(
                'Không tìm thấy form đăng ký.'
            );
        }

        const errorBoxElement =
            form.querySelector(
                '[data-form-error]'
            );

        form.addEventListener(
            'submit',
            async (event) => {

                event.preventDefault();

                if (form.dataset.busy) {
                    return;
                }

                form.dataset.busy = '1';

                const button =
                    form.querySelector(
                        'button[type="submit"]'
                    );

                if (button) {
                    button.disabled = true;
                }

                if (errorBoxElement) {
                    errorBoxElement.hidden =
                        true;

                    errorBoxElement.innerHTML =
                        '';
                }

                const data =
                    new FormData(form);

                const body = {
                    name:
                        data.get('full_name'),

                    email:
                        data.get('email'),

                    phone:
                        data.get('phone'),

                    password:
                        data.get('password'),

                    password_confirmation:
                        data.get(
                            'password_confirmation'
                        )
                };

                try {

                    if (
                        body.password !==
                        body.password_confirmation
                    ) {
                        throw new Error(
                            'Hai mật khẩu chưa khớp.'
                        );
                    }

                    const response =
                        await request(
                            '/auth/register',
                            {
                                method: 'POST',
                                body
                            }
                        );

                    if (
                        response.data?.token
                    ) {

                        sessionStorage.setItem(
                            'access_token',
                            response.data.token
                        );

                        localStorage.removeItem(
                            'access_token'
                        );

                        if (response.data?.user) {
                            localStorage.setItem(
                                'current_user',
                                JSON.stringify(response.data.user)
                            );
                        }

                        location.href =
                            url('shop');

                        return;
                    }

                    location.href =
                        url('login');

                } catch (error) {

                    if (errorBoxElement) {

                        errorBoxElement.innerHTML =
                            errorBox(error);

                        errorBoxElement.hidden =
                            false;

                    } else {

                        console.error(
                            error
                        );
                    }

                } finally {

                    delete form.dataset.busy;

                    if (button) {
                        button.disabled =
                            false;
                    }
                }
            }
        );

        return;
    }

    const form =
        document.querySelector(
            '[data-login-form]'
        );

    if (!form) {
        throw new Error(
            'Không tìm thấy form đăng nhập.'
        );
    }

    const errorBoxElement =
        form.querySelector(
            '[data-form-error]'
        );

    form.addEventListener(
        'submit',
        async (event) => {

            event.preventDefault();

            if (form.dataset.busy) {
                return;
            }

            form.dataset.busy =
                '1';

            const button =
                form.querySelector(
                    'button[type="submit"]'
                );

            if (button) {
                button.disabled =
                    true;
            }

            if (errorBoxElement) {

                errorBoxElement.hidden =
                    true;

                errorBoxElement.innerHTML =
                    '';
            }

            const data =
                new FormData(form);

            const body = {
                email:
                    data.get('email'),

                password:
                    data.get('password')
            };

            try {

                const response =
                    await request(
                        '/auth/login',
                        {
                            method: 'POST',
                            body
                        }
                    );

                if (
                    response.data?.token
                ) {

                    if (data.get('remember')) {
                        localStorage.setItem(
                            'access_token',
                            response.data.token
                        );
                        sessionStorage.removeItem(
                            'access_token'
                        );
                    } else {
                        sessionStorage.setItem(
                            'access_token',
                            response.data.token
                        );
                        localStorage.removeItem(
                            'access_token'
                        );
                    }

                    // Có thể lưu user nếu backend trả về
                    if (
                        response.data?.user
                    ) {

                        localStorage.setItem(
                            'current_user',
                            JSON.stringify(
                                response.data.user
                            )
                        );
                    }

                    location.href =
                        url('shop');

                    return;
                }

                throw new Error(
                    'API chưa trả token đăng nhập.'
                );

            } catch (error) {

                if (errorBoxElement) {

                    errorBoxElement.innerHTML =
                        errorBox(error);

                    errorBoxElement.hidden =
                        false;

                } else {

                    console.error(
                        error
                    );
                }

            } finally {

                delete form.dataset.busy;

                if (button) {
                    button.disabled =
                        false;
                }
            }
        }
    );
}
export {
    auth
};
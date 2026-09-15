import {
    request,
    errorBox,
    url
} from './api.core.js';

function initPasswordControls(form) {
    form
        .querySelectorAll('[data-toggle-password]')
        .forEach((button) => {
            const field =
                button.closest('.password-field');

            const input =
                field?.querySelector('input');

            if (!input) {
                return;
            }

            button.addEventListener(
                'click',
                () => {
                    const showing =
                        input.type === 'text';

                    input.type =
                        showing
                            ? 'password'
                            : 'text';

                    button.setAttribute(
                        'aria-label',
                        showing
                            ? 'Hiện mật khẩu'
                            : 'Ẩn mật khẩu'
                    );

                    const icon =
                        button.querySelector('i');

                    if (icon) {
                        icon.dataset.feather =
                            showing
                                ? 'eye'
                                : 'eye-off';

                        if (
                            typeof feather !==
                            'undefined'
                        ) {
                            feather.replace({
                                class: 'ui-icon',
                                'aria-hidden': 'true',
                                focusable: 'false'
                            });
                        }
                    }
                }
            );
        });
}

function initPasswordConfirmation(form) {
    const password =
        form.querySelector('[name="password"]');

    const confirmation =
        form.querySelector(
            '[name="password_confirmation"]'
        );

    if (!password || !confirmation) {
        return;
    }

    const validate = () => {
        confirmation.setCustomValidity(
            password.value &&
            confirmation.value &&
            password.value !== confirmation.value
                ? 'Hai mật khẩu chưa khớp.'
                : ''
        );
    };

    password.addEventListener('input', validate);
    confirmation.addEventListener('input', validate);
}

function setBusy(form, busy) {
    const button =
        form.querySelector(
            'button[type="submit"]'
        );

    if (busy) {
        form.dataset.busy = '1';
    } else {
        delete form.dataset.busy;
    }

    if (button) {
        button.disabled = busy;
    }
}

function resetError(errorBoxElement) {
    if (!errorBoxElement) {
        return;
    }

    errorBoxElement.hidden = true;
    errorBoxElement.innerHTML = '';
}

function showFormError(errorBoxElement, error) {
    if (errorBoxElement) {
        errorBoxElement.innerHTML =
            errorBox(error);

        errorBoxElement.hidden = false;

        return;
    }

    console.error(error);
}

async function auth(register = false) {
    const form =
        document.querySelector(
            register
                ? '[data-register-form]'
                : '[data-login-form]'
        );

    if (!form) {
        throw new Error(
            register
                ? 'Không tìm thấy form đăng ký.'
                : 'Không tìm thấy form đăng nhập.'
        );
    }

    initPasswordControls(form);
    initPasswordConfirmation(form);

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

            const password =
                form.querySelector('[name="password"]');

            const confirmation =
                form.querySelector(
                    '[name="password_confirmation"]'
                );

            if (password && confirmation) {
                confirmation.setCustomValidity(
                    password.value !==
                    confirmation.value
                        ? 'Hai mật khẩu chưa khớp.'
                        : ''
                );
            }

            if (!form.checkValidity()) {
                form.reportValidity();

                return;
            }

            setBusy(form, true);
            resetError(errorBoxElement);

            const data =
                new FormData(form);

            const body = register
                ? {
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
                }
                : {
                    email:
                        data.get('email'),

                    password:
                        data.get('password')
                };

            try {
                await request(
                    register
                        ? '/auth/register'
                        : '/auth/login',
                    {
                        method: 'POST',
                        body,
                        headers: {
                            'X-Use-Cookie-Auth':
                                '1'
                        }
                    }
                );

                localStorage.removeItem(
                    'current_user'
                );

                sessionStorage.removeItem(
                    'access_token'
                );

                localStorage.removeItem(
                    'access_token'
                );

                location.href =
                    url('account');

            } catch (error) {
                showFormError(
                    errorBoxElement,
                    error
                );

            } finally {
                setBusy(form, false);
            }
        }
    );
}

export {
    auth
};

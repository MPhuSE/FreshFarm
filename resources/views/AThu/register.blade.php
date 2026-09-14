<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Đăng ký tài khoản</title>
    <link rel="stylesheet" href="{{ asset('AThu/style.css') }}">
</head>

<body class="register-page">
    <div class="auth-card">
        <div class="form-panel">
            <form id="register" class="register">
                <h1>Đăng ký tài khoản</h1>
                <div>
                    <label for="name">Họ tên: </label>
                    <input type="text" id="name" placeholder="Nhập họ và tên" required>
                </div>
                <div>
                    <label for="email">Email: </label>
                    <input type="email" id="email" placeholder="Nhập email của bạn" required>
                </div>
                <div>
                    <label for="phone">Số điện thoại: </label>
                    <input type="tel" id="phone" placeholder="Nhập số điện thoại" required>
                </div>
                <div class="password-field">
                    <label for="password">Mật khẩu: </label>
                    <input type="password" id="password" placeholder="Nhập mật khẩu" required>
                    <button type="button" class="toggle-password" data-target="password">👁</button>
                </div>
                <div class="password-field">
                    <label for="confirm_password">Xác nhận mật khẩu: </label>
                    <input type="password" id="confirm_password" placeholder="Nhập lại mật khẩu" required>
                    <button type="button" class="toggle-password" data-target="confirm_password">👁</button>
                </div>
                <button type="submit" id="registerBtn">Đăng ký</button>
                <div id="errorMsg" class="error-box"></div>
                <p class="login-link">Đã có tài khoản? <a href="/login">Đăng nhập ngay!</a></p>
            </form>
        </div>
        <div class="brand-panel">
            <h2>Tham gia cùng chúng tôi!</h2>
            <p>Tạo tài khoản để bắt đầu trải nghiệm nông sản tươi sạch mỗi ngày.</p>
        </div>
    </div>

    <script>
        document.querySelectorAll('.toggle-password').forEach(btn => {
            btn.addEventListener('click', function () {
                const input = document.getElementById(this.dataset.target);
                input.type = input.type === 'password' ? 'text' : 'password';
                this.textContent = input.type === 'password' ? '👁' : '🙈';
            });
        });

        document.getElementById('register').addEventListener('submit', async function (e) {
            e.preventDefault();

            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const phone = document.getElementById('phone').value;
            const password = document.getElementById('password').value;
            const confirm_password = document.getElementById('confirm_password').value;
            const errorBox = document.getElementById('errorMsg');
            const btn = document.getElementById('registerBtn');
            errorBox.innerHTML = '';

            if (password != confirm_password) {
                errorBox.innerHTML = '<p>Xác nhận lại mật khẩu!</p>';
                return;
            }

            btn.disabled = true;
            btn.textContent = 'Đang xử lý...';

            try {
                const response = await fetch('http://localhost:8000/api/v1/auth/register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        name: name,
                        email: email,
                        phone: phone,
                        password: password,
                        password_confirmation: confirm_password
                    })
                });

                const result = await response.json();

                if (result.success) {
                    localStorage.setItem('token', result.data.token);
                    alert('Đăng ký thành công!');
                } else {
                    let allErrors = '';
                    for (const field in result.errors) {
                        result.errors[field].forEach(msg => {
                            allErrors += `<p>${msg}</p>`;
                        });
                    }
                    errorBox.innerHTML = allErrors;
                }
            } catch (error) {
                errorBox.innerHTML = '<p>Không kết nối được tới server!</p>';
            } finally {
                btn.disabled = false;
                btn.textContent = 'Đăng ký';
            }
        });
    </script>
</body>

</html>
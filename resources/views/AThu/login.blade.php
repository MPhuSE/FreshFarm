<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="{{ asset('AThu/style.css') }}">
</head>

<body class="login-page">
    <div class="auth-card">
        <div class="brand-panel">
            <h2>Chào mừng trở lại!</h2>
            <p>Đăng nhập để tiếp tục mua sắm nông sản sạch cùng FreshFarm.</p>
        </div>
        <div class="form-panel">
            <form class="login" id="login">
                <h1>Đăng nhập</h1>
                <div>
                    <input type="email" id="email" placeholder="Nhập email của bạn" required>
                </div>
                <div class="password-field">
                    <input type="password" id="password" placeholder="Nhập mật khẩu" required>
                    <button type="button" class="toggle-password" data-target="password">👁</button>
                </div>
                <div class="form-options">
                    <label class="checkbox-label">
                        <input type="checkbox" id="remember"> Ghi nhớ đăng nhập
                    </label>
                    <a href="#" class="forgot-link">Quên mật khẩu?</a>
                </div>
                <button type="submit" id="loginBtn">Đăng nhập</button>
                <div id="errorMsg" class="error-box"></div>
                <p>Chưa có tài khoản? <a href="/register">Đăng ký ngay!</a></p>
            </form>
        </div>
        <script>
            document.querySelectorAll('.toggle-password').forEach(btn => {
                btn.addEventListener('click', function () {
                    const input = document.getElementById(this.dataset.target);
                    input.type = input.type === 'password' ? 'text' : 'password';
                    this.textContent = input.type === 'password' ? '👁' : '🙈';
                });
            });

            document.getElementById('login').addEventListener('submit', async function (e) {
                e.preventDefault();
                const email = document.getElementById('email').value;
                const password = document.getElementById('password').value;
                const errorBox = document.getElementById('errorMsg');
                const btn = document.getElementById('loginBtn');
                errorBox.innerHTML = '';

                if (email === '' || password === '') {
                    errorBox.innerHTML = '<p>Kiểm tra lại mật khẩu và email!</p>';
                    return;
                }

                btn.disabled = true;
                btn.textContent = 'Đang xử lý...';

                try {
                    const response = await fetch('http://localhost:8000/api/v1/auth/login', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ email: email, password: password })
                    });

                    const result = await response.json();

                    if (result.success) {
                        localStorage.setItem('token', result.data.token);
                        alert('Đăng nhập thành công!');
                    } else {
                        errorBox.innerHTML = `<p>${result.message}</p>`;
                    }
                } catch (error) {
                    errorBox.innerHTML = '<p>Không kết nối được tới server!</p>';
                } finally {
                    btn.disabled = false;
                    btn.textContent = 'Đăng nhập';
                }
            });
        </script>
</body>

</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="{{ asset('css/AThu/style.css') }}">
</head>
<body class="login-page">
    <form class="login" id="login">
        <h1>Đăng nhập</h1>
        <div>
            <input type="email" id="email" placeholder="Nhập email của bạn" required >
        </div>
        <div>
            <input type="password" id="password" placeholder="Nhập mật khẩu" required>
        </div>
        <button type="submit">Đăng nhập</button>
        <p id="errorMsg" style="color:red;"></p>
        <p>Chưa có tài khoản? <a href="/register">Đăng ký ngay!</a></p>
    </form>
</body>
<script>
    document.getElementById('login').addEventListener('submit', function(e){
        e.preventDefault(); 
        const email = document.getElementById('email').value; 
        const password = document.getElementById('password').value; 
        if (email === '' || password === ''){
            document.getElementById('errorMsg').innerText = 'Kiểm tra lại mật khẩu và email!'; 
            return; 
        }
        alert('Đăng nhập thành công!')
    });

</script>
</html>
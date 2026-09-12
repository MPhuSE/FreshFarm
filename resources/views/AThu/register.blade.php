<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký tài khoản</title>
    <link rel="stylesheet" href="{{ asset('css/AThu/style.css') }}">
</head>
<body class="register-page">
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
            <label for="password">Mật khẩu: </label>
            <input type="password" id="password" placeholder="Nhập mật khẩu: " required>
        </div>
        <div>
            <label for="confirm_password">Xác nhận mật khẩu mới: </label>
            <input type="password" id="confirm_password" placeholder="Nhập lại mật khẩu: " required>
        </div>
        <button type="submit">Đăng ký</button>
        <p id="errorMsg" style="color:red;"></p>
         <p class="login-link"> Đã có tài khoản?<a href="/login">Đăng nhập ngay!</a></p>
    </form>
    <script>
        document.getElementById('register').addEventListener('submit', function(e){
            e.preventDefault(); 
            const password = document.getElementById('password').value; 
            const confirm_password = document.getElementById('confirm_password').value;
            if(password != confirm_password)
            {
                document.getElementById('errorMsg').innerText = 'Xác nhận lại mật khẩu!';
                return; 
            }
            alert('Đăng ký thành công!');
        }); 
        
    </script>
</body>
</html>
<form action="{{ route('register') }}" method="POST">
    @csrf
    <h2>ĐĂNG KÝ</h2>
    <input type="text" name="name" placeholder="Họ tên" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Mật khẩu" required>
    <input type="password" name="password_confirmation" placeholder="Nhập lại mật khẩu" required>
    <button type="submit">Đăng ký</button>
</form>
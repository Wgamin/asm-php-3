@extends('admin.layouts.admin_auth')

@section('title', 'Đăng ký Admin')

@section('content')
    <form action="{{ route('admin.register') }}" method="POST">
        @csrf
        
        <div class="mb-3">
            <label class="form-label">Họ tên</label>
            <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
            @error('name') <span class="text-danger fs-7">{{ $message }}</span> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
            @error('email') <span class="text-danger fs-7">{{ $message }}</span> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Mật khẩu</label>
            <input type="password" name="password" class="form-control" required>
            @error('password') <span class="text-danger fs-7">{{ $message }}</span> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Nhập lại mật khẩu</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>

        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-admin btn-lg">TẠO TÀI KHOẢN ADMIN</button>
        </div>

        <div class="text-center">
            <a href="{{ route('admin.login') }}" class="text-muted fs-7">Đã có tài khoản? Đăng nhập</a>
        </div>
    </form>
@endsection
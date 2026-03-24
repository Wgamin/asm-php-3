@extends('admin.layouts.admin_auth')

@section('title', 'Đăng nhập')

@section('content')
    <form action="{{ route('admin.login') }}" method="POST">
        @csrf
        
        @if ($errors->any())
            <div class="alert alert-danger py-2">
                <ul class="mb-0 fs-7">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mb-3">
            <label class="form-label">Email Admin</label>
            <input type="email" name="email" class="form-control" placeholder="admin@gmail.com" required value="{{ old('email') }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Mật khẩu</label>
            <input type="password" name="password" class="form-control" placeholder="******" required>
        </div>

        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-admin btn-lg">ĐĂNG NHẬP</button>
        </div>

        <div class="text-center">
            <a href="{{ route('admin.register') }}" class="text-muted fs-7">Tạo tài khoản Admin (Dev/Test)</a>
        </div>
    </form>
@endsection
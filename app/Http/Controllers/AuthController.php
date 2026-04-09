<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            if (auth()->user()->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            return redirect('/');
        }

        return back()->withErrors(['email' => 'Email hoặc mật khẩu không đúng.']);
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'user',
        ]);

        return redirect()->route('login')->with('success', 'Đăng ký thành công!');
    }

    public function logout()
    {
        Auth::logout();

        return redirect('/');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->string('email')->toString())->first();
        if (! $user) {
            return back()
                ->withErrors(['email' => 'Không tìm thấy tài khoản nào với email này.'])
                ->withInput($request->only('email'));
        }

        $existing = DB::table($this->passwordResetTable())
            ->where('email', $user->email)
            ->first();

        if ($existing && $existing->created_at) {
            $availableAt = Carbon::parse($existing->created_at)->addSeconds($this->passwordResetThrottleSeconds());
            if ($availableAt->isFuture()) {
                return back()
                    ->withErrors(['email' => 'Bạn vừa yêu cầu quá gần. Vui lòng đợi một chút rồi thử lại.'])
                    ->withInput($request->only('email'));
            }
        }

        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        DB::table($this->passwordResetTable())->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make($otp),
                'created_at' => now(),
            ]
        );

        $user->sendPasswordOtpNotification($otp, $this->passwordResetExpireMinutes());

        return redirect()
            ->route('password.reset', ['email' => $user->email])
            ->with('success', 'Mã OTP đã được gửi vào email của bạn.');
    }

    public function showResetPassword(Request $request)
    {
        return view('auth.reset-password', [
            'email' => (string) old('email', $request->query('email', '')),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => ['required', 'digits:6'],
            'password' => 'required|confirmed|min:6',
        ]);

        $user = User::where('email', $request->string('email')->toString())->first();
        if (! $user) {
            return back()
                ->withErrors(['email' => 'Không tìm thấy tài khoản nào với email này.'])
                ->withInput($request->only('email'));
        }

        $tokenRecord = DB::table($this->passwordResetTable())
            ->where('email', $user->email)
            ->first();

        if (! $tokenRecord || ! $tokenRecord->created_at) {
            return back()
                ->withErrors(['otp' => 'Mã OTP không hợp lệ hoặc đã hết hạn.'])
                ->withInput($request->only('email'));
        }

        $expiresAt = Carbon::parse($tokenRecord->created_at)->addMinutes($this->passwordResetExpireMinutes());
        if ($expiresAt->isPast()) {
            DB::table($this->passwordResetTable())
                ->where('email', $user->email)
                ->delete();

            return back()
                ->withErrors(['otp' => 'Mã OTP đã hết hạn. Vui lòng yêu cầu mã mới.'])
                ->withInput($request->only('email'));
        }

        if (! Hash::check($request->string('otp')->toString(), $tokenRecord->token)) {
            return back()
                ->withErrors(['otp' => 'Mã OTP không chính xác.'])
                ->withInput($request->only('email'));
        }

        $user->forceFill([
            'password' => Hash::make($request->string('password')->toString()),
            'remember_token' => Str::random(60),
        ])->save();

        DB::table($this->passwordResetTable())
            ->where('email', $user->email)
            ->delete();

        event(new PasswordReset($user));

        return redirect()
            ->route('login')
            ->with('success', 'Mật khẩu đã được cập nhật thành công. Bạn có thể đăng nhập ngay bây giờ.');
    }

    public function showAdminLogin()
    {
        return view('admin.auth.login');
    }

    public function adminLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            if (auth()->user()->role === 'admin') {
                $request->session()->regenerate();

                return redirect()->route('admin.dashboard');
            }

            Auth::logout();

            return back()->withErrors(['email' => 'Tài khoản này không có quyền truy cập quản trị.']);
        }

        return back()->withErrors(['email' => 'Email hoặc mật khẩu không đúng.']);
    }

    public function showAdminRegister()
    {
        return view('admin.auth.register');
    }

    public function adminRegister(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'admin',
        ]);

        return redirect()->route('admin.login')->with('success', 'Tạo tài khoản Admin thành công!');
    }

    public function storeAdmin(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin',
        ]);

        return redirect()->back()->with('success', 'Đã tạo tài khoản Admin mới thành công!');
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                $user->update(['google_id' => $googleUser->getId()]);
                Auth::login($user);
            } else {
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'password' => Hash::make(Str::random(24)),
                    'role' => 'user',
                ]);
                Auth::login($user);
            }

            return redirect('/')->with('success', 'Đăng nhập bằng Google thành công!');
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['email' => 'Có lỗi xảy ra khi đăng nhập bằng Google. Vui lòng thử lại!']);
        }
    }

    protected function passwordResetTable(): string
    {
        return (string) config('auth.passwords.users.table', 'password_reset_tokens');
    }

    protected function passwordResetExpireMinutes(): int
    {
        return (int) config('auth.passwords.users.expire', 60);
    }

    protected function passwordResetThrottleSeconds(): int
    {
        return (int) config('auth.passwords.users.throttle', 60);
    }
}

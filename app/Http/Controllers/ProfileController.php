<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $user->load(['wishlists.category', 'wishlists']);

        $orders = $user->orders()->latest()->get();
        $addresses = $user->addresses()->get();
        $defaultAddress = $addresses->firstWhere('is_default', true) ?? $addresses->first();
        $compareIds = array_values(array_map('intval', session()->get('compare', [])));
        $compareProducts = Product::with(['category', 'variants'])
            ->whereIn('id', $compareIds)
            ->get()
            ->sortBy(function ($product) use ($compareIds) {
                return array_search($product->id, $compareIds);
            })
            ->values();

        $editingAddress = null;
        $editAddressId = (int) request('edit_address', 0);
        if ($editAddressId > 0) {
            $editingAddress = $addresses->firstWhere('id', $editAddressId);
        }

        return view('profile.index', compact(
            'user',
            'orders',
            'addresses',
            'defaultAddress',
            'editingAddress',
            'compareProducts'
        ));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:30',
            'avatar' => 'nullable|image|max:2048',
            'remove_avatar' => 'nullable|boolean',
            'password' => 'nullable|min:6|confirmed',
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->phone = $data['phone'];

        if ($request->boolean('remove_avatar') && $user->avatar) {
            Storage::disk('public')->delete($user->avatar);
            $user->avatar = null;
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $user->avatar = $request->file('avatar')->store('avatars', 'public');
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return back()
            ->with('success', 'Cập nhật hồ sơ thành công!')
            ->with('profile_tab', 'info');
    }

    public function storeAddress(Request $request)
    {
        $user = Auth::user();
        $data = $this->validateAddress($request);
        $shouldBeDefault = $request->boolean('is_default') || ! $user->addresses()->exists();

        DB::transaction(function () use ($user, $data, $shouldBeDefault) {
            if ($shouldBeDefault) {
                $user->addresses()->update(['is_default' => false]);
            }

            $user->addresses()->create([
                ...$data,
                'is_default' => $shouldBeDefault,
            ]);
        });

        return redirect()
            ->route('profile', ['tab' => 'addresses'])
            ->with('success', 'Đã thêm địa chỉ giao hàng.')
            ->with('profile_tab', 'addresses');
    }

    public function updateAddress(Request $request, UserAddress $address)
    {
        $user = Auth::user();
        $address = $this->ownedAddress($address);
        $data = $this->validateAddress($request);
        $shouldBeDefault = $request->boolean('is_default');

        DB::transaction(function () use ($user, $address, $data, &$shouldBeDefault) {
            $hasOtherAddresses = $user->addresses()->where('id', '!=', $address->id)->exists();

            if (! $hasOtherAddresses) {
                $shouldBeDefault = true;
            }

            if (! $shouldBeDefault && $address->is_default) {
                $shouldBeDefault = ! $user->addresses()
                    ->where('id', '!=', $address->id)
                    ->where('is_default', true)
                    ->exists();
            }

            if ($shouldBeDefault) {
                $user->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
            }

            $address->update([
                ...$data,
                'is_default' => $shouldBeDefault,
            ]);
        });

        return redirect()
            ->route('profile', ['tab' => 'addresses'])
            ->with('success', 'Đã cập nhật địa chỉ giao hàng.')
            ->with('profile_tab', 'addresses');
    }

    public function destroyAddress(UserAddress $address)
    {
        $user = Auth::user();
        $address = $this->ownedAddress($address);
        $wasDefault = $address->is_default;

        DB::transaction(function () use ($user, $address, $wasDefault) {
            $address->delete();

            if ($wasDefault || ! $user->addresses()->where('is_default', true)->exists()) {
                $fallback = $user->addresses()->latest('id')->first();
                if ($fallback) {
                    $fallback->update(['is_default' => true]);
                }
            }
        });

        return redirect()
            ->route('profile', ['tab' => 'addresses'])
            ->with('success', 'Đã xóa địa chỉ giao hàng.')
            ->with('profile_tab', 'addresses');
    }

    public function setDefaultAddress(UserAddress $address)
    {
        $user = Auth::user();
        $address = $this->ownedAddress($address);

        DB::transaction(function () use ($user, $address) {
            $user->addresses()->update(['is_default' => false]);
            $address->update(['is_default' => true]);
        });

        return redirect()
            ->route('profile', ['tab' => 'addresses'])
            ->with('success', 'Đã đặt địa chỉ mặc định.')
            ->with('profile_tab', 'addresses');
    }

    protected function validateAddress(Request $request): array
    {
        return $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'province' => ['required', 'string', 'max:255'],
            'district' => ['required', 'string', 'max:255'],
            'ward' => ['required', 'string', 'max:255'],
            'address_line' => ['required', 'string', 'max:500'],
        ]);
    }

    protected function ownedAddress(UserAddress $address): UserAddress
    {
        if ($address->user_id !== (int) Auth::id()) {
            abort(404);
        }

        return $address;
    }
}

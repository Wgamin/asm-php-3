<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SettingController extends Controller
{
    public function index()
    {
        $admin = Auth::user();
        $warehouse = Warehouse::query()->default()->latest('id')->first();

        return view('admin.settings.index', compact('admin', 'warehouse'));
    }

    public function update(Request $request)
    {
        $admin = Auth::user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $admin->id],
            'password' => ['nullable', 'min:6', 'confirmed'],
            'warehouse_name' => ['required', 'string', 'max:255'],
            'warehouse_phone' => ['required', 'string', 'max:30'],
            'warehouse_province' => ['required', 'string', 'max:255'],
            'warehouse_district' => ['required', 'string', 'max:255'],
            'warehouse_ward' => ['required', 'string', 'max:255'],
            'warehouse_address_line' => ['required', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($admin, $data, $request) {
            $admin->name = $data['name'];
            $admin->email = $data['email'];

            if ($request->filled('password')) {
                $admin->password = Hash::make($data['password']);
            }

            $admin->save();

            $warehouse = Warehouse::query()->default()->first() ?? new Warehouse();
            Warehouse::query()->update(['is_default' => false]);
            $warehouse->fill([
                'name' => $data['warehouse_name'],
                'phone' => $data['warehouse_phone'],
                'province' => $data['warehouse_province'],
                'district' => $data['warehouse_district'],
                'ward' => $data['warehouse_ward'],
                'address_line' => $data['warehouse_address_line'],
                'is_default' => true,
                'is_active' => true,
            ]);
            $warehouse->save();
        });

        return back()->with('success', 'Đã cập nhật tài khoản quản trị và kho mặc định.');
    }
}

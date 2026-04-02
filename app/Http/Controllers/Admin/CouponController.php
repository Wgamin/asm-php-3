<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::latest()->paginate(10);

        return view('admin.coupons.index', compact('coupons'));
    }

    public function create()
    {
        return view('admin.coupons.create');
    }

    public function store(Request $request)
    {
        Coupon::create($this->validatedData($request));

        return redirect()->route('admin.coupons.index')->with('success', 'Tạo coupon thành công!');
    }

    public function edit(Coupon $coupon)
    {
        return view('admin.coupons.edit', compact('coupon'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $coupon->update($this->validatedData($request, $coupon));

        return redirect()->route('admin.coupons.index')->with('success', 'Cập nhật coupon thành công!');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return redirect()->route('admin.coupons.index')->with('success', 'Xóa coupon thành công!');
    }

    protected function validatedData(Request $request, ?Coupon $coupon = null): array
    {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Za-z0-9\\-_]+$/',
                Rule::unique('coupons', 'code')->ignore($coupon?->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', Rule::in([Coupon::TYPE_FIXED, Coupon::TYPE_PERCENT])],
            'value' => ['required', 'numeric', 'min:0.01'],
            'min_order_amount' => ['nullable', 'numeric', 'min:0'],
            'max_discount_amount' => ['nullable', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($validated['type'] === Coupon::TYPE_PERCENT && $validated['value'] > 100) {
            throw ValidationException::withMessages([
                'value' => 'Coupon phần trăm không được vượt quá 100%.',
            ]);
        }

        $validated['code'] = strtoupper($validated['code']);
        $validated['min_order_amount'] = $validated['min_order_amount'] ?? null;
        $validated['max_discount_amount'] = $validated['max_discount_amount'] ?? null;
        $validated['usage_limit'] = $validated['usage_limit'] ?? null;
        $validated['starts_at'] = $validated['starts_at'] ?? null;
        $validated['expires_at'] = $validated['expires_at'] ?? null;
        $validated['is_active'] = $request->boolean('is_active');

        return $validated;
    }
}

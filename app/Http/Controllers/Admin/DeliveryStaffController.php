<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Delivery staff are NOT permanent employees and are not related to the owner,
 * so unlike the Owner/Cashier accounts (which are seeded once and permanent),
 * delivery staff accounts only exist if the Owner creates them here.
 * There is no public registration route for the "delivery" role — see routes/auth.php.
 */
class DeliveryStaffController extends Controller
{
    public function index()
    {
        $staff = User::where('role', 'delivery')
            ->withCount([
                'deliveries as active_deliveries_count' => fn($q) => $q->whereIn('status', ['out_for_delivery']),
                'deliveries as completed_deliveries_count' => fn($q) => $q->where('status', 'completed'),
            ])
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->get();

        return view('admin.delivery-staff.index', compact('staff'));
    }

    public function create()
    {
        return view('admin.delivery-staff.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'required|string|max:30',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role' => 'delivery',
            'is_active' => true,
            'email_verified_at' => now(), // owner-created accounts are trusted immediately
        ]);

        return redirect()->route('admin.delivery-staff.index')
            ->with('success', 'Delivery staff account created. Share the login credentials with them directly.');
    }

    public function edit(User $deliveryStaff)
    {
        abort_unless($deliveryStaff->role === 'delivery', 404);
        return view('admin.delivery-staff.edit', ['staff' => $deliveryStaff]);
    }

    public function update(Request $request, User $deliveryStaff)
    {
        abort_unless($deliveryStaff->role === 'delivery', 404);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($deliveryStaff->id)],
            'phone' => 'required|string|max:30',
            'is_active' => 'required|boolean',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $deliveryStaff->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'is_active' => $validated['is_active'],
        ]);

        if (! empty($validated['password'])) {
            $deliveryStaff->update(['password' => Hash::make($validated['password'])]);
        }

        return back()->with('success', 'Delivery staff account updated.');
    }

    /**
     * Delivery staff are temporary/non-permanent, so the owner can remove their account.
     * Blocked only while they still have a delivery in progress — reassign or wait for it
     * to complete/be cancelled first, so an order is never left pointing at a deleted user.
     */
    public function destroy(User $deliveryStaff)
    {
        abort_unless($deliveryStaff->role === 'delivery', 404);

        $activeDeliveries = $deliveryStaff->deliveries()->where('status', 'out_for_delivery')->count();

        if ($activeDeliveries > 0) {
            throw ValidationException::withMessages([
                'delivery' => "Cannot delete: this staff member has {$activeDeliveries} delivery(ies) currently out for delivery. Reassign or wait for completion first.",
            ]);
        }

        // Keep historical/completed order records intact — just detach the reference.
        $deliveryStaff->deliveries()->update(['assigned_delivery_id' => null]);
        $deliveryStaff->delete();

        return redirect()->route('admin.delivery-staff.index')
            ->with('success', 'Delivery staff account deleted.');
    }
}

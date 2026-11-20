<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StaffController extends Controller
{
    public function index(): View
    {
        $this->ensureOwner();
        $staff = User::query()->orderBy('role')->orderBy('name')->get();

        return view('staff.index', compact('staff'));
    }

    public function promote(User $user): RedirectResponse
    {
        $this->ensureOwner();

        if ($user->role !== 'owner') {
            $user->update(['role' => 'owner']);
        }

        return redirect()->route('staff.index')->with('status', 'Staff member promoted to owner.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->ensureOwner();
        abort_if($user->is(auth()->user()), 403, 'You cannot remove your own account.');

        if ($user->orders()->exists()) {
            return back()->withErrors(['staff' => 'This account has order history and cannot be removed.']);
        }

        if ($user->isOwner() && User::query()->where('role', 'owner')->count() <= 1) {
            return back()->withErrors(['staff' => 'At least one owner account must remain.']);
        }

        DB::transaction(fn () => $user->delete());

        return redirect()->route('staff.index')->with('status', 'Staff member removed.');
    }

    private function ensureOwner(): void
    {
        abort_unless(auth()->user()->isOwner(), 403);
    }
}

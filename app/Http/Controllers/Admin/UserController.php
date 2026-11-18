<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $role = (string) $request->query('role');
        $q = trim((string) $request->query('q'));

        $users = User::query()
            ->when(Role::tryFrom($role), fn ($query) => $query->where('role', $role))
            ->when($q, fn ($query) => $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%");
            }))
            ->orderBy('role')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'role' => $role,
            'q' => $q,
            'roles' => Role::cases(),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create', [
            'user' => new User(),
            'assignable' => $this->assignable(),
            'isSelf' => false,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $user = new User($data);
        $user->email_verified_at = now();
        $user->save();

        return redirect()->route('admin.users.index')->with('success', "Akun {$user->name} ditambahkan.");
    }

    public function edit(Request $request, User $user): View
    {
        $this->guardOwner($user);

        return view('admin.users.edit', [
            'user' => $user,
            'assignable' => $this->assignable(),
            'isSelf' => $user->id === $request->user()->id,
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->guardOwner($user);

        $data = $this->validated($request, $user);
        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', "Akun {$user->name} diperbarui.");
    }

    private function guardOwner(User $user): void
    {
        abort_if($user->role === Role::Owner, 403, 'Akun owner tidak dapat diubah dari panel admin.');
    }

    /** @return Role[] */
    private function assignable(): array
    {
        return [Role::Customer, Role::Operator, Role::Admin];
    }

    private function validated(Request $request, ?User $user = null): array
    {
        $isSelf = $user && $user->id === $request->user()->id;

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user?->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'role' => $isSelf
                ? ['nullable']
                : ['required', Rule::in(array_map(fn ($r) => $r->value, $this->assignable()))],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'name.max' => 'Nama maksimal 100 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah dipakai akun lain.',
            'phone.max' => 'Nomor telepon maksimal 20 karakter.',
            'address.max' => 'Alamat maksimal 500 karakter.',
            'role.required' => 'Pilih peran akun.',
            'role.in' => 'Peran tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        // Admin tidak boleh mengubah peran atau menonaktifkan akunnya sendiri
        if ($isSelf) {
            unset($data['role']);
            $data['is_active'] = true;
        } else {
            $data['is_active'] = $request->boolean('is_active');
        }

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        return $data;
    }
}
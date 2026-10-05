<?php

namespace App\Domain\Utilisateurs\Http\Controllers;

use App\Domain\Utilisateurs\Http\Requests\ResetPasswordRequest;
use App\Domain\Utilisateurs\Http\Requests\StoreUserRequest;
use App\Domain\Utilisateurs\Http\Requests\UpdateUserRequest;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Enums\UserRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', User::class);

        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        $users = $query->latest()->paginate(15)->withQueryString();
        $roles = UserRole::cases();

        return view('utilisateurs.index', compact('users', 'roles'));
    }

    public function create(): View
    {
        $this->authorize('create', User::class);

        $roles = UserRole::cases();

        return view('utilisateurs.create', compact('roles'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $data['is_active'] = $request->boolean('is_active', true);

        User::create($data);

        return redirect()->route('utilisateurs.index')
            ->with('success', 'Utilisateur créé avec succès.');
    }

    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        $roles = UserRole::cases();

        return view('utilisateurs.edit', compact('user', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $data = $request->validated();

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $data['is_active'] = $request->boolean('is_active', false);

        $user->update($data);

        return redirect()->route('utilisateurs.index')
            ->with('success', 'Utilisateur mis à jour avec succès.');
    }

    public function resetPassword(ResetPasswordRequest $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $user->update([
            'password' => Hash::make($request->input('password')),
        ]);

        return redirect()->route('utilisateurs.index')
            ->with('success', "Le mot de passe de {$user->name} a été réinitialisé avec succès.");
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        if (auth()->id() === $user->id) {
            return redirect()->route('utilisateurs.index')
                ->with('error', 'Vous ne pouvez pas supprimer votre propre compte utilisateur.');
        }

        try {
            $userName = $user->name;
            $user->delete();

            return redirect()->route('utilisateurs.index')
                ->with('success', "L'utilisateur '{$userName}' a été supprimé avec succès.");
        } catch (\Illuminate\Database\QueryException $e) {
            // L'utilisateur possède des opérations enregistrées (ventes, retours, pertes, mouvements) :
            // Désactivation automatique pour préserver la traçabilité financière et de stock.
            $user->update(['is_active' => false]);

            return redirect()->route('utilisateurs.index')
                ->with('info', "L'utilisateur '{$user->name}' possède un historique d'opérations enregistrées. Son compte a été désactivé pour conserver la traçabilité.");
        }
    }
}

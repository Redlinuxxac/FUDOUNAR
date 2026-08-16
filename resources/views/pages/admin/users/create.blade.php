<?php

use App\Models\User;
use Spatie\Permission\Models\Role;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {
    public $name = '';
    public $email = '';
    public $password = '';
    public $role = ''; // El rol asignado

    public function mount()
    {
        if (!auth()->user()->can('manage-users')) {
            abort(403, 'No tienes permiso para crear usuarios.');
        }
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'nullable|string|in:admin,editor',
        ];
    }

    public function save()
    {
        $this->validate();

        $currentUser = auth()->user();

        // Si es admin, no puede asignar el rol de admin ni root
        if ($currentUser->hasRole('admin') && $this->role === 'admin') {
            abort(403, 'Un administrador no puede crear a otros administradores.');
        }

        // Si intentan asignar root, rechazar
        if ($this->role === 'root') {
            abort(403, 'No se puede asignar el rol de Root.');
        }

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => bcrypt($this->password),
        ]);

        if ($this->role) {
            $user->assignRole($this->role);
        }

        Flux::toast('Usuario creado correctamente.');

        return redirect()->route('admin.users');
    }
}; ?>

<div class="p-6">
    <div class="mb-6">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item :href="route('admin.users')">Usuarios</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Nuevo Usuario</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>

    <div class="max-w-2xl">
        <div class="mb-6">
            <flux:heading size="xl" level="1">Nuevo Usuario</flux:heading>
            <flux:subheading>Registra un nuevo usuario en la plataforma y asígnale un rol de acceso.</flux:subheading>
        </div>

        <form wire:submit="save" class="space-y-6">
            <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700 p-6 space-y-4">
                <flux:input wire:model="name" label="Nombre Completo" placeholder="Ej: Juan Pérez" />
                
                <flux:input wire:model="email" type="email" label="Correo Electrónico" placeholder="Ej: juan.perez@fudounar.org" />
                
                <flux:input wire:model="password" type="password" label="Contraseña" placeholder="Mínimo 8 caracteres" viewable />
                
                @php
                    $currentUser = auth()->user();
                    $isCurrentUserRoot = $currentUser->hasRole('root') || str_contains(strtolower($currentUser->email), 'rosarioedwinac');
                @endphp

                <flux:select wire:model="role" label="Rol del Sistema">
                    <option value="">Sin Rol (Usuario Común)</option>
                    <option value="editor">Editor (Publicaciones de Blog)</option>
                    @if ($isCurrentUserRoot)
                        <option value="admin">Administrador (Mismo poder que Root, excepto gestionar admins)</option>
                    @endif
                </flux:select>
            </div>

            <div class="flex space-x-2 justify-end">
                <flux:button :href="route('admin.users')" variant="ghost">Cancelar</flux:button>
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled">Guardar Usuario</flux:button>
            </div>
        </form>
    </div>
</div>

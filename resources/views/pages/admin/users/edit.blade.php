<?php

use App\Models\User;
use Spatie\Permission\Models\Role;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {
    public User $user;
    public $name = '';
    public $email = '';
    public $password = '';
    public $role = '';

    public function mount(User $user)
    {
        if (!auth()->user()->can('manage-users')) {
            abort(403, 'No tienes permiso para gestionar usuarios.');
        }

        $currentUser = auth()->user();
        $isUserRoot = $user->hasRole('root') || str_contains(strtolower($user->email), 'rosarioedwinac');
        $isCurrentUserRoot = $currentUser->hasRole('root') || str_contains(strtolower($currentUser->email), 'rosarioedwinac');

        // Si el usuario a editar es Root y el usuario actual NO es ese mismo Root, abortar con 403
        if ($isUserRoot && !$isCurrentUserRoot) {
            abort(403, 'No tienes permiso para ver o modificar al usuario Root.');
        }

        $isUserAdmin = $user->hasRole('admin');

        // Si el usuario logueado es admin, no puede editar a un admin
        if ($currentUser->hasRole('admin') && $isUserAdmin) {
            abort(403, 'Un administrador no puede editar a otros administradores.');
        }

        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        
        // Obtener el primer rol asignado (si tiene)
        $this->role = $user->getRoleNames()->first() ?: '';
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $this->user->id,
            'password' => 'nullable|string|min:8',
            'role' => 'nullable|string|in:admin,editor',
        ];
    }

    public function save()
    {
        $this->validate();

        $currentUser = auth()->user();

        // 1. Nadie puede editar al root a menos que sea él mismo
        $isUserRoot = $this->user->hasRole('root') || str_contains(strtolower($this->user->email), 'rosarioedwinac');
        if ($isUserRoot && $currentUser->id !== $this->user->id) {
            abort(403, 'No tienes permiso para modificar al usuario Root.');
        }

        // 2. Si el usuario actual es admin, no puede asignar el rol de admin ni root
        if ($currentUser->hasRole('admin') && $this->role === 'admin') {
            abort(403, 'Un administrador no puede asignar el rol de Administrador.');
        }

        // 3. No se puede asignar el rol root a nadie
        if ($this->role === 'root') {
            abort(403, 'No se puede asignar el rol de Root.');
        }

        // 4. Si el usuario se está editando a sí mismo, no puede quitarse el rol para no quedar sin acceso
        if ($currentUser->id === $this->user->id && $this->role !== $this->user->getRoleNames()->first()) {
            Flux::toast('No puedes cambiar tu propio rol para evitar perder el acceso.', 'danger');
            return;
        }

        $updateData = [
            'name' => $this->name,
            'email' => $this->email,
        ];

        if ($this->password) {
            $updateData['password'] = bcrypt($this->password);
        }

        $this->user->update($updateData);

        // Si el usuario que se edita es el root, nunca le quitamos ni cambiamos el rol root
        if (!$isUserRoot) {
            $this->user->syncRoles($this->role ? [$this->role] : []);
        }

        Flux::toast('Usuario actualizado correctamente.');

        return redirect()->route('admin.users');
    }
}; ?>

<div class="p-6">
    <div class="mb-6">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item :href="route('admin.users')">Usuarios</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Editar Usuario</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>

    <div class="max-w-2xl">
        <div class="mb-6">
            <flux:heading size="xl" level="1">Editar Usuario: {{ $user->name }}</flux:heading>
            <flux:subheading>Modifica los detalles de la cuenta y los roles asignados del usuario.</flux:subheading>
        </div>

        <form wire:submit="save" class="space-y-6">
            <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700 p-6 space-y-4">
                <flux:input wire:model="name" label="Nombre Completo" />
                
                <flux:input wire:model="email" type="email" label="Correo Electrónico" />
                
                <flux:input wire:model="password" type="password" label="Nueva Contraseña" placeholder="Dejar en blanco para mantener la actual" viewable />
                
                @php
                    $currentUser = auth()->user();
                    $isCurrentUserRoot = $currentUser->hasRole('root') || str_contains(strtolower($currentUser->email), 'rosarioedwinac');
                    $isEditingRoot = $user->hasRole('root') || str_contains(strtolower($user->email), 'rosarioedwinac');
                @endphp

                @if ($isEditingRoot)
                    <div class="text-xs text-red-500 font-medium mt-2">
                        El rol de usuario Root es exclusivo y no puede ser alterado.
                    </div>
                @else
                    <flux:select wire:model="role" label="Rol del Sistema">
                        <option value="">Sin Rol (Usuario Común)</option>
                        <option value="editor">Editor (Publicaciones de Blog)</option>
                        @if ($isCurrentUserRoot)
                            <option value="admin">Administrador (Mismo poder que Root, excepto gestionar admins)</option>
                        @endif
                    </flux:select>
                @endif
            </div>

            <div class="flex space-x-2 justify-end">
                <flux:button :href="route('admin.users')" variant="ghost">Cancelar</flux:button>
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled">Guardar Cambios</flux:button>
            </div>
        </form>
    </div>
</div>

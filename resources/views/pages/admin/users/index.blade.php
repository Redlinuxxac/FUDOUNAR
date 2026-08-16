<?php

use App\Models\User;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {
    use WithPagination;

    public $search = '';

    public function mount()
    {
        if (!auth()->user()->can('manage-users')) {
            abort(403, 'No tienes permiso para gestionar usuarios.');
        }
    }

    public function with()
    {
        $currentUser = auth()->user();
        $isCurrentUserRoot = $currentUser->hasRole('root') || str_contains(strtolower($currentUser->email), 'rosarioedwinac');

        $query = User::query()
            ->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%");
            });

        if (!$isCurrentUserRoot) {
            $query->whereDoesntHave('roles', function ($q) {
                $q->where('name', 'root');
            })->where('email', 'not like', 'rosarioedwinac%');
        }

        return [
            'users' => $query->latest()->paginate(10),
        ];
    }

    public function delete($id)
    {
        $userToDelete = User::findOrFail($id);
        $currentUser = auth()->user();

        // 1. Nadie puede borrar al usuario root
        if ($userToDelete->hasRole('root') || str_contains(strtolower($userToDelete->email), 'rosarioedwinac')) {
            abort(403, 'No se puede eliminar al usuario Root.');
        }

        // 2. Si el usuario actual es admin, no puede borrar a otros admin
        if ($currentUser->hasRole('admin') && $userToDelete->hasRole('admin')) {
            abort(403, 'Un administrador no puede eliminar a otro administrador.');
        }

        // 3. Verificar permisos generales de eliminación
        if (!$currentUser->hasRole('root') && !$currentUser->hasRole('admin')) {
            abort(403, 'No tienes permisos para eliminar usuarios.');
        }

        $userToDelete->delete();
        Flux::toast('Usuario eliminado.');
    }
}; ?>

<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <flux:heading size="xl" level="1">Gestión de Usuarios</flux:heading>
            <flux:subheading>Administra las cuentas de usuario, asigna roles y gestiona permisos.</flux:subheading>
        </div>
        <flux:button :href="route('admin.users.create')" variant="primary" icon="plus" wire:navigate>Nuevo Usuario</flux:button>
    </div>

    <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700 overflow-hidden">
        <div class="p-4 border-b border-neutral-200 dark:border-neutral-700 flex items-center">
            <flux:input 
                wire:model.live="search" 
                placeholder="Buscar por nombre o correo..." 
                icon="magnifying-glass" 
                kbd="Ctrl+K"
                class="max-w-sm" 
                x-on:keydown.window.ctrl.k.prevent="$el.focus()"
                x-on:keydown.window.cmd.k.prevent="$el.focus()"
            />
        </div>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>Nombre</flux:table.column>
                <flux:table.column>Correo Electrónico</flux:table.column>
                <flux:table.column>Roles</flux:table.column>
                <flux:table.column>Fecha de Registro</flux:table.column>
                <flux:table.column>Acciones</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($users as $user)
                    @php
                        $isRoot = $user->hasRole('root') || str_contains(strtolower($user->email), 'rosarioedwinac');
                        $isAdmin = $user->hasRole('admin');
                        $isEditor = $user->hasRole('editor');
                        
                        $currentUser = auth()->user();
                        $isCurrentUserRoot = $currentUser->hasRole('root');
                        
                        // Permiso de edición
                        $canEdit = $isCurrentUserRoot || (!$isRoot && !$isAdmin);
                        
                        // Permiso de eliminación
                        $canDelete = !$isRoot && ($isCurrentUserRoot || !$isAdmin);
                    @endphp
                    <flux:table.row :key="$user->id">
                        <flux:table.cell class="font-medium">
                            <div class="flex items-center gap-2">
                                <flux:avatar :name="$user->name" :initials="$user->initials()" size="sm" />
                                <span>{{ $user->name }}</span>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>{{ $user->email }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-1 flex-wrap">
                                @forelse ($user->getRoleNames() as $roleName)
                                    @php
                                        $color = match($roleName) {
                                            'root' => 'red',
                                            'admin' => 'blue',
                                            'editor' => 'green',
                                            default => 'gray'
                                        };
                                    @endphp
                                    <flux:badge :color="$color" size="sm" class="uppercase">
                                        {{ $roleName }}
                                    </flux:badge>
                                @empty
                                    <flux:badge color="gray" size="sm">SIN ROL</flux:badge>
                                @endforelse
                            </div>
                        </flux:table.cell>
                        <flux:table.cell class="text-neutral-500 text-xs">
                            {{ $user->created_at ? $user->created_at->format('d/m/Y') : '-' }}
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex space-x-2">
                                @if ($canEdit)
                                    <flux:button :href="route('admin.users.edit', $user)" size="sm" variant="ghost" icon="pencil-square" wire:navigate />
                                @else
                                    <flux:button size="sm" variant="ghost" icon="pencil-square" class="opacity-30 cursor-not-allowed" disabled />
                                @endif

                                @if ($canDelete && $user->id !== $currentUser->id)
                                    <flux:button wire:click="delete({{ $user->id }})" 
                                                 wire:confirm="¿Estás seguro de eliminar a este usuario?"
                                                 size="sm" variant="ghost" color="red" icon="trash" />
                                @else
                                    <flux:button size="sm" variant="ghost" color="red" icon="trash" class="opacity-30 cursor-not-allowed" disabled />
                                @endif
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5" class="text-center py-12">
                            <flux:text>No se encontraron usuarios.</flux:text>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <div class="p-4 border-t border-neutral-200 dark:border-neutral-700">
            {{ $users->links() }}
        </div>
    </div>
</div>

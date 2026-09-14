<?php

use Spatie\Permission\Models\Role;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {
    public function mount()
    {
        if (!auth()->user()->can('manage-users')) {
            abort(403, 'No tienes permiso para ver los roles.');
        }
    }

    public function with()
    {
        return [
            'roles' => Role::with('permissions')->get(),
        ];
    }

    public function delete($id)
    {
        $role = Role::findOrFail($id);

        // Bloqueo de seguridad: No se pueden eliminar roles protegidos
        if (in_array($role->name, ['root', 'admin', 'editor'])) {
            abort(403, 'No tienes permiso para eliminar roles protegidos del sistema.');
        }

        $role->delete();
        Flux::toast('Rol eliminado correctamente.');
    }
}; ?>

<div class="p-6">
    <div class="mb-6">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item :href="route('admin.users')">Roles y Permisos</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Roles</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>

    <div class="flex justify-between items-center mb-6">
        <div>
            <flux:heading size="xl" level="1">Roles del Sistema</flux:heading>
            <flux:subheading>Visualiza los niveles de acceso (roles) del sistema y los permisos que tienen asignados.</flux:subheading>
        </div>
        <flux:button :href="route('admin.roles.create')" variant="primary" icon="plus" wire:navigate>Nuevo Rol</flux:button>
    </div>

    <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900/50">
                    <th class="p-4 text-xs font-semibold text-neutral-500 uppercase tracking-wider w-[15%]">Nombre del Rol</th>
                    <th class="p-4 text-xs font-semibold text-neutral-500 uppercase tracking-wider w-[45%]">Descripción / Ámbito de Acción</th>
                    <th class="p-4 text-xs font-semibold text-neutral-500 uppercase tracking-wider w-[25%]">Permisos Asociados</th>
                    <th class="p-4 text-xs font-semibold text-neutral-500 uppercase tracking-wider w-[15%]">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                @foreach ($roles as $role)
                    @php
                        $color = match($role->name) {
                            'root' => 'red',
                            'admin' => 'blue',
                            'editor' => 'green',
                            default => 'gray'
                        };
                        
                        $description = match($role->name) {
                            'root' => 'Superusuario del sistema con acceso total y privilegios exclusivos de gestión de administradores.',
                            'admin' => 'Administrador con poder total sobre el contenido y usuarios, excepto la gestión de otros administradores.',
                            'editor' => 'Usuario con privilegios exclusivos y limitados para la redacción y publicación de posts en el blog.',
                            default => 'Usuario estándar sin privilegios administrativos.'
                        };

                        $isProtected = in_array($role->name, ['root', 'admin', 'editor']);
                    @endphp
                    <tr class="hover:bg-neutral-50/50 dark:hover:bg-neutral-900/10">
                        <td class="p-4 align-top">
                            <div class="min-w-[120px]">
                                <flux:badge :color="$color" size="md" class="uppercase font-semibold">
                                    {{ $role->name }}
                                </flux:badge>
                            </div>
                        </td>
                        <td class="p-4 align-top pr-12">
                            <div class="text-neutral-600 dark:text-neutral-300 text-sm leading-relaxed whitespace-normal max-w-xl">
                                {{ $description }}
                            </div>
                        </td>
                        <td class="p-4 align-top">
                            <div class="flex gap-1.5 flex-wrap max-w-sm">
                                @forelse ($role->permissions as $permission)
                                    <flux:badge color="zinc" size="sm" class="lowercase font-mono text-[10px]">
                                        {{ $permission->name }}
                                    </flux:badge>
                                @empty
                                    <span class="text-xs text-neutral-400">Sin permisos explícitos</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="p-4 align-top">
                            <div class="flex space-x-2">
                                @if (!$isProtected)
                                    <flux:button :href="route('admin.roles.edit', $role)" size="sm" variant="ghost" icon="pencil-square" wire:navigate />
                                    <flux:button wire:click="delete({{ $role->id }})" 
                                                 wire:confirm="¿Estás seguro de eliminar este rol?"
                                                 size="sm" variant="ghost" color="red" icon="trash" />
                                @else
                                    <flux:button size="sm" variant="ghost" icon="pencil-square" class="opacity-30 cursor-not-allowed" disabled />
                                    <flux:button size="sm" variant="ghost" color="red" icon="trash" class="opacity-30 cursor-not-allowed" disabled />
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

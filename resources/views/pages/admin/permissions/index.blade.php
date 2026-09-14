<?php

use Spatie\Permission\Models\Permission;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {
    public function mount()
    {
        if (!auth()->user()->can('manage-users')) {
            abort(403, 'No tienes permiso para ver los permisos.');
        }
    }

    public function with()
    {
        return [
            'permissions' => Permission::with('roles')->get(),
        ];
    }

    public function delete($id)
    {
        $permission = Permission::findOrFail($id);

        // Bloqueo de seguridad: No se pueden eliminar permisos protegidos
        $protectedPermissions = ['manage-users', 'create-admins', 'delete-admins', 'manage-blog', 'manage-activities', 'manage-courses'];
        if (in_array($permission->name, $protectedPermissions)) {
            abort(403, 'No tienes permiso para eliminar permisos protegidos del sistema.');
        }

        $permission->delete();
        Flux::toast('Permiso eliminado correctamente.');
    }
}; ?>

<div class="p-6">
    <div class="mb-6">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item :href="route('admin.users')">Roles y Permisos</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Permisos</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>

    <div class="flex justify-between items-center mb-6">
        <div>
            <flux:heading size="xl" level="1">Permisos del Sistema</flux:heading>
            <flux:subheading>Lista de permisos de seguridad asignados para controlar la ejecución de funcionalidades.</flux:subheading>
        </div>
        <flux:button :href="route('admin.permissions.create')" variant="primary" icon="plus" wire:navigate>Nuevo Permiso</flux:button>
    </div>

    <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900/50">
                    <th class="p-4 text-xs font-semibold text-neutral-500 uppercase tracking-wider w-[20%]">Nombre del Permiso</th>
                    <th class="p-4 text-xs font-semibold text-neutral-500 uppercase tracking-wider w-[45%]">Descripción / Acción Protegida</th>
                    <th class="p-4 text-xs font-semibold text-neutral-500 uppercase tracking-wider w-[20%]">Roles que lo Poseen</th>
                    <th class="p-4 text-xs font-semibold text-neutral-500 uppercase tracking-wider w-[15%]">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                @foreach ($permissions as $permission)
                    @php
                        $description = match($permission->name) {
                            'manage-users' => 'Permite listar, crear, editar y eliminar usuarios en el panel administrativo.',
                            'create-admins' => 'Permiso exclusivo de Root para otorgar perfiles de Administrador a otras cuentas.',
                            'delete-admins' => 'Permiso exclusivo de Root para eliminar cuentas con roles de Administrador.',
                            'manage-blog' => 'Permite redactar, modificar y publicar posts en el blog.',
                            'manage-activities' => 'Permite administrar y programar las actividades comunitarias de la organización.',
                            'manage-courses' => 'Permite gestionar los cursos, lecciones y materiales educativos del sitio.',
                            default => 'Permiso genérico de control de acceso.'
                        };

                        $isProtected = in_array($permission->name, ['manage-users', 'create-admins', 'delete-admins', 'manage-blog', 'manage-activities', 'manage-courses']);
                    @endphp
                    <tr class="hover:bg-neutral-50/50 dark:hover:bg-neutral-900/10">
                        <td class="p-4 align-top font-mono text-xs font-semibold text-blue-600 dark:text-blue-400">
                            {{ $permission->name }}
                        </td>
                        <td class="p-4 align-top pr-12">
                            <div class="text-neutral-600 dark:text-neutral-300 text-sm leading-relaxed whitespace-normal max-w-xl">
                                {{ $description }}
                            </div>
                        </td>
                        <td class="p-4 align-top">
                            <div class="flex gap-1 flex-wrap max-w-xs">
                                @forelse ($permission->roles as $role)
                                    @php
                                        $color = match($role->name) {
                                            'root' => 'red',
                                            'admin' => 'blue',
                                            'editor' => 'green',
                                            default => 'gray'
                                        };
                                    @endphp
                                    <flux:badge :color="$color" size="sm" class="uppercase">
                                        {{ $role->name }}
                                    </flux:badge>
                                @empty
                                    <span class="text-xs text-neutral-400">Sin roles</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="p-4 align-top">
                            <div class="flex space-x-2">
                                @if (!$isProtected)
                                    <flux:button :href="route('admin.permissions.edit', $permission)" size="sm" variant="ghost" icon="pencil-square" wire:navigate />
                                    <flux:button wire:click="delete({{ $permission->id }})" 
                                                 wire:confirm="¿Estás seguro de eliminar este permiso?"
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

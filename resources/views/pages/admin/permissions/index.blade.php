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
}; ?>

<div class="p-6">
    <div class="mb-6">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item :href="route('admin.users')">Roles y Permisos</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Permisos</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>

    <div class="mb-6">
        <flux:heading size="xl" level="1">Permisos del Sistema</flux:heading>
        <flux:subheading>Lista de permisos de seguridad asignados para controlar la ejecución de funcionalidades.</flux:subheading>
    </div>

    <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700 overflow-hidden">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Nombre del Permiso</flux:table.column>
                <flux:table.column>Descripción / Acción Protegida</flux:table.column>
                <flux:table.column>Roles que lo Poseen</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
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
                    @endphp
                    <flux:table.row :key="$permission->id">
                        <flux:table.cell class="font-mono text-xs font-semibold text-blue-600 dark:text-blue-400">
                            {{ $permission->name }}
                        </flux:table.cell>
                        <flux:table.cell class="text-neutral-600 dark:text-neutral-300 text-sm max-w-md">
                            {{ $description }}
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-1 flex-wrap">
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
                                    <span class="text-xs text-neutral-400">Sin roles asignados</span>
                                @endforelse
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </div>
</div>

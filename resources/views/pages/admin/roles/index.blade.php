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
}; ?>

<div class="p-6">
    <div class="mb-6">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item :href="route('admin.users')">Roles y Permisos</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Roles</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>

    <div class="mb-6">
        <flux:heading size="xl" level="1">Roles del Sistema</flux:heading>
        <flux:subheading>Visualiza los niveles de acceso (roles) del sistema y los permisos que tienen asignados.</flux:subheading>
    </div>

    <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700 overflow-hidden">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Nombre del Rol</flux:table.column>
                <flux:table.column>Descripción / Ámbito de Acción</flux:table.column>
                <flux:table.column>Permisos Asociados</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
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
                    @endphp
                    <flux:table.row :key="$role->id">
                        <flux:table.cell class="font-semibold">
                            <flux:badge :color="$color" size="md" class="uppercase font-semibold">
                                {{ $role->name }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell class="text-neutral-600 dark:text-neutral-300 text-sm max-w-md">
                            {{ $description }}
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-1.5 flex-wrap">
                                @forelse ($role->permissions as $permission)
                                    <flux:badge color="zinc" size="sm" class="lowercase font-mono text-[10px]">
                                        {{ $permission->name }}
                                    </flux:badge>
                                @empty
                                    <span class="text-xs text-neutral-400">Sin permisos explícitos</span>
                                @endforelse
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </div>
</div>

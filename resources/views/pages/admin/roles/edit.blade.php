<?php

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {
    public Role $role;
    public $name = '';
    public $selectedPermissions = [];

    public function mount(Role $role)
    {
        if (!auth()->user()->can('manage-users')) {
            abort(403, 'No tienes permiso para gestionar roles.');
        }

        if (in_array($role->name, ['root', 'admin', 'editor'])) {
            abort(403, 'No se puede editar un rol protegido del sistema.');
        }

        $this->role = $role;
        $this->name = $role->name;
        $this->selectedPermissions = $role->permissions->pluck('name')->toArray();
    }

    public function with()
    {
        return [
            'permissions' => Permission::all(),
        ];
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|min:3|max:255|unique:roles,name,' . $this->role->id,
            'selectedPermissions' => 'nullable|array',
            'selectedPermissions.*' => 'exists:permissions,name',
        ];
    }

    public function save()
    {
        $this->validate();

        if (in_array(strtolower($this->name), ['root', 'admin', 'editor'])) {
            abort(403, 'No se puede cambiar el nombre a un rol protegido del sistema.');
        }

        $this->role->update([
            'name' => strtolower($this->name),
        ]);

        $this->role->syncPermissions($this->selectedPermissions);

        Flux::toast('Rol actualizado correctamente.');

        return redirect()->route('admin.roles');
    }
}; ?>

<div class="p-6">
    <div class="mb-6">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item :href="route('admin.roles')">Roles</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Editar Rol</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>

    <div class="max-w-2xl">
        <div class="mb-6">
            <flux:heading size="xl" level="1">Editar Rol: {{ $role->name }}</flux:heading>
            <flux:subheading>Actualiza el nombre del rol y modifica sus permisos asignados.</flux:subheading>
        </div>

        <form wire:submit="save" class="space-y-6">
            <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700 p-6 space-y-6">
                <flux:input wire:model="name" label="Nombre del Rol" />
                
                <div class="space-y-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300">Asignar Permisos al Rol</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach ($permissions as $permission)
                            @php
                                $desc = match($permission->name) {
                                    'manage-users' => 'Administración de cuentas de usuario',
                                    'create-admins' => 'Crear perfiles de Administrador (Root)',
                                    'delete-admins' => 'Eliminar cuentas de Administrador (Root)',
                                    'manage-blog' => 'Redactar y publicar en el blog',
                                    'manage-activities' => 'Gestionar actividades comunitarias',
                                    'manage-courses' => 'Gestionar plataforma educativa / cursos',
                                    default => 'Permiso de control'
                                };
                            @endphp
                            <div class="flex items-start gap-3 p-3 rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50/50 dark:bg-neutral-900/30">
                                <flux:checkbox wire:model="selectedPermissions" value="{{ $permission->name }}" />
                                <div class="grid leading-tight">
                                    <span class="text-sm font-semibold font-mono text-neutral-800 dark:text-neutral-200">{{ $permission->name }}</span>
                                    <span class="text-xs text-neutral-500 mt-0.5">{{ $desc }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="flex space-x-2 justify-end">
                <flux:button :href="route('admin.roles')" variant="ghost">Cancelar</flux:button>
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled">Guardar Cambios</flux:button>
            </div>
        </form>
    </div>
</div>

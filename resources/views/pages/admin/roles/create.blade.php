<?php

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {
    public $name = '';
    public $selectedPermissions = [];

    public function mount()
    {
        if (!auth()->user()->can('manage-users')) {
            abort(403, 'No tienes permiso para crear roles.');
        }
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
            'name' => 'required|string|min:3|max:255|unique:roles,name',
            'selectedPermissions' => 'nullable|array',
            'selectedPermissions.*' => 'exists:permissions,name',
        ];
    }

    public function save()
    {
        if (in_array(strtolower($this->name), ['root', 'admin', 'editor'])) {
            abort(403, 'No se puede crear un rol con el nombre de un rol protegido del sistema.');
        }

        $this->validate();

        $role = Role::create([
            'name' => strtolower($this->name),
        ]);

        if (!empty($this->selectedPermissions)) {
            $role->syncPermissions($this->selectedPermissions);
        }

        Flux::toast('Rol creado correctamente.');

        return redirect()->route('admin.roles');
    }
}; ?>

<div class="p-6">
    <div class="mb-6">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item :href="route('admin.roles')">Roles</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Nuevo Rol</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>

    <div class="max-w-2xl">
        <div class="mb-6">
            <flux:heading size="xl" level="1">Nuevo Rol de Acceso</flux:heading>
            <flux:subheading>Define un nuevo rol en el sistema y selecciona los permisos asociados.</flux:subheading>
        </div>

        <form wire:submit="save" class="space-y-6">
            <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700 p-6 space-y-6">
                <flux:input wire:model="name" label="Nombre del Rol" placeholder="Ej: moderador" />
                
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
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled">Guardar Rol</flux:button>
            </div>
        </form>
    </div>
</div>

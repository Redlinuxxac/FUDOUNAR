<?php

use Spatie\Permission\Models\Permission;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {
    public Permission $permission;
    public $name = '';

    public function mount(Permission $permission)
    {
        if (!auth()->user()->can('manage-users')) {
            abort(403, 'No tienes permiso para gestionar permisos.');
        }

        $protected = ['manage-users', 'create-admins', 'delete-admins', 'manage-blog', 'manage-activities', 'manage-courses'];
        if (in_array($permission->name, $protected)) {
            abort(403, 'No se puede editar un permiso protegido del sistema.');
        }

        $this->permission = $permission;
        $this->name = $permission->name;
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|min:3|max:255|unique:permissions,name,' . $this->permission->id,
        ];
    }

    public function save()
    {
        $this->validate();

        $protected = ['manage-users', 'create-admins', 'delete-admins', 'manage-blog', 'manage-activities', 'manage-courses'];
        if (in_array(strtolower($this->name), $protected)) {
            abort(403, 'No se puede cambiar el nombre a un permiso protegido del sistema.');
        }

        $this->permission->update([
            'name' => strtolower($this->name),
        ]);

        Flux::toast('Permiso actualizado correctamente.');

        return redirect()->route('admin.permissions');
    }
}; ?>

<div class="p-6">
    <div class="mb-6">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item :href="route('admin.permissions')">Permisos</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Editar Permiso</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>

    <div class="max-w-xl">
        <div class="mb-6">
            <flux:heading size="xl" level="1">Editar Permiso: {{ $permission->name }}</flux:heading>
            <flux:subheading>Actualiza el nombre del permiso de acceso.</flux:subheading>
        </div>

        <form wire:submit="save" class="space-y-6">
            <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700 p-6 space-y-4">
                <flux:input wire:model="name" label="Nombre del Permiso" />
            </div>

            <div class="flex space-x-2 justify-end">
                <flux:button :href="route('admin.permissions')" variant="ghost">Cancelar</flux:button>
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled">Guardar Cambios</flux:button>
            </div>
        </form>
    </div>
</div>

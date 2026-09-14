<?php

use Spatie\Permission\Models\Permission;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {
    public $name = '';

    public function mount()
    {
        if (!auth()->user()->can('manage-users')) {
            abort(403, 'No tienes permiso para crear permisos.');
        }
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|min:3|max:255|unique:permissions,name',
        ];
    }

    public function save()
    {
        $this->validate();

        $protected = ['manage-users', 'create-admins', 'delete-admins', 'manage-blog', 'manage-activities', 'manage-courses'];
        if (in_array(strtolower($this->name), $protected)) {
            abort(403, 'No se puede crear un permiso con un nombre protegido del sistema.');
        }

        Permission::create([
            'name' => strtolower($this->name),
        ]);

        Flux::toast('Permiso creado correctamente.');

        return redirect()->route('admin.permissions');
    }
}; ?>

<div class="p-6">
    <div class="mb-6">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item :href="route('admin.permissions')">Permisos</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Nuevo Permiso</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>

    <div class="max-w-xl">
        <div class="mb-6">
            <flux:heading size="xl" level="1">Nuevo Permiso del Sistema</flux:heading>
            <flux:subheading>Crea un nuevo permiso para proteger y controlar funciones de la plataforma.</flux:subheading>
        </div>

        <form wire:submit="save" class="space-y-6">
            <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700 p-6 space-y-4">
                <flux:input wire:model="name" label="Nombre del Permiso" placeholder="Ej: manage-banners" />
            </div>

            <div class="flex space-x-2 justify-end">
                <flux:button :href="route('admin.permissions')" variant="ghost">Cancelar</flux:button>
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled">Guardar Permiso</flux:button>
            </div>
        </form>
    </div>
</div>

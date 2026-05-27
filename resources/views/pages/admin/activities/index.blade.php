<?php

use App\Enums\ActivityStatus;
use App\Models\Activity;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {
    use WithPagination;

    public $search = '';

    public function with()
    {
        return [
            'activities' => Activity::query()
                ->where('title', 'like', "%{$this->search}%")
                ->latest()
                ->paginate(10),
        ];
    }

    public function delete($id)
    {
        Activity::findOrFail($id)->delete();
        Flux::toast('Actividad eliminada.');
    }
}; ?>

<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <flux:heading size="xl" level="1">Gestión de Actividades</flux:heading>
            <flux:subheading>Crea, edita y programa tus actividades comunitarias.</flux:subheading>
        </div>
        <flux:button :href="route('admin.activities.create')" variant="primary" icon="plus" wire:navigate>Nueva Actividad</flux:button>
    </div>

    <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700 overflow-hidden">
        <div class="p-4 border-b border-neutral-200 dark:border-neutral-700 flex items-center">
            <flux:input 
                wire:model.live="search" 
                placeholder="Buscar actividad..." 
                icon="magnifying-glass" 
                kbd="Ctrl+K"
                class="max-w-sm" 
                x-on:keydown.window.ctrl.k.prevent="$el.focus()"
                x-on:keydown.window.cmd.k.prevent="$el.focus()"
            />
        </div>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>Imagen</flux:table.column>
                <flux:table.column>Título</flux:table.column>
                <flux:table.column>Estado</flux:table.column>
                <flux:table.column>Inicio</flux:table.column>
                <flux:table.column>Acciones</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($activities as $activity)
                    <flux:table.row :key="$activity->id">
                        <flux:table.cell>
                            <img src="{{ $activity->image }}" class="w-12 h-12 rounded-lg object-cover border border-neutral-200">
                        </flux:table.cell>
                        <flux:table.cell class="font-medium">{{ $activity->title }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge :color="$activity->status->color()" size="sm">
                                {{ $activity->status->label() }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell class="text-neutral-500 text-xs">
                            {{ $activity->started_at ? $activity->started_at->format('d/m/Y H:i') : '-' }}
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex space-x-2">
                                <flux:button :href="route('admin.activities.edit', $activity)" size="sm" variant="ghost" icon="pencil-square" wire:navigate />
                                <flux:button wire:click="delete({{ $activity->id }})" 
                                             wire:confirm="¿Estás seguro de eliminar esta actividad?"
                                             size="sm" variant="ghost" color="red" icon="trash" />
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5" class="text-center py-12">
                            <flux:text>No se encontraron actividades.</flux:text>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <div class="p-4 border-t border-neutral-200 dark:border-neutral-700">
            {{ $activities->links() }}
        </div>
    </div>
</div>

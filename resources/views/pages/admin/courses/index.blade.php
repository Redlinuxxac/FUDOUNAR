<?php

use App\Enums\CourseStatus;
use App\Enums\CourseModality;
use App\Models\Course;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {
    use WithPagination;

    public $search = '';

    public function with()
    {
        return [
            'courses' => Course::query()
                ->where('title', 'like', "%{$this->search}%")
                ->latest()
                ->paginate(10),
        ];
    }

    public function delete($id)
    {
        Course::findOrFail($id)->delete();
        Flux::toast('Curso eliminado.');
    }
}; ?>

<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <flux:heading size="xl" level="1">Gestión de Cursos</flux:heading>
            <flux:subheading>Administra la oferta académica y programas de formación.</flux:subheading>
        </div>
        <flux:button :href="route('admin.courses.create')" variant="primary" icon="plus" wire:navigate>Nuevo Curso</flux:button>
    </div>

    <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700 overflow-hidden">
        <div class="p-4 border-b border-neutral-200 dark:border-neutral-700">
            <flux:input 
                wire:model.live="search" 
                placeholder="Buscar curso..." 
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
                <flux:table.column>Modalidad</flux:table.column>
                <flux:table.column>Duración</flux:table.column>
                <flux:table.column>Estado</flux:table.column>
                <flux:table.column>Acciones</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($courses as $course)
                    <flux:table.row :key="$course->id">
                        <flux:table.cell>
                            <img src="{{ $course->image }}" class="w-12 h-12 rounded-lg object-cover border border-neutral-200">
                        </flux:table.cell>
                        <flux:table.cell class="font-medium">{{ $course->title }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge :color="$course->modality->color()" size="sm" inset="top bottom">
                                {{ $course->modality->label() }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell class="text-neutral-500">{{ $course->duration }} hrs</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge :color="$course->status->color()" size="sm">
                                {{ $course->status->label() }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex space-x-2">
                                <flux:button :href="route('admin.courses.edit', $course)" size="sm" variant="ghost" icon="pencil-square" wire:navigate />
                                <flux:button wire:click="delete({{ $course->id }})" 
                                             wire:confirm="¿Estás seguro de eliminar este curso?"
                                             size="sm" variant="ghost" color="red" icon="trash" />
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" class="text-center py-12">
                            <flux:text>No se encontraron cursos registrados.</flux:text>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <div class="p-4 border-t border-neutral-200 dark:border-neutral-700">
            {{ $courses->links() }}
        </div>
    </div>
</div>

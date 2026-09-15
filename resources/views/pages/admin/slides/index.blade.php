<?php

use App\Models\Slide;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {
    use WithPagination;

    public $search = '';

    public function with()
    {
        return [
            'slides' => Slide::query()
                ->when($this->search, function ($query) {
                    $query->where(function ($q) {
                        $q->where('title', 'like', "%{$this->search}%")
                            ->orWhere('subtitle', 'like', "%{$this->search}%");
                    });
                })
                ->ordered()
                ->paginate(10),
        ];
    }

    public function toggleStatus($id)
    {
        $slide = Slide::findOrFail($id);
        $slide->is_active = ! $slide->is_active;
        $slide->save();

        Flux::toast($slide->is_active ? 'Slide activado.' : 'Slide desactivado.');
    }

    public function moveUp($id)
    {
        $slide = Slide::findOrFail($id);
        $previousSlide = Slide::where('order', '<', $slide->order)
            ->orderBy('order', 'desc')
            ->first();

        if ($previousSlide) {
            $tempOrder = $slide->order;
            $slide->order = $previousSlide->order;
            $previousSlide->order = $tempOrder;
            $slide->save();
            $previousSlide->save();
            Flux::toast('Orden actualizado.');
        }
    }

    public function moveDown($id)
    {
        $slide = Slide::findOrFail($id);
        $nextSlide = Slide::where('order', '>', $slide->order)
            ->orderBy('order', 'asc')
            ->first();

        if ($nextSlide) {
            $tempOrder = $slide->order;
            $slide->order = $nextSlide->order;
            $nextSlide->order = $tempOrder;
            $slide->save();
            $nextSlide->save();
            Flux::toast('Orden actualizado.');
        }
    }

    public function delete($id)
    {
        Slide::findOrFail($id)->delete();
        Flux::toast('Slide eliminado correctamente.');
    }
}; ?>

<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <flux:heading size="xl" level="1">Gestión del Carrusel (Slides)</flux:heading>
            <flux:subheading>Administra las imágenes y mensajes destacados de la página principal.</flux:subheading>
        </div>
        <flux:button :href="route('admin.slides.create')" variant="primary" icon="plus" wire:navigate>Nuevo Slide</flux:button>
    </div>

    <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700 overflow-hidden">
        <div class="p-4 border-b border-neutral-200 dark:border-neutral-700 flex items-center justify-between">
            <flux:input 
                wire:model.live="search" 
                placeholder="Buscar slide por título o subtítulo..." 
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
                <flux:table.column>Título y Subtítulo</flux:table.column>
                <flux:table.column>Botones</flux:table.column>
                <flux:table.column>Orden</flux:table.column>
                <flux:table.column>Estado</flux:table.column>
                <flux:table.column>Acciones</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($slides as $slide)
                    <flux:table.row :key="$slide->id">
                        <flux:table.cell>
                            <div class="w-20 h-12 rounded-lg overflow-hidden border border-neutral-200 dark:border-neutral-700 bg-neutral-900">
                                <img src="{{ $slide->image_url }}" class="w-full h-full object-cover" alt="{{ $slide->title }}">
                            </div>
                        </flux:table.cell>
                        <flux:table.cell class="max-w-xs">
                            <div class="font-medium text-gray-900 dark:text-white">{{ $slide->title }}</div>
                            @if($slide->subtitle)
                                <div class="text-xs text-neutral-500 truncate">{{ $slide->subtitle }}</div>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="space-y-1 text-xs">
                                @if($slide->button_text)
                                    <div><span class="font-semibold text-red-600">P:</span> {{ $slide->button_text }} <span class="text-neutral-400">({{ $slide->button_link }})</span></div>
                                @endif
                                @if($slide->button_secondary_text)
                                    <div><span class="font-semibold text-gray-600 dark:text-gray-300">S:</span> {{ $slide->button_secondary_text }} <span class="text-neutral-400">({{ $slide->button_secondary_link }})</span></div>
                                @endif
                                @if(!$slide->button_text && !$slide->button_secondary_text)
                                    <span class="text-neutral-400 italic">Sin botones</span>
                                @endif
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center space-x-1">
                                <span class="font-semibold text-sm w-6 text-center">{{ $slide->order }}</span>
                                <div class="flex flex-col space-y-0.5">
                                    <button wire:click="moveUp({{ $slide->id }})" title="Subir orden" class="text-neutral-400 hover:text-neutral-700 dark:hover:text-white p-0.5 cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                    </button>
                                    <button wire:click="moveDown({{ $slide->id }})" title="Bajar orden" class="text-neutral-400 hover:text-neutral-700 dark:hover:text-white p-0.5 cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <button wire:click="toggleStatus({{ $slide->id }})" class="cursor-pointer" title="Click para alternar estado">
                                <flux:badge :color="$slide->is_active ? 'emerald' : 'zinc'" size="sm">
                                    {{ $slide->is_active ? 'Activo' : 'Inactivo' }}
                                </flux:badge>
                            </button>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex space-x-2">
                                <flux:button :href="route('admin.slides.edit', $slide)" size="sm" variant="ghost" icon="pencil-square" wire:navigate title="Editar" />
                                <flux:button wire:click="delete({{ $slide->id }})" 
                                             wire:confirm="¿Estás seguro de eliminar este slide del carrusel?"
                                             size="sm" variant="ghost" color="red" icon="trash" title="Eliminar" />
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" class="text-center py-12">
                            <flux:text>No se encontraron slides configurados.</flux:text>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <div class="p-4 border-t border-neutral-200 dark:border-neutral-700">
            {{ $slides->links() }}
        </div>
    </div>
</div>

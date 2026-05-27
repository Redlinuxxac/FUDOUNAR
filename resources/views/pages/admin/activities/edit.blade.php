<?php

use App\Enums\ActivityStatus;
use App\Models\Activity;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Storage;

new #[Layout('layouts.app')] class extends Component {
    use WithFileUploads;

    public Activity $activity;

    // Form fields
    public $title = '';
    public $description = '';
    public $status = 'draft';
    public $started_at = '';
    public $image; // Temporary file
    public $currentImage;

    public function mount(Activity $activity)
    {
        $this->activity = $activity;
        $this->title = $activity->title;
        $this->description = $activity->description;
        $this->status = $activity->status->value;
        $this->started_at = $activity->started_at ? $activity->started_at->format('Y-m-d\TH:i') : '';
        $this->currentImage = $activity->image;
    }

    protected function rules()
    {
        return [
            'title' => 'required|min:3',
            'description' => 'required',
            'status' => 'required',
            'started_at' => [
                $this->status === ActivityStatus::UPCOMING->value ? 'required' : 'nullable',
                'date',
                $this->status === ActivityStatus::UPCOMING->value ? 'after:now' : '',
            ],
            'image' => 'nullable|image|max:2048',
        ];
    }

    public function save()
    {
        $this->validate();

        $imagePath = $this->currentImage;

        if ($this->image) {
            $imagePath = $this->image->store('activities', 'public');
            $imagePath = Storage::url($imagePath);
        }

        $this->activity->update([
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'started_at' => ($this->status === 'upcoming' && $this->started_at) ? $this->started_at : null,
            'image' => $imagePath,
        ]);

        Flux::toast('Actividad actualizada correctamente.');

        return redirect()->route('admin.activities');
    }
}; ?>

<div class="p-6">
    <div class="mb-6">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item :href="route('admin.activities')">Actividades</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Editar Actividad</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>

    <div class="max-w-4xl">
        <div class="mb-6">
            <flux:heading size="xl" level="1">Editar Actividad</flux:heading>
            <flux:subheading>Actualiza la información de la actividad comunitaria.</flux:subheading>
        </div>

        <form wire:submit="save" class="space-y-8">
            <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700 p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Columna Izquierda: Datos Básicos -->
                    <div class="space-y-4">
                        <flux:input wire:model="title" label="Título de la Actividad" placeholder="Ej: Jornada de Salud" />
                        
                        <flux:select wire:model.live="status" label="Estado actual">
                            @foreach (ActivityStatus::cases() as $stat)
                                <option value="{{ $stat->value }}">{{ $stat->label() }}</option>
                            @endforeach
                        </flux:select>

                        <div x-show="$wire.status === 'upcoming'" x-transition>
                            <flux:input type="datetime-local" wire:model="started_at" label="Fecha y Hora de Inicio" />
                        </div>
                    </div>

                    <!-- Columna Derecha: Imagen -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300">Imagen de Portada</label>
                        <div 
                            x-data="{ isDragging: false }"
                            @dragover.prevent="isDragging = true"
                            @dragleave.prevent="isDragging = false"
                            @drop.prevent="isDragging = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'))"
                            class="relative border-2 border-dashed rounded-2xl flex flex-col items-center justify-center p-4 transition-all min-h-[200px]"
                            :class="isDragging ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-neutral-200 dark:border-neutral-700 bg-gray-50 dark:bg-neutral-900'"
                        >
                            @if ($image)
                                <img src="{{ $image->temporaryUrl() }}" class="w-full h-40 object-cover rounded-xl shadow-sm mb-2">
                                <flux:button size="xs" variant="ghost" color="red" wire:click="$set('image', null)">Cambiar imagen</flux:button>
                            @elseif ($currentImage)
                                <img src="{{ $currentImage }}" class="w-full h-40 object-cover rounded-xl shadow-sm mb-2">
                                <p class="text-[10px] text-gray-400 italic">Imagen actual (arrastra una nueva para cambiar)</p>
                            @else
                                <div class="text-center">
                                    <flux:icon name="photo" class="size-10 text-neutral-300 mx-auto mb-2" />
                                    <p class="text-xs text-neutral-500 font-medium">Arrastra una imagen aquí</p>
                                    <p class="text-[10px] text-neutral-400 mt-1">o haz clic para seleccionar</p>
                                </div>
                            @endif

                            <input type="file" wire:model="image" x-ref="fileInput" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            
                            <div wire:loading wire:target="image" class="absolute inset-0 bg-white/80 dark:bg-neutral-800/80 flex items-center justify-center rounded-2xl">
                                <flux:icon name="arrow-path" class="size-6 animate-spin text-blue-600" />
                            </div>
                        </div>
                        @error('image') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Editor de Texto Enriquecido (Trix) -->
                <div class="mt-8 space-y-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300">Descripción Detallada</label>
                    
                    <div
                        x-data="{ description: @entangle('description') }"
                        x-init="
                            $watch('description', value => {
                                if (value !== $refs.trix.value) {
                                    $refs.trix.editor.loadHTML(value);
                                }
                            })
                        "
                        wire:ignore
                    >
                        <input id="description" type="hidden" x-model="description">
                        <trix-editor 
                            x-ref="trix"
                            input="description" 
                            x-on:trix-change="description = $event.target.value"
                            class="trix-content w-full px-4 py-3 rounded-xl border border-neutral-200 dark:border-neutral-700 focus:ring-2 focus:ring-blue-500 outline-none transition min-h-[250px] bg-white dark:bg-neutral-900 dark:text-white" 
                            placeholder="Escribe aquí los detalles de la actividad..."
                        ></trix-editor>
                    </div>
                </div>
            </div>

            <div class="flex space-x-2 justify-end">
                <flux:button :href="route('admin.activities')" variant="ghost">Cancelar</flux:button>
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled">Guardar Cambios</flux:button>
            </div>
        </form>
    </div>
</div>

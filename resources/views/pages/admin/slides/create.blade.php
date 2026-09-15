<?php

use App\Models\Slide;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Storage;

new #[Layout('layouts.app')] class extends Component {
    use WithFileUploads;

    public string $title = '';
    public string $subtitle = '';
    public $image;
    public string $image_url_custom = '';
    public string $button_text = 'Saber más';
    public string $button_link = '/quienes-somos';
    public string $button_secondary_text = 'Contáctanos';
    public string $button_secondary_link = '/contacto';
    public int $order = 0;
    public bool $is_active = true;

    public function mount(): void
    {
        $maxOrder = Slide::max('order') ?? 0;
        $this->order = $maxOrder + 1;
    }

    protected function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:1000',
            'image' => 'nullable|image|max:3072',
            'image_url_custom' => 'nullable|string|max:500',
            'button_text' => 'nullable|string|max:100',
            'button_link' => 'nullable|string|max:255',
            'button_secondary_text' => 'nullable|string|max:100',
            'button_secondary_link' => 'nullable|string|max:255',
            'order' => 'required|integer',
            'is_active' => 'boolean',
        ];
    }

    public function save()
    {
        $this->validate();

        $imagePath = null;

        if ($this->image) {
            $stored = $this->image->store('slides', 'public');
            $imagePath = Storage::url($stored);
        } elseif (! empty($this->image_url_custom)) {
            $imagePath = $this->image_url_custom;
        } else {
            $imagePath = '/slider/slide1.jpg';
        }

        Slide::create([
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'image' => $imagePath,
            'button_text' => $this->button_text ?: null,
            'button_link' => $this->button_link ?: null,
            'button_secondary_text' => $this->button_secondary_text ?: null,
            'button_secondary_link' => $this->button_secondary_link ?: null,
            'order' => $this->order,
            'is_active' => $this->is_active,
        ]);

        Flux::toast('Slide creado exitosamente.');

        return redirect()->route('admin.slides');
    }
}; ?>

<div class="p-6">
    <div class="mb-6">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item :href="route('admin.slides')" wire:navigate>Slides</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Nuevo Slide</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>

    <div class="max-w-4xl">
        <div class="mb-6">
            <flux:heading size="xl" level="1">Nuevo Slide</flux:heading>
            <flux:subheading>Agrega una diapositiva destacada para el carrusel de la página de inicio.</flux:subheading>
        </div>

        <form wire:submit="save" class="space-y-8">
            <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700 p-6 space-y-6">
                <!-- Datos Principales -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <flux:input wire:model="title" label="Título del Slide" placeholder="Ej: Uniendo Culturas" required />
                        
                        <flux:textarea wire:model="subtitle" label="Subtítulo / Mensaje" placeholder="Ej: República Dominicana y Aruba trabajando juntas." rows="3" />
                        
                        <div class="grid grid-cols-2 gap-4">
                            <flux:input type="number" wire:model="order" label="Orden de Aparición" />
                            
                            <div class="flex flex-col justify-center pt-5">
                                <flux:checkbox wire:model="is_active" label="Slide Activo (Visible)" />
                            </div>
                        </div>
                    </div>

                    <!-- Imagen -->
                    <div class="space-y-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300">Imagen de Fondo del Slide</label>
                        
                        <div 
                            x-data="{ isDragging: false }"
                            @dragover.prevent="isDragging = true"
                            @dragleave.prevent="isDragging = false"
                            @drop.prevent="isDragging = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'))"
                            class="relative border-2 border-dashed rounded-2xl flex flex-col items-center justify-center p-4 transition-all min-h-[180px] bg-neutral-50 dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700"
                        >
                            @if ($image)
                                <div class="relative w-full h-40 rounded-lg overflow-hidden group">
                                    <img src="{{ $image->temporaryUrl() }}" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <flux:text class="text-white text-xs font-semibold">Click o arrastra para cambiar</flux:text>
                                    </div>
                                </div>
                            @elseif ($image_url_custom)
                                <div class="relative w-full h-40 rounded-lg overflow-hidden group">
                                    <img src="{{ $image_url_custom }}" class="w-full h-full object-cover" onerror="this.src='/slider/slide1.jpg'">
                                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <flux:text class="text-white text-xs font-semibold">Vista previa de URL</flux:text>
                                    </div>
                                </div>
                            @else
                                <div class="text-center p-4">
                                    <flux:icon name="photo" class="mx-auto size-12 text-neutral-400 mb-2" />
                                    <flux:heading size="sm">Sube una imagen o arrástrala aquí</flux:heading>
                                    <flux:subheading class="text-xs text-neutral-500">PNG, JPG o WEBP (máx. 3MB)</flux:subheading>
                                </div>
                            @endif
                            <input x-ref="fileInput" type="file" wire:model="image" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept="image/*">
                        </div>

                        <div wire:loading wire:target="image" class="text-xs text-blue-600 font-medium">
                            Cargando vista previa de imagen...
                        </div>
                        @error('image') <span class="text-xs text-red-600">{{ $message }}</span> @enderror

                        <flux:input wire:model.live="image_url_custom" label="O ingresa una URL/ruta directa (opcional)" placeholder="Ej: /slider/slide1.jpg o https://..." />
                    </div>
                </div>

                <!-- Botones / Enlaces -->
                <div class="border-t border-neutral-200 dark:border-neutral-700 pt-6">
                    <flux:heading size="lg" class="mb-4">Botones de Acción (Llamados a la Acción)</flux:heading>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Botón Principal -->
                        <div class="space-y-3 p-4 bg-neutral-50 dark:bg-neutral-900 rounded-xl border border-neutral-200 dark:border-neutral-700">
                            <flux:heading size="sm" class="text-red-600 font-semibold">Botón Principal (Rojo)</flux:heading>
                            <flux:input wire:model="button_text" label="Texto del Botón" placeholder="Ej: Saber más" />
                            <flux:input wire:model="button_link" label="Enlace / Ruta" placeholder="Ej: /quienes-somos o https://..." />
                        </div>

                        <!-- Botón Secundario -->
                        <div class="space-y-3 p-4 bg-neutral-50 dark:bg-neutral-900 rounded-xl border border-neutral-200 dark:border-neutral-700">
                            <flux:heading size="sm" class="text-neutral-700 dark:text-neutral-300 font-semibold">Botón Secundario (Blanco)</flux:heading>
                            <flux:input wire:model="button_secondary_text" label="Texto del Botón" placeholder="Ej: Contáctanos" />
                            <flux:input wire:model="button_secondary_link" label="Enlace / Ruta" placeholder="Ej: /contacto o https://..." />
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <flux:button :href="route('admin.slides')" variant="ghost" wire:navigate>Cancelar</flux:button>
                <flux:button type="submit" variant="primary" icon="check" wire:loading.attr="disabled">Guardar Slide</flux:button>
            </div>
        </form>
    </div>
</div>

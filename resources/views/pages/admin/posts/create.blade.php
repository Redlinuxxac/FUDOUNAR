<?php

use App\Enums\PostStatus;
use App\Models\Post;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Storage;

new #[Layout('layouts.app')] class extends Component {
    use WithFileUploads;

    // Form fields
    public $title = '';
    public $content = '';
    public $status = 'draft';
    public $published_at = '';
    public $image; // Temporary file

    protected function rules()
    {
        return [
            'title' => 'required|min:3',
            'content' => 'required',
            'status' => 'required|in:' . implode(',', array_column(PostStatus::cases(), 'value')),
            'published_at' => [
                $this->status === PostStatus::UPCOMING->value ? 'required' : 'nullable',
                'date',
                $this->status === PostStatus::UPCOMING->value ? 'after:now' : '',
            ],
            'image' => 'nullable|image|max:2048',
        ];
    }

    public function save()
    {
        $this->validate();

        $imagePath = null;

        if ($this->image) {
            $imagePath = $this->image->store('blog', 'public');
            $imagePath = Storage::url($imagePath);
        }

        Post::create([
            'title' => $this->title,
            'content' => $this->content,
            'status' => $this->status,
            'published_at' => ($this->status === PostStatus::UPCOMING->value && $this->published_at) ? $this->published_at : null,
            'image' => $imagePath ?: 'https://image.pollinations.ai/prompt/blog-news-humanitarian?width=800&height=600&seed=' . rand(1, 1000),
        ]);

        Flux::toast('Artículo publicado correctamente.');

        return redirect()->route('admin.posts');
    }

    public function updatedStatus($value)
    {
        if ($value !== PostStatus::UPCOMING->value) {
            $this->published_at = '';
        }
    }
}; ?>

<div class="p-6">
    <div class="mb-6">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item :href="route('admin.posts')">Blog</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Nuevo Artículo</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>

    <div class="max-w-5xl">
        <div class="mb-6">
            <flux:heading size="xl" level="1">Nuevo Artículo</flux:heading>
            <flux:subheading>Redacta y publica una nueva noticia o artículo en el blog.</flux:subheading>
        </div>

        <form wire:submit="save" class="space-y-8">
            <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700 p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Datos -->
                    <div class="space-y-4">
                        <flux:input wire:model="title" label="Título del Artículo" />
                        <flux:select wire:model.live="status" label="Estado">
                            @foreach (PostStatus::cases() as $stat)
                                <option value="{{ $stat->value }}">{{ $stat->label() }}</option>
                            @endforeach
                        </flux:select>
                        <div x-show="$wire.status === 'upcoming'" x-transition>
                            <flux:input type="datetime-local" wire:model="published_at" label="Fecha de Publicación" />
                        </div>
                    </div>

                    <!-- Imagen -->
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
                                <img src="{{ $image->temporaryUrl() }}" class="w-full h-40 object-cover rounded-xl mb-2">
                                <flux:button size="xs" variant="ghost" color="red" wire:click="$set('image', null)">Cambiar</flux:button>
                            @else
                                <div class="text-center">
                                    <flux:icon name="photo" class="size-10 text-neutral-300 mx-auto mb-2" />
                                    <p class="text-xs text-neutral-500 font-medium text-center px-4">Arrastra o haz clic para subir portada</p>
                                </div>
                            @endif
                            <input type="file" wire:model="image" x-ref="fileInput" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            <div wire:loading wire:target="image" class="absolute inset-0 bg-white/80 dark:bg-neutral-800/80 flex items-center justify-center rounded-2xl">
                                <flux:icon name="arrow-path" class="size-6 animate-spin text-blue-600" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Editor -->
                <div class="mt-8 space-y-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300">Contenido del Artículo</label>
                    
                    <div
                        x-data="{ content: @entangle('content') }"
                        x-init="
                            $watch('content', value => {
                                if (value !== $refs.trix.value) {
                                    $refs.trix.editor.loadHTML(value);
                                }
                            })
                        "
                        wire:ignore
                    >
                        <input id="content" type="hidden" x-model="content">
                        <trix-editor 
                            x-ref="trix"
                            input="content" 
                            x-on:trix-change="content = $event.target.value"
                            class="trix-content w-full px-4 py-3 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 dark:text-white min-h-[350px]"
                        ></trix-editor>
                    </div>
                </div>
            </div>

            <div class="flex space-x-2 justify-end">
                <flux:button :href="route('admin.posts')" variant="ghost">Cancelar</flux:button>
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled">Publicar Artículo</flux:button>
            </div>
        </form>
    </div>
</div>

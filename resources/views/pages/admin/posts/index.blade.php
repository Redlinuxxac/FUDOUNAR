<?php

use App\Enums\PostStatus;
use App\Models\Post;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Gemini\Client as GeminiClient;

new #[Layout('layouts.app')] class extends Component {
    use WithPagination;

    public $search = '';
    public $isGenerating = false;
    public $aiPrompt = '';

    public function with()
    {
        return [
            'posts' => Post::query()
                ->where('title', 'like', "%{$this->search}%")
                ->latest()
                ->paginate(10),
        ];
    }

    public function generateWithAI()
    {
        $this->validate([
            'aiPrompt' => 'required|string|min:5'
        ]);

        $this->isGenerating = true;

        $apiKey = config('services.gemini.key') ?? env('LARAVEL_GEMINI_API_KEY');

        if (!$apiKey) {
            Flux::toast('Error: No se ha configurado la API Key de Gemini.', variant: 'danger');
            $this->isGenerating = false;
            return;
        }

        try {
            $client = Gemini::client($apiKey);
            
            $systemInstruction = "Basado en la siguiente solicitud del usuario, genera un artículo de blog. 
                       Responde estrictamente en formato JSON con los siguientes campos: 
                       'title' (un título impactante), 
                       'content' (el cuerpo del artículo en formato HTML básico, usando etiquetas <p>, <h3>, <ul>, <li>).\n\nSolicitud del usuario: ";
            
            $prompt = $systemInstruction . $this->aiPrompt;

            $result = $client->generativeModel('gemini-2.0-flash')->generateContent($prompt);
            $jsonResponse = $result->text();
            
            // Limpiar la respuesta de posibles bloques de código Markdown
            $jsonResponse = preg_replace('/^```json\s*|\s*```$/i', '', trim($jsonResponse));
            $data = json_decode($jsonResponse, true);

            if (!$data || !isset($data['title']) || !isset($data['content'])) {
                throw new \Exception('La respuesta de la IA no tiene el formato esperado.');
            }

            Post::create([
                'title' => $data['title'],
                'content' => $data['content'],
                'status' => PostStatus::PUBLISHED,
                'published_at' => now(),
                'image' => 'https://image.pollinations.ai/prompt/' . urlencode($data['title']) . '?width=800&height=600&seed=' . rand(1, 9999),
            ]);

            $this->aiPrompt = '';
            Flux::modal('ai-prompt-modal')->close();
            Flux::toast('Artículo generado exitosamente con IA.');
        } catch (\Exception $e) {
            Log::error('Error generating with Gemini: ' . $e->getMessage());
            Flux::toast('Error al generar el artículo: ' . $e->getMessage(), variant: 'danger');
        }

        $this->isGenerating = false;
    }

    public function delete($id)
    {
        Post::findOrFail($id)->delete();
        Flux::toast('Artículo eliminado.');
    }
}; ?>

<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <flux:heading size="xl" level="1">Gestión de Blog</flux:heading>
            <flux:subheading>Redacta, programa y publica tus noticias.</flux:subheading>
        </div>
        <div class="flex items-center gap-2">
            <flux:modal.trigger name="ai-prompt-modal">
                <flux:button icon="sparkles" variant="filled">Crear artículo con AI</flux:button>
            </flux:modal.trigger>
            <flux:button :href="route('admin.posts.create')" variant="primary" icon="plus" wire:navigate>Nuevo Artículo</flux:button>
        </div>
    </div>

    <!-- Modal para el Prompt de IA -->
    <flux:modal name="ai-prompt-modal" class="md:w-96 space-y-6">
        <div>
            <flux:heading size="lg">Generar Artículo con IA</flux:heading>
            <flux:subheading>Describe de qué quieres que trate el nuevo artículo.</flux:subheading>
        </div>

        <form wire:submit="generateWithAI" class="space-y-6">
            <flux:textarea 
                wire:model="aiPrompt" 
                label="Instrucciones para la IA" 
                placeholder="Ej: Escribe un artículo sobre nuestra reciente jornada de vacunación infantil en la comunidad..."
                rows="4"
            />

            <div class="flex space-x-2 justify-end">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancelar</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary" icon="sparkles" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="generateWithAI">Generar</span>
                    <span wire:loading wire:target="generateWithAI">Generando...</span>
                </flux:button>
            </div>
        </form>
    </flux:modal>

    <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700 overflow-hidden">
        <div class="p-4 border-b border-neutral-200 dark:border-neutral-700 flex items-center">
            <flux:input 
                wire:model.live="search" 
                placeholder="Buscar artículo..." 
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
                <flux:table.column>Publicación</flux:table.column>
                <flux:table.column>Acciones</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($posts as $post)
                    <flux:table.row :key="$post->id">
                        <flux:table.cell>
                            <img src="{{ $post->image }}" class="w-12 h-12 rounded-lg object-cover border border-neutral-200">
                        </flux:table.cell>
                        <flux:table.cell class="font-medium max-w-xs truncate">{{ $post->title }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge :color="$post->status->color()" size="sm">
                                {{ $post->status->label() }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell class="text-neutral-500 text-xs">
                            {{ $post->published_at ? $post->published_at->format('d/m/Y H:i') : '-' }}
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex space-x-2">
                                <flux:button :href="route('admin.posts.edit', $post)" size="sm" variant="ghost" icon="pencil-square" wire:navigate />
                                <flux:button wire:click="delete({{ $post->id }})" 
                                             wire:confirm="¿Estás seguro de eliminar este artículo?"
                                             size="sm" variant="ghost" color="red" icon="trash" />
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5" class="text-center py-12">
                            <flux:text>No se encontraron artículos.</flux:text>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <div class="p-4 border-t border-neutral-200 dark:border-neutral-700">
            {{ $posts->links() }}
        </div>
    </div>
</div>

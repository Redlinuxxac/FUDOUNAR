<?php

use App\Models\AboutPage;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Storage;

new #[Layout('layouts.app')] class extends Component {
    use WithFileUploads;

    public AboutPage $about;

    // Form fields
    public $mission_text = '';
    public $mission_image;
    public $vision_text = '';
    public $vision_image;
    public $values_text = '';
    public $values_image;
    public $history_text = '';
    public $team_text = '';
    public $team_image;
    public $impact_text = '';
    public $achievements_text = '';
    public $why_donate_text = '';

    // Current images
    public $current_mission_image;
    public $current_vision_image;
    public $current_values_image;
    public $current_team_image;

    public function mount()
    {
        $this->about = AboutPage::first() ?? new AboutPage();
        
        $this->mission_text = $this->about->mission_text;
        $this->current_mission_image = $this->about->mission_image;
        
        $this->vision_text = $this->about->vision_text;
        $this->current_vision_image = $this->about->vision_image;
        
        $this->values_text = $this->about->values_text;
        $this->current_values_image = $this->about->values_image;
        
        $this->history_text = $this->about->history_text;
        
        $this->team_text = $this->about->team_text;
        $this->current_team_image = $this->about->team_image;
        
        $this->impact_text = $this->about->impact_text;
        $this->achievements_text = $this->about->achievements_text;
        $this->why_donate_text = $this->about->why_donate_text;
    }

    public function save()
    {
        $this->validate([
            'mission_text' => 'required',
            'vision_text' => 'required',
            'values_text' => 'required',
            'history_text' => 'required',
            'mission_image' => 'nullable|image|max:2048',
            'vision_image' => 'nullable|image|max:2048',
            'values_image' => 'nullable|image|max:2048',
            'team_image' => 'nullable|image|max:2048',
        ]);

        $data = [
            'mission_text' => $this->mission_text,
            'vision_text' => $this->vision_text,
            'values_text' => $this->values_text,
            'history_text' => $this->history_text,
            'team_text' => $this->team_text,
            'impact_text' => $this->impact_text,
            'achievements_text' => $this->achievements_text,
            'why_donate_text' => $this->why_donate_text,
        ];

        if ($this->mission_image) {
            $path = $this->mission_image->store('about', 'public');
            $data['mission_image'] = Storage::url($path);
        }

        if ($this->vision_image) {
            $path = $this->vision_image->store('about', 'public');
            $data['vision_image'] = Storage::url($path);
        }

        if ($this->values_image) {
            $path = $this->values_image->store('about', 'public');
            $data['values_image'] = Storage::url($path);
        }

        if ($this->team_image) {
            $path = $this->team_image->store('about', 'public');
            $data['team_image'] = Storage::url($path);
        }

        $this->about->update($data);

        Flux::toast('Contenido actualizado correctamente.');
        
        $this->current_mission_image = $this->about->mission_image;
        $this->current_vision_image = $this->about->vision_image;
        $this->current_values_image = $this->about->values_image;
        $this->current_team_image = $this->about->team_image;
        $this->reset(['mission_image', 'vision_image', 'values_image', 'team_image']);
    }
}; ?>

<div class="p-6">
    <div class="mb-6">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item>Administración</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Páginas</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Quiénes Somos</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>

    <div class="max-w-6xl">
        <div class="flex justify-between items-center mb-6">
            <div>
                <flux:heading size="xl" level="1">Editar "Quiénes Somos"</flux:heading>
                <flux:subheading>Gestiona el contenido principal de la página informativa de la fundación.</flux:subheading>
            </div>
            <flux:button wire:click="save" variant="primary" icon="check" wire:loading.attr="disabled">Guardar Cambios</flux:button>
        </div>

        <form wire:submit="save" class="space-y-8">
            <!-- Sección: Misión, Visión y Valores -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Misión -->
                <div class="bg-white dark:bg-neutral-800 p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 space-y-4">
                    <flux:heading size="lg">Misión</flux:heading>
                    <flux:textarea wire:model="mission_text" label="Texto de Misión" rows="4" />
                    
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300">Imagen</label>
                        <div class="relative group">
                            @if ($mission_image)
                                <img src="{{ $mission_image->temporaryUrl() }}" class="w-full h-32 object-cover rounded-lg">
                            @elseif ($current_mission_image)
                                <img src="{{ $current_mission_image }}" class="w-full h-32 object-cover rounded-lg">
                            @else
                                <div class="w-full h-32 bg-gray-100 dark:bg-neutral-900 rounded-lg flex items-center justify-center">
                                    <flux:icon name="photo" class="size-8 text-neutral-300" />
                                </div>
                            @endif
                            <input type="file" wire:model="mission_image" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        </div>
                    </div>
                </div>

                <!-- Visión -->
                <div class="bg-white dark:bg-neutral-800 p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 space-y-4">
                    <flux:heading size="lg">Visión</flux:heading>
                    <flux:textarea wire:model="vision_text" label="Texto de Visión" rows="4" />
                    
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300">Imagen</label>
                        <div class="relative group">
                            @if ($vision_image)
                                <img src="{{ $vision_image->temporaryUrl() }}" class="w-full h-32 object-cover rounded-lg">
                            @elseif ($current_vision_image)
                                <img src="{{ $current_vision_image }}" class="w-full h-32 object-cover rounded-lg">
                            @else
                                <div class="w-full h-32 bg-gray-100 dark:bg-neutral-900 rounded-lg flex items-center justify-center">
                                    <flux:icon name="photo" class="size-8 text-neutral-300" />
                                </div>
                            @endif
                            <input type="file" wire:model="vision_image" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        </div>
                    </div>
                </div>

                <!-- Valores -->
                <div class="bg-white dark:bg-neutral-800 p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 space-y-4">
                    <flux:heading size="lg">Valores</flux:heading>
                    <flux:textarea wire:model="values_text" label="Texto de Valores" rows="4" />
                    
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300">Imagen</label>
                        <div class="relative group">
                            @if ($values_image)
                                <img src="{{ $values_image->temporaryUrl() }}" class="w-full h-32 object-cover rounded-lg">
                            @elseif ($current_values_image)
                                <img src="{{ $current_values_image }}" class="w-full h-32 object-cover rounded-lg">
                            @else
                                <div class="w-full h-32 bg-gray-100 dark:bg-neutral-900 rounded-lg flex items-center justify-center">
                                    <flux:icon name="photo" class="size-8 text-neutral-300" />
                                </div>
                            @endif
                            <input type="file" wire:model="values_image" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Historia y Equipo -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-white dark:bg-neutral-800 p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 space-y-4">
                    <flux:heading size="lg">Nuestra Historia</flux:heading>
                    <flux:textarea wire:model="history_text" label="Texto Completo" rows="8" />
                </div>

                <div class="bg-white dark:bg-neutral-800 p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 space-y-4">
                    <flux:heading size="lg">Nuestro Equipo</flux:heading>
                    <flux:textarea wire:model="team_text" label="Texto Informativo" rows="4" />
                    
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300">Imagen del Equipo</label>
                        <div class="relative group">
                            @if ($team_image)
                                <img src="{{ $team_image->temporaryUrl() }}" class="w-full h-40 object-cover rounded-lg">
                            @elseif ($current_team_image)
                                <img src="{{ $current_team_image }}" class="w-full h-40 object-cover rounded-lg">
                            @else
                                <div class="w-full h-40 bg-gray-100 dark:bg-neutral-900 rounded-lg flex items-center justify-center border-2 border-dashed border-neutral-200 dark:border-neutral-700">
                                    <flux:icon name="photo" class="size-10 text-neutral-300" />
                                </div>
                            @endif
                            <input type="file" wire:model="team_image" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Impacto, Logros y Donación (Rich Text / Lists) -->
            <div class="bg-white dark:bg-neutral-800 p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <flux:label>Áreas de Impacto (Lista HTML)</flux:label>
                        <div
                            x-data="{ impact: @entangle('impact_text') }"
                            x-init="
                                $watch('impact', value => {
                                    if (value !== $refs.trixImpact.value) {
                                        $refs.trixImpact.editor.loadHTML(value);
                                    }
                                })
                            "
                            wire:ignore
                        >
                            <input id="impact_text" type="hidden" x-model="impact">
                            <trix-editor 
                                x-ref="trixImpact"
                                input="impact_text" 
                                x-on:trix-change="impact = $event.target.value"
                                class="trix-content min-h-[150px] bg-white dark:bg-neutral-900 dark:text-white rounded-lg border border-neutral-200 dark:border-neutral-700"
                            ></trix-editor>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <flux:label>Nuestros Logros (Lista HTML)</flux:label>
                        <div
                            x-data="{ achievements: @entangle('achievements_text') }"
                            x-init="
                                $watch('achievements', value => {
                                    if (value !== $refs.trixAchievements.value) {
                                        $refs.trixAchievements.editor.loadHTML(value);
                                    }
                                })
                            "
                            wire:ignore
                        >
                            <input id="achievements_text" type="hidden" x-model="achievements">
                            <trix-editor 
                                x-ref="trixAchievements"
                                input="achievements_text" 
                                x-on:trix-change="achievements = $event.target.value"
                                class="trix-content min-h-[150px] bg-white dark:bg-neutral-900 dark:text-white rounded-lg border border-neutral-200 dark:border-neutral-700"
                            ></trix-editor>
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <flux:heading size="lg">¿Por qué Donar?</flux:heading>
                    <flux:textarea wire:model="why_donate_text" rows="3" />
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <flux:button type="submit" variant="primary" icon="check" wire:loading.attr="disabled">Guardar Cambios</flux:button>
            </div>
        </form>
    </div>
</div>

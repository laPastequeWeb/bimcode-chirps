<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Validate;

new class extends Component {

    #[Validate('required|string|max:255')]
    public string $message = ''; 

    public function store(): void
    {
        $validated = $this->validate();
        auth()->user()->posts()->create($validated);
        $this->message = '';
        $this->dispatch('post-created');
        $this->dispatch('show-toast', __('Vos impressions sont publiées !'), 'success');
    } 

}; ?>

<div class="h-[210px]">
    <form   wire:submit="store"
            x-data
            @keydown.window.ctrl.enter="$refs.binding.click()"
            @keydown.window.meta.enter="$refs.binding.click()"
            class="flex flex-col justify-end"
    > 
        <x-input-error :messages="$errors->get('message')" class="mt-2 text-xs" />
        <div class="relative">
            <textarea
                wire:model="message"
                placeholder="{{ __('Que voulez-vous dire aujourd\'hui ?') }}"
                class="block w-full min-h-40 p-3 bg-slate-800 text-white resize-none border-gray-300 focus:border-indigo-950 focus:ring focus:ring-indigo-950 focus:ring-opacity-50 rounded-md shadow-sm transition-all"
            ></textarea>
            <span class="absolute bottom-1 right-2 text-[10px] italic text-white">
                (CTRL+ENTER pour valider sans cliquer "POSTER")
            </span>
        </div>
        <x-primary-button x-ref="binding" class="w-fit mt-4 ml-auto bg-gradient-to-r from-violet-950 to-violet-800 border-none hover:saturate-150 transition-all duration-700 ease-in-out">{{ __('Poster') }}</x-primary-button>
    </form> 
</div>

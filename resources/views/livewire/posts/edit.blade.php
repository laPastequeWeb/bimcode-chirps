<?php

use App\Models\Post; 
use Livewire\Volt\Component;
use Livewire\Attributes\Validate;

new class extends Component {
    public Post $post; 
 
    #[Validate('required|string|max:255')]
    public string $message = '';
 
    public function mount(): void
    {
        $this->message = $this->post->message;
    }
 
    public function update(): void
    {
        $this->authorize('update', $this->post);
        $validated = $this->validate();
        $this->post->update($validated);
        $this->dispatch('post-updated');
        $this->dispatch('show-toast', __('Hop, une correction de faite.'), 'info');
    }
 
    public function cancel(): void
    {
        $this->dispatch('post-edit-canceled');
    } 
}; ?>

<div>
    <form wire:submit="update">
        <textarea
            wire:model="message"
            class="block w-full bg-slate-800 text-white resize-none border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
        ></textarea>
 
        <x-input-error :messages="$errors->get('message')" class="mt-2" />
        <div class="flex justify-between">
            <button class="mt-4 text-[10px] text-red-200 underline" wire:click.prevent="cancel">Cancel</button>
            <x-primary-button class="mt-4 bg-gradient-to-r from-violet-500 to-pink-500 border-none hover:saturate-150 transition-all duration-700 ease-in-out shadow-lg">{{ __('Save') }}</x-primary-button>
        </div>
    </form> 
</div>

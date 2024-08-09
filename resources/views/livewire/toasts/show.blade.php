<?php

use Livewire\Attributes\On;
use Livewire\Volt\Component;

new class extends Component {
    
    public array $messages;

    #[On('show-toast')]
    public function show($message, $type)
    {
        $this->messages[] = [
            'message' => $message,
            'type' => $type,
            'id' => uniqid()
        ];
    }

    public function delete($id)
    {
        $this->messages = array_filter($this->messages, function($message) use ($id) {
            return $message['id'] !== $id;
        });
    }
}; ?>

<div class="fixed bottom-0 left-0 p-4 space-y-4 z-50">
    @foreach($messages as $message)
        <div 
            class="p-4 rounded shadow-lg text-white 
                   @if($message['type'] === 'success') bg-green-500 
                   @elseif($message['type'] === 'error') bg-red-500 
                   @elseif($message['type'] === 'info') bg-gray-500 
                   @endif"
            x-data="{ show: true }"
            x-show="show"
            x-init="setTimeout(() => show = false, 28000)"
            wire:key="{{ $message['id'] }}"
        >
            <div class="flex justify-between items-center text-sm max-w-[280px]">
                <span>{!! $message['message'] !!}</span>
                <button class="ml-4" @click="show = false" wire:click="delete('{{ $message['id'] }}')">×</button>
            </div>
        </div>
    @endforeach
</div>

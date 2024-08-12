<?php

use App\Models\User;
use App\Models\Post;
use Livewire\Attributes\On;
use Livewire\Volt\Component;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

new class extends Component {

    public Collection $posts; 
    public ?Post $editing = null;
    public int $toLoad = 10;
 
    public function mount(): void
    {
        $this->getPosts();
    } 

    #[On('post-created')]
    public function getPosts(): void
    {
        $this->posts = Post::with('user')
            ->latest()
            ->limit($this->toLoad)
            ->get();
    }

    public function loadMore()
    {
        if(Post::count() > $this->toLoad) {
            $this->toLoad += 5;
            $this->getPosts();
        }
    }

    public function edit(Post $post): void
    {
        $this->editing = $post;
        $this->getPosts();
    }

    #[On('post-edit-canceled')]
    #[On('post-updated')] 
    public function disableEditing(): void
    {
        $this->editing = null;
        $this->getPosts();
    } 

    public function delete(Post $post): void
    {
        $this->authorize('delete', $post);
        
        if($post->delete()) {
            $this->getPosts();
            $this->dispatch('show-toast', __('Votre post à été supprimé.'), 'info');
        } else {
            $this->dispatch('show-toast', __('Aïe... votre post n\'a pas pû être supprimé...'), 'error');
            Log::error("Post {$post->id} couldn't be deleted");
        }
    }

    public function subscribe(User $targetUser): void
    {
        $user = auth()->user();

        if(!$user->subscriptions()->where('subscribed_user_id', $targetUser->id)->exists()) {
            $user->subscriptions()->attach($targetUser->id);
            $this->dispatch('show-toast', __("Vous vous êtes abonné à {$targetUser->name}"), 'success');
        } else {
            $this->dispatch('show-toast', __("Vous vous êtes déjà abonné à {$targetUser->name}"), 'error');
        }
    }

    public function unsubscribe(User $targetUser): void
    {
        $user = auth()->user();

        if($user->subscriptions()->where('subscribed_user_id', $targetUser->id)->exists()) {
            $user->subscriptions()->detach($targetUser->id);
            $this->dispatch('show-toast', __("Vous vous êtes désabonnés de {$targetUser->name}"), 'success');
        } else {
            $this->dispatch('show-toast', __("Vous ne pouvez pas vous désabonner d'un utilisateur que nous ne suivez pas."), 'error');
        }
    }

}; ?>

<div>
    <div    id="list" 
            class="h-[44vh] md:h-[calc(100vh-300px)] mt-6 bg-slate-800 shadow-sm rounded-lg divide-y divide-slate-700 overflow-y-scroll"
            style="-ms-overflow-style: none; scrollbar-width: none;"
            x-data 
            x-init="$el.addEventListener('scroll', () => {
                if ($el.scrollTop + $el.clientHeight >= $el.scrollHeight) {
                    setTimeout(() => {
                        $wire.call('loadMore');
                    }, 500);
                }
            })"
    > 
        @foreach ($posts as $post)
            <div class="p-6 flex space-x-2 relative" wire:key="{{ $post->id }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white -scale-x-100" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                
                <div class="flex-1">
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="text-white">
                                {{ $post->user->name }}
                            </span>
                            <small class="ml-2 text-[8px] text-white">{{ $post->created_at->format('j M Y, g:i a') }}</small>
                            @unless ($post->created_at->eq($post->updated_at))
                                <small class="text-white text-[8px]"> &middot; {{ __('edited') }}</small>
                            @endunless
                        </div>
                            <x-dropdown>
                                <x-slot name="trigger">
                                    <button>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                        </svg>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    @if($post->user->is(auth()->user()))
                                    <x-dropdown-link wire:click="edit({{ $post->id }})" class="cursor-pointer text-white hover:bg-slate-700 flex items-center">
                                        <svg class="h-4 w-4 mr-4 invert" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160L0 416c0 53 43 96 96 96l256 0c53 0 96-43 96-96l0-96c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 96c0 17.7-14.3 32-32 32L96 448c-17.7 0-32-14.3-32-32l0-256c0-17.7 14.3-32 32-32l96 0c17.7 0 32-14.3 32-32s-14.3-32-32-32L96 64z"/></svg>
                                        {{ __('Edit') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link wire:click="delete({{ $post->id }})" wire:confirm="Are you sure to delete this post?" class="cursor-pointer text-white hover:bg-slate-700 flex items-center"> 
                                        <svg class="h-4 w-4 mr-4 invert" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M135.2 17.7L128 32 32 32C14.3 32 0 46.3 0 64S14.3 96 32 96l384 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-96 0-7.2-14.3C307.4 6.8 296.3 0 284.2 0L163.8 0c-12.1 0-23.2 6.8-28.6 17.7zM416 128L32 128 53.2 467c1.6 25.3 22.6 45 47.9 45l245.8 0c25.3 0 46.3-19.7 47.9-45L416 128z"/></svg>
                                        {{ __('Delete') }}
                                    </x-dropdown-link>
                                    @endif
                                    @if(!$post->user->is(auth()->user()))
                                        @if(!auth()->user()->subscriptions()->where('subscribed_user_id', $post->user->id)->exists())
                                            <x-dropdown-link wire:click="subscribe({{ $post->user->id }})" class="cursor-pointer text-white hover:bg-slate-700 flex items-center"> 
                                                <svg class="h-4 w-4 mr-4 invert" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M224 0c-17.7 0-32 14.3-32 32l0 19.2C119 66 64 130.6 64 208l0 18.8c0 47-17.3 92.4-48.5 127.6l-7.4 8.3c-8.4 9.4-10.4 22.9-5.3 34.4S19.4 416 32 416l384 0c12.6 0 24-7.4 29.2-18.9s3.1-25-5.3-34.4l-7.4-8.3C401.3 319.2 384 273.9 384 226.8l0-18.8c0-77.4-55-142-128-156.8L256 32c0-17.7-14.3-32-32-32zm45.3 493.3c12-12 18.7-28.3 18.7-45.3l-64 0-64 0c0 17 6.7 33.3 18.7 45.3s28.3 18.7 45.3 18.7s33.3-6.7 45.3-18.7z"/></svg>
                                                {{ __('Subscribe') }}
                                            </x-dropdown-link>
                                        @else
                                            <x-dropdown-link wire:click="unsubscribe({{ $post->user->id }})" class="cursor-pointer text-white hover:bg-slate-700 flex items-center"> 
                                                <svg class="h-4 w-4 mr-4 invert" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M38.8 5.1C28.4-3.1 13.3-1.2 5.1 9.2S-1.2 34.7 9.2 42.9l592 464c10.4 8.2 25.5 6.3 33.7-4.1s6.3-25.5-4.1-33.7l-90.2-70.7c.2-.4 .4-.9 .6-1.3c5.2-11.5 3.1-25-5.3-34.4l-7.4-8.3C497.3 319.2 480 273.9 480 226.8l0-18.8c0-77.4-55-142-128-156.8L352 32c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 19.2c-42.6 8.6-79 34.2-102 69.3L38.8 5.1zM406.2 416L160 222.1l0 4.8c0 47-17.3 92.4-48.5 127.6l-7.4 8.3c-8.4 9.4-10.4 22.9-5.3 34.4S115.4 416 128 416l278.2 0zm-40.9 77.3c12-12 18.7-28.3 18.7-45.3l-64 0-64 0c0 17 6.7 33.3 18.7 45.3s28.3 18.7 45.3 18.7s33.3-6.7 45.3-18.7z"/></svg>
                                                {{ __('Unsubscribe') }}
                                            </x-dropdown-link>
                                        @endif
                                    @endif
                                </x-slot>
                            </x-dropdown>
                    </div>
                    @if ($post->is($editing)) 
                        <livewire:posts.edit :post="$post" :key="$post->id" />
                    @else
                        <p class="mt-4 text-lg text-white">{{ $post->message }}</p>
                    @endif 
                </div>
                @if(auth()->user()->subscriptions()->where('subscribed_user_id', $post->user->id)->exists())
                    <span class="absolute bottom-0 right-0 flex items-center justify-center h-8 w-8 p-2 rounded-full bg-blue-500 scale-[.5]">
                        <svg class="h-4 w-4 text-gray-600 invert" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"/></svg>
                    </span>
                @endif
            </div>
        @endforeach
    </div>
    <div wire:loading.class="block" wire:loading.remove.class="hidden" class="absolute bottom-2 right-2 hidden">
        <svg class="animate-spin h-10 w-10 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
    </div>
</div>

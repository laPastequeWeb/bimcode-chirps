<?php

use Livewire\Volt\Component;
use App\Services\GitService;

new class extends Component {

    public array $commits = [];

    public function mount()
    {
        $this->commits = Cache::get("commits") ??
            (new GitService("laPastequeWeb", "bimcode-chirps", "commits?sha=main"))
                ->public()
                ->getCommitsMessages();
    }
}; ?>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-white">
                <div class="flex justify-around">
                    <div class="flex-1 max-w-[50%]">
                        <h2 class="mb-6 font-semibold text-xl leading-tight">
                            {{ __("Fonctionnalités supplémentaires :") }}
                        </h2>
                        <ul class="space-y-4">
                            <li class="flex items-center space-x-3">
                                <span class="flex-shrink-0 w-6 h-6 bg-indigo-600 rounded-full flex items-center justify-center text-white text-sm font-bold">
                                    1
                                </span>
                                <span class="hover:text-indigo-400 transition duration-300">{{ __("Toasts en fonction des actions de l'utilisateur") }}</span>
                            </li>
                            <li class="flex items-center space-x-3">
                                <span class="flex-shrink-0 w-6 h-6 bg-indigo-600 rounded-full flex items-center justify-center text-white text-sm font-bold">
                                    2
                                </span>
                                <span class="hover:text-indigo-400 transition duration-300">{{ __("Possibilité de s'abonner aux publications d'autres utilisateurs et de se désabonner") }}</span>
                            </li>
                            <li class="flex items-center space-x-3">
                                <span class="flex-shrink-0 w-6 h-6 bg-indigo-600 rounded-full flex items-center justify-center text-white text-sm font-bold">
                                    3
                                </span>
                                <span class="hover:text-indigo-400 transition duration-300">{{ __("Scroll infini sur les posts") }}</span>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="flex-1 max-w-[50%]">
                        <h2 class="mb-6 font-semibold text-xl leading-tight">
                            {{ __("Dernières activités sur le projet (commits git) :") }}
                        </h2>
                        <ul class="space-y-4">
                            @forelse ($commits as $key => $commit)
                                <li class="flex items-center space-x-3">
                                    <span class="flex-shrink-0 w-6 h-6 bg-indigo-600 rounded-full flex items-center justify-center text-white text-sm font-bold">
                                        {{ $key+1 }}
                                    </span>
                                    <span class="hover:text-indigo-400 transition duration-300">
                                        {{ ucfirst($commit) }}
                                    </span>
                                </li>
                            @empty
                                <p class="italic">{{ __("Aucun commit effectué ! Ou alors ... on a atteint le quota de requête sur l'API Github !") }}</p>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

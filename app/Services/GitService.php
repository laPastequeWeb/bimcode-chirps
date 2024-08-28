<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GitService
{
    public string $baseUrl = "https://api.github.com/repos";
    public string $composedUrl;
    public string $username;
    public string $repository;
    public bool $public;
    public string $objective;

    public function __construct($username, $repository, $objective)
    {
        $this->username = $username;
        $this->repository = $repository;
        $this->objective = $objective;
        $this->composedUrl = "{$this->baseUrl}/{$username}/{$repository}/{$objective}";
    }

    public function public(): GitService
    {
        $this->public = true;
        return $this;
    }


    public function getCommitsMessages(): Array
    {
        try {
            $response = (new Client)->get($this->composedUrl);
            $commitsData = json_decode($response->getBody(), true);
            $commitMessages = array_map(function ($commit) {
                return $commit['commit']['message'];
            }, $commitsData);

            Cache::put('commits', $commitMessages, 86400);
            return $commitMessages;

        } catch (\Exception $e) {
            Log::info('Error: ' . $e->getMessage());
            return [];
        }
    }
}

<?php

namespace App\Services\Evolution;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EvolutionApiService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $defaultInstance;

    public function __construct()
    {
        $this->baseUrl = rtrim(env('EVOLUTION_API_BASE_URL'), '/');
        $this->apiKey = env('EVOLUTION_API_KEY');
        $this->defaultInstance = env('EVOLUTION_API_DEFAULT_INSTANCE');
    }

    private function getHeaders(): array
    {
        return [
            'apikey' => $this->apiKey,
            'Content-Type' => 'application/json',
        ];
    }

    public function checkConnectionState(?string $instanceName = null): array
    {
        $instance = $instanceName ?? $this->defaultInstance;
        $url = "{$this->baseUrl}/instance/connectionState/{$instance}";

        $response = Http::withHeaders($this->getHeaders())->get($url);

        return $response->json();
    }

    
    public function sendText(string $number, string $text, ?string $instanceName = null): array|bool
    {
        $instance = $instanceName ?? $this->defaultInstance;
        $url = "{$this->baseUrl}/message/sendText/{$instance}";

        try {
            $response = Http::withHeaders($this->getHeaders())->post($url, [
                'number' => $number,
                'text' => $text,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error("WhatsApp SendText Error: " . $response->body());
            return false;

        } catch (\Exception $e) {
            Log::error("WhatsApp SendText Exception: " . $e->getMessage());
            return false;
        }
    }

    
    public function checkNumberExists(string $number, ?string $instanceName = null): bool
    {
        $instance = $instanceName ?? $this->defaultInstance;
        $url = "{$this->baseUrl}/chat/whatsappNumbers/{$instance}";

        $response = Http::withHeaders($this->getHeaders())->post($url, [
            'numbers' => [$number]
        ]);

        if ($response->successful()) {
            $data = $response->json();
            return isset($data[0]['exists']) && $data[0]['exists'] === true;
        }

        return false;
    }
}
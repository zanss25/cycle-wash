<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $apiUrl;
    protected string $apiToken;

    public function __construct()
    {
        // Using Fonnte API (bisa diganti Wablas, WooWA, dll)
        $this->apiUrl = config('services.whatsapp.api_url', 'https://api.fonnte.com/send');
        $this->apiToken = config('services.whatsapp.token', '');
    }

    public function send(string $phone, string $message): bool
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->apiToken,
            ])->post($this->apiUrl, [
                'target' => $this->formatPhone($phone),
                'message' => $message,
            ]);

            if ($response->successful()) {
                Log::info("WhatsApp sent to {$phone}");
                return true;
            }

            Log::error("WhatsApp failed to {$phone}: " . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error("WhatsApp error: " . $e->getMessage());
            return false;
        }
    }

    protected function formatPhone(string $phone): string
    {
        // Convert 08xxx to 628xxx
        if (str_starts_with($phone, '0')) {
            return '62' . substr($phone, 1);
        }
        return $phone;
    }
}

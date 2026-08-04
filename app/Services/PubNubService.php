<?php

namespace App\Services;

use PubNub\PNConfiguration;
use PubNub\PubNub;
use PubNub\Exceptions\PubNubException;
use Illuminate\Support\Facades\Log;

class PubNubService
{
    private PubNub $pubnub;

    public function __construct()
    {
        $config = new PNConfiguration();
        $config->setPublishKey(config('services.pubnub.publish_key'));
        $config->setSubscribeKey(config('services.pubnub.subscribe_key'));
        $config->setSecretKey(config('services.pubnub.secret_key'));
        $config->setUuid(config('services.pubnub.uuid'));
        $config->setSecure(true);

        $this->pubnub = new PubNub($config);
    }

    public function publish(string $channel, array $data): bool
    {
        try {
            $this->pubnub
                ->publish()
                ->channel($channel)
                ->message([
                    'event'     => $data['event'] ?? 'update',
                    'payload'   => $data['payload'] ?? [],
                    'timestamp' => now()->toIso8601String(),
                ])
                ->sync();

            Log::info('📡 PubNub published', [
                'channel' => $channel,
                'event'   => $data['event'] ?? null,
            ]);

            return true;
        } catch (PubNubException $e) {
            Log::error('❌ PubNub publish failed', [
                'channel' => $channel,
                'error'   => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function broadcast(array $channels, array $data): void
    {
        foreach ($channels as $channel) {
            $this->publish($channel, $data);
        }
    }
}
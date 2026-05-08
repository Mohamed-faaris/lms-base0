<?php

namespace App\Services;

use PostHog\PostHog;

class PostHogService
{
    protected static bool $initialized = false;

    protected static function init(): void
    {
        if (self::$initialized) {
            return;
        }

        $apiKey = config('posthog.api_key');
        $host = config('posthog.host');

        if (! empty($apiKey)) {
            PostHog::init($apiKey, [
                'host' => $host ?: 'https://app.posthog.com',
            ]);
            self::$initialized = true;
        }
    }

    public static function capture(string $distinctId, string $event, array $properties = []): void
    {
        if (config('posthog.disabled')) {
            return;
        }

        self::init();

        if (! self::$initialized) {
            return;
        }

        PostHog::capture([
            'distinctId' => $distinctId,
            'event' => $event,
            'properties' => $properties,
        ]);
    }

    public static function identify(string $distinctId, array $properties = []): void
    {
        if (config('posthog.disabled')) {
            return;
        }

        self::init();

        if (! self::$initialized) {
            return;
        }

        PostHog::identify([
            'distinctId' => $distinctId,
            'properties' => $properties,
        ]);
    }
}

<?php

namespace BenCarr\Hyperlink\Concerns;

trait InteractsWithProfileData
{
    public function profile($key, $default = null)
    {
        $profile = $this->config('profile');
        if ($profile === 'custom') {
            return $this->config($key, $default);
        }

        return config("statamic.hyperlink.profiles.$profile.$key", $default);
    }

    protected function availableProfiles(): array
    {
        return collect(config('statamic.hyperlink.profiles'))
            ->put('custom', [])
            ->keys()
            ->mapWithKeys(fn($handle) => [$handle => str($handle)->title()])
            ->toArray();
    }

    protected function profileConstraints($key, $default = []): array
    {
        return collect($this->profile($key, $default))
            ->filter()
            ->filter(fn($value) => is_string($value))
            ->toArray();
    }
}

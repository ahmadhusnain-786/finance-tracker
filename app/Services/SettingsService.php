<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    private const CACHE_KEY = 'app.settings.all';

    /**
     * All settings as a key => value array.
     */
    public function all(): array
    {
        return Cache::remember(self::CACHE_KEY, 3600, function () {
            return Setting::query()
                ->get()
                ->mapWithKeys(fn ($s) => [$s->key => $this->castValue($s)])
                ->all();
        });
    }

    /**
     * Get one setting with a default fallback.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $all = $this->all();

        return $all[$key] ?? $default;
    }

    /**
     * Set (create or update) a setting.
     */
    public function set(string $key, mixed $value, string $type = 'string'): void
    {
        Setting::updateOrCreate(
            ['key' => $key],
            [
                'value' => is_array($value) ? json_encode($value) : (string) $value,
                'type'  => $type,
            ]
        );

        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Bulk-set multiple settings.
     */
    public function setMany(array $values): void
    {
        foreach ($values as $key => $value) {
            $type = is_bool($value) ? 'boolean' : (is_int($value) ? 'integer' : 'string');
            $this->set($key, $value, $type);
        }

        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Return the default schema — keys, labels, defaults, types.
     */
    public function schema(): array
    {
        return [
            [
                'key'         => 'app_name',
                'label'       => 'Application Name',
                'type'        => 'string',
                'default'     => config('app.name'),
                'help'        => 'Shown in the navbar, page titles, and emails.',
            ],
            [
                'key'         => 'default_currency',
                'label'       => 'Default Currency',
                'type'        => 'select',
                'default'     => 'PKR',
                'options'     => ['PKR', 'USD', 'EUR', 'GBP', 'AED', 'SAR', 'CAD', 'AUD'],
                'help'        => 'Used for new users who haven\'t set a currency in Settings.',
            ],
            [
                'key'         => 'default_timezone',
                'label'       => 'Default Timezone',
                'type'        => 'string',
                'default'     => 'Asia/Karachi',
                'help'        => 'PHP timezone identifier, e.g. Asia/Karachi, UTC, America/New_York.',
            ],
            [
                'key'         => 'user_registration_enabled',
                'label'       => 'User Registration Enabled',
                'type'        => 'boolean',
                'default'     => true,
                'help'        => 'When off, new users cannot register. Existing users are unaffected.',
            ],
            [
                'key'         => 'maintenance_message',
                'label'       => 'Maintenance Message',
                'type'        => 'string',
                'default'     => 'We are performing scheduled maintenance. Please check back soon.',
                'help'        => 'Shown to users during maintenance mode.',
            ],
        ];
    }

    private function castValue(Setting $setting): mixed
    {
        return match ($setting->type) {
            'boolean' => (bool) $setting->value,
            'integer' => (int) $setting->value,
            'json'    => json_decode($setting->value, true),
            default   => $setting->value,
        };
    }
}
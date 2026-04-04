<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model
{
    protected $fillable = ['gateway', 'key', 'value'];

    public static function getGateway(string $gateway): array
    {
        return static::where('gateway', $gateway)
            ->pluck('value', 'key')
            ->toArray();
    }

    public static function setGateway(string $gateway, array $data): void
    {
        foreach ($data as $key => $value) {
            static::updateOrCreate(
                ['gateway' => $gateway, 'key' => $key],
                ['value'   => $value]
            );
        }
    }

    public static function isActive(string $gateway): bool
    {
        return static::where('gateway', $gateway)
            ->where('key', 'is_active')
            ->value('value') === '1';
    }
}

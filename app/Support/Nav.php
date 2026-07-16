<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\Route;

final class Nav
{
    /**
     * @return array<int, array{route: string, label: string}>
     */
    public static function linksFor(?User $user): array
    {
        $user ??= auth()->user();

        if (! $user) {
            return self::registered(config('navigation.default'));
        }

        $role = $user->getRoleNames()->first();

        $links = is_string($role) && is_array(config("navigation.role.{$role}"))
            ? config("navigation.role.{$role}")
            : config('navigation.default');

        return self::registered($links);
    }

    /**
     * @param  array<int, array<string, string>>  $links
     * @return array<int, array{route: string, label: string}>
     */
    private static function registered(array $links): array
    {
        return array_values(array_filter(
            $links,
            fn (array $link): bool => isset($link['route'], $link['label']) && Route::has($link['route']),
        ));
    }
}

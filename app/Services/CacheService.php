<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

/**
 * Service pour gérer le cache de l'application.
 */
class CacheService
{
    /**
     * Durée du cache en minutes (configurable).
     */
    protected int $cacheDuration;

    public function __construct()
    {
        $this->cacheDuration = config('cache.ttl', 60); // 60 minutes par défaut
    }

    /**
     * Récupère les données depuis le cache ou exécute le callback.
     *
     * @return mixed
     */
    public function remember(string $key, callable $callback, ?int $duration = null)
    {
        $duration = $duration ?? $this->cacheDuration;

        return Cache::remember($key, now()->addMinutes($duration), $callback);
    }

    /**
     * Supprime une clé du cache.
     */
    public function forget(string $key): bool
    {
        return Cache::forget($key);
    }

    /**
     * Supprime toutes les clés correspondant à un pattern.
     */
    public function forgetByPattern(string $pattern): void
    {
        // Pour les patterns comme 'abonnes_*', on supprime directement les clés connues
        // Dans un environnement de production, utiliser Redis avec SCAN
        $prefix = str_replace('*', '', $pattern);

        // Supprimer les clés de pagination courantes (pages 1 à 10)
        for ($i = 1; $i <= 10; $i++) {
            Cache::forget("{$prefix}page_{$i}");
        }
    }

    /**
     * Vide tout le cache.
     */
    public function flush(): bool
    {
        return Cache::flush();
    }

    /**
     * Enregistre une clé dans la liste des clés de cache.
     */
    protected function registerKey(string $key): void
    {
        $keys = Cache::get('cache_keys', []);

        if (! in_array($key, $keys)) {
            $keys[] = $key;
            Cache::forever('cache_keys', $keys);
        }
    }
}

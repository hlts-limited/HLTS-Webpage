<?php
/**
 * Simple sliding-window rate limit stored in files under storage/.
 *
 *   if (!rate_limit('form:' . client_ip(), 5, 60)) { ...too many... }
 */

function rate_limit(string $key, int $max, int $seconds): bool
{
    $dir = STORAGE_DIR . '/ratelimit';
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }

    $file = $dir . '/' . hash('sha256', $key);
    $handle = @fopen($file, 'c+');
    if ($handle === false) {
        return true; // Fail open rather than block real visitors.
    }

    flock($handle, LOCK_EX);
    $stored = json_decode((string) stream_get_contents($handle), true);
    $cutoff = time() - $seconds;
    $hits = array_values(array_filter(is_array($stored) ? $stored : [], fn ($t) => is_int($t) && $t > $cutoff));

    $allowed = count($hits) < $max;
    if ($allowed) {
        $hits[] = time();
    }

    ftruncate($handle, 0);
    rewind($handle);
    fwrite($handle, json_encode($hits));
    flock($handle, LOCK_UN);
    fclose($handle);

    return $allowed;
}

function rate_limit_clear(string $key): void
{
    @unlink(STORAGE_DIR . '/ratelimit/' . hash('sha256', $key));
}

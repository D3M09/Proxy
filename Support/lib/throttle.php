<?php

declare(strict_types=1);

/**
 * Tiny file-backed rate limiter, used to slow down admin login brute force.
 * Keys are stored hashed so the file never leaks raw IP addresses.
 */
final class Throttle
{
    public function __construct(
        private string $file,
        private int $maxAttempts = 8,
        private int $window = 900,
    ) {
    }

    /** Seconds the caller must wait, or 0 when not blocked. */
    public function retryAfter(string $key): int
    {
        $entry = $this->read()[$this->key($key)] ?? null;
        if (!is_array($entry)) {
            return 0;
        }
        $elapsed = time() - (int) ($entry['first'] ?? 0);
        if ($elapsed > $this->window || (int) ($entry['count'] ?? 0) < $this->maxAttempts) {
            return 0;
        }
        return max(1, $this->window - $elapsed);
    }

    public function hit(string $key): void
    {
        $hashed = $this->key($key);
        $window = $this->window;
        $this->update(static function (array &$state) use ($hashed, $window): void {
            $now = time();
            $entry = $state[$hashed] ?? ['count' => 0, 'first' => $now];
            if ($now - (int) ($entry['first'] ?? $now) > $window) {
                $entry = ['count' => 0, 'first' => $now];
            }
            $entry['count'] = (int) ($entry['count'] ?? 0) + 1;
            $entry['last'] = $now;
            $state[$hashed] = $entry;
        });
    }

    public function clear(string $key): void
    {
        $hashed = $this->key($key);
        $this->update(static function (array &$state) use ($hashed): void {
            unset($state[$hashed]);
        });
    }

    // -----------------------------------------------------------------------

    private function key(string $key): string
    {
        return hash('sha256', $key);
    }

    /** @return array<string,mixed> */
    private function read(): array
    {
        if (!is_file($this->file)) {
            return [];
        }
        $raw = @file_get_contents($this->file);
        if ($raw === false || $raw === '') {
            return [];
        }
        $state = json_decode($raw, true);
        return is_array($state) ? $state : [];
    }

    /** @param callable(array<string,mixed>&):void $mutator */
    private function update(callable $mutator): void
    {
        $dir = dirname($this->file);
        if (!is_dir($dir) && !@mkdir($dir, 0700, true) && !is_dir($dir)) {
            return;
        }
        $fh = @fopen($this->file, 'c+b');
        if ($fh === false) {
            return;
        }
        try {
            if (!flock($fh, LOCK_EX)) {
                return;
            }
            $raw = (string) stream_get_contents($fh);
            $state = json_decode($raw, true);
            $state = is_array($state) ? $state : [];

            $mutator($state);
            $state = $this->prune($state);

            ftruncate($fh, 0);
            rewind($fh);
            fwrite($fh, json_encode($state, JSON_UNESCAPED_SLASHES) ?: '{}');
            fflush($fh);
        } finally {
            flock($fh, LOCK_UN);
            fclose($fh);
        }
    }

    /** Drop stale entries so the file cannot grow forever. */
    private function prune(array $state): array
    {
        $cutoff = time() - ($this->window * 2);
        $fresh = [];
        foreach ($state as $id => $entry) {
            $stamp = is_array($entry) ? (int) ($entry['last'] ?? $entry['first'] ?? 0) : 0;
            if ($stamp >= $cutoff) {
                $fresh[$id] = $entry;
            }
        }
        if (count($fresh) > 1000) {
            uasort($fresh, static fn ($a, $b): int => (int) ($b['last'] ?? 0) <=> (int) ($a['last'] ?? 0));
            $fresh = array_slice($fresh, 0, 1000, true);
        }
        return $fresh;
    }
}

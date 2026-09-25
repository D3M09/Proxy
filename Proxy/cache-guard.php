<?php
/**
 * Guards for the on-disk cache directory.
 *
 * Proxy/cache/ holds copies of upstream assets plus runtime state (per-session
 * entries, fill locks, health snapshots). None of it may be served straight off
 * disk: Proxy/index.php is the only thing allowed to hand those files out, since
 * it rewrites them on the way. Two layers enforce that - the deny rule in
 * Proxy/.htaccess and the same rule in cache/.htaccess - plus the built-in
 * server whitelist in router.php.
 *
 * The failure this file closes: cache maintenance ("purge cache" in the admin
 * panel, the sampled gcCache() on the serving path) walks the whole tree and
 * deletes what it finds. A naive walk deletes .htaccess as well, which silently
 * turns the cache into a directory of directly fetchable files - including the
 * per-session copies. Hence one rule for every walker: cache maintenance never
 * touches a dotfile, and the guard is rewritten whenever it goes missing.
 */

/**
 * Whether any component of a cache-relative path is a dotfile.
 *
 * Guard files are dotfiles (.htaccess, .gitignore) and dot-directories
 * (.well-known) hold tooling state rather than cache entries, so "starts with a
 * dot" is the whole rule. Pass paths relative to the cache directory.
 */
function cacheGuardProtectedPath(string $path): bool
{
    foreach (explode('/', str_replace('\\', '/', $path)) as $part) {
        if ($part !== '' && $part[0] === '.' && $part !== '.' && $part !== '..') {
            return true;
        }
    }
    return false;
}

/** Canonical content of the cache deny rule the web server enforces. */
function cacheGuardHtaccess(): string
{
    return <<<'HT'
# Cached upstream assets on disk. These are low-value copies of public files, but
# they must never be served directly: Proxy/index.php is the only thing allowed to
# hand them out (it applies the brand/script rewriting on the way). Requests for
# /Proxy/cache/... are already redirected away by Proxy/.htaccess and the root
# router; this is the second layer.
<IfModule mod_authz_core.c>
    Require all denied
</IfModule>
<IfModule !mod_authz_core.c>
    Order allow,deny
    Deny from all
</IfModule>

Options -Indexes
HT;
}

/** Canonical content of the ignore rule that keeps runtime cache state out of git. */
function cacheGuardGitignore(): string
{
    return <<<'GI'
# Runtime cache state: sessions, fill locks, health snapshots and freshly fetched
# upstream assets. It is per-machine, not source. Tracked files stay tracked; this
# only stops the churn from showing up as untracked noise.
*
!.gitignore
!.htaccess
GI;
}

/**
 * Make sure the cache directory exists and carries its guard files.
 *
 * A guard file that is missing or empty (an interrupted write, a deploy that
 * skips dotfiles, a purge by hand) is rewritten. Returns true when something was
 * written.
 */
function cacheGuardEnsure(string $dir): bool
{
    if (!is_dir($dir) && !@mkdir($dir, 0755, true) && !is_dir($dir)) {
        return false;
    }
    $wrote = false;
    $guards = [
        ['.htaccess', cacheGuardHtaccess(), 64],
        ['.gitignore', cacheGuardGitignore(), 32],
    ];
    foreach ($guards as [$name, $body, $minBytes]) {
        $file = $dir . '/' . $name;
        if (is_file($file) && (int) @filesize($file) >= $minBytes) {
            continue;
        }
        if (@file_put_contents($file, $body) !== false) {
            $wrote = true;
        }
    }
    return $wrote;
}

/**
 * Recursively delete cache entries, never a guard file, then let the guards be
 * rewritten if they were somehow gone. Returns the number of files removed.
 */
function cachePurgeContents(string $dir): int
{
    if (!is_dir($dir)) {
        return 0;
    }
    $removed = 0;
    $prefix = strlen(rtrim($dir, '/\\')) + 1;
    try {
        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($it as $entry) {
            $rel = substr($entry->getPathname(), $prefix);
            if (cacheGuardProtectedPath($rel)) {
                continue;
            }
            if ($entry->isDir()) {
                @rmdir($entry->getPathname()); // only succeeds when empty
            } elseif (@unlink($entry->getPathname())) {
                $removed++;
            }
        }
    } catch (Exception $e) {
        // Unreadable entry: purge what we already walked, keep serving.
    }
    cacheGuardEnsure($dir);
    return $removed;
}

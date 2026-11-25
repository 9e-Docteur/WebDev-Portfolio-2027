<?php

const STATS_BOT_PATTERN = '/bot|crawl|spider|slurp|preview|fetch|curl|wget|python|httpclient|headless|lighthouse|pingdom|monitor|facebookexternalhit|embedly|whatsapp|discord|telegram|skype/i';

function track_page_view(PDO $bdd, string $path): void
{
    try {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET' || !empty($_SESSION['admin_id'])) {
            return;
        }
        if (($_SERVER['HTTP_DNT'] ?? '') === '1' || ($_SERVER['HTTP_SEC_GPC'] ?? '') === '1') {
            return;
        }
        $purpose = strtolower((string) ($_SERVER['HTTP_SEC_PURPOSE'] ?? $_SERVER['HTTP_PURPOSE'] ?? ''));
        if (str_contains($purpose, 'prefetch')) {
            return;
        }

        $agent = (string) ($_SERVER['HTTP_USER_AGENT'] ?? '');
        if ($agent === '' || preg_match(STATS_BOT_PATTERN, $agent)) {
            return;
        }

        $stmt = $bdd->prepare('INSERT INTO page_views (path, referrer, device, browser, visitor) VALUES (:path, :referrer, :device, :browser, :visitor)');
        $stmt->execute([
            'path' => mb_substr($path, 0, 255),
            'referrer' => stats_referrer(),
            'device' => stats_device($agent),
            'browser' => stats_browser($agent),
            'visitor' => substr(hash('sha256', stats_daily_salt($bdd) . '|' . ($_SERVER['REMOTE_ADDR'] ?? '') . '|' . $agent), 0, 16),
        ]);

        if (random_int(1, 100) === 1) {
            purge_old_data($bdd);
        }
    } catch (Throwable $exception) {
        error_log('Stats error: ' . $exception->getMessage());
    }
}

function stats_daily_salt(PDO $bdd): string
{
    $today = date('Y-m-d');
    $stored = $bdd->query("SELECT value FROM settings WHERE name = 'stats_salt'")->fetchColumn();

    if ($stored && str_starts_with($stored, $today . ':')) {
        return substr($stored, 11);
    }

    $salt = bin2hex(random_bytes(16));
    $stmt = $bdd->prepare("INSERT INTO settings (name, value) VALUES ('stats_salt', :value) ON DUPLICATE KEY UPDATE value = VALUES(value)");
    $stmt->execute(['value' => $today . ':' . $salt]);

    return $salt;
}

function stats_referrer(): ?string
{
    $host = parse_url((string) ($_SERVER['HTTP_REFERER'] ?? ''), PHP_URL_HOST);
    if (!$host) {
        return null;
    }

    $host = preg_replace('/^www\./i', '', strtolower($host));
    $own = preg_replace('/^www\./i', '', strtolower(explode(':', $_SERVER['HTTP_HOST'] ?? '')[0]));

    return $host === $own ? null : mb_substr($host, 0, 255);
}

function stats_device(string $agent): string
{
    if (preg_match('/ipad|tablet|kindle|silk|(android(?!.*mobile))/i', $agent)) {
        return 'Tablette';
    }
    if (preg_match('/mobile|iphone|ipod|android|blackberry|opera mini|iemobile/i', $agent)) {
        return 'Mobile';
    }

    return 'Ordinateur';
}

function stats_browser(string $agent): string
{
    $browsers = [
        'Edge' => '/edg\//i',
        'Opera' => '/opr\/|opera/i',
        'Samsung Internet' => '/samsungbrowser/i',
        'Firefox' => '/firefox|fxios/i',
        'Chrome' => '/chrome|crios/i',
        'Safari' => '/safari/i',
    ];
    foreach ($browsers as $name => $pattern) {
        if (preg_match($pattern, $agent)) {
            return $name;
        }
    }

    return 'Autre';
}

function purge_old_data(PDO $bdd): void
{
    $bdd->exec('DELETE FROM messages WHERE created_at < (NOW() - INTERVAL 12 MONTH)');
    $bdd->exec('DELETE FROM page_views WHERE created_at < (NOW() - INTERVAL 13 MONTH)');
}

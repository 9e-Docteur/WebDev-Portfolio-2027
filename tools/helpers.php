<?php
require_once __DIR__ . '/env.php';

const CV_PATH = 'assets/cv/loris-populaire-cv.pdf';

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . csrf_token() . '">';
}

function csrf_valid(): bool
{
    return is_string($_POST['csrf'] ?? null) && hash_equals(csrf_token(), $_POST['csrf']);
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function take_flashes(): array
{
    $flashes = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);

    return $flashes;
}

function redirect(string $url): never
{
    header('Location: ' . $url);
    exit();
}

function bold_text(string $text): string
{
    return preg_replace('/\*\*(.+?)\*\*/u', '<b>$1</b>', e($text));
}

function rich_text(string $text): string
{
    $html = '';
    foreach (preg_split('/\R+/', trim($text)) as $line) {
        $html .= '<p>' . bold_text($line) . '</p>';
    }

    return $html;
}

function base_url(string $path = ''): string
{
    $base = env('APP_URL');

    if (!$base) {
        $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $dir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
        $base = ($https ? 'https' : 'http') . '://' . $host . $dir;
    }

    return rtrim($base, '/') . '/' . ltrim($path, '/');
}

function social_links(): array
{
    $networks = [
        'SOCIAL_LINKEDIN' => ['label' => 'LinkedIn', 'icon' => 'bi-linkedin'],
        'SOCIAL_GITHUB' => ['label' => 'GitHub', 'icon' => 'bi-github'],
        'SOCIAL_FACEBOOK' => ['label' => 'Facebook', 'icon' => 'bi-facebook'],
        'SOCIAL_INSTAGRAM' => ['label' => 'Instagram', 'icon' => 'bi-instagram'],
        'SOCIAL_YOUTUBE' => ['label' => 'YouTube', 'icon' => 'bi-youtube'],
    ];

    $links = [];
    foreach ($networks as $key => $network) {
        $url = trim((string) env($key, ''));
        if ($url === '') {
            continue;
        }
        if (!preg_match('#^https?://#i', $url)) {
            $url = 'https://' . $url;
        }
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            $links[] = $network + ['url' => $url];
        }
    }

    return $links;
}

function slugify(string $text): string
{
    if (function_exists('transliterator_transliterate')) {
        $text = transliterator_transliterate('Any-Latin; Latin-ASCII; Lower()', $text) ?: $text;
    } else {
        $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text) ?: $text;
    }
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);

    return trim($text, '-') ?: 'projet';
}

function project_url(array $project): string
{
    return base_url('projet/' . rawurlencode($project['slug']));
}

function excerpt(string $text, int $length = 155): string
{
    $text = trim(preg_replace('/\s+/u', ' ', str_replace('**', '', $text)));
    if (mb_strlen($text) <= $length) {
        return $text;
    }

    return rtrim(mb_substr($text, 0, $length - 1), " ,.;:") . '…';
}

function lines(?string $text): array
{
    return array_values(array_filter(array_map('trim', preg_split('/\R/', (string) $text)), 'strlen'));
}

function asset(string $path): string
{
    $file = dirname(__DIR__) . '/' . ltrim($path, '/');

    return is_file($file) ? $path . '?v=' . filemtime($file) : $path;
}

function cv_url(): ?string
{
    $file = dirname(__DIR__) . '/' . CV_PATH;

    return is_file($file) ? base_url('cv.pdf') . '?v=' . filemtime($file) : null;
}

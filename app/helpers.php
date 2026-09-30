<?php
declare(strict_types=1);

/* ---------- Échappement & URLs ---------- */

function e(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function base_url(): string
{
    if (SITE_URL !== '') {
        return rtrim((string) SITE_URL, '/');
    }
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    return ($https ? 'https' : 'http') . '://' . $host;
}

function url(string $path = ''): string
{
    return '/' . ltrim($path, '/');
}

function abs_url(string $path = ''): string
{
    return base_url() . url($path);
}

function asset(string $path): string
{
    $file = ROOT . '/assets/' . ltrim($path, '/');
    $v = is_file($file) ? substr(base_convert((string) filemtime($file), 10, 36), -6) : '0';
    return '/assets/' . ltrim($path, '/') . '?v=' . $v;
}

function redirect(string $to, int $code = 303): void
{
    header('Location: ' . $to, true, $code);
    exit;
}

function current_path(): string
{
    return '/' . trim((string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');
}

function is_active(string $prefix): bool
{
    $p = current_path();
    return $prefix === '/' ? $p === '/' : ($p === $prefix || strncmp($p, $prefix . '/', strlen($prefix) + 1) === 0);
}

function is_static(): bool
{
    return filter_var(STATIC_BUILD, FILTER_VALIDATE_BOOL);
}

/* ---------- Requêtes ---------- */

function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function post(string $key, string $default = ''): string
{
    $v = $_POST[$key] ?? $default;
    return is_string($v) ? trim($v) : $default;
}

function get(string $key, string $default = ''): string
{
    $v = $_GET[$key] ?? $default;
    return is_string($v) ? trim($v) : $default;
}

function client_ip(): string
{
    return substr((string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'), 0, 64);
}

function json_out($data, int $code = 200): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/* ---------- Sécurité ---------- */

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(24));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function csrf_check(?string $token = null): bool
{
    $token ??= (string) ($_POST['_csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    return $token !== '' && hash_equals(csrf_token(), $token);
}

function require_csrf(): void
{
    if (!csrf_check()) {
        http_response_code(403);
        exit('Session expirée : rechargez la page puis réessayez.');
    }
}

/* ---------- Messages flash ---------- */

function flash(string $type, string $msg): void
{
    $_SESSION['flash'][] = ['type' => $type, 'msg' => $msg];
}

function flashes(): array
{
    $f = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $f;
}

/* ---------- Texte ---------- */

function slugify(string $s): string
{
    $s = strtr($s, ['à' => 'a', 'â' => 'a', 'ä' => 'a', 'á' => 'a', 'ç' => 'c', 'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
        'î' => 'i', 'ï' => 'i', 'í' => 'i', 'ô' => 'o', 'ö' => 'o', 'ó' => 'o', 'ù' => 'u', 'û' => 'u', 'ü' => 'u', 'ú' => 'u',
        'ÿ' => 'y', 'œ' => 'oe', 'æ' => 'ae', 'À' => 'a', 'É' => 'e', 'È' => 'e', 'Ç' => 'c']);
    $s = strtolower($s);
    $s = preg_replace('/[^a-z0-9]+/', '-', $s) ?? '';
    return trim($s, '-') ?: 'item';
}

function excerpt(string $s, int $len = 160): string
{
    $s = trim(preg_replace('/\s+/u', ' ', strip_tags($s)) ?? '');
    if (mb_strlen($s) <= $len) {
        return $s;
    }
    $cut = mb_substr($s, 0, $len);
    $sp = mb_strrpos($cut, ' ');
    return rtrim(mb_substr($cut, 0, $sp ?: $len), " ,;:.") . '…';
}

function valid_email(string $s): bool
{
    return (bool) filter_var($s, FILTER_VALIDATE_EMAIL) && mb_strlen($s) <= 190;
}

function valid_phone(string $s): bool
{
    return (bool) preg_match('/^\+?[0-9 ().-]{8,20}$/', $s);
}

/* ---------- Argent ---------- */

function money_factor(): int
{
    return 10 ** (int) CURRENCY_DECIMALS;
}

function money(int $minor, bool $withCurrency = true): string
{
    $f = money_factor();
    $dec = $minor % $f === 0 ? 0 : (int) CURRENCY_DECIMALS;
    $s = number_format($minor / $f, $dec, ',', "\u{202F}");
    return $withCurrency ? $s . "\u{00A0}" . CURRENCY : $s;
}

function parse_money(string $s): ?int
{
    $s = str_replace(["\u{202F}", "\u{00A0}", ' ', CURRENCY], '', $s);
    $s = str_replace(',', '.', $s);
    if ($s === '' || !is_numeric($s) || (float) $s < 0) {
        return null;
    }
    return (int) round((float) $s * money_factor());
}

/* ---------- Contact ---------- */

function whatsapp_link(string $text = ''): string
{
    return 'https://wa.me/' . preg_replace('/\D+/', '', (string) SITE_WHATSAPP) . ($text !== '' ? '?text=' . rawurlencode($text) : '');
}

function fmt_date(string $dt, bool $withTime = false): string
{
    $mois = ['janv.', 'févr.', 'mars', 'avr.', 'mai', 'juin', 'juil.', 'août', 'sept.', 'oct.', 'nov.', 'déc.'];
    $t = strtotime($dt) ?: time();
    $s = date('j', $t) . ' ' . $mois[(int) date('n', $t) - 1] . ' ' . date('Y', $t);
    return $withTime ? $s . ' · ' . date('H:i', $t) : $s;
}

/* ---------- Métadonnées de page ---------- */

function page_meta(?array $m = null): array
{
    static $meta = [];
    if ($m !== null) {
        $meta = array_merge($meta, $m);
    }
    return $meta;
}

/* ---------- Icônes SVG (traits 1.6px, 24×24) ---------- */

function icon(string $name, string $class = ''): string
{
    static $paths = [
        'arrow-right'  => '<path d="M5 12h14M13 6l6 6-6 6"/>',
        'arrow-up-right' => '<path d="M7 17 17 7M8 7h9v9"/>',
        'arrow-left'   => '<path d="M19 12H5M11 18l-6-6 6-6"/>',
        'bag'          => '<path d="M6 7h12l1 13H5L6 7Z"/><path d="M9 7a3 3 0 0 1 6 0"/>',
        'close'        => '<path d="M6 6l12 12M18 6 6 18"/>',
        'menu'         => '<path d="M4 8h16M4 16h16"/>',
        'check'        => '<path d="m5 12.5 4.5 4.5L19 7"/>',
        'plus'         => '<path d="M12 5v14M5 12h14"/>',
        'minus'        => '<path d="M5 12h14"/>',
        'trash'        => '<path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3"/>',
        'search'       => '<circle cx="11" cy="11" r="6.5"/><path d="m20 20-4.2-4.2"/>',
        'mail'         => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3.5 6.5 8.5 6.5 8.5-6.5"/>',
        'phone'        => '<path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2Z"/>',
        'whatsapp'     => '<path d="M4 20l1.3-3.9A8.5 8.5 0 1 1 8.2 19L4 20Z"/><path d="M9 8.6c0 3 2.4 5.9 5.6 6.4l1.2-1.5-2-1-1 .9c-1-.5-1.9-1.4-2.4-2.4l.9-1-1-2L9 8.6Z"/>',
        'pin'          => '<path d="M12 21s-7-6.2-7-11.5a7 7 0 0 1 14 0C19 14.8 12 21 12 21Z"/><circle cx="12" cy="9.5" r="2.5"/>',
        'clock'        => '<circle cx="12" cy="12" r="8.5"/><path d="M12 7.5V12l3 2"/>',
        'shield'       => '<path d="M12 3 5 6v5c0 4.5 3 8.3 7 10 4-1.7 7-5.5 7-10V6l-7-3Z"/><path d="m9 12 2 2 4-4"/>',
        'zap'          => '<path d="M13 2 4 14h7l-1 8 9-12h-7l1-8Z"/>',
        'sparkles'     => '<path d="M12 3l1.8 4.7L18.5 9.5l-4.7 1.8L12 16l-1.8-4.7L5.5 9.5l4.7-1.8L12 3Z"/><path d="M19 15l.8 2.2L22 18l-2.2.8L19 21l-.8-2.2L16 18l2.2-.8L19 15Z"/>',
        'globe'        => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.6 3.8 5.6 3.8 9s-1.3 6.4-3.8 9c-2.5-2.6-3.8-5.6-3.8-9S9.5 5.6 12 3Z"/>',
        'monitor'      => '<rect x="3" y="4" width="18" height="12" rx="2"/><path d="M8 20h8M12 16v4"/>',
        'pen'          => '<path d="M12 19 19 12l-3-3-7 7-1 4 4-1Z"/><path d="m16 9 2-2a2 2 0 0 0-3-3l-2 2"/><path d="M3 21h6"/>',
        'palette'      => '<path d="M12 3a9 9 0 1 0 0 18c1.1 0 1.8-.9 1.5-1.9-.4-1.2.4-2.1 1.6-2.1H17a4 4 0 0 0 4-4c0-5.5-4-10-9-10Z"/><circle cx="7.5" cy="11" r="1.2"/><circle cx="10" cy="7" r="1.2"/><circle cx="15" cy="7.5" r="1.2"/>',
        'megaphone'    => '<path d="M4 10v4h3l8 4V6L7 10H4Z"/><path d="M18 9a4 4 0 0 1 0 6M7 14l1.5 5h2.5l-1-4.5"/>',
        'trophy'       => '<path d="M8 4h8v5a4 4 0 0 1-8 0V4Z"/><path d="M8 6H5a3 3 0 0 0 3 4M16 6h3a3 3 0 0 1-3 4M12 13v4M8.5 20h7M9.5 17h5"/>',
        'layers'       => '<path d="m12 3 9 5-9 5-9-5 9-5Z"/><path d="m3 13 9 5 9-5"/>',
        'cpu'          => '<rect x="6" y="6" width="12" height="12" rx="2"/><rect x="9.5" y="9.5" width="5" height="5"/><path d="M9 2v4M15 2v4M9 18v4M15 18v4M2 9h4M2 15h4M18 9h4M18 15h4"/>',
        'play'         => '<circle cx="12" cy="12" r="9"/><path d="m10 8.5 5 3.5-5 3.5v-7Z"/>',
        'briefcase'    => '<rect x="3" y="7" width="18" height="13" rx="2"/><path d="M9 7V5h6v2M3 12h18"/>',
        'star'         => '<path d="m12 3.5 2.6 5.3 5.9.9-4.3 4.1 1 5.8L12 16.9l-5.2 2.7 1-5.8-4.3-4.1 5.9-.9L12 3.5Z"/>',
        'lock'         => '<rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V8a4 4 0 0 1 8 0v3"/>',
        'chevron-down' => '<path d="m6 9 6 6 6-6"/>',
        'chevron-right'=> '<path d="m9 6 6 6-6 6"/>',
        'user'         => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
        'eye'          => '<path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/>',
        'gift'         => '<rect x="3" y="8" width="18" height="4" rx="1"/><path d="M5 12v9h14v-9M12 8v13M12 8S10.5 3 8 4s0 4 4 4Zm0 0s1.5-5 4-4 0 4-4 4Z"/>',
        'download'     => '<path d="M12 4v11M7 10l5 5 5-5M5 20h14"/>',
        'chart'        => '<path d="M4 20V4M4 20h16"/><path d="m7 15 4-4 3 3 5-6"/>',
        'target'       => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1.2"/>',
        'compass'      => '<circle cx="12" cy="12" r="9"/><path d="m15.5 8.5-2 5-5 2 2-5 5-2Z"/>',
        'rocket'       => '<path d="M5 15c-1.5 1.5-2 5-2 5s3.5-.5 5-2M9 15l-3-3c1.5-4 5-9 12-9 0 7-5 10.5-9 12Z"/><circle cx="14.5" cy="9.5" r="1.5"/>',
        'headset'      => '<path d="M4 14v-2a8 8 0 0 1 16 0v2"/><rect x="3" y="14" width="4" height="6" rx="1.5"/><rect x="17" y="14" width="4" height="6" rx="1.5"/><path d="M19 20a3 3 0 0 1-3 2h-3"/>',
        'refresh'      => '<path d="M20 11a8 8 0 0 0-14.6-4.5L4 8M4 4v4h4M4 13a8 8 0 0 0 14.6 4.5L20 16M20 20v-4h-4"/>',
        'logout'       => '<path d="M15 4h4v16h-4M10 8l-4 4 4 4M6 12h10"/>',
        'grid'         => '<rect x="4" y="4" width="7" height="7" rx="1.5"/><rect x="13" y="4" width="7" height="7" rx="1.5"/><rect x="4" y="13" width="7" height="7" rx="1.5"/><rect x="13" y="13" width="7" height="7" rx="1.5"/>',
        'box'          => '<path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Z"/><path d="m4 7.5 8 4.5 8-4.5M12 12v9"/>',
        'inbox'        => '<path d="M3 13h5l1.5 3h5L16 13h5"/><path d="M5 5h14l2 8v6H3v-6l2-8Z"/>',
        'users'        => '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0"/><path d="M16 4.5a3.5 3.5 0 0 1 0 7M18 14a6.5 6.5 0 0 1 3.5 6"/>',
        'edit'         => '<path d="M4 20h4L19 9l-4-4L4 16v4Z"/><path d="m13.5 6.5 4 4"/>',
        'external'     => '<path d="M14 4h6v6M20 4l-9 9M18 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h5"/>',
        'copy'         => '<rect x="8" y="8" width="12" height="12" rx="2"/><path d="M16 8V5a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h3"/>',
        'instagram'    => '<rect x="3.5" y="3.5" width="17" height="17" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.2" cy="6.8" r=".9" fill="currentColor"/>',
        'facebook'     => '<path d="M14 21v-8h3l.5-3.5H14V7.7c0-1 .4-1.7 1.8-1.7h1.8V3a22 22 0 0 0-2.7-.2C12.3 2.8 10.5 4.4 10.5 7.3v2.2h-3V13h3v8"/>',
        'linkedin'     => '<rect x="3.5" y="3.5" width="17" height="17" rx="3"/><path d="M8 10.5V17M8 7.5v.01M11.5 17v-6.5M11.5 13.5c0-2 1.2-3 2.6-3s2.4.9 2.4 3V17"/>',
        'tiktok'       => '<path d="M14 3v11.5a3.5 3.5 0 1 1-3.5-3.5"/><path d="M14 3c.4 2.6 2.2 4.4 5 4.6"/>',
    ];
    $p = $paths[$name] ?? $paths['sparkles'];
    return '<svg class="ico ' . e($class) . '" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $p . '</svg>';
}

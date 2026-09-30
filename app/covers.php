<?php
/**
 * Visuels vectoriels des réalisations (générés à la volée, sans image externe).
 * Remplacez-les par vos vraies photos de projets quand elles sont prêtes :
 * déposez assets/img/work/{slug}.webp et elles seront utilisées automatiquement.
 */
declare(strict_types=1);

function cover_mark(string $mark, string $fill, string $stroke): string
{
    switch ($mark) {
        case 'leaf':
            return '<path d="M50 6C80 26 84 66 50 94C16 66 20 26 50 6Z" fill="' . $fill . '"/><path d="M50 18V84M50 40l-12-9M50 56l13-10M50 70l-10-7" stroke="' . $stroke . '" stroke-width="2.2" fill="none" stroke-linecap="round"/>';
        case 'wave':
            return '<circle cx="50" cy="50" r="42" fill="none" stroke="' . $fill . '" stroke-width="5"/><path d="M16 58c8-8 16-8 24 0s16 8 24 0 14-8 22 0M20 72c7-6 14-6 21 0s14 6 21 0 12-6 19 0" stroke="' . $fill . '" stroke-width="5" fill="none" stroke-linecap="round"/><circle cx="62" cy="34" r="9" fill="' . $fill . '"/>';
        case 'bolt':
            return '<path d="M58 4 20 56h28l-8 40 42-56H54L58 4Z" fill="' . $fill . '"/>';
        case 'gem':
            return '<path d="M18 38 34 14h32l16 24-32 50L18 38Z" fill="' . $fill . '"/><path d="M18 38h64M34 14l16 24 16-24M50 38v50" stroke="' . $stroke . '" stroke-width="2" fill="none" stroke-linejoin="round"/>';
        case 'sun':
            return '<circle cx="50" cy="50" r="20" fill="' . $fill . '"/><g stroke="' . $fill . '" stroke-width="5" stroke-linecap="round"><path d="M50 8v12M50 80v12M8 50h12M80 50h12M20 20l8 8M72 72l8 8M80 20l-8 8M28 72l-8 8"/></g>';
        case 'star':
            return '<path d="M50 2C54 38 62 46 98 50 62 54 54 62 50 98 46 62 38 54 2 50 38 46 46 38 50 2Z" fill="' . $fill . '"/>';
        case 'k':
        default:
            return '<rect x="6" y="6" width="88" height="88" rx="22" fill="none" stroke="' . $fill . '" stroke-width="5"/><path d="M34 24v52M34 52l30-28M44 44l22 32" stroke="' . $fill . '" stroke-width="8" fill="none" stroke-linecap="round" stroke-linejoin="round"/>';
    }
}

function project_cover(string $slug, array $p): string
{
    static $n = 0;
    $n++;
    $id = 'cv' . $n;
    $photo = ROOT . '/assets/img/work/' . $slug . '.webp';
    if (is_file($photo)) {
        return '<img src="' . e(asset('img/work/' . $slug . '.webp')) . '" alt="' . e($p['title']) . '" loading="lazy" decoding="async">';
    }
    [$bg, $ac, $li] = $p['palette'];
    $title = e($p['title']);
    $upper = e(mb_strtoupper($p['title']));
    $year = e($p['year']);
    $label = e(project_categories()[$p['cat']] ?? '');
    $mark = cover_mark($p['mark'], $ac, $bg);
    $markLi = cover_mark($p['mark'], $li, $bg);

    $defs = <<<SVG
<defs>
  <radialGradient id="{$id}g" cx="50%" cy="38%" r="75%"><stop offset="0" stop-color="{$ac}" stop-opacity=".28"/><stop offset=".55" stop-color="{$bg}" stop-opacity="0"/></radialGradient>
  <linearGradient id="{$id}b" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="{$ac}" stop-opacity=".55"/><stop offset="1" stop-color="{$ac}" stop-opacity="0"/></linearGradient>
  <linearGradient id="{$id}d" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#fff" stop-opacity=".10"/><stop offset="1" stop-color="#fff" stop-opacity="0"/></linearGradient>
</defs>
SVG;

    $fsWeb = mb_strlen($p['title']) > 12 ? 30 : 40;
    switch ($p['style']) {
        case 'web':
            $body = <<<SVG
<rect width="800" height="600" fill="{$bg}"/><rect width="800" height="600" fill="url(#{$id}g)"/>
<g transform="translate(90 92)">
  <rect width="560" height="380" rx="16" fill="{$li}" opacity=".96"/>
  <rect width="560" height="34" rx="16" fill="{$bg}" opacity=".12"/>
  <circle cx="22" cy="17" r="5" fill="{$ac}"/><circle cx="40" cy="17" r="5" fill="{$bg}" opacity=".3"/><circle cx="58" cy="17" r="5" fill="{$bg}" opacity=".3"/>
  <rect x="200" y="11" width="170" height="12" rx="6" fill="{$bg}" opacity=".12"/>
  <rect x="0" y="34" width="560" height="200" fill="{$bg}"/>
  <rect x="0" y="34" width="560" height="200" fill="url(#{$id}g)"/>
  <g transform="translate(410 70) scale(1.25)">{$mark}</g>
  <text x="36" y="118" font-family="Cinzel, serif" font-size="{$fsWeb}" font-weight="700" fill="{$li}">{$title}</text>
  <rect x="36" y="138" width="230" height="9" rx="4.5" fill="{$li}" opacity=".45"/><rect x="36" y="156" width="170" height="9" rx="4.5" fill="{$li}" opacity=".3"/>
  <rect x="36" y="182" width="120" height="30" rx="15" fill="{$ac}"/>
  <g fill="{$bg}" opacity=".9">
    <rect x="36" y="256" width="150" height="96" rx="10" opacity=".1"/><rect x="205" y="256" width="150" height="96" rx="10" opacity=".1"/><rect x="374" y="256" width="150" height="96" rx="10" opacity=".1"/>
  </g>
  <g fill="{$ac}"><rect x="50" y="272" width="40" height="40" rx="8"/><rect x="219" y="272" width="40" height="40" rx="8" opacity=".7"/><rect x="388" y="272" width="40" height="40" rx="8" opacity=".45"/></g>
  <g fill="{$bg}" opacity=".25"><rect x="50" y="324" width="100" height="7" rx="3.5"/><rect x="219" y="324" width="100" height="7" rx="3.5"/><rect x="388" y="324" width="100" height="7" rx="3.5"/></g>
</g>
<g transform="translate(560 250) rotate(6)">
  <rect width="170" height="320" rx="26" fill="#0b0d12"/><rect x="8" y="8" width="154" height="304" rx="20" fill="{$bg}"/>
  <rect x="8" y="8" width="154" height="304" rx="20" fill="url(#{$id}g)"/>
  <g transform="translate(55 40) scale(.6)">{$mark}</g>
  <text x="85" y="140" text-anchor="middle" font-family="Cinzel, serif" font-size="17" font-weight="700" fill="{$li}">{$title}</text>
  <rect x="35" y="156" width="100" height="6" rx="3" fill="{$li}" opacity=".4"/>
  <rect x="45" y="176" width="80" height="22" rx="11" fill="{$ac}"/>
  <rect x="22" y="220" width="126" height="74" rx="10" fill="{$li}" opacity=".12"/>
</g>
<rect width="800" height="600" fill="url(#{$id}d)"/>
SVG;
            break;

        case 'event':
            $body = <<<SVG
<rect width="800" height="600" fill="{$bg}"/>
<g opacity=".9"><path d="M140 0 60 600h180L200 0Z" fill="url(#{$id}b)"/><path d="M400 0 300 600h200L430 0Z" fill="url(#{$id}b)" opacity=".7"/><path d="M660 0 560 600h180L700 0Z" fill="url(#{$id}b)"/></g>
<rect width="800" height="600" fill="url(#{$id}g)"/>
<circle cx="400" cy="280" r="150" fill="none" stroke="{$ac}" stroke-width="2" opacity=".6"/>
<circle cx="400" cy="280" r="128" fill="none" stroke="{$li}" stroke-width="1" stroke-dasharray="3 7" opacity=".6"/>
<g transform="translate(360 170) scale(.8)">{$mark}</g>
<text x="400" y="300" text-anchor="middle" font-family="Cinzel, serif" font-size="38" font-weight="700" fill="{$li}" letter-spacing="2">{$upper}</text>
<text x="400" y="340" text-anchor="middle" font-family="Sora, sans-serif" font-size="20" font-weight="600" fill="{$ac}" letter-spacing="10">{$year}</text>
<g fill="{$ac}"><circle cx="120" cy="120" r="4"/><circle cx="690" cy="90" r="3"/><circle cx="640" cy="460" r="5"/><circle cx="170" cy="470" r="3"/><circle cx="560" cy="170" r="2.5"/><circle cx="250" cy="200" r="2"/></g>
<rect x="0" y="520" width="800" height="44" fill="{$ac}"/>
<text x="400" y="549" text-anchor="middle" font-family="Sora, sans-serif" font-size="15" font-weight="700" fill="{$bg}" letter-spacing="6">PARTENAIRE OFFICIEL · SPONSOR · ACTIVATION · PARTENAIRE OFFICIEL</text>
SVG;
            break;

        case 'social':
            $tiles = '';
            $words = ['NO', 'LIMITS', $upper, 'JOIN', 'US', 'NOW', 'MOVE', 'FAST', '2025'];
            for ($i = 0; $i < 9; $i++) {
                $x = 250 + ($i % 3) * 102;
                $y = 90 + intdiv($i, 3) * 102;
                $f = [$ac, $li, $bg][$i % 3];
                $t = [$bg, $bg, $ac][$i % 3];
                $w = e($words[$i]);
                $fs = mb_strlen($words[$i]) > 6 ? 12 : 20;
                $tiles .= "<rect x=\"$x\" y=\"$y\" width=\"96\" height=\"96\" rx=\"8\" fill=\"$f\"" . ($f === $bg ? ' stroke="' . $ac . '" stroke-opacity=".4"' : '') . "/><text x=\"" . ($x + 48) . "\" y=\"" . ($y + 55) . "\" text-anchor=\"middle\" font-family=\"Sora, sans-serif\" font-weight=\"800\" font-size=\"$fs\" fill=\"$t\">$w</text>";
            }
            $body = <<<SVG
<rect width="800" height="600" fill="{$bg}"/><rect width="800" height="600" fill="url(#{$id}g)"/>
<g transform="translate(230 50)"><rect width="346" height="420" rx="30" fill="#000" opacity=".55"/></g>
<g transform="translate(-10 0)">{$tiles}</g>
<g transform="translate(560 380)"><rect width="190" height="84" rx="18" fill="{$li}"/><text x="22" y="40" font-family="Sora, sans-serif" font-weight="800" font-size="26" fill="{$bg}">4,2 M</text><text x="22" y="64" font-family="Sora, sans-serif" font-size="13" fill="{$bg}" opacity=".7">vues sur les Reels</text></g>
<g transform="translate(60 110)"><rect width="150" height="64" rx="16" fill="{$ac}"/><text x="20" y="40" font-family="Sora, sans-serif" font-weight="800" font-size="20" fill="{$bg}">×5 ROI</text></g>
<text x="400" y="545" text-anchor="middle" font-family="Cinzel, serif" font-size="30" font-weight="700" fill="{$li}">{$title}</text>
SVG;
            break;

        case 'brand':
        default:
            $body = <<<SVG
<rect width="800" height="600" fill="{$bg}"/><rect width="800" height="600" fill="url(#{$id}g)"/>
<g stroke="{$ac}" stroke-opacity=".16" fill="none">
  <circle cx="400" cy="240" r="130"/><circle cx="400" cy="240" r="86"/><path d="M0 240h800M400 0v600M270 110l260 260M530 110 270 370"/>
</g>
<g transform="translate(335 170) scale(1.3)">{$mark}</g>
<text x="400" y="420" text-anchor="middle" font-family="Cinzel, serif" font-size="54" font-weight="700" fill="{$li}" letter-spacing="3">{$title}</text>
<text x="400" y="452" text-anchor="middle" font-family="Sora, sans-serif" font-size="13" fill="{$ac}" letter-spacing="7">IDENTITÉ · {$year}</text>
<g transform="translate(300 500)" font-family="Sora, sans-serif" font-size="10" fill="{$li}" fill-opacity=".6">
  <circle cx="20" cy="20" r="18" fill="{$bg}" stroke="{$li}" stroke-opacity=".4"/><circle cx="100" cy="20" r="18" fill="{$ac}"/><circle cx="180" cy="20" r="18" fill="{$li}"/>
</g>
<g transform="translate(60 60) scale(.34)" opacity=".5">{$markLi}</g>
SVG;
    }

    return '<svg viewBox="0 0 800 600" role="img" aria-label="' . $title . ' — ' . $label . '" preserveAspectRatio="xMidYMid slice" xmlns="http://www.w3.org/2000/svg">' . $defs . $body . '</svg>';
}

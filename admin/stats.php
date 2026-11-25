<?php
require_once __DIR__ . '/tools/secu.php';

$pageTitle = 'Statistiques';
$currentPage = 'stats';

$periods = [7 => '7 jours', 30 => '30 jours', 90 => '90 jours', 365 => '12 mois'];
$days = (int) ($_GET['days'] ?? 30);
if (!isset($periods[$days])) {
    $days = 30;
}

$since = (new DateTimeImmutable('today'))->modify('-' . ($days - 1) . ' days');
$params = ['since' => $since->format('Y-m-d 00:00:00')];

function stats_query(PDO $bdd, string $sql, array $params): array
{
    $stmt = $bdd->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll();
}

$totals = stats_query($bdd, 'SELECT COUNT(*) AS views, COUNT(DISTINCT visitor, DATE(created_at)) AS visitors FROM page_views WHERE created_at >= :since', $params)[0];
$today = stats_query($bdd, 'SELECT COUNT(*) AS views FROM page_views WHERE created_at >= CURDATE()', [])[0]['views'];

$perDay = [];
foreach (stats_query($bdd, 'SELECT DATE(created_at) AS day, COUNT(*) AS views, COUNT(DISTINCT visitor) AS visitors FROM page_views WHERE created_at >= :since GROUP BY DATE(created_at)', $params) as $row) {
    $perDay[$row['day']] = $row;
}

$series = [];
$step = $days > 90 ? 'month' : 'day';
if ($step === 'day') {
    for ($date = $since; $date <= new DateTimeImmutable('today'); $date = $date->modify('+1 day')) {
        $key = $date->format('Y-m-d');
        $series[] = ['label' => $date->format('d/m'), 'long' => $date->format('d/m/Y'), 'views' => (int) ($perDay[$key]['views'] ?? 0), 'visitors' => (int) ($perDay[$key]['visitors'] ?? 0)];
    }
} else {
    $months = [];
    foreach ($perDay as $day => $row) {
        $key = substr($day, 0, 7);
        $months[$key]['views'] = ($months[$key]['views'] ?? 0) + $row['views'];
        $months[$key]['visitors'] = ($months[$key]['visitors'] ?? 0) + $row['visitors'];
    }
    for ($date = $since->modify('first day of this month'); $date <= new DateTimeImmutable('today'); $date = $date->modify('+1 month')) {
        $key = $date->format('Y-m');
        $series[] = ['label' => $date->format('m/y'), 'long' => $date->format('m/Y'), 'views' => (int) ($months[$key]['views'] ?? 0), 'visitors' => (int) ($months[$key]['visitors'] ?? 0)];
    }
}
$maxViews = max(1, ...array_column($series, 'views'));

$pages = stats_query($bdd, 'SELECT path AS label, COUNT(*) AS views FROM page_views WHERE created_at >= :since GROUP BY path ORDER BY views DESC LIMIT 10', $params);
$referrers = stats_query($bdd, "SELECT COALESCE(referrer, 'Accès direct') AS label, COUNT(*) AS views FROM page_views WHERE created_at >= :since GROUP BY referrer ORDER BY views DESC LIMIT 10", $params);
$devices = stats_query($bdd, 'SELECT device AS label, COUNT(*) AS views FROM page_views WHERE created_at >= :since GROUP BY device ORDER BY views DESC', $params);
$browsers = stats_query($bdd, 'SELECT browser AS label, COUNT(*) AS views FROM page_views WHERE created_at >= :since GROUP BY browser ORDER BY views DESC', $params);

$lists = [
    ['title' => 'Pages les plus vues', 'icon' => 'bi-file-earmark-text', 'rows' => $pages],
    ['title' => 'Provenance', 'icon' => 'bi-signpost-split', 'rows' => $referrers],
    ['title' => 'Appareils', 'icon' => 'bi-phone', 'rows' => $devices],
    ['title' => 'Navigateurs', 'icon' => 'bi-browser-chrome', 'rows' => $browsers],
];

include __DIR__ . '/partials/header.php';
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-6">
    <div>
        <h1 class="fs-3xl mb-1"><i class="bi bi-bar-chart me-2 fg-primary"></i>Statistiques</h1>
        <p class="fg-3 mb-0">Sans cookie, sans service tiers. Les visites avec « Do Not Track » et les tiennes ne sont pas comptées.</p>
    </div>
    <div class="nav nav-pills">
        <?php foreach ($periods as $value => $label): ?>
            <a class="nav-link<?= $value === $days ? ' active' : '' ?>" href="stats.php?days=<?= $value ?>"><?= e($label) ?></a>
        <?php endforeach; ?>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="card"><div class="card-body">
        <p class="fg-3 mb-1">Pages vues · <?= e($periods[$days]) ?></p>
        <p class="fs-3xl fw-bold mb-0"><?= number_format((int) $totals['views'], 0, ',', ' ') ?></p>
    </div></div>
    <div class="card"><div class="card-body">
        <p class="fg-3 mb-1">Visiteurs · <?= e($periods[$days]) ?></p>
        <p class="fs-3xl fw-bold mb-0"><?= number_format((int) $totals['visitors'], 0, ',', ' ') ?></p>
    </div></div>
    <div class="card"><div class="card-body">
        <p class="fg-3 mb-1">Pages vues aujourd'hui</p>
        <p class="fs-3xl fw-bold mb-0"><?= number_format((int) $today, 0, ',', ' ') ?></p>
    </div></div>
</div>

<div class="card mb-6">
    <div class="card-body">
        <p class="fw-semibold mb-4">Pages vues par <?= $step === 'day' ? 'jour' : 'mois' ?></p>
        <?php if ((int) $totals['views'] === 0): ?>
            <p class="fg-3 mb-0">Pas encore de visite sur cette période.</p>
        <?php else: ?>
            <div class="stats-chart" role="img" aria-label="Pages vues par <?= $step === 'day' ? 'jour' : 'mois' ?>, maximum <?= $maxViews ?>">
                <div class="stats-scale"><span><?= $maxViews ?></span><span>0</span></div>
                <div class="stats-bars">
                    <?php foreach ($series as $index => $point): ?>
                        <div class="stats-bar" tabindex="0">
                            <span class="stats-fill" style="height: <?= round($point['views'] / $maxViews * 100, 2) ?>%"></span>
                            <span class="stats-tip"><b><?= e($point['long']) ?></b><?= $point['views'] ?> vue<?= $point['views'] > 1 ? 's' : '' ?> · <?= $point['visitors'] ?> visiteur<?= $point['visitors'] > 1 ? 's' : '' ?></span>
                            <?php if ($index % max(1, (int) ceil(count($series) / 10)) === 0): ?>
                                <span class="stats-label"><?= e($point['label']) ?></span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <details class="mt-4">
                <summary class="fg-3">Voir les chiffres en tableau</summary>
                <table class="table table-sm mt-2">
                    <thead><tr><th>Date</th><th class="text-end">Pages vues</th><th class="text-end">Visiteurs</th></tr></thead>
                    <tbody>
                        <?php foreach (array_reverse($series) as $point): ?>
                            <tr><td><?= e($point['long']) ?></td><td class="text-end"><?= $point['views'] ?></td><td class="text-end"><?= $point['visitors'] ?></td></tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </details>
        <?php endif; ?>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <?php foreach ($lists as $list): ?>
        <?php $total = max(1, array_sum(array_column($list['rows'], 'views'))); ?>
        <div class="card">
            <div class="card-body">
                <p class="fw-semibold mb-3"><i class="bi <?= e($list['icon']) ?> me-2 fg-primary"></i><?= e($list['title']) ?></p>
                <?php if (!$list['rows']): ?>
                    <p class="fg-3 mb-0">Rien pour l'instant.</p>
                <?php else: ?>
                    <ul class="stats-list">
                        <?php foreach ($list['rows'] as $row): ?>
                            <li>
                                <span class="stats-list-bar" style="width: <?= round($row['views'] / $total * 100, 2) ?>%"></span>
                                <span class="stats-list-label"><?= e($row['label']) ?></span>
                                <span class="stats-list-value"><?= (int) $row['views'] ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>

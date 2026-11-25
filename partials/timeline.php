<?php
$timelineKinds = [
    'experience' => 'Expérience',
    'formation' => 'Formation',
    'benevolat' => 'Bénévolat',
];
$usedKinds = array_values(array_unique(array_column($timeline, 'kind')));
?>
<section class="slide" id="parcours" aria-labelledby="parcours-title">
    <div class="slide-header">
        <h2 id="parcours-title">Parcours et expériences</h2>
        <p>Formation, jobs étudiants et engagements</p>
    </div>

    <div class="timeline">
        <?php if (count($usedKinds) > 1): ?>
            <p class="timeline-legend">
                <?php foreach ($timelineKinds as $kind => $label): ?>
                    <?php if (in_array($kind, $usedKinds, true)): ?>
                        <span class="timeline-key timeline-<?= e($kind) ?>"><?= e($label) ?></span>
                    <?php endif; ?>
                <?php endforeach; ?>
            </p>
        <?php endif; ?>

        <ol>
            <?php foreach ($timeline as $step): ?>
                <?php $kind = isset($timelineKinds[$step['kind']]) ? $step['kind'] : 'experience'; ?>
                <li class="timeline-item timeline-<?= e($kind) ?>">
                    <span class="timeline-date"><?= e($step['period']) ?></span>
                    <span class="timeline-mark" title="<?= e($timelineKinds[$kind]) ?>"><span class="visually-hidden"><?= e($timelineKinds[$kind]) ?></span></span>
                    <div class="timeline-body">
                        <h3><?= e($step['title']) ?></h3>
                        <p class="timeline-org"><?= e($step['organization']) ?><?= $step['location'] ? ' · ' . e($step['location']) : '' ?></p>
                        <?php if ($step['description']): ?>
                            <div class="timeline-desc"><?= rich_text($step['description']) ?></div>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ol>
    </div>
</section>

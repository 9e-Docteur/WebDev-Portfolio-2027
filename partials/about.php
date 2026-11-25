<section class="slide" id="a-propos" aria-labelledby="a-propos-title">
    <div class="slide-header">
        <h2 id="a-propos-title">À propos de moi</h2>
        <p>Qui suis-je ?</p>
    </div>

    <div class="terminal" data-terminal>
        <div class="terminal-bar">
            <span class="terminal-dot terminal-dot-red"></span>
            <span class="terminal-dot terminal-dot-yellow"></span>
            <span class="terminal-dot terminal-dot-green"></span>
            <span class="terminal-title">loris@portfolio: ~</span>
        </div>

        <div class="terminal-body">
            <div class="terminal-step">
                <p class="terminal-line"><span class="terminal-prompt">&gt;</span> <span class="terminal-cmd">whoami</span></p>
                <div class="terminal-out">
                    <?= rich_text($whoami) ?>
                </div>
            </div>

            <div class="terminal-step">
                <p class="terminal-line"><span class="terminal-prompt">&gt;</span> <span class="terminal-cmd">ls passions/</span></p>
                <div class="terminal-out terminal-ls">
                    <?php foreach ($passions as $passion): ?>
                        <button type="button" class="terminal-folder" data-folder="<?= e($passion['name']) ?>"
                            data-text="<?= e($passion['description']) ?>"><?= e($passion['name']) ?>/</button>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="terminal-step">
                <p class="terminal-line"><span class="terminal-prompt">&gt;</span> <span class="terminal-cmd">cat objectifs.txt</span></p>
                <ul class="terminal-out terminal-todo">
                    <?php foreach ($objectives as $objective): ?>
                        <li><span class="terminal-box">[ ]</span> <?= bold_text($objective['text']) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="terminal-step terminal-detail" aria-live="polite" hidden>
                <p class="terminal-line"><span class="terminal-prompt">&gt;</span> <span class="terminal-cmd"></span></p>
                <p class="terminal-out"></p>
            </div>

            <p class="terminal-line"><span class="terminal-prompt">&gt;</span><span class="terminal-cursor"></span></p>
        </div>
    </div>
</section>

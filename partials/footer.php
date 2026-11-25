<?php
$footerBase = ($activeNav ?? 'accueil') === 'accueil' ? '' : base_url();
$footerSocials = social_links();
?>
<footer class="site-footer">
    <div class="footer-inner">
        <div class="footer-brand">
            <a href="<?= e($footerBase) ?>#accueil" class="footer-logo">
                <img src="assets/icons/favicon.svg" alt="" width="44" height="44" loading="lazy">
                <span>
                    <span class="title-name">Loris Populaire</span>
                    <span class="title-job">Web Developer</span>
                </span>
            </a>
            <p>Étudiant en BES Web Developer à l'EPSE, basé à Enghien.</p>
        </div>

        <nav class="footer-col" aria-label="Plan du site">
            <p class="footer-title">Navigation</p>
            <ul>
                <li><a href="<?= e($footerBase) ?>#a-propos">À propos</a></li>
                <li><a href="<?= e($footerBase) ?>#parcours">Parcours</a></li>
                <li><a href="<?= e($footerBase) ?>#projets">Projets</a></li>
                <li><a href="<?= e($footerBase) ?>#competences">Compétences</a></li>
                <li><a href="<?= e($footerBase) ?>#contact">Contact</a></li>
            </ul>
        </nav>

        <div class="footer-col">
            <p class="footer-title">Me contacter</p>
            <ul>
                <li><a href="mailto:lorisplp.pro@gmail.com">lorisplp.pro@gmail.com</a></li>
                <li><a href="<?= e($footerBase) ?>#contact">Formulaire de contact</a></li>
                <?php if (cv_url()): ?>
                    <li><a href="<?= e(cv_url()) ?>" target="_blank" rel="noopener">Télécharger mon CV</a></li>
                <?php endif; ?>
            </ul>
            <?php if ($footerSocials): ?>
                <div class="footer-socials">
                    <?php foreach ($footerSocials as $social): ?>
                        <a href="<?= e($social['url']) ?>" target="_blank" rel="noopener" aria-label="<?= e($social['label']) ?>" title="<?= e($social['label']) ?>">
                            <i class="bi <?= e($social['icon']) ?>" aria-hidden="true"></i>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="footer-bottom">
        <p>© <?= date('Y') ?> Loris Populaire · <a href="<?= e(base_url('mentions-legales')) ?>">Mentions légales</a> · <a href="<?= e(base_url('confidentialite')) ?>">Confidentialité</a></p>
        <a href="#accueil" class="footer-top" data-back-to-top>Retour en haut <i class="bi bi-arrow-up" aria-hidden="true"></i></a>
    </div>
</footer>

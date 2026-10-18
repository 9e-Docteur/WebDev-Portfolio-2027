<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <?php include 'partials/head.php'; ?>
</head>

<body>
    <?php include 'partials/nav.php'; ?>
    <div class="slide">
        <div class="centered flex-direction-column">
            <img src="assets/svg/avatar.svg" alt="Avatar de Loris Populaire" id="avatar" class="dark-bg">
            <div class="title txt-centered">
                <h1>Loris Populaire</h1>
                <h2>Web Developper</h2>
            </div>
            <div class="badges">
                <img src="assets/svg/badges/html.svg" alt="Badge HTML">
                <img src="assets/svg/badges/css.svg" alt="Badge CSS">
                <img src="assets/svg/badges/js.svg" alt="Badge JS">
                <img src="assets/svg/badges/php.svg" alt="Badge PHP">
                <img src="assets/svg/badges/react.svg" alt="Badge React">
                <img src="assets/svg/badges/symfony.svg" alt="Badge Symfony">
            </div>
        </div>
    </div>

    <!-- Slide a propos de moi -->
    <div class="slide">
        <div class="slide-header">
            <h1>A propos de moi</h1>
            <h2>Qui suis-je ?</h2>
        </div>
    </div>

    <!-- Slide Projets -->
    <div class="slide">
        <div class="slide-header">
            <h1>Projets</h1>
            <h2>Conception personnel dans mon temps libre ou une réalisation pour une personne, un groupe de personnes,
                ou dans le cadre de mes cours</h2>
        </div>

        <div class="card-holder">
            <!-- DocMod -->
            <div class="card">
                <div class="card-content">
                    <img src="assets/img/projets/docmod.png" alt="Photo du projet DocMod">
                    <h1>DocMod</h1>
                    <p>Mod Minecraft conçu en 2021. Encore en développement sur mon temps libre sur des technologies de
                        plus
                        en plus complexe</p>
                </div>
            </div>

            <!-- VivaFest -->
            <div class="card">
                <div class="card-content">
                    <img src="assets/img/projets/vivafest.png" alt="Photo du projet VivaFest">
                    <h1>VivaFest</h1>
                    <p>Dans le cadre de mon examen de première année de WebDev en 2026, j'ai du réalisé un site
                        festivalier. Site inspiré
                        de La Semo.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="slide">
        <div class="slide-header">
            <h1>Compétences</h1>
            <h2>Les langues de programmations et technologies sur lesquels je suis aguérit</h2>
        </div>
        <div class="compt-holder">
            <!-- HTML -->
            <div class="compt-item">
                <img src="assets/img/competences/html.png" alt="Icône de HTML">

                <div class="compt-back">
                    <p>HTML</p>
                </div>
            </div>

            <!-- CSS -->
            <div class="compt-item">
                <img src="assets/img/competences/css.png" alt="Icône de CSS">

                <div class="compt-back">
                    <p>CSS</p>
                </div>
            </div>

            <!-- JS -->
            <div class="compt-item">
                <img src="assets/img/competences/js.png" alt="Icône de JS">

                <div class="compt-back">
                    <p>JS</p>
                </div>
            </div>

            <!-- PHP -->
            <div class="compt-item">
                <img src="assets/img/competences/php.png" alt="Icône de PHP">

                <div class="compt-back">
                    <p>PHP</p>
                </div>
            </div>

            <!-- REACT -->
            <div class="compt-item">
                <img src="assets/img/competences/react.webp" alt="Icône de React">

                <div class="compt-back">
                    <p>React</p>
                </div>
            </div>

            <!-- Symfony -->
            <div class="compt-item">
                <img src="assets/img/competences/symfony.webp" alt="Icône de Symfony">

                <div class="compt-back">
                    <p>Symfony</p>
                </div>
            </div>

            <!-- Vue -->
            <div class="compt-item">
                <img src="assets/img/competences/vue.webp" alt="Icône de Vue">

                <div class="compt-back">
                    <p>Vue</p>
                </div>
            </div>
        </div>

        <div class="slide-header">
            <h1>Logiciels utilisés</h1>
            <h2>Ce que j'utilise pour le développement</h2>
        </div>
        <div class="compt-holder">
            <!-- VS Code -->
            <div class="compt-item">
                <img src="assets/img/logiciels/vscode.webp" alt="Icône de VSCode">

                <div class="compt-back">
                    <p>VS Code</p>
                </div>
            </div>

            <!-- Idea -->
            <div class="compt-item">
                <img src="assets/img/logiciels/idea.webp" alt="Icône de idea">

                <div class="compt-back">
                    <p>Intellij Idea</p>
                </div>
            </div>

            <!-- PHPStorm -->
            <div class="compt-item">
                <img src="assets/img/logiciels/phpstorm.png" alt="Icône de PhpStorm">

                <div class="compt-back">
                    <p>Intellij PhpStorm</p>
                </div>
            </div>

            <!-- Docker -->
            <div class="compt-item">
                <img src="assets/img/logiciels/docker.webp" alt="Icône de docker">

                <div class="compt-back">
                    <p>Docker</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Slide Contact -->
    <div class="slide">
        <div class="slide-header">
            <h1>Contact</h1>
            <h2>Me contacter</h2>
        </div>

        <div class="flex">
            <div class="small-contact-left">
                <div class="ct-item">
                    <i class="bi bi-envelope"></i>
                    <p>Adresse e-mail : lorisplp.pro@gmail.com</p>
                </div>

                <div class="ct-item">
                    <i class="bi bi-phone"></i>
                    <p>Téléphone : 0493 02 75 18</p>
                </div>

                <div class="ct-item">
                    <i class="bi bi-cursor"></i>
                    <p>Localisation : Enghien, Belgique</p>
                </div>
            </div>
            <div class="contact-form-right">
                <div class="two-fields">
                    <div class="form-group">
                        <label for="name">Nom :</label>
                        <input type="text" name="name" id="name" placeholder="Nom" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="firstname">Prénom :</label>
                        <input type="text" name="firstname" id="firstname" placeholder="Prénom" class="form-control"
                            required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="mail">Adresse e-mail :</label>
                    <input type="text" name="mail" id="mail" placeholder="Adresse e-mail" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="phone">Téléphone :</label>
                    <input type="text" name="phone" id="phone" placeholder="Téléphone" class="form-control">
                </div>
                <div class="form-group">
                    <label for="msg">Message :</label>
                    <textarea name="msg" id="msg" required></textarea>
                </div>
                <a class="btn btn-success" href="#">Envoyer</a>
            </div>
        </div>
    </div>
</body>
<script src="assets/js/bg.js"></script>

</html>
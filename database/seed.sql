USE portfolio;

INSERT INTO settings (name, value) VALUES
('whoami', '**Loris Populaire**, étudiant en 2e année du **BES Web Developer** à l''**EPSE** de Marcq (Enghien).
Passionné d''informatique depuis l''âge de **9 ans**, j''ai touché à tout : de l''IT jusqu''à l''électronique.
Ambitieux, toujours à la recherche de nouveaux projets et de solutions concrètes aux problèmes.
Je parle **français**, **néerlandais** et **anglais**, j''apprends l''**allemand** et j''aimerais un jour apprendre l''**espagnol**.')
ON DUPLICATE KEY UPDATE value = VALUES(value);

INSERT INTO passions (name, description, position) VALUES
('informatique', 'J''adore mettre les mains dans le PC : système d''exploitation, composants, montage... Ça m''a même donné envie de me lancer un jour dans l''électronique, que je n''ai pas encore pratiquée.', 1),
('aviation', 'Entre 2000 et 2500 heures de simulation de vol, surtout sur Boeing 737, Airbus A320 et depuis peu Airbus A380. Des vols complets : planification, démarrage à froid, décollage, atterrissage et roulage jusqu''à la porte. Une passion née en 2019, lors d''un aller-retour Bruxelles - Turquie.', 2),
('sport', 'Grand fan de course à pied et de natation. Je vais à la salle (Basic-Fit) sur mon temps libre, et j''aimerais faire plus de vélo.', 3),
('modding_minecraft', 'Je développe DocMod, un mod Minecraft codé en Java, depuis 2021.', 4),
('backend', 'Je préfère le back-end au front-end : j''aime gérer, structurer et organiser les choses. Le front ne me dérange pas du tout, mais le back est un défi qui me motive.', 5),
('sysadmin', 'J''adore gérer des serveurs : j''héberge les miens chez moi, avec Docker, des petits sites web pour la famille ou pour me simplifier la gestion. Je touche aussi à l''Active Directory, au partage de fichiers et au NAS.', 6);

INSERT INTO objectives (text, position) VALUES
('Décrocher un **stage en 2027**', 1),
('Simplifier le quotidien des gens avec des outils utiles', 2),
('Rendre la gestion plus facile pour tout le monde', 3);

INSERT INTO skills (name, image, light_bg, position) VALUES
('HTML', 'assets/img/competences/html.png', 0, 1),
('CSS', 'assets/img/competences/css.png', 0, 2),
('JS', 'assets/img/competences/js.png', 0, 3),
('PHP', 'assets/img/competences/php.png', 0, 4),
('React', 'assets/img/competences/react.webp', 0, 5),
('Symfony', 'assets/img/competences/symfony.webp', 1, 6),
('Vue', 'assets/img/competences/vue.webp', 0, 7);

INSERT INTO software (name, image, light_bg, position) VALUES
('VS Code', 'assets/img/logiciels/vscode.webp', 0, 1),
('IntelliJ IDEA', 'assets/img/logiciels/idea.webp', 0, 2),
('PhpStorm', 'assets/img/logiciels/phpstorm.png', 0, 3),
('Docker', 'assets/img/logiciels/docker.webp', 0, 4);

INSERT INTO projects (title, slug, description, image, position) VALUES
('DocMod', 'docmod', 'Mod Minecraft conçu en 2021. Encore en développement sur mon temps libre sur des technologies de plus en plus complexes.', 'assets/img/projets/docmod.png', 1),
('Casilux', 'casilux', 'Site web réalisé bénévolement pour une mini-entreprise de mon ancienne école, l''Athénée Royal d''Enghien. Actuellement en développement.', NULL, 2),
('VivaFest', 'vivafest', 'Dans le cadre de mon examen de première année de WebDev en 2026, j''ai dû réaliser un site festivalier, inspiré de La Semo.', 'assets/img/projets/vivafest.png', 3);

UPDATE projects SET kind = 'Mod Minecraft', context = 'Projet personnel', year = '2021', status = 'En cours' WHERE slug = 'docmod';
UPDATE projects SET kind = 'Site web', context = 'Bénévolat', role = 'Développeur', status = 'En cours' WHERE slug = 'casilux';
UPDATE projects SET kind = 'Site web', context = 'Examen EPSE', year = '2026', status = 'Terminé' WHERE slug = 'vivafest';

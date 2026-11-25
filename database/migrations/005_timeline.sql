CREATE TABLE IF NOT EXISTS timeline (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kind VARCHAR(20) NOT NULL DEFAULT 'experience',
    title VARCHAR(120) NOT NULL,
    organization VARCHAR(120) NOT NULL,
    location VARCHAR(120) NULL,
    period VARCHAR(60) NOT NULL,
    description TEXT NULL,
    position INT NOT NULL DEFAULT 0
);

INSERT INTO timeline (kind, title, organization, location, period, description, position)
SELECT * FROM (
    SELECT 'benevolat' AS kind, 'Développeur et accompagnateur bénévole' AS title, 'Casilux, mini-entreprise de l''Athénée Royal d''Enghien' AS organization, 'Enghien' AS location, 'Depuis novembre 2026' AS period, 'Réalisation du site web de la mini-entreprise et accompagnement des élèves **chaque jeudi** : outils numériques, partage d''expérience.' AS description, 1 AS position
    UNION ALL SELECT 'experience', 'Étudiant chauffeur', 'Laboratoire Bauduin', NULL, 'Depuis juin 2026', 'Job étudiant à temps partiel.', 2
    UNION ALL SELECT 'formation', 'Formation en développement web', 'EPSE', 'Marcq', '2025 - 2027', 'PHP, Symfony, JavaScript, bases de données et projets en équipe. Stage prévu du 22 février au 9 mai 2027.', 3
    UNION ALL SELECT 'formation', 'Diplôme de l''enseignement secondaire', 'Athénée Royal d''Enghien', 'Enghien', 'Diplômé en juin 2025', 'Sciences économiques, informatique de gestion, mathématique IV, langues modernes II (FR, NL, AN).', 4
    UNION ALL SELECT 'experience', 'Responsable technique', 'FreshSip, mini-entreprise LJE', 'Enghien', 'Sept. 2024 - juin 2025', 'Mini-entreprise du programme **Les Jeunes Entreprises** : gestion des **fournisseurs** et des **clients**, **calcul des coûts** avec le directeur financier.', 5
    UNION ALL SELECT 'experience', 'Job étudiant', 'Delhaize Belgium', NULL, 'Oct. 2023 - juil. 2026', 'Temps partiel : caisse, boulangerie et service client.', 6
) seed
WHERE NOT EXISTS (SELECT 1 FROM timeline);

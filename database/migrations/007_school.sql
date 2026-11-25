UPDATE timeline SET
    title = 'Enseignement secondaire général',
    organization = 'Athénée Royal d''Enghien',
    location = 'Enghien',
    period = '2019 - 2025',
    description = 'Immersion **bilingue français-néerlandais** pendant tout le secondaire. Diplômé en juin 2025.\n2022 - 2025 : **sciences économiques**, mathématique IV, informatique de gestion et anglais (langue moderne II).\n2021 - 2022 : **sciences fortes** et anglais (langue moderne).'
WHERE title IN ('Diplôme de l''enseignement secondaire', 'Enseignement secondaire général');

INSERT INTO timeline (kind, title, organization, location, period, description, position)
SELECT 'formation', 'Maternelle et primaire en immersion', 'Athénée Royal d''Enghien', 'Enghien', '2010 - 2019', 'Maternelle (2010 - 2013) puis primaire (2013 - 2019) en **immersion bilingue français-néerlandais**.', COALESCE(MAX(position), 0) + 1
FROM timeline
WHERE NOT EXISTS (SELECT 1 FROM timeline WHERE title = 'Maternelle et primaire en immersion');

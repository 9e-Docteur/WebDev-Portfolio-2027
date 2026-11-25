INSERT INTO skills (name, image, light_bg, position)
SELECT 'Java', 'assets/img/competences/java.png', 1, COALESCE(MAX(position), 0) + 1 FROM skills
WHERE NOT EXISTS (SELECT 1 FROM skills WHERE name = 'Java');

INSERT INTO skills (name, image, light_bg, position)
SELECT 'MySQL', 'assets/img/competences/mysql.png', 1, COALESCE(MAX(position), 0) + 1 FROM skills
WHERE NOT EXISTS (SELECT 1 FROM skills WHERE name = 'MySQL');

UPDATE projects SET
    description = 'Site d''un festival de musique ouvert à tous, inspiré de La Semo à Enghien. Réalisé seul pour mon examen de 1re année de WebDev.',
    tagline = 'Un festival ouvert à tous, inspiré de La Semo à Enghien',
    kind = 'Site web',
    context = 'Examen de 1re année, EPSE',
    year = '2026',
    duration = 'Design de mars à juin, développement en juin 2026',
    role = 'Seul, du design au code',
    status = 'Terminé',
    content = 'Pour mon examen de première année de développement web, j''ai revisité le site de **La Semo**, un festival de musique belge qui se tient à Enghien, sous un nouveau nom : **VivaFest**.\nLe fil rouge est l''**ouverture d''esprit** : un festival ouvert à tous, quels que soient les goûts. Rock, jazz, RnB, rap ou musique classique se retrouvent dans des « mondes » répartis dans le parc d''Enghien.\nPour que ce message se voie au premier coup d''œil, j''ai choisi une identité **ludique et enfantine** : fond illustré, écritures dessinées à la main et couleurs vives.',
    features = 'Bandeau défilant avec tout le line-up du festival\nProgrammation sur 3 jours, avec un carrousel des artistes par jour\nPage « Nos engagements » : gobelets réutilisables, nettoyage du parc, alcootests\nInfos pratiques : lieu, dates, accès PMR et badge VIP dédié\nFormulaire de contact et liens bénévolat, job et accréditation\nSite responsive, du mobile à l''ordinateur',
    url_site = 'https://webdev.lorispopulaire.be',
    url_code = 'https://github.com/9e-Docteur/webepse-exam-2026'
WHERE slug = 'vivafest';

UPDATE projects SET
    description = 'Mon mod Minecraft sur l''univers de Doctor Who, développé en Java depuis 2021 et publié sur Modrinth et CurseForge.',
    tagline = 'Mon mod Minecraft dans l''univers de Doctor Who',
    kind = 'Mod Minecraft',
    context = 'Projet personnel',
    year = '2021',
    duration = 'Depuis 2021',
    role = 'Fondateur et développeur',
    status = 'En cours',
    content = 'DocMod est né d''un **rêve d''enfant** : avoir mon propre mod Minecraft, sur le thème de la série britannique **Doctor Who**. C''est avec ce projet que j''ai découvert le langage **Java**.\nLe mod ajoute à Minecraft des dimensions, des objets, des blocs et des créatures tirés de la série : tournevis sonique, armes et armures, minerais, blocs décoratifs, Daleks et Cybermen.\nAutour du mod s''est formée une petite équipe, la **DocTeam** : développeurs, graphistes et modérateurs, avec une communauté sur Discord. Le mod est publié sur **Modrinth** et **CurseForge**.',
    features = 'Nouvelles dimensions et structures à explorer\nObjets de la série : tournevis sonique, armes, armures et outils\nBlocs décoratifs et fonctionnels, nouveaux minerais\nCréatures de Doctor Who : Daleks et Cybermen\nConnexion à une API pour afficher les capes de la DocTeam et les joueurs en ligne sur le serveur\nCompatible Forge, Minecraft 1.19.2 et 1.19.3',
    url_site = 'https://modrinth.com/mod/docmod',
    url_code = 'https://github.com/9e-Docteur/DocMod'
WHERE slug = 'docmod';

UPDATE projects SET
    description = 'Site web réalisé bénévolement pour la mini-entreprise de mon ancienne école, l''Athénée Royal d''Enghien, que j''accompagne chaque semaine.',
    tagline = 'Un coup de main bénévole à la mini-entreprise de mon ancienne école',
    kind = 'Site web',
    context = 'Bénévolat',
    year = '2026',
    duration = 'Depuis novembre 2026',
    role = 'Développeur bénévole et accompagnateur',
    status = 'En cours',
    content = '**Casilux** est une mini-entreprise d''élèves de l''**Athénée Royal d''Enghien**, mon ancienne école. Je réalise **bénévolement** son site web pour l''aider à se faire connaître et à se développer.\nAu-delà du site, je les accompagne **chaque jeudi**, quand je n''ai pas cours : je leur apprends à utiliser les outils numériques, je partage mon expérience et je les aide à avancer plus facilement.\nLe site est en cours de développement.'
WHERE slug = 'casilux';

INSERT IGNORE INTO project_skills (project_id, skill_id)
SELECT p.id, s.id FROM projects p JOIN skills s ON s.name IN ('HTML', 'CSS', 'JS') WHERE p.slug = 'vivafest';

INSERT IGNORE INTO project_skills (project_id, skill_id)
SELECT p.id, s.id FROM projects p JOIN skills s ON s.name = 'Java' WHERE p.slug = 'docmod';

INSERT IGNORE INTO project_skills (project_id, skill_id)
SELECT p.id, s.id FROM projects p JOIN skills s ON s.name IN ('HTML', 'CSS', 'JS', 'PHP', 'Symfony', 'MySQL') WHERE p.slug = 'casilux';

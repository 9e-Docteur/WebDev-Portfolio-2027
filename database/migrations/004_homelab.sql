INSERT INTO skills (name, image, light_bg, position)
SELECT 'Linux', 'assets/img/competences/linux.png', 0, COALESCE(MAX(position), 0) + 1 FROM skills
WHERE NOT EXISTS (SELECT 1 FROM skills WHERE name = 'Linux');

INSERT INTO skills (name, image, light_bg, position)
SELECT 'Docker', 'assets/img/logiciels/docker.webp', 0, COALESCE(MAX(position), 0) + 1 FROM skills
WHERE NOT EXISTS (SELECT 1 FROM skills WHERE name = 'Docker');

INSERT INTO skills (name, image, light_bg, position)
SELECT 'Proxmox', 'assets/img/competences/proxmox.png', 0, COALESCE(MAX(position), 0) + 1 FROM skills
WHERE NOT EXISTS (SELECT 1 FROM skills WHERE name = 'Proxmox');

INSERT INTO skills (name, image, light_bg, position)
SELECT 'Windows Server', 'assets/img/competences/windows.png', 0, COALESCE(MAX(position), 0) + 1 FROM skills
WHERE NOT EXISTS (SELECT 1 FROM skills WHERE name = 'Windows Server');

INSERT INTO projects (title, slug, description, image, tagline, kind, context, duration, role, status, content, features, position)
SELECT
    'Homelab',
    'homelab',
    'Mon infrastructure maison sous Proxmox : serveurs virtuels, NAS, Docker, réseau et téléphonie IP, que j''administre au quotidien.',
    'assets/img/uploads/8077cefac508e392c31decf2.jpg',
    'Mon infrastructure maison : virtualisation, réseau et services auto-hébergés',
    'Infrastructure',
    'Projet personnel',
    'En continu',
    'Conception et administration',
    'En cours',
    'Mon homelab est mon terrain d''entraînement : j''y déploie et j''administre de vrais services, comme en entreprise, mais chez moi.\nLe cœur est un **mini-PC HP ProDesk** sous **Proxmox VE**. Il fait tourner une vingtaine de machines virtuelles : NAS, **Windows Server**, postes **Windows Pro**, machines **Debian** et serveurs **Docker** gérés avec **Portainer**. Deux **NAS physiques**, un Synology et un Western Digital, complètent le stockage.\nCôté réseau, un **switch manageable 24 ports Gigabit + 4 SFP** et des switches TP-Link relient le tout. J''ai aussi mis en place de la **téléphonie professionnelle** avec un IPBX.\nCes services me servent au quotidien et me permettent de tester ce que je développe dans des conditions réelles.',
    'Hyperviseur Proxmox VE sur un mini-PC HP ProDesk\nNAS virtualisés et NAS physiques Synology et Western Digital, pour le partage de fichiers et les sauvegardes\nWindows Server avec Active Directory et postes Windows Pro\nMachines Debian et conteneurs Docker administrés avec Portainer\nBureau à distance depuis un navigateur avec Apache Guacamole\nOutils réseau : iperf et OpenSpeedTest pour mesurer les débits\nTéléphonie IP professionnelle avec un IPBX\nSwitch manageable 24 ports Gigabit + 4 SFP et switches TP-Link\nServices du quotidien : téléchargeur de vidéos YouTube, base de données d''un projet personnel',
    COALESCE(MAX(position), 0) + 1
FROM projects
WHERE NOT EXISTS (SELECT 1 FROM projects WHERE slug = 'homelab');

INSERT INTO project_images (project_id, image, position)
SELECT p.id, i.image, i.position
FROM projects p
JOIN (
    SELECT 'assets/img/uploads/90ffbf56a75f940e724dad2b.jpg' AS image, 1 AS position
    UNION ALL SELECT 'assets/img/uploads/8e51f1af88ae28f43e468060.jpg', 2
    UNION ALL SELECT 'assets/img/uploads/39cef34026cbe27c2b551b8b.jpg', 3
    UNION ALL SELECT 'assets/img/uploads/130584b12157e38530b34442.jpg', 4
    UNION ALL SELECT 'assets/img/uploads/022634d0be8e18d8a4e83853.jpg', 5
) i
WHERE p.slug = 'homelab'
AND NOT EXISTS (SELECT 1 FROM project_images WHERE project_id = p.id);

INSERT IGNORE INTO project_skills (project_id, skill_id)
SELECT p.id, s.id FROM projects p JOIN skills s ON s.name IN ('Linux', 'Docker', 'Proxmox', 'Windows Server') WHERE p.slug = 'homelab';

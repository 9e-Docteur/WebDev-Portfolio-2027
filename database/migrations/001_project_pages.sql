USE portfolio;

ALTER TABLE projects
    ADD COLUMN slug VARCHAR(120) NULL AFTER title,
    ADD COLUMN tagline VARCHAR(160) NULL AFTER image,
    ADD COLUMN kind VARCHAR(60) NULL AFTER tagline,
    ADD COLUMN context VARCHAR(100) NULL AFTER kind,
    ADD COLUMN year VARCHAR(20) NULL AFTER context,
    ADD COLUMN duration VARCHAR(60) NULL AFTER year,
    ADD COLUMN role VARCHAR(100) NULL AFTER duration,
    ADD COLUMN status VARCHAR(40) NULL AFTER role,
    ADD COLUMN content TEXT NULL AFTER status,
    ADD COLUMN features TEXT NULL AFTER content,
    ADD COLUMN learnings TEXT NULL AFTER features,
    ADD COLUMN url_site VARCHAR(255) NULL AFTER learnings,
    ADD COLUMN url_code VARCHAR(255) NULL AFTER url_site,
    ADD COLUMN updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

UPDATE projects SET slug = TRIM(BOTH '-' FROM REGEXP_REPLACE(LOWER(title), '[^a-z0-9]+', '-')) WHERE slug IS NULL;
UPDATE projects SET slug = CONCAT('projet-', id) WHERE slug = '';
UPDATE projects p
    JOIN (SELECT slug FROM projects GROUP BY slug HAVING COUNT(*) > 1) d ON d.slug = p.slug
    SET p.slug = CONCAT(p.slug, '-', p.id);

ALTER TABLE projects MODIFY slug VARCHAR(120) NOT NULL, ADD UNIQUE KEY projects_slug_unique (slug);

CREATE TABLE IF NOT EXISTS project_skills (
    project_id INT UNSIGNED NOT NULL,
    skill_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (project_id, skill_id),
    FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE,
    FOREIGN KEY (skill_id) REFERENCES skills (id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS project_images (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id INT UNSIGNED NOT NULL,
    image VARCHAR(255) NOT NULL,
    position INT NOT NULL DEFAULT 0,
    FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE
);

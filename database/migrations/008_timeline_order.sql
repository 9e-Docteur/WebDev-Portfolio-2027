ALTER TABLE timeline ADD COLUMN start_month CHAR(7) NULL AFTER period;

UPDATE timeline SET start_month = '2026-11' WHERE title = 'Développeur et accompagnateur bénévole';
UPDATE timeline SET start_month = '2026-06' WHERE title = 'Étudiant chauffeur';
UPDATE timeline SET start_month = '2025-09' WHERE title = 'BES Web Developer';
UPDATE timeline SET start_month = '2024-09' WHERE title = 'Responsable technique';
UPDATE timeline SET start_month = '2023-10' WHERE title = 'Job étudiant';
UPDATE timeline SET start_month = '2019-09' WHERE title = 'Enseignement secondaire général';
UPDATE timeline SET start_month = '2010-09' WHERE title = 'Maternelle et primaire en immersion';

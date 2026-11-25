UPDATE timeline SET
    title = 'BES Web Developer',
    organization = 'EPSE, Enseignement de Promotion Sociale d''Enghien',
    location = 'Marcq (Enghien)',
    description = '**Brevet de l''Enseignement Supérieur** en développement web : HTML, CSS, JavaScript (React, Vue.js), PHP et création d''API avec **Symfony**, bases de données, UI/UX, CMS et veille technologique. Stage prévu du 22 février au 9 mai 2027.'
WHERE organization = 'EPSE' OR title = 'Formation en développement web';

UPDATE settings SET value = REPLACE(
    value,
    'étudiant en **2e année de WebDev** à l''**EPSE** de Marcq.',
    'étudiant en 2e année du **BES Web Developer** à l''**EPSE** de Marcq (Enghien).'
) WHERE name = 'whoami';

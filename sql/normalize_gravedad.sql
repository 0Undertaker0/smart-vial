-- Normalize gravedad to canonical values: baja, media, alta, fatal
-- This script is idempotent and keeps unknown values as-is.

UPDATE incidentes SET gravedad = CASE
    WHEN LOWER(TRIM(gravedad)) IN ('leve','leves','lev') THEN 'baja'
    WHEN LOWER(TRIM(gravedad)) IN ('baja','low') THEN 'baja'
    WHEN LOWER(TRIM(gravedad)) IN ('media','mediana','moderada','moderado') THEN 'media'
    WHEN LOWER(TRIM(gravedad)) IN ('grave','graves','alto','altas') THEN 'alta'
    WHEN LOWER(TRIM(gravedad)) IN ('alta') THEN 'alta'
    WHEN LOWER(TRIM(gravedad)) IN ('fatal') THEN 'fatal'
    ELSE gravedad
END
WHERE gravedad IS NOT NULL;

-- Set empty values to a sane default (media)
UPDATE incidentes SET gravedad = 'media' WHERE gravedad IS NULL OR TRIM(gravedad) = ''; 

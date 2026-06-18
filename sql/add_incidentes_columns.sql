-- Añadir columnas opcionales para filtros avanzados de incidentes
ALTER TABLE incidentes
  ADD COLUMN departamento VARCHAR(100) NULL AFTER gravedad,
  ADD COLUMN municipio VARCHAR(100) NULL AFTER departamento,
  ADD COLUMN zona VARCHAR(255) NULL AFTER municipio,
  ADD COLUMN tipo_evento VARCHAR(50) NULL AFTER zona,
  ADD COLUMN estado_tramite VARCHAR(50) NULL DEFAULT 'Registrado' AFTER tipo_evento,
  ADD COLUMN responsable_agente_id INT NULL AFTER estado_tramite,
  ADD COLUMN responsable_unidad VARCHAR(100) NULL AFTER responsable_agente_id;

-- Índices recomendados para mejorar rendimiento en filtros
CREATE INDEX idx_incidentes_departamento ON incidentes(departamento);
CREATE INDEX idx_incidentes_municipio ON incidentes(municipio);
CREATE INDEX idx_incidentes_fecha ON incidentes(fecha);
CREATE INDEX idx_incidentes_gravedad ON incidentes(gravedad);
CREATE INDEX idx_incidentes_tipo_evento ON incidentes(tipo_evento);
CREATE INDEX idx_incidentes_estado_tramite ON incidentes(estado_tramite);

-- Añadir columna para almacenar la dirección legible (reverse-geocoding)
ALTER TABLE incidentes
  ADD COLUMN direccion VARCHAR(512) NULL AFTER lng;

-- Nota: ejecutar este archivo desde tu cliente MySQL (o usar tools/apply_migrations.php)

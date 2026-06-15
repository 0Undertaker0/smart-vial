-- SMARTVIAL MySQL schema
CREATE DATABASE IF NOT EXISTS smartvial DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE smartvial;

CREATE TABLE roles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL
);

CREATE TABLE permisos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  clave VARCHAR(100) NOT NULL,
  descripcion VARCHAR(255)
);

CREATE TABLE roles_permisos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  role_id INT NOT NULL,
  permiso_id INT NOT NULL,
  FOREIGN KEY (role_id) REFERENCES roles(id),
  FOREIGN KEY (permiso_id) REFERENCES permisos(id)
);

CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  activo TINYINT DEFAULT 1,
  role_id INT DEFAULT 2,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (role_id) REFERENCES roles(id)
);

CREATE TABLE incidentes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(255),
  descripcion TEXT,
  lat DECIMAL(10,7),
  lng DECIMAL(10,7),
  gravedad VARCHAR(20),
  user_id INT,
  fecha DATETIME,
  activo TINYINT DEFAULT 1,
  FOREIGN KEY (user_id) REFERENCES usuarios(id)
);

CREATE TABLE fotografias (
  id INT AUTO_INCREMENT PRIMARY KEY,
  incidente_id INT,
  archivo VARCHAR(255),
  fecha DATETIME,
  FOREIGN KEY (incidente_id) REFERENCES incidentes(id)
);

CREATE TABLE auditoria (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT,
  accion VARCHAR(255),
  tabla_afectada VARCHAR(100),
  registro_id INT,
  fecha DATETIME DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO roles (id,nombre) VALUES (1,'admin'),(2,'agente'),(3,'ciudadano');

INSERT INTO permisos (clave,descripcion) VALUES
('permiso_view','Ver permisos'),
('permiso_create','Crear permisos'),
('permiso_edit','Editar permisos'),
('permiso_delete','Eliminar permisos'),
('user_view','Ver usuarios'),
('user_create','Crear usuarios'),
('user_delete','Eliminar usuarios'),
('role_view','Ver roles'),
('role_create','Crear roles'),
('role_assign','Asignar permisos a roles'),
('role_permissions','Ver/editar permisos de rol'),
('incident_view','Ver incidentes'),
('incident_create','Crear incidentes'),
('incident_edit','Editar incidentes'),
('incident_delete','Eliminar incidentes'),
('report_view','Ver reportes'),
('report_export','Exportar reportes');

-- Default admin user: password 'admin123'
INSERT INTO usuarios (nombre,email,password,activo,role_id) VALUES ('Administrador','admin@smartvial.local', '$2y$10$QwH7fYwqQJqZkY8rE/2g7.OJ8e6N5Zvj9QKx0mR6Cq9rZf0uZpX2S',1,1);

-- Assign all permisos to admin role
INSERT INTO roles_permisos (role_id,permiso_id) SELECT 1,id FROM permisos;

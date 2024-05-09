DROP DATABASE IF EXISTS bibliopocketDB;
CREATE DATABASE bibliopocketDB;
USE bibliopocketDB;

/* CREACIÓN ENTIDADES */
CREATE TABLE admins (
  id_admin VARCHAR(128) PRIMARY KEY NOT NULL,
  nombre_admin VARCHAR(128) UNIQUE NOT NULL,
  contrasenha_admin VARCHAR(70) NOT NULL
);

CREATE TABLE usuarios (
  id VARCHAR(128) PRIMARY KEY NOT NULL,
  nombre_usuario VARCHAR(128) UNIQUE NOT NULL,
  contrasenha_usuario VARCHAR(70) NOT NULL,
  email_usuario VARCHAR(256) UNIQUE NOT NULL,
  user_pic VARCHAR(128),
  fecha_registro DATETIME NOT NULL,
  fecha_ultimo_login DATETIME NOT NULL
);

/* Tabla para almacenar contraseñas temporales cuando la persona usuaria olvida su contraseña */
CREATE TABLE contrasenhas_temporales (
  id VARCHAR(128) PRIMARY KEY NOT NULL,
  email_usuario VARCHAR(256) NOT NULL,
  contrasenha_temporal VARCHAR(70) NOT NULL,
  fecha_expiracion TIMESTAMP NOT NULL,
  CONSTRAINT fk_contrasenhasTemporalesUsuarios FOREIGN KEY (email_usuario) REFERENCES usuarios(email_usuario) ON DELETE CASCADE
);

/* Tabla para llevar un registro de los bloqueos temporales ejecutados contra una persona usuaria */
CREATE TABLE bloqueos (
  id VARCHAR(128) PRIMARY KEY NOT NULL,
  tipo INT UNIQUE NOT NULL
  -- TODO: descripcion VARCHAR(256) NOT NULL
  -- TODO: nivel_gravedad INT NOT NULL
);

CREATE TABLE bloqueos_usuarios (
  id VARCHAR(128) PRIMARY KEY NOT NULL,
  id_bloqueo VARCHAR(128) NOT NULL,
  id_usuario VARCHAR(128) NOT NULL,
  fecha_expiracion TIMESTAMP NOT NULL,
  CONSTRAINT fk_BloqueosUsuarios_Usuarios FOREIGN KEY (id_usuario) REFERENCES usuarios(id),
  CONSTRAINT fk_BloqueosUsuarios_Bloqueos FOREIGN KEY (id_bloqueo) REFERENCES bloqueos(id)
);

CREATE TABLE libros (
  id VARCHAR(128) PRIMARY KEY NOT NULL,
  titulo VARCHAR(256) NOT NULL,
  subtitulo VARCHAR(256),
  autoria VARCHAR(256) NOT NULL,
  descripcion VARCHAR(5000),
  portada VARCHAR(512),
  num_paginas INTEGER,
  editorial VARCHAR(128),
  anho_publicacion VARCHAR(4),
  enlace_API VARCHAR(256),
  estado INT, -- 0: "Pendiente" | 1: "Leyendo" | 2: "Leído"
  fecha_adicion DATETIME NOT NULL,
  id_usuario VARCHAR(128) NOT NULL,
  CONSTRAINT fk_LibrosUsuarios FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
);

-- 'Categorías' como atributo multivaluado de 'Libros'
CREATE TABLE categorias (
  id VARCHAR(128) PRIMARY KEY NOT NULL,
  nombre VARCHAR(256) NOT NULL,
  fecha_adicion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  id_libro VARCHAR(128) NOT NULL,
  CONSTRAINT fk_CategoriasLibros FOREIGN KEY (id_libro) REFERENCES libros(id) ON DELETE CASCADE
);

CREATE TABLE valoraciones (
  id VARCHAR(128) PRIMARY KEY NOT NULL,
  val_numerica INT NOT NULL,
  resenha VARCHAR(2000),
  fecha_emision DATETIME NOT NULL,
  id_usuario VARCHAR(128) NOT NULL,
  CONSTRAINT fk_ValoracionesUsuarios FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
);


-- Creación de admin para testing
INSERT INTO admins VALUES(
  UUID(),
  "adminBPtesting",
  "$2y$10$eJbGd/sAG6v7sMn6kM7g/Oaxg6qkjCzjWt.GMG7qiR1lM4jR.iMXC"
);

-- Creación de usuario para testing
INSERT INTO usuarios VALUES(
  UUID(),
  "testing",
  "$2y$10$X6E8.2fofYExDpAVmzDrzeEAeKdfCCXMizPGOnyYeRk24vmlh1ksG",
  "test@test.test",
  NULL,
  NOW(),
  NOW()
);

-- Creación de motivos de bloqueo para testing
INSERT INTO bloqueos VALUES
  (UUID(), 1),
  (UUID(), 2),
  (UUID(), 3),
  (UUID(), 4),
  (UUID(), 5);


-- Se activa la programación de eventos
SET GLOBAL event_scheduler = ON;

-- Para eliminar aquellas contraseñas temporales cuya fecha de expiración haya pasado
CREATE EVENT delete_contrasenhas_temporales
ON SCHEDULE EVERY 1 MINUTE
DO DELETE FROM contrasenhas_temporales WHERE fecha_expiracion <= CURRENT_TIMESTAMP();
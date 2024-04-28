DROP DATABASE IF EXISTS bibliopocketDB;
CREATE DATABASE bibliopocketDB;
USE bibliopocketDB;

/* CREACIÓN ENTIDADES */
CREATE TABLE usuarios (
  id VARCHAR(128) PRIMARY KEY NOT NULL,
  nombre_usuario VARCHAR(128) UNIQUE NOT NULL,
  contrasenha_usuario VARCHAR(70) NOT NULL,
  email_usuario VARCHAR(256) UNIQUE NOT NULL,
  user_pic VARCHAR(128),
  fecha_registro DATETIME NOT NULL,
  fecha_ultimo_login DATETIME NOT NULL
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
  id_libro VARCHAR(128) NOT NULL,
  fecha_adicion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
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
DROP DATABASE IF EXISTS bibliopocketDB;
CREATE DATABASE bibliopocketDB;
USE bibliopocketDB;

/* CREACIÓN ENTIDADES */
-- Usuarios
CREATE TABLE usuarios (
  id VARCHAR(128) PRIMARY KEY NOT NULL,
  nombre_usuario VARCHAR(128),
  contrasenha_usuario VARCHAR(70),
  email_usuario VARCHAR(255),
  user_pic VARCHAR(128),
  ultimo_login DATETIME NOT NULL
);

-- Libros (personalizados)
CREATE TABLE libros (
  id VARCHAR(128) PRIMARY KEY NOT NULL,
  titulo VARCHAR(255),
  subtitulo VARCHAR(255),
  autoria VARCHAR(255),
  descripcion TEXT,
  portada VARCHAR(255),
  num_paginas INTEGER,
  editorial VARCHAR(128),
  anho_publicacion VARCHAR(4),
  enlace_API VARCHAR(255),
  estado ENUM("Pendiente","Leyendo", "Leido"),
  fecha_adicion DATETIME NOT NULL,
  id_usuario VARCHAR(128) NOT NULL,
  CONSTRAINT fk_LibrosUsuarios FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
);

CREATE TABLE categorias (
  id VARCHAR(128) NOT NULL,
  nombre VARCHAR(255) NOT NULL
);

CREATE TABLE libros_categorias (
  id_libro VARCHAR(128) NOT NULL,
  id_categoria VARCHAR(128) NOT NULL,
  PRIMARY KEY(id_libro, categoria),
  CONSTRAINT fk_LibrosRelLibCat FOREIGN KEY (id_libro) REFERENCES libros(id),
  CONSTRAINT fk_CategoriasRelLibCat FOREIGN KEY (id_categoria) REFERENCES categorias(id)
);
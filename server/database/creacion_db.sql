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
  id VARCHAR(128) NOT NULL,
  titulo VARCHAR(255),
  subtitulo VARCHAR(255),
  autoria VARCHAR(255),
  descripcion VARCHAR(512),
  portada VARCHAR(255),
  numPaginas INTEGER,
  editorial VARCHAR(128),
  anhoPublicacion VARCHAR(4),
  enlaceAPI VARCHAR(255),
  id_usuario VARCHAR(128) NOT NULL,
  CONSTRAINT fk_LibrosUsuarios FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
);

CREATE TABLE libros_categorias (
  id_libro VARCHAR(128) NOT NULL,
  categoria VARCHAR(255) NOT NULL,
  PRIMARY KEY(id_libro, categoria)
);
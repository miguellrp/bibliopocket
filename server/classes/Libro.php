<?php
include_once($_SERVER["DOCUMENT_ROOT"]."/bibliopocket/server/database/Conector.php");

class Libro {
  private $id;
  private $titulo;
  private $subtitulo;
  private $autoria;
  private $descripcion;
  private $portada;
  private $numPaginas;
  private $editorial;
  private $anhoPublicacion;
  private $enlaceAPI;
  private $conexionDB;

  function __construct($id) {
    $this->conexionDB = new Conector;
    $this->id         = $id;

    $camposDB = $this->getCamposDB();
    $this->titulo           = $camposDB["tituloDB"];
    $this->subtitulo        = $camposDB["subtituloDB"];
    $this->autoria          = $camposDB["autoriaDB"];
    $this->descripcion      = $camposDB["descripcionDB"];
    $this->portada          = $camposDB["portadaDB"];
    $this->numPaginas       = $camposDB["numPaginasDB"];
    $this->editorial        = $camposDB["editorialDB"];
    $this->anhoPublicacion  = $camposDB["anhoPublicacionDB"];
    $this->enlaceAPI        = $camposDB["enlaceAPIDB"];
  }

  private function getCamposDB() {
    $id = $this->getId();
    $camposDB = [];

    $queryDB = $this->conexionDB->conn->prepare("SELECT * FROM libros WHERE id = :id");
    $queryDB->execute(array(":id" => $id));
    $queryDB = $queryDB->fetch(PDO::FETCH_ASSOC);


    $camposDB["tituloDB"]           = $queryDB["titulo"];
    $camposDB["subtituloDB"]        = $queryDB["subtitulo"];
    $camposDB["autoriaDB"]          = $queryDB["autoria"];
    $camposDB["descripcionDB"]      = $queryDB["descripcion"];
    $camposDB["portadaDB"]          = $queryDB["portada"];
    $camposDB["numPaginasDB"]       = $queryDB["numPaginas"];
    $camposDB["editorialDB"]        = $queryDB["editorial"];
    $camposDB["anhoPublicacionDB"]  = $queryDB["anhoPublicacion"];
    $camposDB["enlaceAPIDB"]        = $queryDB["enlaceAPI"];

    return $camposDB;
  }

  // --- GETTERS ---
  function getId() {
    return $this->id;
  }

  function getTitulo() {
    return $this->titulo;
  }

  function getSubtitulo() {
    return $this->subtitulo;
  }

  function getAutoria() {
    return $this->autoria;
  }

  function getDescripcion() {
    return $this->descripcion;
  }

  function getPortada() {
    return $this->portada;
  }

  function getNumPaginas() {
    return $this->numPaginas;
  }

  function getEditorial() {
    return $this->editorial;
  }

  function getAnhoPublicacion() {
    return $this->anhoPublicacion;
  }

  function getEnlaceAPI() {
    return $this->enlaceAPI;
  }
}
?>
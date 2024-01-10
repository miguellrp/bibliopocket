<?php
include($_SERVER["DOCUMENT_ROOT"]."/bibliopocket/server/database/Conector.php");

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

    $camposDB["tituloDB"]           = $queryDB->fetchColumn(1);
    $camposDB["subtituloDB"]        = $queryDB->fetchColumn(2);
    $camposDB["autoriaDB"]          = $queryDB->fetchColumn(3);
    $camposDB["descripcionDB"]      = $queryDB->fetchColumn(4);
    $camposDB["portadaDB"]          = $queryDB->fetchColumn(5);
    $camposDB["numPaginasDB"]       = $queryDB->fetchColumn(6);
    $camposDB["editorialDB"]        = $queryDB->fetchColumn(7);
    $camposDB["anhoPublicacionDB"]  = $queryDB->fetchColumn(8);
    $camposDB["enlaceAPIDB"]        = $queryDB->fetchColumn(9);

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
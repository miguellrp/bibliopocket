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

  function __construct($datosLibro) {
    $this->id = $datosLibro["id"];
    $this->titulo = $datosLibro["titulo"];
    $this->subtitulo = $datosLibro["subtitulo"];
    $this->autoria = $datosLibro["autoria"];
  }
}

?>
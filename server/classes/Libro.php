<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/server/database/Conector.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/server/handlers/Util.php");

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
  private $estado;
  private $fechaAdicion;
  private $conexionDB;

  function __construct($id) {
    $this->conexionDB = new Conector;
    $this->id         = $id??Util::generarId();
    $datosLibro       = [];

    // Cuando se crea desde un form (Google API, libro desde 0...):
    if (isset($_POST["anhadir-libro"]) || isset($_POST["anhadir-nuevo-libro"]) || isset($_POST["modificar-libro"])) {
      $datosLibro = [
        "titulo"            => $_POST["titulo"],
        "subtitulo"         => $_POST["subtitulo"],
        "autoria"           => $_POST["autoria"],
        "descripcion"       => $_POST["descripcion"],
        "portada"           => $_POST["portada"]??null,
        "numPaginas"        => $_POST["numPaginas"],
        "editorial"         => $_POST["editorial"],
        "anhoPublicacion"   => $_POST["anhoPublicacion"],
        "enlaceAPI"         => $_POST["enlaceAPI"],
        "estado"            => $_POST["estado"],
        "fechaAdicion"      => date("Y-m-d H:i:s")
      ];
    // Cuando se crea desde la DB:
    } else {
      $camposDB = $this->getCamposDB();
      $datosLibro["titulo"]           = $camposDB["tituloDB"];
      $datosLibro["subtitulo"]        = $camposDB["subtituloDB"];
      $datosLibro["autoria"]          = $camposDB["autoriaDB"];
      $datosLibro["descripcion"]      = $camposDB["descripcionDB"];
      $datosLibro["portada"]          = $camposDB["portadaDB"];
      $datosLibro["numPaginas"]       = $camposDB["numPaginasDB"];
      $datosLibro["editorial"]        = $camposDB["editorialDB"];
      $datosLibro["anhoPublicacion"]  = $camposDB["anhoPublicacionDB"];
      $datosLibro["enlaceAPI"]        = $camposDB["enlaceAPIDB"];
      $datosLibro["estado"]           = $camposDB["estadoDB"];
      $datosLibro["fechaAdicion"]     = $camposDB["fechaAdicionDB"];
    }
    $this->titulo           = $datosLibro["titulo"];
    $this->subtitulo        = $datosLibro["subtitulo"];
    $this->autoria          = $datosLibro["autoria"];
    $this->descripcion      = $datosLibro["descripcion"];
    $this->portada          = $datosLibro["portada"];
    $this->numPaginas       = $datosLibro["numPaginas"];
    $this->editorial        = $datosLibro["editorial"];
    $this->anhoPublicacion  = $datosLibro["anhoPublicacion"];
    $this->enlaceAPI        = $datosLibro["enlaceAPI"];
    $this->estado           = $datosLibro["estado"];
    $this->fechaAdicion     = $datosLibro["fechaAdicion"];

    
    if (isset($_FILES["portadaLibro"]) && is_uploaded_file($_FILES["portadaLibro"]["tmp_name"])) {
      $extension = explode(".", $_FILES["portadaLibro"]["name"]);
      $extension = end($extension);
      
      $nombreArchivoImagen = $this->getId().".".$extension;
      $rutaImagen = dirname(__DIR__, 2)."/client/assets/images/portadas/" . $nombreArchivoImagen;
      $rutaSinExtension = explode(".", $rutaImagen);
      $rutaSinExtension = glob($rutaSinExtension[0].".*");
      
      if (!empty($rutaSinExtension)) unlink($rutaSinExtension[0]);

      move_uploaded_file($_FILES["portadaLibro"]["tmp_name"], $rutaImagen);
      $this->portada = "http://localhost/client/assets/images/portadas/" . $nombreArchivoImagen;
    }
  }

  private function getCamposDB() {
    $id = $this->getId();
    $camposDB = [];

    $query = $this->conexionDB->conn->prepare("SELECT * FROM libros WHERE id = :id");
    $query->execute(array(":id" => $id));
    $query = $query->fetch(PDO::FETCH_ASSOC);

    $camposDB["tituloDB"]           = $query["titulo"];
    $camposDB["subtituloDB"]        = $query["subtitulo"];
    $camposDB["autoriaDB"]          = $query["autoria"];
    $camposDB["descripcionDB"]      = $query["descripcion"];
    $camposDB["portadaDB"]          = $query["portada"];
    $camposDB["numPaginasDB"]       = $query["num_paginas"];
    $camposDB["editorialDB"]        = $query["editorial"];
    $camposDB["anhoPublicacionDB"]  = $query["anho_publicacion"];
    $camposDB["enlaceAPIDB"]        = $query["enlace_API"];
    $camposDB["estadoDB"]           = $query["estado"];
    $camposDB["fechaAdicionDB"]     = $query["fecha_adicion"];

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

  function getEstado() {
    return $this->estado;
  }

  function getFechaAdicion() {
    return $this->fechaAdicion;
  }

  /* MÉTODOS DE LA CLASE CONECTORES CON DB */
  function modificarLibroDB() {
    try {
      $query = $this->conexionDB->conn->prepare("UPDATE libros SET
        titulo            = :titulo,
        subtitulo         = :subtitulo,
        autoria           = :autoria,
        descripcion       = :descripcion,
        portada           = :portada,
        num_paginas       = :numPaginas,
        editorial         = :editorial,
        anho_publicacion  = :anhoPublicacion,
        estado            = :estado
      WHERE id = :id");

      $query->execute(array(
        ":titulo"           => $this->getTitulo(),
        ":subtitulo"        => $this->getSubtitulo(),
        ":autoria"          => $this->getAutoria(),
        ":descripcion"      => $this->getDescripcion(),
        ":portada"          => $this->getPortada(),
        ":numPaginas"       => $this->getNumPaginas(),
        ":editorial"        => $this->getEditorial(),
        ":anhoPublicacion"  => $this->getAnhoPublicacion(),
        ":estado"           => $this->getEstado(),
        ":id"               => $this->getId()
      ));
    }
    catch (PDOException $exception) {
      echo "Ocurrió un error al actualizar los datos del libro. ". $exception->getMessage();
    }
  }


  // --- Métodos complementarios ---
  function getEstadoTexto() {
    switch ($this->estado) {
      case 0:
        $estadoTexto = "Pendiente";
        break;
      case 1:
        $estadoTexto = "Leyendo";
        break;
      case 2:
        $estadoTexto = "Leído";
    }

    return $estadoTexto;
  }
}

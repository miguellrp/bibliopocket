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
  private $categorias;
  private $conexionDB;

  function __construct($id) {
    $this->conexionDB = new Conector;
    $this->id         = $id;
    $datosLibro = [];

    // Cuando se crea desde un form (Google API, libro desde 0...):
    if (isset($_POST["anhadir-libro"])) {
      $datosLibro = [
        "titulo"            => $_POST["titulo"],
        "subtitulo"         => $_POST["subtitulo"],
        "autoria"           => $_POST["autoria"],
        "descripcion"       => $_POST["descripcion"],
        "portada"           => $_POST["portada"],
        "numPaginas"        => $_POST["numPaginas"],
        "editorial"         => $_POST["editorial"],
        "anhoPublicacion"   => $_POST["anhoPublicacion"],
        "enlaceAPI"         => $_POST["enlaceAPI"]
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
      foreach($camposDB["categoriasDB"] as $categoriaDB) {
        array_push($this->categorias, $categoriaDB);
      }
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
  }

  private function getCamposDB() {
    $id = $this->getId();
    $camposDB = [];

    $query = $this->conexionDB->conn->prepare("SELECT * FROM libros WHERE id = :id");
    $query->execute(array(":id" => $id));
    $query = $query->fetch(PDO::FETCH_ASSOC);

    $querycategorias = $this->conexionDB->conn->prepare("SELECT categoria FROM libros_categorias WHERE id_libro = :id");
    $querycategorias->execute(array(":id" => $id));
    $categoriasDB = $querycategorias->fetchAll(PDO::FETCH_COLUMN);

    $camposDB["tituloDB"]           = $query["titulo"];
    $camposDB["subtituloDB"]        = $query["subtitulo"];
    $camposDB["autoriaDB"]          = $query["autoria"];
    $camposDB["descripcionDB"]      = $query["descripcion"];
    $camposDB["portadaDB"]          = $query["portada"];
    $camposDB["numPaginasDB"]       = $query["numPaginas"];
    $camposDB["editorialDB"]        = $query["editorial"];
    $camposDB["anhoPublicacionDB"]  = $query["anhoPublicacion"];
    $camposDB["enlaceAPIDB"]        = $query["enlaceAPI"];
    $camposDB["categoriasDB"]       = [];
    foreach($categoriasDB as $categoriaDB) {
      array_push($camposDB["categoriasDB"], $categoriaDB);
    }

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

  function getCategorias() {
    return $this->categorias;
  }
}
?>
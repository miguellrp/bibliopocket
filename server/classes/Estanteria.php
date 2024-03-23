<?php
include_once($_SERVER["DOCUMENT_ROOT"]."/bibliopocket/server/database/Conector.php");

class Estanteria {
  private $idUsuario;
  private $libros;
  private $conexionDB;


  function __construct($idUsuario) {
    $this->conexionDB = new Conector;

    $this->idUsuario = $idUsuario;
    $this->libros = [];

    foreach($this->getLibrosIDs() as $libroID) {
      array_push($this->libros, new Libro($libroID));
    }
  }

  // --- GETTERS ---
  function getIdUsuario() {
    return $this->idUsuario;
  }
  
  function getLibros() {
    return $this->libros;
  }


   /* MÉTODOS DE LA CLASE CONECTORES CON DB */
   private function getLibrosIDs() {
    try {
      $query = $this->conexionDB->conn->prepare("SELECT id FROM libros
        WHERE id_usuario = :userID");

      $query->execute(array(":userID" => $this->getIdUsuario()));
    }
    catch (PDOException $exception) {
      echo "Ocurrió un error al cargar la estantería. ". $exception->getMessage();
    }

    return $query->fetchAll(PDO::FETCH_COLUMN);
  }

  function registrarLibro($libro) {
    try {
      $query = $this->conexionDB->conn->prepare("INSERT INTO libros
        VALUES (:id, :titulo, :subtitulo, :autoria,
        :descripcion, :portada, :numPaginas, :editorial,
        :anhoPublicacion, :enlaceAPI, :estado, :fechaAdicion, :userID)");
      
      $query->execute(array(
        ":id"               => $libro->getId(),
        ":titulo"           => $libro->getTitulo(),
        ":subtitulo"        => $libro->getSubtitulo(),
        ":autoria"          => $libro->getAutoria(),
        ":descripcion"      => $libro->getDescripcion(),
        ":portada"          => $libro->getPortada(),
        ":numPaginas"       => $libro->getNumPaginas(),
        ":editorial"        => $libro->getEditorial(),
        ":anhoPublicacion"  => $libro->getAnhoPublicacion(),
        ":enlaceAPI"        => $libro->getEnlaceAPI(),
        ":estado"           => $libro->getEstado(),
        ":fechaAdicion"     => $libro->getFechaAdicion(),
        ":userID"           => $this->getIdUsuario()
      ));
    }
    catch (PDOException $exception) {
      echo "Ocurrió un error al registrar el libro. ". $exception->getMessage();
    }
  }

  function ordenarEstanteriaPorFechaAdicion() {
    usort($this->libros, function($libro1, $libro2) {
      return strtotime($libro2->getFechaAdicion()) - strtotime($libro1->getFechaAdicion());
    });
  }

  function getUltimasLecturas($numLibros) {
    $ultimosLibrosAnhadidos = [];
    $this->ordenarEstanteriaPorFechaAdicion();
    
    $numLibros = ($numLibros <= count($this->getLibros()))
      ? $numLibros
      : count($this->getLibros());

    for ($indiceLibro = 0; $indiceLibro < $numLibros; $indiceLibro++)
      array_push($ultimosLibrosAnhadidos, $this->getLibros()[$indiceLibro]);
    
    return $ultimosLibrosAnhadidos;
  }
}
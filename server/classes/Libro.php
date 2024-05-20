<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/server/classes/Categoria.php");
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
      if (isset($_POST["anhadir-libro"])) $this->id .= Util::anhadirDiferencia();
      
      $datosLibro = [
        "titulo"            => $_POST["titulo"],
        "subtitulo"         => $_POST["subtitulo"],
        "autoria"           => $_POST["autoria"],
        "descripcion"       => $_POST["descripcion"],
        "portada"           => !empty($_POST["portada"]) ? $_POST["portada"] : null,
        "numPaginas"        => !empty($_POST["numPaginas"]) ? $_POST["numPaginas"] : null,
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

  function render() {
    $categoriasLibroDB = Categoria::getCategoriasDe($this->getId());
    $categoriasLibroTags = "";
    
    foreach($categoriasLibroDB as $categoriaDB) {
      $categoriasLibroTags .= "<input type='hidden' name='categorias[]' value='".$categoriaDB."'>";
    }

    echo "
    <div class='libro'>
      <div class='portada-container'>
        <img src='".$this->getPortada()."' class='portada'>
        <img src='/client/assets/images/marcador-".strtolower($this->getEstadoTexto()).".svg' class='marcador'>
      </div>
      <div class='datos-libro'>
        <div class='cabecera'>
          <p class='titulo'>".$this->getTitulo()."</p>
          <p class='subtitulo'>".$this->getSubtitulo()."</p>
        </div>
        <hr>
        <p class='autoria'>".$this->getAutoria()."</p>
        <p class='editorial'>".$this->getEditorial()."</p>
        <p class='anho-publicacion'>".$this->getAnhoPublicacion()."</p>
        <div class='grupo-buttons-libro'>
          <svg xmlns='http://www.w3.org/2000/svg' class='icon eliminar' fill='var(--primary-color)' viewBox='0 0 256 256'><path d='M216,48H176V40a24,24,0,0,0-24-24H104A24,24,0,0,0,80,40v8H40a8,8,0,0,0,0,16h8V208a16,16,0,0,0,16,16H192a16,16,0,0,0,16-16V64h8a8,8,0,0,0,0-16ZM112,168a8,8,0,0,1-16,0V104a8,8,0,0,1,16,0Zm48,0a8,8,0,0,1-16,0V104a8,8,0,0,1,16,0Zm0-120H96V40a8,8,0,0,1,8-8h48a8,8,0,0,1,8,8Z'></path></svg>
          <svg xmlns='http://www.w3.org/2000/svg' class='icon modificar' fill='var(--primary-color)' viewBox='0 0 256 256'><path d='M227.31,73.37,182.63,28.68a16,16,0,0,0-22.63,0L36.69,152A15.86,15.86,0,0,0,32,163.31V208a16,16,0,0,0,16,16H92.69A15.86,15.86,0,0,0,104,219.31L227.31,96a16,16,0,0,0,0-22.63ZM51.31,160l90.35-90.35,16.68,16.69L68,176.68ZM48,179.31,76.69,208H48Zm48,25.38L79.31,188l90.35-90.35h0l16.68,16.69Z'></path></svg>
        </div>
        <form name='datosLibro' class='hidden'>
          <input type='hidden' name='id' value='".$this->getId()."'>
          <input type='hidden' name='titulo' value='".$this->getTitulo()."'>
          <input type='hidden' name='subtitulo' value='".$this->getSubtitulo()."'>
          <input type='hidden' name='descripcion' value='".$this->getDescripcion()."'>
          <input type='hidden' name='portada' value='".$this->getPortada()."'>
          <input type='hidden' name='autoria' value='".$this->getAutoria()."'>
          <input type='hidden' name='numPaginas' value='".$this->getNumPaginas()."'>
          <input type='hidden' name='editorial' value='".$this->getEditorial()."'>
          <input type='hidden' name='anhoPublicacion' value='".$this->getAnhoPublicacion()."'>
          <input type='hidden' name='enlaceAPI' value='".$this->getEnlaceAPI()."'>
          <input type='hidden' name='estado' value='".$this->getEstado()."'>
        </form>
        <form name='categorias-hidden' class='hidden'>
          $categoriasLibroTags
        </form>
      </div>
    </div>";
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
    return ($this->autoria != null) ? $this->autoria : "Autoría no disponible";
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
    return ($this->editorial != null) ? $this->editorial : "Editorial no disponible";
  }

  function getAnhoPublicacion() {
    return ($this->anhoPublicacion != null) ? $this->anhoPublicacion : "s.f.";
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

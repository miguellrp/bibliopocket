<?php
session_start();
include_once "../server/classes/Usuario.php";
include_once "../server/classes/Libro.php";

$conn = new Conector;

if (isset($_SESSION["usuarioActivo"]))
  $usuarioActivo = new Usuario($_SESSION["usuarioActivo"]["id"]);

if(!isset($_SESSION["estanteria"])) {
  $_SESSION["estanteria"] = [];
  $estanteriaDB = [];
}

if (isset($_POST["anhadir-libro"])) {
  $libro = [
    "id"                => $_POST["id"],
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
  
  // Si el libro a añadir no está guardado todavía, se añade a su estantería:
  if (!$usuarioActivo->libroGuardado($libro)) $usuarioActivo->registrarLibro($libro);
  header("Location: index.php");
}

if (isset($_POST["modificar-libro"])) {
  
}

if (isset($_POST["eliminar"])) $conn->eliminarLibro($_POST["idLibroEstante"]);

?>
<!DOCTYPE html>
<html lang="es-ES">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bibliopocket 📘 Mi estantería</title>
  <link rel="stylesheet" href="/bibliopocket/client/styles/globals.css">
  <link rel="stylesheet" href="./styles.css">
</head>

<body>
  <?php if (!isset($usuarioActivo)): ?>
    <div class="caja-contenido">
      <h2>👀 ¿A dónde quieres ir?</h2>
      <h3>Parece que primero tienes que <a href="../index.php">iniciar sesión</a></h3>
    </div>
  <?php else: ?>
    <custom-header pagina-activa="mi-estanteria"></custom-header>
    <h1>Estantería de
      <?= $usuarioActivo->getNombreUsuario() ?>
    </h1>
    <div class="grupo-buttons">
      <custom-button
        id="busqueda-button"
        data-contenido="Añadir libro 🔎"
        background-color="var(--secondary-contrast-color)"
        font-color="var(--secondary-lighten-color)"
      >
      </custom-button>
      <custom-button
        id="busqueda-button"
        data-contenido="Añadir libro desde 0 ➕"
        background-color="var(--secondary-lighten-color)"
        font-color="var(--secondary-contrast-color)"
      >
      </custom-button>
    </div>

    <!-- Modal Búsqueda libro API -->
    <dialog class="modal" id="busqueda-libro-modal">
      <h2>AÑADIR NUEVO LIBRO 🔎</h2>
      <label for="buscador-libro">Buscar por título:
        <input type="search" id="buscador-libro" placeholder="Cien años de soledad" class="input-buscador">
      </label>
      <div class="resultados-busqueda">
        <img src="/bibliopocket/client/assets/images/torre-libros.svg">
      </div>
    </dialog>
    
    <div class="estanteria">
      <?php
        $estanteriaUsuario = [];
        foreach($usuarioActivo->getLibrosIDs() as $libroID) {
          array_push($estanteriaUsuario, new Libro($libroID));
        }

        foreach($estanteriaUsuario as $libro) {
          echo "<div class='libro'>
            <img src='".$libro->getPortada()."' class='portada'>
            <div class='datos-libro'>
              <div class='cabecera'>
                <p class='titulo'>".$libro->getTitulo()."</p>
                <p class='subtitulo'>".$libro->getSubtitulo()."</p>
              </div>
              <hr>
              <p class='autoria'>".$libro->getAutoria()."</p>
              <p class='editorial'>".$libro->getEditorial()."</p>
              <p class='anho-publicacion'>".$libro->getAnhoPublicacion()."</p>
              <div class='grupo-buttons'>
                <svg xmlns='http://www.w3.org/2000/svg' class='icon eliminar' fill='var(--primary-color)' viewBox='0 0 256 256'><path d='M216,48H176V40a24,24,0,0,0-24-24H104A24,24,0,0,0,80,40v8H40a8,8,0,0,0,0,16h8V208a16,16,0,0,0,16,16H192a16,16,0,0,0,16-16V64h8a8,8,0,0,0,0-16ZM112,168a8,8,0,0,1-16,0V104a8,8,0,0,1,16,0Zm48,0a8,8,0,0,1-16,0V104a8,8,0,0,1,16,0Zm0-120H96V40a8,8,0,0,1,8-8h48a8,8,0,0,1,8,8Z'></path></svg>
                <svg xmlns='http://www.w3.org/2000/svg' class='icon modificar' fill='var(--primary-color)' viewBox='0 0 256 256'><path d='M227.31,73.37,182.63,28.68a16,16,0,0,0-22.63,0L36.69,152A15.86,15.86,0,0,0,32,163.31V208a16,16,0,0,0,16,16H92.69A15.86,15.86,0,0,0,104,219.31L227.31,96a16,16,0,0,0,0-22.63ZM51.31,160l90.35-90.35,16.68,16.69L68,176.68ZM48,179.31,76.69,208H48Zm48,25.38L79.31,188l90.35-90.35h0l16.68,16.69Z'></path></svg>
              </div>
              <form name='datosLibro'>
                <input type='hidden' name='id' value='".$libro->getId()."'>
                <input type='hidden' name='titulo' value='".$libro->getTitulo()."'>
                <input type='hidden' name='subtitulo' value='".$libro->getSubtitulo()."'>
                <input type='hidden' name='autoria' value='".$libro->getAutoria()."'>
                <input type='hidden' name='editorial' value='".$libro->getEditorial()."'>
                <input type='hidden' name='anhoPublicacion' value='".$libro->getAnhoPublicacion()."'>
              </form>
            </div>
          </div>";
        }
      ?>
    </div>

  <?php endif; ?>
  <script src="./script.js"></script>
  <script src="../client/components/CustomHeader.js"></script>
  <script src="../client/components/CustomButton.js"></script>
  <script src="../client/handlers/themeHandler.js"></script>
  <script src="../client/handlers/APIBooksHandler.js" type="module"></script>
</body>

</html>
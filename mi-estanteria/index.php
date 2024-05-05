<?php
session_start();
require_once($_SERVER["DOCUMENT_ROOT"]."/server/classes/Estanteria.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/server/classes/Libro.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/server/classes/Usuario.php");

// -- CONSTANTES GLOBALES --
$cargaLibrosInicial = 8;
$conn = new Conector;

// Para controlar feedback en cambios hechos por la persona usuaria:
if (!isset($_SESSION["toast"])) $_SESSION["toast"]["showToast"] = false;


if (isset($_SESSION["usuarioActivo"])) 
  $usuarioActivo = new Usuario($_SESSION["usuarioActivo"]["id"]);

if (isset($_POST["anhadir-libro"]) || isset($_POST["anhadir-nuevo-libro"])) {
  $idLibro = isset($_POST["anhadir-libro"]) ? $_POST["id"] : uniqid();
  $nuevoLibro = new Libro($idLibro);
  $estanteriaDB = new Estanteria($usuarioActivo->getId());

  // Si el libro a añadir no está guardado todavía, se añade a su estantería:
  if (!$usuarioActivo->esLibroGuardado($nuevoLibro)) {
    $estanteriaDB->registrarLibro($nuevoLibro);

    if (isset($_POST["categorias-tagify-nuevo-libro"])) {
      $categorias = $_POST["categorias-tagify-nuevo-libro"];
  
      foreach($categorias as $nombreCategoria) {
        $categoria = new Categoria($nombreCategoria);
        
        if ($categoria->sinAsociarEn($idLibro)) $categoria->asociarA($idLibro);
      }
    }

    $_SESSION["toast"]["tipo"] = "ok";
    $_SESSION["toast"]["mensaje"] = "Se ha añadido el libro a tu estantería";
  } else {
    $_SESSION["toast"]["tipo"] = "info";
    $_SESSION["toast"]["mensaje"] = "Ya has añadido este libro a tu estantería";
  }
  $_SESSION["toast"]["showToast"] = true;

  header("Location: index.php");
  session_write_close();
}

if (isset($_POST["modificar-libro"])) {
  $idLibro = $_POST["idLibroEstante"];
  $libroSeleccionado = new Libro($idLibro);
  $libroSeleccionado->modificarLibroDB();

  $categoriasDB = Categoria::getCategoriasDe($idLibro);
  if (isset($_POST["categorias-tagify-$idLibro"])) {
    $categorias = $_POST["categorias-tagify-$idLibro"];

    foreach($categorias as $nombreCategoria) {
      $categoria = new Categoria($nombreCategoria);
      
      if ($categoria->sinAsociarEn($idLibro)) $categoria->asociarA($idLibro);
    }

    foreach($categoriasDB as $nombreCategoriaDB) {
      if (!in_array($nombreCategoriaDB, $categorias)) {
        $categoriaDB = new Categoria($nombreCategoriaDB);
        $categoriaDB->eliminarCategoriaDe($idLibro);
      }
    }
  } else {
    Categoria::vaciarCategoriasDe($idLibro);
  }

  $_SESSION["toast"]["tipo"] = "ok";
  $_SESSION["toast"]["mensaje"] = "Se han actualizado los datos del libro correctamente";
  $_SESSION["toast"]["showToast"] = true;

  header("Location: index.php");
  session_write_close();
}

if (isset($_POST["eliminar-libro"])) {
  $conn->eliminarLibro($_POST["idLibroEstante"]);

  $_SESSION["toast"]["tipo"] = "ok";
  $_SESSION["toast"]["mensaje"] = "Se ha eliminado el libro de tu estantería";
  $_SESSION["toast"]["showToast"] = true;


  header("Location: index.php");
  session_write_close();
}

if (isset($usuarioActivo)) {
  $estanteriaUsuario = new Estanteria($usuarioActivo->getId());
  $idsLibrosEstanteria = $estanteriaUsuario->getLibrosIds(0, $cargaLibrosInicial);
}
?>
<!DOCTYPE html>
<html lang="es-ES">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BiblioPocket | Mi estantería</title>
  <link rel="icon" type="image/png" href="/client/assets/images/favicon.png">
  <link rel="stylesheet" href="/client/styles/globals.css">
  <link rel="stylesheet" href="./styles.css">
  <script src="/client/components/CustomButton.js"></script>
  <script src="/client/components/CustomHeader.js"></script>
  <script src="/client/components/CustomImageUploader.js"></script>
  <script src="/client/components/CustomTagify.js"></script>
  <script src="/client/components/CustomToast.js"></script>
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
        id="nuevo-libro-button"
        data-contenido="Crear libro desde 0 ➕"
        background-color="var(--secondary-lighten-color)"
        font-color="var(--secondary-contrast-color)"
      >
      </custom-button>
    </div>
    <main>
    <?php if (!empty($idsLibrosEstanteria)): ?>
      <details class="filtros">
        <summary>Filtrar por:</summary>
        <div class="grupo-filtros">
          <label>Filtrar por estado:
            <div class="grupo-estados-libro">
              <label for="pendiente-filtro">
                <input type="checkbox" id="pendiente-filtro" name="estados-filtro[]" value="0" checked>
                Pendiente
              </label>
              <label for="leyendo-filtro">
                <input type="checkbox" id="leyendo-filtro" name="estados-filtro[]" value="1" checked>
                Leyendo
              </label>
              <label for="leido-filtro">
                <input type="checkbox" id="leido-filtro" name="estados-filtro[]" value="2" checked>
                Leído
              </label>
            </div>
          </label>

          <label for="sub-titulo-filtro">Título/subtítulo:
            <input type="text" id="sub-titulo-filtro" name="sub-titulo-filtro">
          </label>
          
          <label for="autoria-filtro">Autoría:
            <input type="text" id="autoria-filtro" name="autoria-filtro">
          </label>
          
          <label for="editorial-filtro">Editorial:
            <input type="text" id="editorial-filtro" name="editorial-filtro">
          </label>

          <label for="anho-publicacion-filtro">Año de publicación:
            <input type="text" id="anho-publicacion-filtro" name="anho-publicacion-filtro">
          </label>

          <label>Categorías:
            <custom-tagify tipo-filtro="true"></custom-tagify>
            <div class="categorias-tags"></div>
          </label>
        </div>
      </details>
      <?php endif; ?>
      <section class="estanteria" id-usuario=<?= $usuarioActivo->getId()?>>
        <?php
          if (empty($idsLibrosEstanteria)) {
            echo "<div class='empty-bookshelf'>
              <img src='/client/assets/images/empty-bookshelf.svg' alt='Una persona agarrando un libro cerca de una estantería de libros parcialmente vacía.'>
              <small>Tu estantería está vacía, ¡añade libros buscándolos en Google Books o creándolos desde 0!</small>
            </div>";
          } else {
            foreach($idsLibrosEstanteria as $idLibro) {
              $libro = new Libro($idLibro);
              $libro->render();
            }
            echo "<div class='filters-not-found'>
              <img src='/client/assets/images/filters-not-found.svg' alt='Una persona buscando entre diferentes opciones que le salen en una pantalla.'>
              <small>No hay libros en tu estantería con los filtros aplicados</small>
            </div>";
          }
        ?>
      </section>
    </main>

  <?php endif; ?>
    <custom-toast></custom-toast>

    <?php
      $showToast = $_SESSION["toast"]["showToast"];
        
      if ($showToast) {
        $tipo = $_SESSION["toast"]["tipo"];
        $mensaje = $_SESSION["toast"]["mensaje"];

        echo '<script>
          const toast = document.querySelector("custom-toast");
          toast.setMensaje("'.$mensaje.'");
          toast.setTipo("'.$tipo.'");
      
          toast.showToast();
        </script>';
      }
      unset($_SESSION["toast"]);
    ?>
  
  <script src="/client/handlers/APIBooksHandler.js" type="module"></script>
  <script src="/client/handlers/filtradorEstanteriaHandler.js"></script>
  <script src="/client/handlers/infiniteScrollHandler.js" type="module"></script>
  <script src="/client/handlers/listenersBotonesLibroHandler.js" type="module"></script>
  <script src="/client/handlers/themeHandler.js"></script>
  <script src="./script.js" type="module"></script>
</body>

</html>
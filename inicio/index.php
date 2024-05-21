<?php
session_start();
require_once($_SERVER["DOCUMENT_ROOT"]."/server/classes/Estanteria.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/server/classes/Libro.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/server/classes/Usuario.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/server/handlers/Util.php");


// ⚠️ REVISABLE (establecer último login sólo una vez y no cada vez que refresque la página de inicio):
// AÑADIR TAREA CRON a servidor para que vaya eliminando usuarios de la BBDD con último login hace más de 30 días p.ej.
if (isset($_SESSION["usuarioActivo"])) {
  $usuarioActivo = new Usuario($_SESSION["usuarioActivo"]["id"]);
  $usuarioActivo->setUltimoLoginDB();

  $estanteriaUsuario = new Estanteria($usuarioActivo->getId());
  $ultimosLibros = $estanteriaUsuario->getUltimosLibrosAnhadidos(5);
}
?>
<!DOCTYPE html>
<html lang="es-ES">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BiblioPocket | Inicio</title>
  <link rel="icon" type="image/png" href="/client/assets/images/favicon.png">
  <link rel="stylesheet" href="/client/styles/globals.css">
  <link rel="stylesheet" href="./styles.css">
  <script src="/client/components/CustomHeader.js"></script>
</head>

<body>
  <?php if (!isset($usuarioActivo)): ?>
    <div class="caja-contenido">
      <h2>👀 ¿A dónde quieres ir?</h2>
      <h3>Parece que primero tienes que <a href="/index.php">iniciar sesión</a></h3>
    </div>
  <?php elseif($usuarioActivo->estaBloqueado()): Util::mostrarPantallaUsuarioBloqueado($usuarioActivo->getId()) ?>
  <?php else: ?>
    <custom-header pagina-activa="inicio"></custom-header>
    <h2>👋 ¡Hola,
      <a class="username-title" href="/mi-perfil">
        <?= $usuarioActivo->getNombreUsuario() ?>
      </a>!
      </h2>
      <?php if (count($ultimosLibros) <= 0): ?>
        <h3>No hay nada que mostrar por aquí todavía 👀</h3>
        <small>Todavía no has añadido ningún libro</small>
      <?php else: ?>
        <h3>Tus últimos libros añadidos</h3>
        <div class="ultimos-libros">
        <?php
            foreach($ultimosLibros as $idLibro) {
              $libro = new Libro($idLibro);
              echo "<article class='libro'>
                <div class='portada-container'>
                  <img src='".$libro->getPortada()."' class='portada'>
                </div>
                <strong class='titulo' title='".$libro->getTitulo()."'>".$libro->getTitulo()."</strong>
                <small>".$libro->getFechaAdicion()."</small>
              </article>";
            }
          ?>
        </div>

      <?php endif; ?>
  <?php endif; ?>
  <script src="/client/handlers/themeHandler.js"></script>
</body>

</html>
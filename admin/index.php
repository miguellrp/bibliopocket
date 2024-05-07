<?php
session_start();
include_once $_SERVER['DOCUMENT_ROOT']."/server/classes/Admin.php";

if (isset($_SESSION["adminActivo"])) {
  $adminActivo = new Admin($_SESSION["adminActivo"]["idAdmin"]);
}
?>
<!DOCTYPE html>
<html lang="es-ES">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BiblioPocket | Admin</title>
    <link rel="icon" type="image/png" href="/client/assets/images/favicon.png">
    <link rel="stylesheet" href="/client/styles/globals.css">
  </head>

  <body>
    <?php if (!isset($adminActivo)): ?>
      <div class="caja-contenido">
        <h2>👀 ¿A dónde quieres ir?</h2>
        <h3>Parece que primero tienes que <a href="/index.php">iniciar sesión</a></h3>
      </div>
    <?php else: ?>
      <h2>🔐 Panel de admin [<?=$adminActivo->getNombreAdmin() ?>]</h2>
  <?php endif; ?>
  <script src="/client/handlers/themeHandler.js"></script>
  </body>
</html>
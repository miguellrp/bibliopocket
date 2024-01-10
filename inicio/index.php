<?php
session_start();
include_once "../server/classes/Usuario.php";

if (isset($_SESSION["nombreUsuario"]) && !isset($_SESSION["usuarioActivo"])) {
  $usuarioID = $conn->getUsuarioActualID($_SESSION["nombreUsuario"]);
  $usuarioActivo = new Usuario($usuarioID);
  $_SESSION["usuarioActivo"] = array(
    "id"              => $usuarioActivo->getId(),
    "nombreUsuario"   => $usuarioActivo->getNombreUsuario(),
    "correo"          => $usuarioActivo->getEmail(),
    "userPic"         => $usuarioActivo->getUserPic(),
    "ultimoLogin"     => $usuarioActivo->getUltimoLogin()
  );
}

// ⚠️ REVISABLE (establecer último login sólo una vez y no cada vez que refresque la página de inicio):
// AÑADIR TAREA CRON a servidor para que vaya eliminando usuarios de la BBDD con último login hace más de 30 días p.ej.
if (isset($_SESSION["usuarioActivo"])) {
  $usuarioActivo = new Usuario($_SESSION["usuarioActivo"]["id"]);
  $usuarioActivo->setUltimoLoginDB();
}
?>
<!DOCTYPE html>
<html lang="es-ES">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bibliopocket 📙 Home</title>
  <link rel="stylesheet" href="/bibliopocket/client/styles/globals.css">
  <link rel="stylesheet" href="./styles.css">
  <script src="../client/components/CustomHeader.js"></script>
</head>

<body>
  <?php if (!isset($_SESSION["usuarioActivo"])): ?>
    <div class="caja-contenido">
      <h2>👀 ¿A dónde quieres ir?</h2>
      <h3>Parece que primero tienes que <a href="../index.php">iniciar sesión</a></h3>
    </div>
  <?php else: ?>
    <custom-header pagina-activa="inicio"></custom-header>
    <h2>Bienvenid@ de nuevo,
      <a class="username-title" href="/bibliopocket/mi-perfil">
        <?= $_SESSION["usuarioActivo"]["nombreUsuario"] ?>
      </a>
    </h2>
    <h3>Tus últimas lecturas</h3>
  <?php endif; ?>
  <script src="../client/handlers/themeHandler.js"></script>
</body>

</html>
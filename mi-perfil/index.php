<?php
session_start();
include_once "../server/classes/Usuario.php";

$usuarioActivo = new Usuario($_SESSION["usuarioActivo"]["id"]);
?>
<!DOCTYPE html>
<html lang="es-ES">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bibliopocket 📗 Mi perfil</title>
  <link rel="stylesheet" href="/bibliopocket/client/styles/globals.css">
  <link rel="stylesheet" href="styles.css">
</head>

<body>
  <?php if (!isset($usuarioActivo)): ?>
    <div class="caja-contenido">
      <h2>👀 ¿A dónde quieres ir?</h2>
      <h3>Parece que primero tienes que <a href="../index.php">iniciar sesión</a></h3>
    </div>
  <?php else: ?>
    <custom-header pagina-activa="mi-perfil"></custom-header>
    <form class="datos-user" action="" method="POST">
      <section class="username-pic">
        <div class="wrap-user-pic">
            <img src="/bibliopocket/client/assets/images/user-pics/<?= $usuarioActivo->getUserPicPathDB() ?>"
            class="user-pic" alt="Foto de perfil de <?= $usuarioActivo->getNombreUsuario() ?>">
            <div class="userpic-upload">
              <label for="userpic-input">
                <img src="/bibliopocket/client/assets/images/pencil-icon.png" class="lapiz-icon">
              </label>
              <input id="userpic-input" type="file" name="userpic" />
            </div>   
        </div>
        <input type="text" class="username" name="username" value="<?= $usuarioActivo->getNombreUsuario() ?>">
      </section>
      <label>Modo claro/oscuro:
        <img src="/bibliopocket/client/assets/images/moon-icon.png" class="icon darklight sun" tabindex="1"
          alt="Símbolo de una media luna (menguante) para representar el modo oscuro de la web">
      </label>
      <input type="submit" value="GUARDAR CAMBIOS" name="modificacion-datos-user">
    </form>

  <?php endif; ?>
  <script src="../client/components/CustomHeader.js"></script>
  <script src="../client/handlers/themeHandler.js"></script>
</body>

</html>
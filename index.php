<?php
include_once "server/classes/Usuario.php";
session_start();

$conn = new Conector();
$loginValido = true;


if (isset($_POST["log-out"]))
  session_destroy();

// Comprobación login
if (isset($_POST["login-check"])) {
  $nombreUsuario = $_POST["nombre-usuario-log"];
  $contrasenha = $_POST["contrasenha-log"];
  $usuarioTemp = new Usuario("idTemporal",$nombreUsuario, $contrasenha);


  if ($usuarioTemp->loginDB()) {
    $usuarioID = $conn->getUsuarioActualID($usuarioTemp->getNombreUsuario());
    $usuarioActivo = new Usuario($usuarioID);
    $_SESSION["usuarioActivo"] = array(
      "id"              => $usuarioActivo->getId(),
      "nombreUsuario"   => $usuarioActivo->getNombreUsuario(),
      "correo"          => $usuarioActivo->getEmail(),
      "userPic"         => $usuarioActivo->getUserPic(),
      "ultimoLogin"     => $usuarioActivo->getUltimoLogin()
    );

    $loginValido = true;
    header("location: inicio/");
  }
  else {
    $loginValido = false;
    session_destroy();
  }
}

// Comprobación registro
if (isset($_POST["registro-check"]) || isset($_POST["codigo"]) && $_COOKIE["codigoGenerado"] != $_POST["codigo"]) {
  $codigoGenerado = rand(1000, 9999);
  $tiempoExpiracionCodigo = time() + 300;
  setcookie("codigoGenerado", $codigoGenerado, $tiempoExpiracionCodigo);

  if (isset($_POST["registro-check"])) {
    $usuario = $_POST["nombre-usuario-reg"];
    $contrasenha = $_POST["contrasenha-reg"];
    $email = $_POST["email-usuario-reg"];
  } else {
    $usuario = $_POST["usuario"];
    $contrasenha = $_POST["contrasenha"];
    $email = $_POST["email"];
  }

  echo "
    <dialog class='modal' id='confirmacion-registro' open>
      <form action='".$_SERVER['PHP_SELF']."' method='POST'>
        <label for='codigo'>Introduce el código enviado a tu correo: </label>
        <input type='text' class='input-text' minlength=4 maxlength=4 id='codigo' name='codigo' required>
        <input type='submit' class='submit-btn' name='confirm-registro-check' value='Enviar'>
        <input type='hidden' name='usuario' value='".$usuario."'>
        <input type='hidden' name='contrasenha' value='".$contrasenha."'>
        <input type='hidden' name='email' value='".$email."'>
      </form>
    </dialog>
  ";

  // TO-DO: Configuración CORREO ✉️
  // $para      = $email;
  // $titulo    = "BiblioPocket 📚 Correo de confirmación";
  // $mensaje   = "Hola, ".$usuario."!\r\nAquí está tu código de confirmación: ".$codigoGenerado;
  // $cabeceras = 'From: bibliopocket@correo.com' . "\r\n" .
  //     'Reply-To: bibliopocket@correo.com' . "\r\n" .
  //     'X-Mailer: PHP/' . phpversion();

  // mail($para, $titulo, $mensaje, $cabeceras);
}

// Confirmación registro (email válido)
if (isset($_POST["confirm-registro-check"]) && $_COOKIE["codigoGenerado"] == $_POST["codigo"]) {
  $nombreUsuario = $_POST["usuario"];
  $contrasenha = $_POST["contrasenha"];
  $email = $_POST["email"];
  $usuarioRegistrado = new Usuario("idTemporalRegistro", $nombreUsuario, $contrasenha, $email);

  if ($usuarioRegistrado->registrarUsuarioDB()) {
    $usuarioRegistrado = new Usuario($conn->getUsuarioActualID($usuarioRegistrado->getNombreUsuario()));
    $_SESSION["usuarioActivo"] = array(
      "id"              => $usuarioRegistrado->getId(),
      "nombreUsuario"   => $usuarioRegistrado->getNombreUsuario(),
      "correo"          => $usuarioRegistrado->getEmail(),
      "userPic"         => $usuarioRegistrado->getUserPic(),
      "ultimoLogin"     => $usuarioRegistrado->getUltimoLogin()
    );
    
    header("location: inicio/");
  }
}
?>

<!DOCTYPE html>
<html lang="es-ES">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BiblioPocket</title>
  <link rel="icon" type="image/png" href="/bibliopocket/client/assets/images/favicon.png">
  <link rel="stylesheet" href="client/styles/loginPage.css">
  <script src="client/components/CustomButton.js"></script>
</head>

<body>
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      <?php if (!$loginValido): ?>
        let loginModal = document.getElementById("login");
        loginModal.showModal();
      <?php endif; ?>
    });
  </script>
  <img src="client/assets/images/moon-icon.png" class="icon darklight sun" tabindex="1"
    alt="Símbolo de una media luna (menguante) para representar el modo oscuro de la web">
  <h1>BiblioPocket</h1>
  <!-- Modal Registro -->
  <dialog class="modal" id="registro">
    <form id="form-registro" action="<?php $_SERVER['PHP_SELF'] ?>" method="POST">
      <label for="email-usuario-reg">* Correo electrónico:</label>
      <input type="email" class="input-text input-registro" id="email-usuario-reg" name="email-usuario-reg" required>
      <label for="nombre-usuario-reg">* Nombre de usuario:</label>
      <input type="text" class="input-text input-registro" id="nombre-usuario-reg" name="nombre-usuario-reg" required>
      <label for="contrasenha-reg">* Contraseña:</label>
      <input type="password" class="input-text input-registro" id="contrasenha-reg" name="contrasenha-reg" required>
      <label for="confirm-contrasenha">* Repetir contraseña:</label>
      <input type="password" class="input-text input-registro" id="confirm-contrasenha" required>
      <small class="notificacion-registro">Faltan campos requeridos por cubrir (*)</small>
      <input class="submit-btn" type="submit" value="CREAR CUENTA" name="registro-check" disabled>
    </form>
  </dialog>

  <!-- Modal LogIn -->
  <dialog class="modal" id="login">
    <form action="<?php $_SERVER['PHP_SELF'] ?>" method="POST">
      <label for="nombre-usuario-log">Nombre de usuario:</label>
      <input type="text" class="input-text" id="nombre-usuario-log" name="nombre-usuario-log" required>
      <label for="contrasenha-log">Contraseña:</label>
      <input type="password" class="input-text" id="contrasenha-log" name="contrasenha-log" required>
      <input class="submit-btn" type="submit" value="INICIAR SESIÓN" name="login-check">
      <?php if (!$loginValido)
        echo "<p class='warning'>El nombre de usuario o la contraseña son incorrectos</p>" ?>
    </form>
  </dialog>

  <!-- SVG by: https://www.svgrepo.com -->
  <svg class="book" width="150" height="150" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path opacity="0.5" class="color-book"
      d="M12 20.0283V18H8L8 20.0283C8 20.3054 8 20.444 8.09485 20.5C8.18971 20.556 8.31943 20.494 8.57888 20.3701L9.82112 19.7766C9.9089 19.7347 9.95279 19.7138 10 19.7138C10.0472 19.7138 10.0911 19.7347 10.1789 19.7767L11.4211 20.3701C11.6806 20.494 11.8103 20.556 11.9051 20.5C12 20.444 12 20.3054 12 20.0283Z"
      fill="#1C274D"></path>
    <path class="color-book"
      d="M8 18H7.42598C6.34236 18 5.96352 18.0057 5.67321 18.0681C5.15982 18.1785 4.71351 18.4151 4.38811 18.7347C4.27837 18.8425 4.22351 18.8964 4.09696 19.2397C3.97041 19.5831 3.99045 19.7288 4.03053 20.02C4.03761 20.0714 4.04522 20.1216 4.05343 20.1706C4.16271 20.8228 4.36259 21.1682 4.66916 21.4142C4.97573 21.6602 5.40616 21.8206 6.21896 21.9083C7.05566 21.9986 8.1646 22 9.75461 22H14.1854C15.7754 22 16.8844 21.9986 17.7211 21.9083C18.5339 21.8206 18.9643 21.6602 19.2709 21.4142C19.5774 21.1682 19.7773 20.8228 19.8866 20.1706C19.9784 19.6228 19.9965 18.9296 20 18H12V20.0283C12 20.3054 12 20.444 11.9051 20.5C11.8103 20.556 11.6806 20.494 11.4211 20.3701L10.1789 19.7767C10.0911 19.7347 10.0472 19.7138 10 19.7138C9.95279 19.7138 9.9089 19.7347 9.82112 19.7766L8.57888 20.3701C8.31943 20.494 8.18971 20.556 8.09485 20.5C8 20.444 8 20.3054 8 20.0283V18Z"
      fill="#1C274D"></path>
    <path opacity="0.5" class="color-book"
      d="M4.72718 2.73332C5.03258 2.42535 5.46135 2.22456 6.27103 2.11478C7.10452 2.00177 8.2092 2 9.7931 2H14.2069C15.7908 2 16.8955 2.00177 17.729 2.11478C18.5387 2.22456 18.9674 2.42535 19.2728 2.73332C19.5782 3.0413 19.7773 3.47368 19.8862 4.2902C19.9982 5.13073 20 6.24474 20 7.84202L20 18H7.42598C6.34236 18 5.96352 18.0057 5.67321 18.0681C5.15982 18.1785 4.71351 18.4151 4.38811 18.7347C4.27837 18.8425 4.22351 18.8964 4.09696 19.2397C4.02435 19.4367 4 19.5687 4 19.7003V7.84202C4 6.24474 4.00176 5.13073 4.11382 4.2902C4.22268 3.47368 4.42179 3.0413 4.72718 2.73332Z"
      fill="#1C274D"></path>
    <path class="color-book"
      d="M7.25 7C7.25 6.58579 7.58579 6.25 8 6.25H16C16.4142 6.25 16.75 6.58579 16.75 7C16.75 7.41421 16.4142 7.75 16 7.75H8C7.58579 7.75 7.25 7.41421 7.25 7Z"
      fill="#1C274D"></path>
    <path class="color-book"
      d="M8 9.75C7.58579 9.75 7.25 10.0858 7.25 10.5C7.25 10.9142 7.58579 11.25 8 11.25H13C13.4142 11.25 13.75 10.9142 13.75 10.5C13.75 10.0858 13.4142 9.75 13 9.75H8Z"
      fill="#1C274D"></path>
  </svg>

  <h2>Tu biblioteca personal en tu bolsillo digital</h2>
  <div>
    <custom-button
      id="registro-button"
      data-contenido="CREAR CUENTA"
      background-color="var(--primary-color)"
      font-color="var(--secondary-lighten-color)">
    </custom-button>
    <custom-button
      id="login-button"
      data-theme=(
      data-contenido="INICIAR SESIÓN"
      background-color="var(--secondary-lighten-color)"
      font-color="var(--primary-color)">
    </custom-button>
  </div>
  <small class="footer-inicio">Icons by: <a href="https://phosphoricons.com/" target="_blank">Phosphor Icons</a></small>
  <script src="client/handlers/registroHandler.js"></script>
  <script src="client/handlers/landingModalsHandler.js"></script>
  <script src="client/handlers/themeHandler.js"></script>
</body>

</html>
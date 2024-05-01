let groupModals = document.querySelectorAll(".modal");

/* Se proporciona la funcionalidad de cerrar el <dialog> al clickar fuera de
su recuadro a todos aquellos elementos que pertenezcan a la clase "modal" */
for (let modal of groupModals) {
  modal.addEventListener("mousedown", event => {
    let modalBounds = modal.getBoundingClientRect();
    if ((event.clientX < modalBounds.left || event.clientX > modalBounds.right)
      || (event.clientY < modalBounds.top || event.clientY > modalBounds.bottom)) {
      if (modal.id == "login" && modal.innerHTML.indexOf("recuperacion-contrasenha") != -1)
        restablecerModalLogin();
      modal.close();
    }
  });
}

/* Se asocian los modales con los botones que ejecutarán su showModal correspondiente */
// Modal de Registro
let modalRegistro = document.getElementById("registro");
let registroButton = document.getElementById("registro-button");

registroButton.addEventListener("click", () => openModal(modalRegistro));

// Modal de Iniciar Sesión
let modalLogin = document.getElementById("login");
let loginButton = document.getElementById("login-button");

loginButton.addEventListener("click", () => openModal(modalLogin));
prepararRecuperacionCuentaModal();


/* --- FUNCIONES COMPLEMENTARIAS --- */
function openModal (modal) {
  modal.showModal();
}

function prepararRecuperacionCuentaModal () {
  let recuperarCuentaLink = modalLogin.querySelector("small");

  if (recuperarCuentaLink == null) {
    const submitButtonLogin = document.querySelector("input[name=login-check]");
    recuperarCuentaLink = document.createElement("small");
    const formModalLogin = modalLogin.querySelector("form");

    formModalLogin.insertBefore(recuperarCuentaLink, submitButtonLogin);
  }

  recuperarCuentaLink.textContent = "He olvidado mi contraseña";
  recuperarCuentaLink.addEventListener("click", () => {
    modalLogin.innerHTML = `
    <form action="" method="POST">
      <label for="correo-recuperacion">Correo electrónico:</label>
      <input type="email" class="input-text" id="correo-recuperacion" name="correo-recuperacion" required>
      <input class="submit-btn" type="submit" value="ENVIAR NUEVA CONTRASEÑA" name="recuperacion-contrasenha">
    </form>
    `;
  });
}

function restablecerModalLogin () {
  modalLogin.innerHTML = `
    <form action="" method="POST">
      <label for="nombre-usuario-log">Nombre de usuario:</label>
      <input type="text" class="input-text" id="nombre-usuario-log" name="nombre-usuario-log" required>
      <label for="contrasenha-log">Contraseña:</label>
      <input type="password" class="input-text" id="contrasenha-log" name="contrasenha-log" required>
      <input class="submit-btn" type="submit" value="INICIAR SESIÓN" name="login-check">
    </form>
  `;

  prepararRecuperacionCuentaModal();
}
const navButtons = document.querySelectorAll(".nav-perfil input");
const opcionesConfiguracionTags = document.querySelectorAll(".configuracion.container li");


prepararListeners();

/* --- FUNCIÓN "MAIN" --- */
function prepararListeners () {
  navButtons.forEach((button) => button.addEventListener("click", prepararSeccionActivaListener));

  prepararCambiarCorreoListener();
  prepararCambiarContrasenhaListener();
  prepararRestablecerCuentaListener();
  prepararEliminarCuentaListener();
}

function prepararSeccionActivaListener () {
  const detallesContainer = document.querySelector(".detalles-cuenta");
  const configuracionContainer = document.querySelector(".configuracion");


  if (this == navButtons[0]) {
    detallesContainer.setAttribute("active", "");
    configuracionContainer.removeAttribute("active");
    navButtons[1].removeAttribute("active");
  }
  else {
    configuracionContainer.setAttribute("active", "");
    detallesContainer.removeAttribute("active");
    navButtons[0].removeAttribute("active");
  }
  this.setAttribute("active", "");
}

function prepararCambiarCorreoListener () {
  const correoUsuario = document.querySelector("input[name='correo']").value;
  const formCambioCorreo = /* html */ `
    <form name="cambio-correo" action="" method="POST" autocomplete="off">
      <label for="correo">Correo de la cuenta:</label>
      <input type="text" id="correo" name="correo-nuevo" value="${correoUsuario}">
      <input type="submit" name="cambiar-correo" value="Actualizar correo">
    </form>
  `;

  opcionesConfiguracionTags[0].addEventListener("click", () => {
    const modalCambioCorreo = generarModal(formCambioCorreo);
    generarCierreModal(modalCambioCorreo);

    modalCambioCorreo.showModal()
  });
}


function prepararCambiarContrasenhaListener () {
  const formCambioContrasenha = /* html */ `
    <form name="cambio-contrasenha" action="" method="POST" autocomplete="off">
      <label for="contrasenha-antigua">Contraseña antigüa:</label>
      <input type="password" id="contrasenha-antigua" name="contrasenha-antigua">
      <label for="contrasenha-nueva">Nueva contraseña:</label>
      <input type="password" id="contrasenha-nueva" name="contrasenha-nueva">
      <label for="contrasenha-nueva-confirmacion">Confirmar nueva contraseña:</label>
      <input type="password" id="contrasenha-nueva-confirmacion" name="contrasenha-nueva-confirmacion">
      <input type="submit" name="cambiar-contrasenha" value="Actualizar contraseña">
    </form>
  `;

  opcionesConfiguracionTags[1].addEventListener("click", () => {
    const modalCambioContrasenha = generarModal(formCambioContrasenha);
    generarCierreModal(modalCambioContrasenha);

    modalCambioContrasenha.showModal();
  });
}

function prepararRestablecerCuentaListener () {
  const formRestablecerCuenta = /* html */ `
    <form name="restablecer-cuenta" action="" method="POST">
      <p>¿Estás segur@ de que quieres restablecer tu cuenta?</p>
      <small>Todos los datos relativos a tu estantería se eliminarán.</small>
      <div class="grupo-buttons">
        <input type="submit" name="restablecer-cuenta" value="Confirmar">
        <custom-button
          data-contenido="Cancelar"
          background-color="var(--seccion-inactiva-background)"
          font-color="var(--seccion-inactiva-foreground)"
          with-shadow="false"
          with-translation="false"
        >
      </div>
      
    </form>
  `;

  opcionesConfiguracionTags[2].addEventListener("click", () => {
    const modalRestablecerCuenta = generarModal(formRestablecerCuenta);
    const cierreButton = modalRestablecerCuenta.querySelector("custom-button");
    generarCierreModal(modalRestablecerCuenta, cierreButton);

    modalRestablecerCuenta.showModal();
    modalRestablecerCuenta.querySelector("input[type=submit]").blur();
  });
}

function prepararEliminarCuentaListener () {
  const formEliminarCuenta = /* html */ `
    <form name="eliminar-cuenta" action="" method="POST">
      <p>¿Estás segur@ de que quieres eliminar tu cuenta?</p>
      <small><strong>Todos tus datos</strong> serán eliminados de la base de datos de <i>BiblioPocket</i>.</small>
      <div class="grupo-buttons">
      <button type="submit" name="eliminar-cuenta" class="delete-button">
        <svg class="button-icon">
          <use xlink:href="/client/assets/images/trash-icon.svg#trash-icon"></use>
        </svg>
          Eliminar cuenta
      </button>
      <custom-button
        data-contenido="Cancelar"
        background-color="var(--seccion-inactiva-background)"
        font-color="var(--seccion-inactiva-foreground)"
        with-shadow="false"
        with-translation="false"
      >
      </div>
      
    </form>
  `;

  opcionesConfiguracionTags[3].addEventListener("click", () => {
    const modalEliminarCuenta = generarModal(formEliminarCuenta);
    const cierreButton = modalEliminarCuenta.querySelector("custom-button");
    generarCierreModal(modalEliminarCuenta, cierreButton);

    modalEliminarCuenta.showModal();
    modalEliminarCuenta.querySelector("button[type=submit]").blur();
  });
}

/* --- FUNCIONES COMPLEMENTARIAS --- */
function generarModal (innerHTML) {
  const modal = document.createElement("dialog");
  modal.setAttribute("class", "modal");
  modal.setAttribute("id", "configuracion-modal");

  modal.innerHTML = innerHTML;
  document.body.appendChild(modal);

  return modal;
}

function generarCierreModal (modal, cierreButton) {
  modal.addEventListener("mousedown", event => {
    let modalBounds = modal.getBoundingClientRect();
    if ((event.clientX < modalBounds.left || event.clientX > modalBounds.right)
      || (event.clientY < modalBounds.top || event.clientY > modalBounds.bottom))
      modal.remove();
  });

  if (cierreButton != undefined) cierreButton.addEventListener("click", () => modal.remove());
}
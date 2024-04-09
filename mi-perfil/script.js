import { previsualizarNuevaImagen } from "/bibliopocket/client/handlers/previewHandler.js";

const navButtons = document.querySelectorAll(".nav-perfil input");
const opcionesConfiguracionTags = document.querySelectorAll(".configuracion.container li");


prepararListeners();

/* --- FUNCIÓN "MAIN" --- */
function prepararListeners () {
  navButtons.forEach((button) => button.addEventListener("click", prepararSeccionActivaListener));

  prepararListenerImageUploader();
  prepararCambiarCorreoListener();
  prepararCambiarContrasenhaListener();
  prepararRestablecerCuentaListener();
  prepararEliminarCuentaListener();
}


function prepararListenerImageUploader () {
  const uploader = document.querySelector("#uploader-input");
  const previewer = document.querySelector(".preview");

  uploader.addEventListener("change", () => previsualizarNuevaImagen(uploader, previewer));
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
  const formCambioCorreo = /* html */ `
    <form name="cambio-correo"=>
      <label for="correo-antiguo">Correo antigüo: 
        <input type="text" id="correo-antiguo" name="correo-antiguo">
      </label>
      <label for="correo-nuevo">Nuevo correo: 
        <input type="text" id="correo-nuevo" name="correo-nuevo">
      </label>
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
    <form name="cambio-contrasenha">
      <label for="contrasenha-antigua">Contraseña antigüa: 
        <input type="text" id="correo-antigua" name="contrasenha-antigua">
      </label>
      <label for="contrasenha-nueva">Nueva contraseña: 
        <input type="text" id="contrasenha-nueva" name="contrasenha-nueva">
      </label>
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
        <input type="submit" name="Confirm" value="Confirmar">
        <input type="button" value="Cancelar">
      </div>
      
    </form>
  `;

  opcionesConfiguracionTags[2].addEventListener("click", () => {
    const modalRestablecerCuenta = generarModal(formRestablecerCuenta);
    const cierreButton = modalRestablecerCuenta.querySelector("input[type='button'");
    generarCierreModal(modalRestablecerCuenta, cierreButton);

    modalRestablecerCuenta.showModal();
  });
}

function prepararEliminarCuentaListener () {
  const formEliminarCuenta = /* html */ `
    <form name="eliminar-cuenta" action="" method="POST">
      <p>¿Estás segur@ de que quieres eliminar tu cuenta?</p>
      <small>Todos tus datos serán eliminados de la base de datos de <i>BiblioPocket</i>.</small>
      <div class="grupo-buttons">
        <input type="submit" name="Confirm" value="Confirmar">
        <input type="button" value="Cancelar">
      </div>
      
    </form>
  `;

  opcionesConfiguracionTags[3].addEventListener("click", () => {
    const modalEliminarCuenta = generarModal(formEliminarCuenta);
    const cierreButton = modalEliminarCuenta.querySelector("input[type='button'");
    generarCierreModal(modalEliminarCuenta, cierreButton);

    modalEliminarCuenta.showModal();
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
  modal.addEventListener("click", event => {
    let modalBounds = modal.getBoundingClientRect();
    if ((event.clientX < modalBounds.left || event.clientX > modalBounds.right)
      || (event.clientY < modalBounds.top || event.clientY > modalBounds.bottom))
      modal.remove();
  });

  if (cierreButton != undefined) cierreButton.addEventListener("click", () => modal.remove());
}
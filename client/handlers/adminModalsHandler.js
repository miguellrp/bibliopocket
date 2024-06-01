// Variable global que permitirá controlar el tipo de modal activo en /admin
let modalActivo;

function getModalEditarPermisosUsuario (opcionElemento) {
  modalActivo = document.querySelector(".editar-permisos-usuario");
  const infoUsuario = getInfoUsuario(opcionElemento);

  if (modalActivo == null) {
    modalActivo = document.createElement("dialog");
    modalActivo.classList.add("modal", "editar-permisos-usuario");

    modalActivo.innerHTML = /* html */ `
      <h3>Editar permisos de <span>${infoUsuario.nombreUsuario}</span></h3>
      <form action="" method="POST">
        <label for="permiso-anhadir-libros">Añadir libros a la estantería:
          <custom-switcher id="permiso-anhadir-libros" data-name="pAnhadirLibros" data-checked=${infoUsuario.pAnhadirLibros}></custom-switcher>
        </label>
        <label for="permiso-consultar-api-externa">Realizar consultas en API externa:
          <custom-switcher id="permiso-consultar-api-externa" data-name="pConsultarApiExterna" data-checked=${infoUsuario.pConsultarApiExterna}></custom-switcher>
        </label>
        <div class="grupo-buttons">
          <input type="submit" class="submit-btn" name="editar-permisos-usuario" value="Confirmar">
          <input type="button" class="submit-btn" value="Cancelar">
        </div>
        <input type="hidden" name="idUsuario" value="${infoUsuario.idUsuario}">
      </form>
    `;
  }
  const cancelarBoton = modalActivo.querySelector("input[type=button]");
  generarCierreModal(modalActivo, cancelarBoton);

  mostrarModal(modalActivo);
}

function getModalEliminarUsuario (opcionElemento) {
  modalActivo = document.querySelector(".eliminar-usuario");
  const infoUsuario = getInfoUsuario(opcionElemento);

  if (modalActivo == null) {
    modalActivo = document.createElement("dialog");
    modalActivo.classList.add("modal", "eliminar-usuario");

    modalActivo.innerHTML = /* html */ `
      <form action="" method="POST">
        <p>¿Confirmas que quieres eliminar a <span>${infoUsuario.nombreUsuario}</span> de la base de datos de <i>BiblioPocket</i>?</p>
        <small class="warning">Esta acción será irreversible</small>
        <div class="grupo-buttons">
          <input type="submit" class="submit-btn" name="eliminar-usuario" value="Confirmar">
          <input type="button" class="submit-btn" value="Cancelar">
        </div>
        <input type="hidden" name="idUsuario" value="${infoUsuario.idUsuario}">
      </form>
    `;
  }
  const cancelarBoton = modalActivo.querySelector("input[type=button");
  generarCierreModal(modalActivo, cancelarBoton);

  mostrarModal(modalActivo);
}

function getModalBloquearUsuario (opcionElemento) {
  modalActivo = document.querySelector(".bloquear-usuario");
  const infoUsuario = getInfoUsuario(opcionElemento);

  if (modalActivo == null) {
    modalActivo = document.createElement("dialog");
    modalActivo.classList.add("modal", "bloquear-usuario");

    const inicioDiaSiguiente = getFechaDiaSiguiente();
    const fechaMaximaPermitida = getFechaMaximaPermitida();

    modalActivo.innerHTML = /* html */ `
      <h3>Establecer bloqueo temporal a <span>${infoUsuario.nombreUsuario}</span></h3>
      <form action="" method="POST">
        <label for="motivo-bloqueo">Selecciona el motivo de bloqueo:</label>
        <select name="motivo-bloqueo" id="motivo-bloqueo"></select>
        <label for="fecha-expiracion">Establece la fecha de expiración de este bloqueo:</label>
        <input type="datetime-local" id="fecha-expiracion" name="fechaExpiracion" min=${inicioDiaSiguiente} max=${fechaMaximaPermitida}>
        <div class="grupo-buttons">
          <input type="submit" class="submit-btn" name="bloquear-usuario" value="Confirmar">
          <input type="button" class="submit-btn" value="Cancelar">
        </div>
        <input type="hidden" name="idUsuario" value="${infoUsuario.idUsuario}">
      </form>
    `;

    const selectMotivosTag = modalActivo.querySelector("#motivo-bloqueo");
    insertarMotivosBloqueo(selectMotivosTag);
  }
  const cancelarBoton = modalActivo.querySelector("input[type=button]");
  generarCierreModal(modalActivo, cancelarBoton);

  mostrarModal(modalActivo);
}


function getInfoUsuario (elementoTabla) {
  const primerCelda = elementoTabla.closest("tr").cells[0];
  const idUsuario = primerCelda.getAttribute("data-id");
  const nombreUsuario = primerCelda.textContent;

  const pAnhadirLibros = primerCelda.getAttribute("p-anhadir-libros") === "true";
  const pConsultarApiExterna = primerCelda.getAttribute("p-consultar-api-externa") === "true";

  return {
    "idUsuario": idUsuario,
    "nombreUsuario": nombreUsuario,
    "pAnhadirLibros": pAnhadirLibros,
    "pConsultarApiExterna": pConsultarApiExterna
  };
}


function getModalActivo () {
  modalActivo = document.querySelector(".modal");
}

function generarCierreModal (modal, cierreButton = null) {
  modal.addEventListener("mousedown", event => {
    let modalBounds = modal.getBoundingClientRect();

    // clientX && clientY != 0 para ignorar el evento de clickar una opción del <select> en modal de bloquear usuario
    if ((event.clientX != 0 && event.clientY != 0) &&
      ((event.clientX < modalBounds.left || event.clientX > modalBounds.right) ||
        (event.clientY < modalBounds.top || event.clientY > modalBounds.bottom)))
      modal.remove();

  });

  if (cierreButton != null) cierreButton.addEventListener("click", () => modal.remove());
}

function mostrarModal (modal) {
  document.body.appendChild(modal);
  modal.showModal();
  modal.querySelector("input[type=submit").blur();
}


function insertarMotivosBloqueo (selectMotivosTag) {
  fetchListaBloqueos().then(response => {
    selectMotivosTag.innerHTML = response;
  }).catch(error => {
    console.error("Error al cargar los motivos de bloqueo: ", error);
  });
}

/* Función asíncrona para recoger los permisos de las personas usuarias registradas en BP */
async function fetchListaRoles () {
  try {
    let response;
    if (location.hostname === "localhost")
      response = await fetch(`http://localhost/server/API.php?tipoPeticion=getLibros&idUsuario=${idUsuario}&paginacion=${indexUltimoLibro}&limitado=${limitado}`);
    else
      response = await fetch(`http://192.168.0.25/server/API.php?tipoPeticion=getLibros&idUsuario=${idUsuario}&paginacion=${indexUltimoLibro}&limitado=${limitado}`);


    const data = await response.text();

    return data;
  } catch (error) {
    throw new Error("Error al obtener los roles de la BD: ", error);
  }
}

/* Función asíncrona para traer la lista de todos los motivos de bloqueo almacenados en la BD */
async function fetchListaBloqueos () {
  try {
    const response = await fetch(`http://localhost/server/API.php?tipoPeticion=getBloqueos`);
    const data = await response.text();

    return data;
  } catch (error) {
    throw new Error("Error al obtener los motivos de bloqueo de la BD: ", error);
  }
}


/* -- Funciones complementarias -- */
function getFechaDiaSiguiente () {
  const inicioDiaSiguiente = new Date();
  inicioDiaSiguiente.setDate(inicioDiaSiguiente.getDate() + 1);
  inicioDiaSiguiente.setUTCHours(0, 0, 0, 0);

  return inicioDiaSiguiente.toISOString().slice(0, 16);
}

function getFechaMaximaPermitida () {
  const fechaMaximaPermitida = new Date();
  fechaMaximaPermitida.setFullYear(fechaMaximaPermitida.getFullYear() + 5);
  fechaMaximaPermitida.setUTCHours(0, 0, 0, 0);

  return fechaMaximaPermitida.toISOString().slice(0, 16);
}
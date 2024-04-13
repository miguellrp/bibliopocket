import * as modalesEstanteria from "/bibliopocket/client/handlers/estanteriaModalsHandler.js";

prepararListenersButtons();

/* --- FUNCIÓN "MAIN" --- */
function prepararListenersButtons () {
  listenerModalBusquedaAPI();
  listenerModalCrearLibro();
  listenersModalEliminarLibro();
  listenersModalModificarLibro();
}


function listenerModalBusquedaAPI () {
  const buscarLibroBtn = document.getElementById("busqueda-button");

  buscarLibroBtn.addEventListener("click", () => {
    const modalBusquedaAPI = modalesEstanteria.getModalBusquedaAPI();
    generarCierreModal(modalBusquedaAPI);

    modalBusquedaAPI.showModal()
  });
}

function listenerModalCrearLibro () {
  const nuevoLibroBtn = document.getElementById("nuevo-libro-button");

  nuevoLibroBtn.addEventListener("click", () => {
    const modalNuevoLibro = modalesEstanteria.getModalDatosLibro();
    generarCierreModal(modalNuevoLibro);

    const estadosLibroTags = modalNuevoLibro.querySelector(".grupo-estados-libro").querySelectorAll(":scope > input");
    estadosLibroTags.forEach((estadoLibroTag) => estadoLibroTag.addEventListener("click", listenersEstadosLibro));

    modalNuevoLibro.showModal();
  });
}

function listenersModalEliminarLibro () {
  const eliminarGroupBtn = document.querySelectorAll(".icon.eliminar");
  eliminarGroupBtn.forEach(eliminarButton => {
    const libroVinculado = eliminarButton.parentNode.parentNode;
    const datosLibro = libroVinculado.querySelector("form").elements;
    const idLibroSeleccionado = datosLibro.id.value;

    eliminarButton.addEventListener("click", () => {
      const modalEliminacion = modalesEstanteria.getModalEliminacion(idLibroSeleccionado);
      generarCierreModal(modalEliminacion);
      modalEliminacion.showModal();
    });
  });
}

function listenersModalModificarLibro () {
  const modificarGroupBtn = document.querySelectorAll(".icon.modificar");
  modificarGroupBtn.forEach(modificarButton => {
    const libroVinculado = modificarButton.parentNode.parentNode;

    modificarButton.addEventListener("click", () => {
      const modalModificacion = modalesEstanteria.getModalDatosLibro(libroVinculado);
      generarCierreModal(modalModificacion);
      modalModificacion.querySelector("#titulo").blur();

      const estadosLibroTags = modalModificacion.querySelector(".grupo-estados-libro").querySelectorAll(":scope > input");
      estadosLibroTags.forEach((estadoLibroTag) => estadoLibroTag.addEventListener("click", listenersEstadosLibro));

      modalModificacion.showModal();
    });
  });
}

function listenersEstadosLibro () {
  const estadosTags = this.parentNode.querySelectorAll("input[type='radio']");

  this.setAttribute("checked", "true");
  estadosTags.forEach((estadoTag) => {
    if (estadoTag != this) estadoTag.setAttribute("checked", "false")
  });

}

/* --- FUNCIONES COMPLEMENTARIAS --- */
function generarCierreModal (modal) {
  modal.addEventListener("mousedown", event => {
    let modalBounds = modal.getBoundingClientRect();
    if ((event.clientX < modalBounds.left || event.clientX > modalBounds.right)
      || (event.clientY < modalBounds.top || event.clientY > modalBounds.bottom))
      modal.close();
  });
}
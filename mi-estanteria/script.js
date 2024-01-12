import * as modalesLibroCreado from "./modalesLibroCreado.js";
import { generarModalNuevoLibro } from "./modalCrearLibro.js";

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
    const modalBusquedaAPI = modalesLibroCreado.generarModalBusquedaAPI();
    anhadirCierreModal(modalBusquedaAPI);

    modalBusquedaAPI.showModal()
  });
}

function listenerModalCrearLibro () {
  const nuevoLibroBtn = document.getElementById("nuevo-libro-button");

  nuevoLibroBtn.addEventListener("click", () => {
    eliminarModalActivo();

    const modalNuevoLibro = generarModalNuevoLibro();
    anhadirCierreModal(modalNuevoLibro);
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
      eliminarModalActivo();

      const modalEliminacion = modalesLibroCreado.generarModalEliminacion(idLibroSeleccionado);
      anhadirCierreModal(modalEliminacion);
      modalEliminacion.showModal();
    });
  });
}

function listenersModalModificarLibro () {
  const modificarGroupBtn = document.querySelectorAll(".icon.modificar");
  modificarGroupBtn.forEach(modificarButton => {
    const libroVinculado = modificarButton.parentNode.parentNode;

    modificarButton.addEventListener("click", () => {
      eliminarModalActivo();

      const modalModificacion = modalesLibroCreado.generarModalModificacion(libroVinculado);
      anhadirCierreModal(modalModificacion);
      modalModificacion.showModal();
      modalModificacion.querySelector("#titulo").blur();
    });
  });
}

/* --- FUNCIONES COMPLEMENTARIAS --- */
function anhadirCierreModal (modal) {
  modal.addEventListener("click", event => {
    let modalBounds = modal.getBoundingClientRect();
    if ((event.clientX < modalBounds.left || event.clientX > modalBounds.right)
      || (event.clientY < modalBounds.top || event.clientY > modalBounds.bottom))
      modal.close();
  });
}

/* Con la finalidad de que no se vayan almacenando modales en el DOM, cada vez que se
abre uno, se elimina el anterior que estuviese activo. */
function eliminarModalActivo () {
  const modalActivo = document.querySelector(".modal");

  if (modalActivo != null) document.body.removeChild(modalActivo);
}
import * as modalesEstanteria from "/client/handlers/estanteriaModalsHandler.js";
import { prepararListenersLibro, listenersEstadosLibro, generarCierreModal } from "/client/handlers/listenersBotonesLibroHandler.js";

prepararListenersButtons();

/* --- FUNCIÓN "MAIN" --- */
function prepararListenersButtons () {
  listenerModalBusquedaAPI();
  listenerModalCrearLibro();
  prepararListenersLibro();
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
import { buscarLibroAPI } from "/bibliopocket/client/handlers/APIBooksHandler.js";

export function generarModalBusquedaAPI () {
  const modalBusquedaAPI = document.createElement("dialog");
  modalBusquedaAPI.setAttribute("class", "modal");
  modalBusquedaAPI.setAttribute("id", "busqueda-libro-modal");

  modalBusquedaAPI.innerHTML = generarBuscadorAPI();

  const buscador = modalBusquedaAPI.querySelector("#buscador-libro");
  const buscadorButton = modalBusquedaAPI.querySelector(".input-buscador.btn");
  buscador.addEventListener("search", buscarLibroAPI);
  buscadorButton.addEventListener("click", buscarLibroAPI);

  document.body.appendChild(modalBusquedaAPI);
  return modalBusquedaAPI;
}

export function generarModalEliminacion (idLibro) {
  const modalEliminacionLibro = document.createElement("dialog");
  modalEliminacionLibro.setAttribute("class", "modal");
  modalEliminacionLibro.setAttribute("id", "eliminar-libro-modal");

  modalEliminacionLibro.innerHTML = generarFormEliminacion(idLibro);
  document.body.appendChild(modalEliminacionLibro);

  return modalEliminacionLibro;
}

export function generarModalModificacion (libroVinculado) {
  const modalModificacionLibro = document.createElement("dialog");
  modalModificacionLibro.setAttribute("class", "modal");
  modalModificacionLibro.setAttribute("id", "modificar-libro-modal");

  modalModificacionLibro.innerHTML = generarFormModificacion(libroVinculado);
  document.body.appendChild(modalModificacionLibro);

  return modalModificacionLibro;
}

function generarBuscadorAPI () {
  return /*html */ `
    <h2>AÑADIR NUEVO LIBRO 🔎</h2>
    <label for="buscador-libro">Buscar por título:
      <input type="search" id="buscador-libro" 
       class="input-buscador" placeholder="Cien años de soledad">
       <input type="button" class="input-buscador btn" value="Buscar">
    </label>
    <div class="resultados-busqueda">
      <img src="/bibliopocket/client/assets/images/torre-libros.svg">
    </div>
  `;
}

function generarFormEliminacion (idLibro) {
  return /* html */`
    <form action="" method="POST">
      <p>¿Estás seguro de que quieres eliminar este libro de tu estantería?</p>
      <small>Todos sus datos modificados se perderán</small>
      <div class="grupo-buttons">
        <input type="submit" name="eliminar" value="Confirmar">
        <input type="button" value="Cancelar">
      </div>
      <input type="hidden" name="idLibroEstante" value="${idLibro}">
    </form>
  `;
}

function generarFormModificacion (libroVinculado) {
  libroVinculado = libroVinculado.querySelector("form").elements;
  const datosLibro = {
    "id": libroVinculado.id.value,
    "titulo": libroVinculado.titulo.value,
    "subtitulo": libroVinculado.subtitulo.value,
    "descripcion": libroVinculado.descripcion.value,
    "portada": libroVinculado.portada.value,
    "autoria": libroVinculado.autoria.value,
    "numPaginas": libroVinculado.numPaginas.value,
    "editorial": libroVinculado.editorial.value,
    "anhoPublicacion": libroVinculado.anhoPublicacion.value,
    "enlaceAPI": libroVinculado.enlaceAPI.value,
    "estado": libroVinculado.estado.value
  };

  // Para comprobar el estado en el que se encuentra la lectura del libro vinculado:
  const leidoChecked = (estadoLibro) => { return (estadoLibro === "Leido") };
  const leyendoChecked = (estadoLibro) => { return (estadoLibro === "Leyendo") };
  const pendienteChecked = (estadoLibro) => { return (estadoLibro === "Pendiente") };

  return /* html */` 
    <h2>Modificar datos del libro ✍️</h2>
    <form action="" method="POST">
      <div class="wrap-portada">
        <img src="${datosLibro.portada}" class="portada" alt="Portada de ${datosLibro.titulo}">
        <div class="portada-upload">
          <label for="portada-input">
            <img src="/bibliopocket/client/assets/images/pencil-icon.png" class="lapiz-icon">
          </label>
          <input id="portada-input" type="file" accept="image/*" name="portada" />
        </div>   
      </div>
      <div class="datos-cabecera">
        <label for="titulo">Título:</label>
        <input type="text" id="titulo" class="input-txt" name="titulo" value="${datosLibro.titulo}">

        <label for="subtitulo">Subtítulo:</label>
        <input type="text" id="subtitulo" class="input-txt" name="subtitulo" value="${datosLibro.subtitulo}">
      </div>

      <label for="descripcion">Descripción:</label>
      <textarea id="descripcion" class="input-txt" name="descripcion">${datosLibro.descripcion}</textarea>

      <label for="autoria">Autoría:</label>
      <input type="text" id="autoria" class="input-txt" name="autoria" value="${datosLibro.autoria}">

      <label for="num-paginas">Nº de páginas:</label>
      <input type="text" id="num-paginas" class="input-txt" name="numPaginas" value="${datosLibro.numPaginas}">

      <label for="editorial">Editorial:</label>
      <input type="text" id="editorial" class="input-txt" name="editorial" value="${datosLibro.editorial}">

      <label for="anho-publicacion">Año de publicación:</label>
      <input type="text" id="anho-publicacion" class="input-txt" name="anhoPublicacion" value="${datosLibro.anhoPublicacion}">

      <label>Estado:</label>
      <div class="grupo-estados-libro">
        <input type="radio" name="estado" id="leido" value="leido" checked=${leidoChecked(datosLibro.estado)}>
        <label for="leido">Leído</label>
        <input type="radio" name="estado" id="leyendo" value="leyendo" checked=${leyendoChecked(datosLibro.estado)}>
        <label for="leyendo">Leyendo</label>
        <input type="radio" name="estado" id="pendiente" value="pendiente" checked=${pendienteChecked(datosLibro.estado)}>
        <label for="pendiente">Pendiente</label>
      </div>

      <label for="categorias">Categorías:</label>
      <input type="text" id="categorias">

      <input type="submit" value="Guardar cambios" name="modificar-libro">
      <input type="hidden" name="idLibroEstante" value="${datosLibro.id}">
      <input type="hidden" name="portada" value="${datosLibro.portada}">
      <input type="hidden" name="enlaceAPI" value="${datosLibro.enlaceAPI}">
    </form>
  `;
}
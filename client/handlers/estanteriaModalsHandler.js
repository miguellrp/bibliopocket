import { buscarLibroAPI } from "/client/handlers/APIBooksHandler.js";

// --- FUNCIONES PARA MOSTRAR MODAL ---
export function getModalBusquedaAPI () {
  let modalBusquedaAPI = document.querySelector("#busqueda-libro-modal");

  if (modalBusquedaAPI === null) {
    modalBusquedaAPI = document.createElement("dialog");
    modalBusquedaAPI.setAttribute("class", "modal");
    modalBusquedaAPI.setAttribute("id", "busqueda-libro-modal");

    modalBusquedaAPI.innerHTML = generarBuscadorAPI();

    const buscadorInput = modalBusquedaAPI.querySelector("input[type=search]");
    const buscadorButton = modalBusquedaAPI.querySelector(".buscador-btn");
    buscadorInput.addEventListener("search", buscarLibroAPI);
    buscadorButton.addEventListener("click", buscarLibroAPI);

    document.body.appendChild(modalBusquedaAPI);
  }

  return modalBusquedaAPI;
}

// Permite que no se pase ningún libro por parámetro (cuando se crea un libro desde 0; no se modifica uno generado)
export function getModalDatosLibro (libroVinculado = null) {
  let modalModificacionLibro = document.querySelector("#datos-libro-modal");

  if (modalModificacionLibro === null) {
    modalModificacionLibro = document.createElement("dialog");
    modalModificacionLibro.setAttribute("class", "modal");
    modalModificacionLibro.setAttribute("id", "datos-libro-modal");

    modalModificacionLibro.innerHTML = generarFormDatosLibro();
    document.body.appendChild(modalModificacionLibro);
  }

  if (libroVinculado === null) actualizarDataForm(0);
  else actualizarDataForm(1, libroVinculado);

  return modalModificacionLibro;
}


export function getModalEliminacion (libroVinculado) {
  let modalEliminacionLibro = document.querySelector("#eliminar-libro-modal");

  if (modalEliminacionLibro === null) {
    modalEliminacionLibro = document.createElement("dialog");
    modalEliminacionLibro.setAttribute("class", "modal");
    modalEliminacionLibro.setAttribute("id", "eliminar-libro-modal");

    modalEliminacionLibro.innerHTML = generarFormEliminacion();
    document.body.appendChild(modalEliminacionLibro);
  } else {
    const formModalEliminacion = modalEliminacionLibro.querySelector("form");
    const idAsociadoAnteriorTag = formModalEliminacion.querySelector("input[type='hidden']");

    formModalEliminacion.removeChild(idAsociadoAnteriorTag);
  }

  vincularFormEliminacion(modalEliminacionLibro, libroVinculado);
  return modalEliminacionLibro;
}


// --- FUNCIONES GENERADORAS ---
function generarBuscadorAPI () {
  return /*html */ `
    <h2>AÑADIR NUEVO LIBRO 🔎</h2>
      <div class="buscador">
        <input type="search" class="input-buscador" placeholder="Buscar por título, autoría, editorial...">
        <button class="buscador-btn">
          <img src="/client/assets/images/magnifying-glass-icon.svg">
        </button>
      </div>
    <div class="resultados-busqueda">
      <img src="/client/assets/images/torre-libros.svg">
    </div>
  `;
}

function generarFormDatosLibro () {
  return /* html */ ` 
    <form action="" method="POST" name="libroModalForm" enctype=multipart/form-data>
      <custom-image-uploader
        width-preview="128px"
        height-preview="192px"
        border-type="2px dashed var(--primary-color)"
        border-radius="5%"
        data-name="portadaLibro"
        data-form="libroModalForm"
        data-type="portadaLibro">
      </custom-image-uploader>  
      <div class="datos-cabecera">
        <label for="titulo">Título:</label>
        <input type="text" id="titulo" class="input-txt" name="titulo" required>

        <label for="subtitulo">Subtítulo:</label>
        <input type="text" id="subtitulo" class="input-txt" name="subtitulo">
      </div>

      <label for="descripcion">Descripción:</label>
      <textarea id="descripcion" class="input-txt" name="descripcion"></textarea>

      <label for="autoria">Autoría:</label>
      <input type="text" id="autoria" class="input-txt" name="autoria">

      <label for="num-paginas">Nº de páginas:</label>
      <input type="number" id="num-paginas" class="input-txt" name="numPaginas">

      <label for="editorial">Editorial:</label>
      <input type="text" id="editorial" class="input-txt" name="editorial">

      <label for="anho-publicacion">Año de publicación:</label>
      <input type="number" id="anho-publicacion" class="input-txt" name="anhoPublicacion">

      <label>Estado:</label>
      <div class="grupo-estados-libro">
        <input type="radio" name="estado" id="pendiente" value="0">
        <label for="pendiente">Pendiente</label>
        <input type="radio" name="estado" id="leyendo" value="1">
        <label for="leyendo">Leyendo</label>
        <input type="radio" name="estado" id="leido" value="2">
        <label for="leido">Leído</label>
      </div>

      <label for="categorias">Categorías:</label>
    </form>
  `;
}

function generarFormEliminacion () {
  return /* html */`
    <form action="" method="POST">
      <p>¿Estás segur@ de que quieres eliminar este libro de tu estantería?</p>
      <p class="referencia"></p>
      <small>Todos sus datos modificados se perderán.</small>
      <div class="grupo-buttons">
        <input type="submit" class="submit-btn" name="eliminar-libro" value="Confirmar">
        <input type="button" class="submit-btn" value="Cancelar">
      </div>
    </form>
  `;
}


// --- FUNCIONES "RELACIONALES" ---
function vincularFormEliminacion (modalEliminacion, libroVinculado) {
  const formEliminacion = modalEliminacion.querySelector("form");

  const idLibroVinculadoTag = document.createElement("input");
  idLibroVinculadoTag.value = libroVinculado.id.value;
  idLibroVinculadoTag.type = "hidden";
  idLibroVinculadoTag.name = "idLibroEstante";

  const referenciaLibro = modalEliminacion.querySelector(".referencia")
  referenciaLibro.innerHTML = `<span>${libroVinculado.titulo.value}</span>, de ${libroVinculado.autoria.value}`;

  formEliminacion.appendChild(idLibroVinculadoTag);
}

function getDataLibro (libro) {
  const categoriasData = libro.querySelectorAll("form[name=categorias-hidden] > input");
  libro = libro.querySelector("form").elements;

  let categorias = [];
  categoriasData.forEach((categoriaTag) => categorias.push(categoriaTag.value));

  let dataLibro = {
    "id": libro.id.value,
    "titulo": libro.titulo.value,
    "subtitulo": libro.subtitulo.value,
    "descripcion": libro.descripcion.value,
    "portada": libro.portada.value,
    "autoria": libro.autoria.value,
    "numPaginas": libro.numPaginas.value,
    "editorial": libro.editorial.value,
    "anhoPublicacion": libro.anhoPublicacion.value,
    "enlaceAPI": libro.enlaceAPI.value,
    "estado": libro.estado.value,
    "categorias": categorias
  };

  return dataLibro;
}

// --- FUNCIONES COMPLEMENTARIAS ---
function getDataForm (tipoForm, libro) {
  let dataModificacionForm;

  if (tipoForm == 1) dataModificacionForm = getDataLibro(libro);

  const dataForm = {
    "titleForm": (tipoForm == 0) ? "Añadir nuevo libro desde 0 📖" : "Modificar datos del libro ✍️",
    "portadaLibro": (tipoForm == 0) ? "" : dataModificacionForm.portada,
    "altPortadaLibro": (tipoForm == 0) ? "Portada del nuevo libro" : `Portada de ${dataModificacionForm.titulo}`,
    "tituloLibro": (tipoForm == 0) ? "" : dataModificacionForm.titulo,
    "subtituloLibro": (tipoForm == 0) ? "" : dataModificacionForm.subtitulo,
    "descripcionLibro": (tipoForm == 0) ? "" : dataModificacionForm.descripcion,
    "autoriaLibro": (tipoForm == 0) ? "" : dataModificacionForm.autoria,
    "numPaginasLibro": (tipoForm == 0) ? "" : dataModificacionForm.numPaginas,
    "editorialLibro": (tipoForm == 0) ? "" : dataModificacionForm.editorial,
    "anhoPublicacionLibro": (tipoForm == 0) ? "" : dataModificacionForm.anhoPublicacion,
    "estadoLibro": (tipoForm == 0) ? 0 : dataModificacionForm.estado,
    "submitButtonText": (tipoForm == 0) ? "Añadir libro" : "Guardar cambios",
    "submitButtonName": (tipoForm == 0) ? "anhadir-nuevo-libro" : "modificar-libro",

    // Input hidden fields:
    "idLibro": (tipoForm == 0) ? null : dataModificacionForm.id,
    "enlaceAPI": (tipoForm == 0) ? null : dataModificacionForm.enlaceAPI,
    "categorias": (tipoForm == 0) ? null : dataModificacionForm.categorias
  };

  return dataForm;
}

function actualizarDataForm (tipoForm, libro) {
  const modalDatosLibro = document.querySelector("#datos-libro-modal");
  const form = modalDatosLibro.querySelector("form");
  let dataForm;

  if (tipoForm == 0) dataForm = getDataForm(0);
  else dataForm = getDataForm(1, libro);

  const camposForm = getCamposForm();

  getTitleForm();

  camposForm.portada.src = dataForm.portadaLibro;
  camposForm.titulo.value = dataForm.tituloLibro;
  camposForm.subtitulo.value = dataForm.subtituloLibro;
  camposForm.descripcion.value = dataForm.descripcionLibro;
  camposForm.autoria.value = dataForm.autoriaLibro;
  camposForm.numPaginas.value = dataForm.numPaginasLibro;
  camposForm.editorial.value = dataForm.editorialLibro;
  camposForm.anhoPublicacion.value = dataForm.anhoPublicacionLibro ?? "s.f";

  if (dataForm.estadoLibro == 0) camposForm.estadoPendiente.setAttribute("checked", "");
  if (dataForm.estadoLibro == 1) camposForm.estadoLeyendo.setAttribute("checked", "");
  if (dataForm.estadoLibro == 2) camposForm.estadoLeido.setAttribute("checked", "");

  getSubmitButtonForm(tipoForm);
  getInputHiddenFields();
  getCategorias();


  // --- FUNCIONES INTERNAS PARA ACTUALIZAR LOS DATOS DEL FORM (creación / modificación de libro) ---
  function getTitleForm () {
    const modalDatosLibro = document.querySelector("#datos-libro-modal");
    let titleFormTag = modalDatosLibro.querySelector("h2");

    if (titleFormTag === null) {
      titleFormTag = document.createElement("h2");
      modalDatosLibro.prepend(titleFormTag);
    }

    titleFormTag.textContent = dataForm.titleForm;
  }

  function getSubmitButtonForm (tipoForm) {
    let submitButtonTag = modalDatosLibro.querySelector("button[type=submit]");

    if (submitButtonTag == null) {
      submitButtonTag = document.createElement("button");
      submitButtonTag.type = "submit";
      form.append(submitButtonTag);
    }

    submitButtonTag.className = "submit-btn";
    submitButtonTag.name = dataForm.submitButtonName;
    submitButtonTag.innerHTML = `
    <svg class=icon>
      <use xlink:href=/client/assets/images/floppy-disk-icon.svg#floppy-disk>
      </use>
    </svg>${(tipoForm == 0) ? "Crear libro" : "Guardar cambios"}`;
  }

  function getInputHiddenFields () {
    let hiddenDataTags = modalDatosLibro.querySelector(".datos-libro");

    if (hiddenDataTags === null) {
      hiddenDataTags = document.createElement("div");
      hiddenDataTags.className = "datos-libro";
      const inputHiddenTags = generarInputHiddenFields();

      inputHiddenTags.forEach((inputHiddenTag) => hiddenDataTags.append(inputHiddenTag));
      form.append(hiddenDataTags);
    } else {
      hiddenDataTags.childNodes[0].value = dataForm.idLibro;
      hiddenDataTags.childNodes[1].value = dataForm.portadaLibro;
      hiddenDataTags.childNodes[2].value = dataForm.enlaceAPI;
    }
  }

  function getCategorias () {
    const datosLibroModal = document.querySelector("#datos-libro-modal");
    const customTagifyTag = crearCustomTagify();
    if (dataForm.idLibro != null) customTagifyTag.setAttribute("id-libro", dataForm.idLibro);
    datosLibroModal.append(customTagifyTag);

    anhadirUltimoElementoForm(customTagifyTag);

    const categoriasHiddenGroup = form.querySelector(".categorias-tagify");

    if (dataForm.categorias != null) {
      dataForm.categorias.forEach((categoria) => {
        const categoriaHidden = document.createElement("input");
        categoriaHidden.name = `categorias-tagify-${dataForm.idLibro}[]`;
        categoriaHidden.type = "hidden";
        categoriaHidden.value = categoria;

        categoriasHiddenGroup.append(categoriaHidden);
      });
    }
  }

  function generarInputHiddenFields () {
    const idLibroTag = document.createElement("input");
    idLibroTag.type = "hidden";
    idLibroTag.name = "idLibroEstante";
    idLibroTag.value = dataForm.idLibro;

    const portadaTag = document.createElement("input");
    portadaTag.type = "hidden";
    portadaTag.name = "portada";
    portadaTag.value = dataForm.portadaLibro;

    const enlaceAPILibroTag = document.createElement("input");
    enlaceAPILibroTag.type = "hidden";
    enlaceAPILibroTag.name = "enlaceAPI";
    enlaceAPILibroTag.value = dataForm.enlaceAPI;

    const categoriasLibro = document.createElement("div");
    categoriasLibro.className = "categorias-tagify";
    categoriasLibro.style.display = "none";

    return [idLibroTag, portadaTag, enlaceAPILibroTag, categoriasLibro];
  }

  function anhadirUltimoElementoForm (ultimoElementoForm) {
    const submitButton = form.querySelector("button[type=submit]");

    // Se inserta el último elemento del form inmediatamante anterior al submit button:
    form.insertBefore(ultimoElementoForm, submitButton);
  }
}

function getCamposForm () {
  const camposForm = document.querySelector("#datos-libro-modal > form");

  return {
    "portada": camposForm.querySelector("custom-image-uploader"),
    "titulo": camposForm.querySelector("#titulo"),
    "subtitulo": camposForm.querySelector("#subtitulo"),
    "descripcion": camposForm.querySelector("#descripcion"),
    "autoria": camposForm.querySelector("#autoria"),
    "numPaginas": camposForm.querySelector("#num-paginas"),
    "editorial": camposForm.querySelector("#editorial"),
    "anhoPublicacion": camposForm.querySelector("#anho-publicacion"),
    "estadoPendiente": camposForm.querySelector(".grupo-estados-libro > #pendiente"),
    "estadoLeyendo": camposForm.querySelector(".grupo-estados-libro > #leyendo"),
    "estadoLeido": camposForm.querySelector(".grupo-estados-libro > #leido")
  };
}

function crearCustomTagify () {
  const datosLibroModal = document.querySelector("#datos-libro-modal");
  let customTagifyTag = datosLibroModal.querySelector("custom-tagify");

  if (customTagifyTag != null) {
    customTagifyTag.remove();
    const categoriasHidden = datosLibroModal.querySelector(".categorias-tagify");
    categoriasHidden.replaceChildren();
  }

  customTagifyTag = document.createElement("custom-tagify");
  customTagifyTag.setAttribute("data-id", "categorias");
  customTagifyTag.setAttribute("data-name", "categorias");
  return customTagifyTag;
}
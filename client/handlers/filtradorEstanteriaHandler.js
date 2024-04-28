const estanteria = document.querySelector(".estanteria");
let librosDOM = document.querySelectorAll(".libro");

// Variables globales para controlar los filtros introducidos:
let filtros = {
  titulo: "",
  autoria: "",
  editorial: "",
  anhoPublicacion: "",
  estados: ["0", "1", "2"],
  categorias: []
};

if (librosDOM.length > 0) prepararListenersFiltros();


// --- FUNCIÓN MAIN ---
function prepararListenersFiltros () {
  const camposFiltrador = getInputsTextFiltrador();
  const estadosFiltrador = document.querySelectorAll("input[type=checkbox]");
  const tagifyFiltrador = document.querySelector("custom-tagify").shadowRoot.querySelector("input");

  setFiltroTextListener(camposFiltrador[0], "titulo");
  setFiltroTextListener(camposFiltrador[1], "autoria");
  setFiltroTextListener(camposFiltrador[2], "editorial");
  setFiltroTextListener(camposFiltrador[3], "anhoPublicacion");
  estadosFiltrador.forEach((estadoFiltro) => setFiltroEstadoListener(estadoFiltro));
  setFiltroTagifyListener(tagifyFiltrador);
}

// Función a la que se llamará con cada input en los campos filtradores por parte de la persona usuaria
function actualizarLibrosFiltrados () {
  let librosOcultados = 0;
  librosDOM.forEach((libroDOM) => {
    const datosLibro = getDatosLibroDOM(libroDOM);
    // "Flags" internas para comprobar que cumple todos los filtros introducidos simultáneamente
    let tituloOk = true;
    let autoriaOk = true;
    let editorialOk = true;
    let anhoPublicacionOk = true;
    let estadoOk = true;
    let categoriasOk = true;
    const incluidaEnLibro = categoria => datosLibro.categorias.includes(categoria);

    if (filtros.titulo != "" && !datosLibro.titulo.includes(filtros.titulo)) tituloOk = false;
    if (filtros.autoria != "" && !datosLibro.autoria.includes(filtros.autoria)) autoriaOk = false;
    if (filtros.editorial != "" && !datosLibro.editorial.includes(filtros.editorial)) editorialOk = false;
    if (filtros.anhoPublicacion != "" && !datosLibro.anhoPublicacion.startsWith(filtros.anhoPublicacion)) anhoPublicacionOk = false;
    if (!filtros.estados.includes(datosLibro.estado)) estadoOk = false;
    if (filtros.categorias.length > 0 && !filtros.categorias.some(incluidaEnLibro)) categoriasOk = false;


    if (tituloOk && autoriaOk && editorialOk && anhoPublicacionOk && estadoOk && categoriasOk) {
      libroDOM.style.display = "flex"; // Mostrar el libro si cumple con ambos filtros
    } else {
      libroDOM.style.display = "none"; // Ocultar el libro si no cumple con algún filtro
      librosOcultados += 1;
    }
  });

  // Si no se han encontrado libros con los filtros aplicados:
  if (librosOcultados == librosDOM.length) mostrarPlaceholder(true);
  else mostrarPlaceholder(false);
}


// --- FUNCIONES COMPLEMENTARIAS ---
function getInputsTextFiltrador () {
  const inputsTextFiltrador = document.querySelectorAll(".grupo-filtros > label > input:not(input[type=submit])");

  return inputsTextFiltrador;
}

function getDatosLibroDOM (libroDOM) {
  const datosLibroDOM = libroDOM.querySelector("form[name=datosLibro]");
  const categoriasLibroDOM = libroDOM.querySelectorAll("form[name=categorias-hidden] > input");
  let categorias = [];
  categoriasLibroDOM.forEach((categoriaLibroDOM) => {
    categorias.push(categoriaLibroDOM.value);
  });

  return {
    "titulo": datosLibroDOM.querySelector("input[name=titulo]").value.toLowerCase(),
    "autoria": datosLibroDOM.querySelector("input[name=autoria]").value.toLowerCase(),
    "editorial": datosLibroDOM.querySelector("input[name=editorial]").value.toLowerCase(),
    "anhoPublicacion": datosLibroDOM.querySelector("input[name=anhoPublicacion]").value,
    "estado": datosLibroDOM.querySelector("input[name=estado]").value,
    "categorias": categorias
  };
}

function setFiltroTextListener (campoFiltro, tipo) {
  campoFiltro.addEventListener("keyup", () => {
    filtros[tipo] = campoFiltro.value.toLowerCase();
    actualizarLibrosFiltrados();
  });
}

function setFiltroEstadoListener (estadoFiltro) {
  estadoFiltro.addEventListener("click", () => {
    if (estadoFiltro.checked) filtros.estados.push(estadoFiltro.value);
    else filtros.estados = filtros.estados.filter((valor) => valor != estadoFiltro.value);
    actualizarLibrosFiltrados();
  })
}

function setFiltroTagifyListener (tagifyFiltro) {
  tagifyFiltro.addEventListener("keypress", (event) => {
    if (event.code == "Enter" || event.code == "Comma") {
      filtros.categorias.push(tagifyFiltro.value);

      const tagGenerada = document.querySelector("custom-tagify").shadowRoot.querySelector(`.tags-group > span[value='${tagifyFiltro.value}']`);
      const removeTagButton = tagGenerada.querySelector("button");
      removeTagButton.addEventListener("click", () => {
        filtros.categorias = filtros.categorias.filter((valor) => valor != tagGenerada.textContent);
        actualizarLibrosFiltrados();
      });

      actualizarLibrosFiltrados();
    }
  });
}

function mostrarPlaceholder (mostrar) {
  const placeholderEstanteria = document.querySelector(".filters-not-found");

  if (mostrar) placeholderEstanteria.style.display = "flex";
  else placeholderEstanteria.style.display = "none";

}
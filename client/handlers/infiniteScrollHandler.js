import { prepararListenersLibro } from "/client/handlers/listenersBotonesLibroHandler.js";

// Variables globales
let bloquearPeticion = false;

["scroll", "touchmove"].forEach(function (event) {
  window.addEventListener(event, cargarNuevosLibros);
});
setCargarEstanteriaListener();


function cargarNuevosLibros () {
  const filtersNotFoundImageTag = document.querySelector(".filters-not-found");
  const librosCargados = document.querySelectorAll(".libro");
  const ultimoLibro = librosCargados[librosCargados.length - 1];

  const scrollHeight = window.scrollY;
  const clientHeight = document.documentElement.clientHeight;
  const currentScroll = scrollHeight + clientHeight;
  const limitScroll = ultimoLibro?.offsetTop ?? filtersNotFoundImageTag.offsetTop;

  if ((currentScroll >= limitScroll) && !bloquearPeticion) getNuevosLibrosDelimitados();
}

// Evento que traerá todos los libros almacenados en la estantería de la persona usuaria si existe un libro con los filtros aplicados pero no aparece:
function setCargarEstanteriaListener () {
  const botonCargarEstanteria = document.createElement("button");
  botonCargarEstanteria.className = "submit-btn";
  botonCargarEstanteria.textContent = "Cargar todos los libros";

  const placeholder = document.querySelector(".filters-not-found");

  placeholder.appendChild(botonCargarEstanteria);

  botonCargarEstanteria.addEventListener("click", () => {
    getNuevosLibrosDelimitados(false);
    botonCargarEstanteria.remove();
    placeholder.querySelector("small").textContent = "No hay ningún libro en tu estantería con los filtros aplicados.";
  });
}


function getNuevosLibrosDelimitados (delimitados = true) {
  const idUsuario = document.querySelector(".estanteria").getAttribute("id-usuario");
  const placeholder = document.querySelector(".filters-not-found");
  const librosCargados = document.querySelectorAll(".libro");

  if (!bloquearPeticion) {
    fetchLibrosIds(idUsuario, librosCargados.length, delimitados)
      .then(response => {
        placeholder.insertAdjacentHTML("beforebegin", response);
        actualizarLibrosFiltrados();
        prepararListenersLibro();

        bloquearPeticion = (response == "") ? true : false; // Una vez la petición no devuelve más resultados, se "bloquea" la llamada al back
      })
      .catch(error => {
        console.error("Error al cargar los libros: ", error);
        bloquearPeticion = false;
      });
  }
}

async function fetchLibrosIds (idUsuario, indexUltimoLibro, limitado) {
  try {
    const response = await fetch(`http://localhost/server/API.php?tipoPeticion=getLibros&idUsuario=${idUsuario}&paginacion=${indexUltimoLibro}&limitado=${limitado}`);
    const data = await response.text();

    return data;
  } catch (error) {
    throw new Error("Error al obtener los IDs de los libros: ", error);
  }
}
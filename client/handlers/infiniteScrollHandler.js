import { prepararListenersLibro } from "/client/handlers/listenersBotonesLibroHandler.js";

// Variables globales
const estanteria = document.querySelector(".estanteria");
const idUsuario = estanteria.getAttribute("id-usuario");
let bloquearPeticion = false;

["scroll", "touchmove"].forEach(function (event) {
  window.addEventListener(event, cargarNuevosLibros);
});

function cargarNuevosLibros () {
  const filtersNotFoundImageTag = document.querySelector(".filters-not-found");
  let librosCargados = document.querySelectorAll(".libro");
  const ultimoLibro = librosCargados[librosCargados.length - 1];

  const scrollHeight = window.scrollY;
  const clientHeight = document.documentElement.clientHeight;
  const currentScroll = scrollHeight + clientHeight;
  const limitScroll = ultimoLibro?.offsetTop ?? filtersNotFoundImageTag.offsetTop;

  if ((currentScroll >= limitScroll) && !bloquearPeticion) {
    bloquearPeticion = true;

    fetchLibrosIds(idUsuario, librosCargados.length)
      .then(response => {
        filtersNotFoundImageTag.insertAdjacentHTML("beforebegin", response);
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

async function fetchLibrosIds (idUsuario, indexUltimoLibro) {
  try {
    const response = await fetch(`http://localhost/server/API.php?tipoPeticion=getLibros&idUsuario=${idUsuario}&paginacion=${indexUltimoLibro}`);
    const data = await response.text();

    return data;
  } catch (error) {
    throw new Error("Error al obtener los IDs de los libros: ", error);
  }
}
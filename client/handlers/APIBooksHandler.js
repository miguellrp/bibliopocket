import { Libro } from "../classes/Libro.js";

/* VARIABLES GLOBALES */
const MAX_RESULTADOS = 15;

export async function buscarLibroAPI () {
  const buscadorTag = document.getElementById("buscador-libro");
  const terminoBuscado = eliminarEspaciosExtra(buscadorTag.value);

  if (terminoBuscado != "") {
    let resultadosAPI = await fetch(`https://www.googleapis.com/books/v1/volumes?q=${terminoBuscado}&maxResults=${MAX_RESULTADOS}`);
    let librosEncontrados = await resultadosAPI.json();

    console.log(librosEncontrados)
    librosEncontrados = parsearResultadosAPI(librosEncontrados);
    mostrarResultadosAPI(librosEncontrados);
  }
}

function parsearResultadosAPI (librosJSON) {
  let librosItems = [];

  librosJSON.items.forEach(item => {
    const dataLibro = item["volumeInfo"];
    const portadaLibro = dataLibro["imageLinks"] === undefined
      ? "/bibliopocket/client/assets/images/portadas/placeholder-portada-libro.webp"
      : dataLibro["imageLinks"]["thumbnail"];

    librosItems.push(new Libro(
      item["id"],
      dataLibro["title"] || "Título no disponible",
      dataLibro["subtitle"] || "",
      dataLibro["authors"] || ["Autoría no disponible"],
      dataLibro["description"] || "Descripción no disponible",
      portadaLibro,
      dataLibro["pageCount"] || 0,
      dataLibro["publisher"] || "Editorial no disponible",
      dataLibro["publishedDate"],
      dataLibro["infoLink"],
      "Pendiente"
    ))
  });

  return librosItems;
}

function mostrarResultadosAPI (arrayResultados) {
  const resultadosTag = document.querySelector(".resultados-busqueda");

  resultadosTag.innerHTML = "";
  arrayResultados.forEach(libro => {
    const libroTag = crearLibroTagAPI(libro);
    resultadosTag.appendChild(libroTag);
  });
}

function crearLibroTagAPI (libro) {
  const libroTag = document.createElement("div");
  libroTag.setAttribute("class", "libro");

  libroTag.innerHTML = `
    <p class="titulo" title="${libro["titulo"]}">${libro["titulo"]}</p>
    <small class="subtitulo" title="${libro["subtitulo"]}">${libro["subtitulo"]}</small>
    <a href="${libro["enlaceAPI"]}" target="_blank">
      <img class="portada" src=${libro["portada"]}>
    </a>
    <p class="autoria" title="${libro["autoria"]}">${libro["autoria"]}</p>
    <p class="anho-publicacion">(${libro["anhoPublicacion"]})</p>
  `;
  /* Se pasan también dentro del form de cada libro los input hidden como método para pasar
  los valores correspondientes en caso de que la persona usuaria lo añada a su estantería */
  libroTag.innerHTML += generarForm(libro);

  return libroTag;
}

function generarForm (libro) {
  return `
    <form action="" method="POST">
      <div>
        <input type="submit" value="➕" name="anhadir-libro">
        <input type="button" value="ℹ">
      </div>
      <input type="hidden" name="id" value="${libro["id"]}">
      <input type="hidden" name="titulo" value="${libro["titulo"]}">
      <input type="hidden" name="subtitulo" value="${libro["subtitulo"]}">
      <input type="hidden" name="portada" value="${libro["portada"]}">
      <input type="hidden" name="autoria" value="${libro["autoria"]}">
      <input type="hidden" name="descripcion" value="${libro["descripcion"]}">
      <input type="hidden" name="categorias" value="${libro["categorias"]}">
      <input type="hidden" name="numPaginas" value="${libro["numPaginas"]}">
      <input type="hidden" name="editorial" value="${libro["editorial"]}">
      <input type="hidden" name="anhoPublicacion" value="${libro["anhoPublicacion"]}">
      <input type="hidden" name="enlaceAPI" value="${libro["enlaceAPI"]}">
      <input type="hidden" name="estado" value="${libro["estado"]}">
    </form>
  `;
}


/* Funcións complementarias */
function eliminarEspaciosExtra (cadena) {
  return cadena.trim();
}
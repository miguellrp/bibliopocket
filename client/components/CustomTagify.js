class CustomTagify extends HTMLElement {
  constructor () {
    super();
    this.attachShadow({ mode: "open" });
  }

  connectedCallback () {
    const id = this.getAttribute("data-id");
    const name = this.getAttribute("data-name");
    const idLibroAsociado = this.getAttribute("id-libro");
    const tipoFiltro = this.getAttribute("tipo-filtro") == "true" ? true : false;
    const categoriasFiltradas = this.getAttribute("categorias-filtradas");

    this.shadowRoot.innerHTML = /* html */`
    <style>
      .custom-tagify {
        width: 100%;

        & .input-main {
          width: 96.5%;
          padding: 7px;
          margin: unset;
          

          font-family: LTCushion;
          font-size: 15px;
          outline: none;
          border: 2px solid var(--primary-color);
          background-color: color-mix(in srgb, var(--secondary-lighten-color), white 30%);

          transition: .6s ease;

          &:focus-visible {
            box-shadow: 0 0 0 3.5px var(--primary-color);
          }
        }

        & #tags-list {
          padding: 10px;
          background-color: red;

          & option {
            background-color: red;
          }
        }
      }

      .tags-group  {
        display: none;
        flex-direction: row;
        flex-wrap: wrap;
        max-width: 100%;
        margin: 10px 0;

        & .tag {
          display: flex;
          justify-content: center;
          align-items: center;
          
          margin: 5px;
          padding: 2px 1px 2px 10px;

          outline: none;
          border: 2px dashed var(--secondary-contrast-color);
          border-radius: 20px;

          
          text-wrap: nowrap;
          font-size: 13px;
          font-weight: bold;
          color: var(--secondary-contrast-color);
          background-color: #ffffff70;

          animation: pop-in .4s ease;

          &::selection  {
            background-color: var(--primary-color);
          }

          & button {
            border: none;
            outline: none;
            background-color: transparent;

            & .icon {
              height: 16px;
              width: 16px;

              border-radius: 30px;
              fill: var(--secondary-contrast-color);

              cursor: pointer;
              transition: .3s ease;

              &:hover {
                background-color: #ffffff80;
              }
            }
          }
        }
      }

      .tags-group:has(span) {
        display: flex;
      }

      @media (max-width: 760px) {
        .custom-tagify {
          flex-direction: column;
        }
      }

    /* Animación al crear las tags: */
    @keyframes pop-in {
      0% {
          opacity: 0;
          transform: scale(0);
      }
      100% {
          opacity: 1;
          transform: scale(1);
      }
    }
    </style>
    <div class="custom-tagify">
      <input type="text" list="tags-list" id="${id}" class="input-main" name=${name} autocomplete="off">
      <div class="tags-group"></div>
    </div>
    `;

    this.setListenerGenerarNuevaTag(idLibroAsociado, tipoFiltro);
    if (!tipoFiltro) this.getCategorias(idLibroAsociado, tipoFiltro);
  }

  setListenerGenerarNuevaTag (idLibroAsociado, tipoFiltro) {
    const tagifyInput = this.shadowRoot.querySelector(".input-main");
    const tags = this.shadowRoot.querySelector(".tags-group");

    tagifyInput.addEventListener("keypress", (key) => {
      if ((key.code == "Enter" || key.code == "Comma") && tagifyInput.value != "") {
        const nuevaTag = generarTag(tagifyInput.value);
        if (tipoFiltro) nuevaTag.setAttribute("value", tagifyInput.value);

        this.setListenerEliminarTag(nuevaTag, tipoFiltro);
        tags.style.display = "flex";
        tags.append(nuevaTag);

        if (!tipoFiltro) {
          const categoriasGroup = document.querySelector("#datos-libro-modal > form > .datos-libro > .categorias-tagify");
          const nuevaHiddenTag = generarHiddenTag(tagifyInput.value, idLibroAsociado);
          categoriasGroup.append(nuevaHiddenTag);
        }

        setTimeout(() => { tagifyInput.value = "" }, 1);
      }
    });
  }

  setListenerEliminarTag (tag, tipoFiltro) {
    const tags = this.shadowRoot.querySelector(".tags-group");
    const eliminarInput = document.createElement("button");
    const eliminarSVG = document.createElementNS("http://www.w3.org/2000/svg", "svg");
    const iconCruz = document.createElementNS("http://www.w3.org/2000/svg", "use");

    eliminarSVG.setAttribute("class", "icon");
    iconCruz.setAttribute("href", "/bibliopocket/client/assets/images/x-icon.svg#x-icon");
    eliminarSVG.append(iconCruz);
    eliminarInput.append(eliminarSVG);

    // Se eliminan tanto la tag mostrada como la oculta asociada a ésta
    eliminarInput.addEventListener("click", () => {
      if (!tipoFiltro) {
        const hiddenTag = document.querySelector(`.categorias-tagify > input[value='${tag.textContent}']`);
        hiddenTag.remove();
      }

      tag.remove();

      if (!tags.hasChildNodes()) tags.style.display = "none";
    });
    tag.append(eliminarInput);
  }

  getCategorias (idLibroAsociado, tipoFiltro) {
    const tags = this.shadowRoot.querySelector(".tags-group");
    const idLibroHidden = document.querySelector(`form > input[name='id'][value='${idLibroAsociado}']`);
    const libroVinculado = idLibroHidden.parentNode.parentNode;
    const categoriasLibroInputs = libroVinculado.querySelectorAll("input[name='categorias[]']");

    categoriasLibroInputs.forEach((inputHiddenCategoria) => {
      const tag = generarTag(inputHiddenCategoria.value);

      this.setListenerEliminarTag(tag, tipoFiltro);
      tags.append(tag);
    });
  }
}

customElements.define("custom-tagify", CustomTagify);


function generarTag (value) {
  const tag = document.createElement("span");
  tag.className = "tag";
  tag.textContent = value;
  tag.name = "tag";

  return tag;
}

function generarHiddenTag (value, idLibro) {
  const hiddenTag = document.createElement("input");
  hiddenTag.name = `categorias-tagify-${idLibro}[]`;
  hiddenTag.type = "hidden";
  hiddenTag.value = value;

  return hiddenTag
}
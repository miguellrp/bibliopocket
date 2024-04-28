class CustomImageUploader extends HTMLElement {
  constructor () {
    super();
    this.attachShadow({ mode: "open" });
    this.internals_ = this.attachInternals();
  }

  connectedCallback () {
    this.render();
  }

  render () {
    const imagenPreview = this.getAttribute("data-src") ?? "/bibliopocket/client/assets/images/user-pics/placeholderUserPic.webp";
    const nameInputTag = this.getAttribute("data-name") ?? "userProfilePic";

    const anchoPreview = this.getAttribute("width-preview") ?? "128px";
    const altoPreview = this.getAttribute("height-preview") ?? "128px";
    const borderType = this.getAttribute("border-type") ?? "3px solid var(--primary-color)";
    const borderRadius = this.getAttribute("border-radius") ?? "50%";

    const lapizIcon = "/bibliopocket/client/assets/images/pencil-icon.png";

    this.shadowRoot.innerHTML = /* html */ `
    <style>
      :root {
        --ancho-previewer: ${anchoPreview};
        --alto-previewer: ${altoPreview};
      }

      .wrap-image-uploader {
        position: relative;

        width: ${anchoPreview};
        max-width: ${anchoPreview};
        height: ${altoPreview};
        max-height:${altoPreview};

        & .preview {
          width: ${anchoPreview};
          max-width: ${anchoPreview};
          height: ${altoPreview};
          max-height:${altoPreview};

          border: ${borderType};
          border-radius: ${borderRadius};
          transition: filter .4s ease;
        }

        & .wrap-uploader {
          & .lapiz-icon {
            top: 50%;
            left: 50%;
            position: absolute;
            padding: 5px;
            border-radius: 10px;
            background-color: #00000080;

            transform: translate(-50%, -50%);
            transition: background .3s ease-in-out;
            cursor: pointer;

            &:hover {
              background-color: #000000;
            }
          }

          & #uploader-input {
            display: none;
          
            & label::before {
              content: none;
            }
          }
        }
      }
    </style>
    <div class="wrap-image-uploader">
      <img src="${imagenPreview}" class="preview" alt="Previsualización de la nueva imagen subida">
      <div class="wrap-uploader" >
        <label for="uploader-input">
          <img src="${lapizIcon}" class="lapiz-icon">
        </label>
        <input id="uploader-input" type="file" accept="image/*" name="${nameInputTag}" />
      </div>
    </div>
    `;

    this.setOnChangeListener();
  }

  static get observedAttributes () {
    return ["data-src"];
  }

  attributeChangedCallback () {
    this.render();
  }

  setOnChangeListener () {
    const uploader = this.shadowRoot.querySelector("#uploader-input");
    const previewer = this.shadowRoot.querySelector(".preview");

    uploader.addEventListener("change", () => {
      this.previsualizarNuevaImagen(uploader, previewer);
      this.generarImagenHidden();
    });
  }

  previsualizarNuevaImagen () {
    const uploader = this.shadowRoot.querySelector("#uploader-input");
    const preview = this.shadowRoot.querySelector(".preview");

    const [nuevaImagen] = uploader.files
    if (nuevaImagen) {
      const nuevaImagenURL = URL.createObjectURL(nuevaImagen);
      preview.src = nuevaImagenURL;
    }
  }

  generarImagenHidden () {
    const nameForm = this.getAttribute("data-form");
    const form = (nameForm != null) ? document.querySelector(`form[name=${nameForm}`) : document.querySelector("form");
    const inputUploader = this.shadowRoot.querySelector("#uploader-input");
    let inputHidden = document.querySelector(".hidden-uploader");

    if (inputHidden == null) {
      inputHidden = document.createElement("input");
      inputHidden.className = "hidden-uploader";
      inputHidden.type = "file";
      inputHidden.style.display = "none";
      inputHidden.name = this.getAttribute("data-name") ?? "userProfilePic";
    }

    const fileList = new DataTransfer();
    fileList.items.add(inputUploader.files[0]);
    inputHidden.files = fileList.files;

    form.appendChild(inputHidden);

    this.eliminarPortadaOriginal();
  }

  eliminarPortadaOriginal () {
    const form = document.querySelector(`form[name=${this.getAttribute("data-form")}`);

    if (form != null) {
      const portadaOriginal = form.querySelector(".datos-libro > input[name=portada]");

      if (portadaOriginal != null) portadaOriginal.remove();
    }
  }

  // GETTER - SETTER para "data-src"
  get src () {
    return this.getAttribute("data-src");
  }

  set src (value) {
    if (value) {
      this.setAttribute("data-src", value);
    } else {
      this.removeAttribute("data-src");
    }
  }
}

customElements.define("custom-image-uploader", CustomImageUploader);
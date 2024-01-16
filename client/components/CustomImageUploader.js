/* TO-DO ✍️: Revisar conexión con form padre para permitir la recogida de valores del custom-component */

class CustomImageUploader extends HTMLElement {
  static formAssociated = true;

  constructor () {
    super();
    this.attachShadow({ mode: "open" });
    this.internals_ = this.attachInternals();
  }

  connectedCallback () {
    const imagenPreview = this.getAttribute("data-src");
    const nameInputTag = this.getAttribute("name");

    const anchoPreview = this.getAttribute("preview-width");
    const altoPreview = this.getAttribute("preview-height");
    const borderRadius = this.getAttribute("border-radius");

    const lapizIcon = "/bibliopocket/client/assets/images/pencil-icon.png";

    this.shadowRoot.innerHTML = /* html */ `
    <style>
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

          border: 3px solid var(--primary-color);
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

    this.uploader = this.shadowRoot.querySelector("#uploader-input");
    this.uploader.addEventListener("change", this);
  }

  handleEvent (event) {
    if (event.type === "change") this.previsualizarNuevaImagen();
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
}

customElements.define("custom-image-uploader", CustomImageUploader);
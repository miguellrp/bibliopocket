class CustomToast extends HTMLElement {
  constructor () {
    super();
    this.attachShadow({ mode: "open" });
  }

  connectedCallback () {
    this.shadowRoot.innerHTML = /* html */`
    <style>
      div {
        display: flex;
        flex-direction: row;
        align-items: center;
        column-gap: 10px;

        position: fixed;
        bottom: 15px;
        left: 50%;
        z-index: 5;
        transform: translateX(-50%);

        padding: 10px;
        opacity: 0;
        transition: .4s ease;

        & .icon {
          width: 29px;
          height: 24px;
          fill: white;
        }

        & p {
          margin: unset;

          color: white;
          font-size: 13px;
          text-align: center;
          text-wrap: balance;

          &::selection {
            background-color: #5b3e13ba;
          }
        }
      }

      .toast.show {
        opacity: 1;
        transform: translate(-50%, -10px);
      }

      @media (max-width: 600px) {
        div {
          flex-direction: column;
          justify-content: center;
          
          & p {
            margin-top: 5px;
          }
        }
      }
    </style>
    <div class="toast">
      <svg class="icon">
        <use></use>
      </svg>
      <p></p>
    </div>
    `;

    const mensaje = this.getAttribute("data-mensaje");
    // Por defecto, el toast será de tipo "info":
    const tipo = this.getAttribute("data-tipo") || "info";

    this.setMensaje(mensaje);
    this.setTipo(tipo);
  }

  // "Métodos" del CustomToast para permitir actualizar sus características
  showToast (duracion = 3500) {
    const toast = this.shadowRoot.querySelector(".toast");

    setTimeout(() => toast.classList.add("show"), 200);
    setTimeout(() => {
      toast.classList.remove("show");
    }, duracion);
  }

  setMensaje (mensaje) {
    const mensajeTag = this.shadowRoot.querySelector(".toast > p");

    mensajeTag.textContent = mensaje;
  }

  setTipo (tipo) {
    const toast = this.shadowRoot.querySelector(".toast");
    const iconTag = toast.querySelector("svg > use");

    let backgroundColor;
    let icon;

    switch (tipo.toLowerCase()) {
      case "warning":
        backgroundColor = "#ea9737";
        icon = "/client/assets/images/warning-icon.svg#warning-icon";
        break;

      case "ok":
        backgroundColor = "#19ce0a";
        icon = "/client/assets/images/ok-icon.svg#ok-icon";
        break;

      case "error":
        backgroundColor = "#d53737";
        icon = "/client/assets/images/error-icon.svg#error-icon";
        break;

      default:
        backgroundColor = "#8f9cff";
        icon = "/client/assets/images/info-icon.svg#info-icon";
    }

    toast.style.backgroundColor = backgroundColor;
    iconTag.setAttribute('href', icon);
  }
}

customElements.define("custom-toast", CustomToast);
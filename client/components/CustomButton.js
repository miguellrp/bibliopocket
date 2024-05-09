class CustomButton extends HTMLElement {
  constructor () {
    super();
    this.attachShadow({ mode: "open" });
  }

  connectedCallback () {
    const contenido = this.getAttribute("data-contenido");
    const backgroundColor = this.getAttribute("background-color") || "transparent";
    const borderColor = this.getAttribute("border-color") || "transparent";
    const fontColor = this.getAttribute("font-color") || "var(--primary-color)";
    const fontSize = this.getAttribute("font-size") || "14px";

    const withTranslation = (this.getAttribute("with-translation") == "false") ? "none" : "translate(2px, 2px)";
    const withShadow = (this.getAttribute("with-shadow") == "false") ? "none" : "4px 4px 0 color-mix(in srgb, var(--background-color) 1%, black 55%)";

    this.shadowRoot.innerHTML = /* html */`
    <style>
      input {
        padding: 10px;
        outline: 3px solid transparent;
        border: 2px solid ${borderColor};
        border-radius: 5px;
        box-shadow: ${withShadow};
        margin: 0 10px;
        text-shadow: none;

        font-family: LTCushion;
        font-size: ${fontSize};
        font-weight: bold;
        color: ${fontColor};
        background-color: ${backgroundColor};

        transition: .2s ease;
        cursor: pointer;

        &:active, &:focus-visible {
          transform: ${withTranslation};
          box-shadow:0 0 0 var(--background-color);
          background-color: color-mix(in srgb, ${backgroundColor} 80%, black);
        }

        &:focus-visible {
          outline: 3px solid var(--secondary-color);
        }
      }
    </style>
    <input type="button" value="${contenido}">
    `;
  }
}

customElements.define("custom-button", CustomButton);
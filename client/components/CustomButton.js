class CustomButton extends HTMLElement {
  constructor () {
    super();
    this.attachShadow({ mode: "open" });
  }

  connectedCallback () {
    const contenido = this.getAttribute("data-contenido");
    const backgroundColor = this.getAttribute("background-color") || "transparent";
    const borderColor = this.getAttribute("border-color") || "var(--primary-color)";
    const fontColor = this.getAttribute("font-color") || "var(--primary-color)";

    this.shadowRoot.innerHTML = /* html */`
    <style>
      input {
        padding: 10px;
        outline: none;
        border: 2px solid ${borderColor};
        border-radius: 5px;
        box-shadow: 4px 4px 0 color-mix(in srgb, var(--background-color) 1%, black 55%);
        margin: 0 10px;
        text-shadow: none;

        font-family: LTCushion;
        font-size: 14px;
        font-weight: bold;
        color: ${fontColor};
        background-color: ${backgroundColor};

        transition: .2s ease;
        cursor: pointer;

        &:active{
          transform: translate(2px, 2px);
          box-shadow:0 0 0 var(--background-color);
          background-color: color-mix(in srgb, ${backgroundColor} 85%, black);
        }

        &:focus-visible {
          transform: translate(2px, 2px);
          box-shadow:0 0 0 var(--background-color);

          background-color: color-mix(in srgb, ${backgroundColor} 85%, black);
        }
      }
    </style>
    <input type="button" value="${contenido}">
    `;
  }
}

customElements.define("custom-button", CustomButton);
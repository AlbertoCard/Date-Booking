<template>
    <div class="qr-container">
      <div ref="qr"></div>
    </div>
  </template>
  
  <script>
  import QRCodeStyling from "qr-code-styling";
  
  export default {
    props: {
      value: { type: String, required: true },
      logo: { type: String, default: "" }
    },
    mounted() {
      this.qrCode = new QRCodeStyling({
        width: 120,
        height: 120,
        data: this.value,
        image: this.logo || undefined,
        qrOptions: {
          errorCorrectionLevel: "M"
        },
        dotsOptions: {
          color: "#3f3f46", // Gris neutro oscuro
          type: "dots"
        },
        backgroundOptions: {
          color: "#f9fafb" // Gris muy claro, casi blanco
        },
        cornersSquareOptions: {
          color: "#64748b", // Azul grisáceo
          type: "extra-rounded"
        },
        cornersDotOptions: {
          color: "#64748b",
          type: "dot"
        },
        imageOptions: {
          crossOrigin: "anonymous",
          margin: 2
        }
      });
  
      this.qrCode.append(this.$refs.qr);
    },
    watch: {
      value(newVal) {
        this.qrCode.update({ data: newVal });
      },
      logo(newLogo) {
        this.qrCode.update({ image: newLogo });
      }
    }
  };
  </script>
  
  <style scoped>
  .qr-container {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 12px;
    border-radius: 12px;
    background: #ffffff;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
  }
  </style>
  
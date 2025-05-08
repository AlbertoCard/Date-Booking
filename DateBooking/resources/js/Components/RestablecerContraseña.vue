<template>
    <div class="reset-password">
      <h2>Restablecer Contraseña</h2>
      <input v-model="email" type="email" placeholder="Ingresa tu correo" />
      <button @click="resetPassword">Enviar Correo de Restablecimiento</button>
      <p v-if="message" :class="messageType">{{ message }}</p>
    </div>
  </template>
  
  <script>
  import { sendPasswordResetEmail } from "firebase/auth";
  import { auth } from "../firebase";
  
  export default {
    data() {
      return {
        email: '',
        message: '',
        messageType: 'error'
      };
    },
    methods: {
      async resetPassword() {
        try {
          if (!this.email) {
            this.message = "Por favor, ingresa un correo electrónico.";
            this.messageType = "error";
            return;
          }
          await sendPasswordResetEmail(auth, this.email);
          this.message = "Correo de restablecimiento enviado. Revisa tu bandeja de entrada.";
          this.messageType = "success";
        } catch (error) {
          console.error("Error al enviar el correo de restablecimiento: ", error.message);
          this.message = "Error al enviar el correo de restablecimiento.";
          this.messageType = "error";
        }
      }
    }
  };
  </script>
  
  <style scoped>
  .reset-password {
    display: flex;
    flex-direction: column;
    align-items: center;
    max-width: 400px;
    margin: 0 auto;
    padding: 20px;
    background-color: #f4f4f9;
    border-radius: 10px;
  }
  
  input {
    margin: 10px 0;
    padding: 10px;
    width: 100%;
    border: 1px solid #ccc;
    border-radius: 5px;
  }
  
  button {
    padding: 10px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
  }
  
  button:hover {
    background-color: #0056b3;
  }
  
  p {
    margin-top: 10px;
  }
  
  .success {
    color: green;
  }
  
  .error {
    color: red;
  }
  </style>
  
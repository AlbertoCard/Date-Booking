<!-- resources/js/Components/Dashboard.vue -->
<template>
    <div>
      <h1>Bienvenido al Dashboard</h1>
      <p>¡Felicidades! Has iniciado sesión correctamente.</p>
      <button @click="logout">Cerrar sesión</button>
    </div>
  </template>
  
  <script>
  import { auth } from '../firebase';  // Importamos la configuración de Firebase
  import { signOut } from 'firebase/auth';  // Método de Firebase para cerrar sesión
  
  export default {
    methods: {
      logout() {
        signOut(auth).then(() => {
          this.$router.push('/login');  // Redirigir al login después de cerrar sesión
        }).catch((error) => {
          console.log('Error al cerrar sesión:', error.message);
        });
      },
    },
  };
  </script>
  
  <style scoped>
  button {
    padding: 10px;
    background-color: #f44336;
    color: white;
    border: none;
    cursor: pointer;
  }
  button:hover {
    background-color: #d32f2f;
  }
  </style>
  
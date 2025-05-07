<template>
    <div class="flex min-h-screen bg-gray-100">
      <!-- Izquierda -->
      <div class="hidden md:flex w-1/2 flex-col items-center justify-center p-12">
        <h1 class="text-3xl font-bold mb-4">DATE-BOOKING</h1>
        <div class="w-64 h-64 bg-gray-300 mb-6"></div>
        <p class="text-gray-600 text-sm text-center">Reserva tus citas de manera rápida y segura con nuestra plataforma.</p>
      </div>
  
      <!-- Derecha: Login -->
      <div class="flex w-full md:w-1/2 items-center justify-center bg-white rounded-l-3xl shadow-lg">
        <div class="w-full max-w-md p-8">
          <h2 class="text-2xl font-bold mb-6 text-center">Inicia Sesión y vive la experiencia!!</h2>
  
          <!-- Botón Google -->
          <button
            @click="loginWithGoogle"
            class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded flex items-center justify-center gap-2 mb-4">
            <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" class="h-5 w-5" />
            Inicia sesión con Google
          </button>
  
          <p class="text-center text-gray-500 text-sm mb-4">o usa tu correo electrónico</p>
  
          <!-- Formulario -->
          <form @submit.prevent="login">
            <div class="mb-4">
              <label class="block text-gray-700 mb-1">Correo electrónico</label>
              <div class="relative">
                <input 
                  type="email" 
                  id="email"
                  v-model="email" 
                  required
                  placeholder="tucorreo@ejemplo.com"
                  class="w-full border border-gray-300 rounded px-4 py-2 pr-10 focus:outline-none focus:ring focus:border-blue-300" />
                <span class="absolute right-3 top-2.5 text-green-500">✔️</span>
              </div>
            </div>
  
            <div class="mb-6">
              <label class="block text-gray-700 mb-1">Contraseña</label>
              <div class="relative">
                <input 
                  type="password" 
                  id="password"
                  v-model="password" 
                  required
                  placeholder="••••••••"
                  class="w-full border border-gray-300 rounded px-4 py-2 pr-10 focus:outline-none focus:ring focus:border-blue-300" />
                <span class="absolute right-3 top-2.5 text-green-500">✔️</span>
              </div>
            </div>
  
            <button
              type="submit"
              class="w-full bg-black text-white py-2 rounded hover:bg-gray-800 font-semibold">Iniciar Sesión</button>
          </form>
  
          <p v-if="errorMessage" class="text-center text-red-500 text-sm mt-4">{{ errorMessage }}</p>
  
          <p class="text-center text-sm text-gray-600 mt-4">
            ¿No tienes cuenta?
            <router-link to="/register" class="text-blue-600 hover:underline">¡Regístrate!</router-link>
          </p>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import { auth } from '../firebase';
  import { signInWithEmailAndPassword, GoogleAuthProvider, signInWithPopup } from 'firebase/auth';
  
  export default {
    data() {
      return {
        email: '',
        password: '',
        errorMessage: '',
      };
    },
    methods: {
      async login() {
        try {
          const userCredential = await signInWithEmailAndPassword(auth, this.email, this.password);
          console.log('Usuario autenticado:', userCredential.user);
          this.$router.push('/dashboard');
        } catch (error) {
          this.errorMessage = this.translateFirebaseError(error.code);
        }
      },
      async loginWithGoogle() {
        try {
          const provider = new GoogleAuthProvider();
          const result = await signInWithPopup(auth, provider);
          console.log('Usuario autenticado con Google:', result.user);
          this.$router.push('/dashboard');
        } catch (error) {
          this.errorMessage = this.translateFirebaseError(error.code);
        }
      },
      translateFirebaseError(code) {
        const errors = {
          'auth/invalid-email': 'Correo electrónico no válido',
          'auth/user-disabled': 'Cuenta deshabilitada',
          'auth/user-not-found': 'Usuario no encontrado',
          'auth/wrong-password': 'Contraseña incorrecta',
          'auth/too-many-requests': 'Demasiados intentos. Intenta más tarde',
          'auth/popup-closed-by-user': 'Has cerrado la ventana de autenticación',
          'auth/cancelled-popup-request': 'Solicitud de autenticación cancelada',
        };
        return errors[code] || 'Error al iniciar sesión';
      }
    },
  };
  </script>
  
  <style scoped>
  /* Estilos adicionales si los necesitas */
  </style>
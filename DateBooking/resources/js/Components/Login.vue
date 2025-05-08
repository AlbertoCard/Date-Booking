<template>
    <div class="flex min-h-screen bg-gray-100">
      <!-- Izquierda (opcional) -->
      <div class="hidden md:flex w-1/2 flex-col items-center justify-center p-12">
        <h1 class="text-3xl font-bold mb-4">DateBooking</h1>
        <div class="w-64 h-64 bg-gray-300 mb-6"></div>
        <p class="text-gray-600 text-sm text-center">
          Accede fácilmente con tu cuenta o con Google.
        </p>
      </div>
  
      <!-- Derecha: Login -->
      <div class="flex w-full md:w-1/2 items-center justify-center bg-white rounded-l-3xl shadow-lg">
        <div class="w-full max-w-md p-8">
          <h2 class="text-2xl font-bold mb-6 text-center">Inicia Sesión</h2>
  
          <!-- Google -->
          <button
            @click="loginWithGoogle"
            class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded flex items-center justify-center gap-2 mb-4"
          >
            <img
              src="https://www.svgrepo.com/show/475656/google-color.svg"
              alt="Google"
              class="h-5 w-5"
            />
            Inicia sesión con Google
          </button>
  
          <p class="text-center text-gray-500 text-sm mb-4">o usa tu correo electrónico</p>
  
          <!-- Formulario -->
          <form @submit.prevent="login">
            <div class="mb-4">
              <label class="block text-gray-700 mb-1">Correo electrónico</label>
              <input
                type="email"
                v-model="email"
                required
                class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring focus:border-blue-300"
                placeholder="usuario@ejemplo.com"
              />
            </div>
  
            <div class="mb-6">
              <label class="block text-gray-700 mb-1">Contraseña</label>
              <input
                type="password"
                v-model="password"
                required
                class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring focus:border-blue-300"
                placeholder="••••••••"
              />
            </div>
  
            <button
              type="submit"
              class="w-full bg-black text-white py-2 rounded hover:bg-gray-800 font-semibold"
            >
              Iniciar Sesión
            </button>
          </form>
  
          <p class="text-center text-sm text-gray-600 mt-4">
            ¿Olvidaste tu contraseña?
            <router-link to="/reset-password" class="text-blue-600 hover:underline">
              Restablécela aquí
            </router-link>
          </p>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  import { useRouter } from 'vue-router';
  import { signInWithEmailAndPassword, signInWithPopup } from 'firebase/auth';
  import { auth, googleProvider } from '../firebase';
  
  const router = useRouter();
  const email = ref('');
  const password = ref('');
  
  const login = async () => {
    try {
      await signInWithEmailAndPassword(auth, email.value, password.value);
      alert('Inicio de sesión exitoso');
      router.push('/dashboard');
    } catch (error) {
      alert('Error al iniciar sesión: ' + error.message);
    }
  };
  
  const loginWithGoogle = async () => {
    try {
      await signInWithPopup(auth, googleProvider);
      router.push('/taskmanager');
    } catch (error) {
      alert('Error con Google Sign-In: ' + error.message);
      console.error(error);
    }
  };
  </script>
  
  <style scoped>
  /* Puedes agregar aquí estilos adicionales si lo deseas */
  </style>
  
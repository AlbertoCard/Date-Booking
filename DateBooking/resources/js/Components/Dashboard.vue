<!-- resources/js/Components/Dashboard.vue -->
<template>
    <div>
      <h1>Bienvenido al Dashboard</h1>
      <p>¡Felicidades! Has iniciado sesión correctamente.</p>
      <p v-if="currentUser">Usuario actual: {{ currentUser.email }}</p>
      <button @click="logout" :disabled="loading">
        {{ loading ? 'Cerrando sesión...' : 'Cerrar sesión' }}
      </button>
      <p v-if="error" class="error-message">{{ error }}</p>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue';
  import { useRouter } from 'vue-router';
  import { auth } from '../firebase';
  import { signOut, onAuthStateChanged } from 'firebase/auth';
  import axios from 'axios';
  
  const router = useRouter();
  const loading = ref(false);
  const currentUser = ref(null);
  const error = ref('');
  
  onMounted(() => {
    onAuthStateChanged(auth, async (user) => {
      currentUser.value = user;
      console.log('Usuario actual:', user ? user.uid : 'No hay usuario');
      
      if (user) {
        try {
          // Activar el usuario en la base de datos
          const response = await axios.put(`/api/usuarios/${user.uid}/activar`);
          console.log('Usuario activado:', response.data);
        } catch (error) {
          console.error('Error al activar usuario:', error);
          // No mostramos el error al usuario ya que no es crítico
        }
      }
    });
  });
  
  const logout = async () => {
    loading.value = true;
    error.value = '';
    console.log('Iniciando proceso de cierre de sesión');
    
    try {
      // Primero actualizamos el estado en la base de datos
      if (currentUser.value) {
        const uid = currentUser.value.uid;
        console.log('Intentando actualizar estado para UID:', uid);
        
        try {
          const response = await axios.put(`/api/usuarios/${uid}/activo`);
          console.log('Respuesta de la API:', response.data);
        } catch (apiError) {
          console.error('Error al actualizar estado:', apiError.response?.data || apiError.message);
          // Continuamos con el cierre de sesión aunque falle la actualización del estado
          console.log('Continuando con el cierre de sesión a pesar del error');
        }
      }
  
      // Luego cerramos sesión en Firebase
      await signOut(auth);
      console.log('Sesión cerrada en Firebase');
      router.push('/login');
    } catch (error) {
      console.error('Error en el proceso de cierre de sesión:', error);
      error.value = 'Error al cerrar sesión: ' + error.message;
    } finally {
      loading.value = false;
    }
  };
  </script>
  
  <style scoped>
  button {
    padding: 10px 20px;
    background-color: #f44336;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s;
  }
  
  button:hover {
    background-color: #d32f2f;
  }
  
  button:disabled {
    background-color: #cccccc;
    cursor: not-allowed;
  }
  
  h1 {
    color: #333;
    margin-bottom: 20px;
  }
  
  p {
    color: #666;
    margin-bottom: 30px;
  }

  .error-message {
    color: #dc2626;
    background-color: #fee2e2;
    padding: 10px;
    border-radius: 4px;
    margin: 10px 0;
  }
  </style>
  
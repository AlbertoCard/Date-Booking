<template>
  <div class="min-h-screen bg-gray-100">
    <!-- Barra de navegación -->
    <nav class="bg-white shadow-lg">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex">
            <div class="flex-shrink-0 flex items-center">
              <h1 class="text-xl font-bold text-gray-800">Dashboard Establecimiento</h1>
            </div>
          </div>
          <div class="flex items-center">
            <button @click="cerrarSesion" class="ml-4 px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-md">
              Cerrar Sesión
            </button>
          </div>
        </div>
      </div>
    </nav>

    <!-- Contenido principal -->
    <main class="py-10">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <h2 class="text-2xl font-semibold text-gray-800 mb-4">Bienvenido, {{ nombreEstablecimiento }}</h2>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { auth } from '../firebase'
import { signOut } from 'firebase/auth'
import axios from 'axios'

const router = useRouter()
const nombreEstablecimiento = ref('')

onMounted(async () => {
  // Verificar si el usuario está autenticado y es un establecimiento
  const user = auth.currentUser
  if (!user) {
    router.push('/login')
    return
  }

  try {
    // Obtener información del usuario del localStorage
    const userData = JSON.parse(localStorage.getItem('userData'))
    
    if (!userData || userData.rol !== 'establecimiento') {
      router.push('/dashboard-cliente')
      return
    }

    // Obtener información del establecimiento
    const estabResponse = await axios.get(`/api/establecimientos/usuario/${user.uid}`)
    nombreEstablecimiento.value = estabResponse.data.establecimientos[0].nombre
  } catch (error) {
    console.error('Error al cargar datos:', error)
    router.push('/login')
  }
})

const cerrarSesion = async () => {
  try {
    await signOut(auth)
    localStorage.removeItem('userData')
    router.push('/login')
  } catch (error) {
    console.error('Error al cerrar sesión:', error)
  }
}
</script> 
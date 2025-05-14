<template>
  <div class="min-h-screen bg-gray-100">
    <nav class="bg-white shadow-lg">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex">
            <div class="flex-shrink-0 flex items-center">
              <h1 class="text-xl font-bold text-gray-800">Dashboard Cliente</h1>
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

    <main class="py-10">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <h2 class="text-2xl font-semibold text-gray-800 mb-4">Bienvenido, {{ nombreUsuario }}</h2>
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
const nombreUsuario = ref('')

onMounted(async () => {
  // Verificar si el usuario está autenticado y es un cliente
  const user = auth.currentUser
  if (!user) {
    router.push('/login')
    return
  }

  try {
    // Obtener información del usuario
    const response = await axios.get(`/api/usuarios/obtener/${user.uid}`)
    if (response.data.usuario.rol !== 'cliente') {
      router.push('/dashboard-establecimiento')
      return
    }
    nombreUsuario.value = response.data.usuario.nombre
  } catch (error) {
    console.error('Error al cargar datos:', error)
  }
})

const cerrarSesion = async () => {
  try {
    await signOut(auth)
    router.push('/login')
  } catch (error) {
    console.error('Error al cerrar sesión:', error)
  }
}
</script> 
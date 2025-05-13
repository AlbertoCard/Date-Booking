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
          
          <!-- Sección de Próximas Citas -->
          <div class="mb-8">
            <h3 class="text-lg font-medium text-gray-700 mb-4">Próximas Citas</h3>
            <div v-if="citas.length > 0" class="grid gap-4 grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
              <div v-for="cita in citas" :key="cita.id" class="bg-gray-50 p-4 rounded-lg shadow">
                <p class="font-semibold">{{ cita.establecimiento }}</p>
                <p class="text-gray-600">{{ cita.fecha }}</p>
                <p class="text-gray-600">{{ cita.hora }}</p>
                <button class="mt-2 px-3 py-1 text-sm text-red-600 hover:text-red-800" @click="cancelarCita(cita.id)">
                  Cancelar
                </button>
              </div>
            </div>
            <p v-else class="text-gray-600">No tienes citas programadas</p>
          </div>

          <!-- Sección de Buscar Establecimientos -->
          <div>
            <h3 class="text-lg font-medium text-gray-700 mb-4">Buscar Establecimientos</h3>
            <div class="flex gap-4 mb-4">
              <input 
                type="text" 
                v-model="busqueda" 
                placeholder="Buscar establecimiento..." 
                class="flex-1 px-4 py-2 border rounded-md"
              >
              <button 
                @click="buscarEstablecimientos" 
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
              >
                Buscar
              </button>
            </div>

            <div v-if="establecimientos.length > 0" class="grid gap-4 grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
              <div v-for="establecimiento in establecimientos" :key="establecimiento.id" class="bg-gray-50 p-4 rounded-lg shadow">
                <h4 class="font-semibold">{{ establecimiento.nombre }}</h4>
                <p class="text-gray-600">{{ establecimiento.direccion }}</p>
                <button 
                  @click="verDisponibilidad(establecimiento.id)" 
                  class="mt-2 px-3 py-1 bg-green-600 text-white rounded-md hover:bg-green-700"
                >
                  Ver Disponibilidad
                </button>
              </div>
            </div>
            <p v-else-if="busquedaRealizada" class="text-gray-600">No se encontraron establecimientos</p>
          </div>
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
const citas = ref([])
const establecimientos = ref([])
const busqueda = ref('')
const busquedaRealizada = ref(false)

onMounted(async () => {
  // Verificar si el usuario está autenticado y es un cliente
  const user = auth.currentUser
  if (!user) {
    router.push('/login')
    return
  }

  try {
    // Obtener información del usuario
    const response = await axios.get(`/api/usuarios/${user.uid}`)
    if (response.data.usuario.rol !== 'cliente') {
      router.push('/dashboard-establecimiento')
      return
    }
    nombreUsuario.value = response.data.usuario.nombre

    // Cargar citas del usuario
    await cargarCitas()
  } catch (error) {
    console.error('Error al cargar datos:', error)
  }
})

const cargarCitas = async () => {
  try {
    const response = await axios.get(`/api/citas/usuario/${auth.currentUser.uid}`)
    citas.value = response.data.citas
  } catch (error) {
    console.error('Error al cargar citas:', error)
  }
}

const buscarEstablecimientos = async () => {
  try {
    const response = await axios.get('/api/establecimientos', {
      params: { busqueda: busqueda.value }
    })
    establecimientos.value = response.data.establecimientos
    busquedaRealizada.value = true
  } catch (error) {
    console.error('Error al buscar establecimientos:', error)
  }
}

const verDisponibilidad = (establecimientoId) => {
  router.push(`/disponibilidad/${establecimientoId}`)
}

const cancelarCita = async (citaId) => {
  if (!confirm('¿Estás seguro de que deseas cancelar esta cita?')) return

  try {
    await axios.delete(`/api/citas/${citaId}`)
    await cargarCitas()
  } catch (error) {
    console.error('Error al cancelar cita:', error)
  }
}

const cerrarSesion = async () => {
  try {
    await signOut(auth)
    router.push('/login')
  } catch (error) {
    console.error('Error al cerrar sesión:', error)
  }
}
</script> 
<template>
  <div class="min-h-screen bg-gray-100">
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

    <main class="py-10">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <h2 class="text-2xl font-semibold text-gray-800 mb-4">{{ establecimiento.nombre }}</h2>
          
          <!-- Información del Establecimiento -->
          <div class="mb-8 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-gray-50 p-4 rounded-lg">
              <h3 class="text-lg font-medium text-gray-700 mb-2">Información General</h3>
              <div class="space-y-2">
                <p><span class="font-medium">Dirección:</span> {{ establecimiento.direccion }}</p>
                <p><span class="font-medium">Teléfono:</span> {{ establecimiento.telefono }}</p>
                <p><span class="font-medium">RFC:</span> {{ establecimiento.rfc }}</p>
                <button 
                  @click="mostrarEditarInfo = true" 
                  class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                >
                  Editar Información
                </button>
              </div>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg">
              <h3 class="text-lg font-medium text-gray-700 mb-2">Estadísticas</h3>
              <div class="space-y-2">
                <p><span class="font-medium">Citas Hoy:</span> {{ estadisticas.citasHoy }}</p>
                <p><span class="font-medium">Citas Pendientes:</span> {{ estadisticas.citasPendientes }}</p>
                <p><span class="font-medium">Citas Completadas:</span> {{ estadisticas.citasCompletadas }}</p>
              </div>
            </div>
          </div>

          <!-- Citas del Día -->
          <div class="mb-8">
            <h3 class="text-lg font-medium text-gray-700 mb-4">Citas de Hoy</h3>
            <div v-if="citasHoy.length > 0" class="grid gap-4 grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
              <div v-for="cita in citasHoy" :key="cita.id" class="bg-gray-50 p-4 rounded-lg shadow">
                <p class="font-semibold">{{ cita.cliente }}</p>
                <p class="text-gray-600">{{ cita.hora }}</p>
                <p class="text-gray-600">{{ cita.servicio }}</p>
                <div class="mt-2 space-x-2">
                  <button 
                    @click="completarCita(cita.id)" 
                    class="px-3 py-1 bg-green-600 text-white rounded-md hover:bg-green-700"
                  >
                    Completar
                  </button>
                  <button 
                    @click="cancelarCita(cita.id)" 
                    class="px-3 py-1 bg-red-600 text-white rounded-md hover:bg-red-700"
                  >
                    Cancelar
                  </button>
                </div>
              </div>
            </div>
            <p v-else class="text-gray-600">No hay citas programadas para hoy</p>
          </div>

          <!-- Modal de Editar Información -->
          <div v-if="mostrarEditarInfo" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
              <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Editar Información</h3>
                <form @submit.prevent="guardarInformacion" class="space-y-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Nombre</label>
                    <input 
                      type="text" 
                      v-model="editInfo.nombre" 
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                    >
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Dirección</label>
                    <input 
                      type="text" 
                      v-model="editInfo.direccion" 
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                    >
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Teléfono</label>
                    <input 
                      type="text" 
                      v-model="editInfo.telefono" 
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                    >
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">RFC</label>
                    <input 
                      type="text" 
                      v-model="editInfo.rfc" 
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                    >
                  </div>
                  <div class="flex justify-end space-x-3">
                    <button 
                      type="button"
                      @click="mostrarEditarInfo = false"
                      class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300"
                    >
                      Cancelar
                    </button>
                    <button 
                      type="submit"
                      class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                    >
                      Guardar
                    </button>
                  </div>
                </form>
              </div>
            </div>
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
const establecimiento = ref({})
const citasHoy = ref([])
const estadisticas = ref({
  citasHoy: 0,
  citasPendientes: 0,
  citasCompletadas: 0
})
const mostrarEditarInfo = ref(false)
const editInfo = ref({
  nombre: '',
  direccion: '',
  telefono: '',
  rfc: ''
})

onMounted(async () => {
  // Verificar si el usuario está autenticado y es un establecimiento
  const user = auth.currentUser
  if (!user) {
    router.push('/login')
    return
  }

  try {
    // Obtener información del usuario y establecimiento
    const userResponse = await axios.get(`/api/usuarios/${user.uid}`)
    if (userResponse.data.usuario.rol !== 'establecimiento') {
      router.push('/dashboard-cliente')
      return
    }

    // Obtener información del establecimiento
    const estabResponse = await axios.get(`/api/establecimientos/usuario/${user.uid}`)
    establecimiento.value = estabResponse.data.establecimientos[0]

    // Inicializar datos de edición
    editInfo.value = {
      nombre: establecimiento.value.nombre,
      direccion: establecimiento.value.direccion,
      telefono: establecimiento.value.telefono,
      rfc: establecimiento.value.rfc
    }

    // Cargar citas y estadísticas
    await Promise.all([
      cargarCitasHoy(),
      cargarEstadisticas()
    ])
  } catch (error) {
    console.error('Error al cargar datos:', error)
  }
})

const cargarCitasHoy = async () => {
  try {
    const response = await axios.get(`/api/citas/establecimiento/${establecimiento.value.id_establecimiento}/hoy`)
    citasHoy.value = response.data.citas
  } catch (error) {
    console.error('Error al cargar citas:', error)
  }
}

const cargarEstadisticas = async () => {
  try {
    const response = await axios.get(`/api/establecimientos/${establecimiento.value.id_establecimiento}/estadisticas`)
    estadisticas.value = response.data.estadisticas
  } catch (error) {
    console.error('Error al cargar estadísticas:', error)
  }
}

const completarCita = async (citaId) => {
  try {
    await axios.put(`/api/citas/${citaId}/completar`)
    await Promise.all([
      cargarCitasHoy(),
      cargarEstadisticas()
    ])
  } catch (error) {
    console.error('Error al completar cita:', error)
  }
}

const cancelarCita = async (citaId) => {
  if (!confirm('¿Estás seguro de que deseas cancelar esta cita?')) return

  try {
    await axios.delete(`/api/citas/${citaId}`)
    await Promise.all([
      cargarCitasHoy(),
      cargarEstadisticas()
    ])
  } catch (error) {
    console.error('Error al cancelar cita:', error)
  }
}

const guardarInformacion = async () => {
  try {
    await axios.put(`/api/establecimientos/${establecimiento.value.id_establecimiento}`, editInfo.value)
    establecimiento.value = { ...establecimiento.value, ...editInfo.value }
    mostrarEditarInfo.value = false
  } catch (error) {
    console.error('Error al actualizar información:', error)
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
<template>
  <div class="flex flex-col h-screen">
    <!-- Solo mostrar Topbar y Sidebar si no estamos en una ruta de autenticación -->
    <template v-if="!isAuthRoute">
      <Topbar :toggle-sidebar="toggleSidebar" />

      <div class="flex flex-1 overflow-hidden">
        <Sidebar v-model="isSidebarOpen" :on-option-selected="toggleSidebar" />

        <main class="flex-1 overflow-y-auto p-4">
          <router-view />
        </main>
      </div>
    </template>

    <!-- Si estamos en una ruta de autenticación, solo mostrar el contenido -->
    <template v-else>
      <router-view />
    </template>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRoute } from 'vue-router'
import Sidebar from './Sidebar.vue'
import Topbar from './Topbar.vue'
import { onMounted, onUnmounted } from 'vue'

const route = useRoute()
const isSidebarOpen = ref(true)

// Comprobar si estamos en una ruta de autenticación
const isAuthRoute = computed(() => {
  return route.path.includes('/login') || 
         route.path.includes('/registro') || 
         route.path.includes('/reset-password')
})

function handleResize() {
  if (window.innerWidth <= 768) {
    isSidebarOpen.value = false
  } else {
    isSidebarOpen.value = true
  }
}

function toggleSidebar() {
  isSidebarOpen.value = !isSidebarOpen.value
}

// Add event listeners for resize
onMounted(() => {
  handleResize()
  window.addEventListener('resize', handleResize)
})

onUnmounted(() => {
  window.removeEventListener('resize', handleResize)
})
</script>

<style>
body {
  background-color: whitesmoke;
  margin: 0;
  padding: 0;
}
</style>
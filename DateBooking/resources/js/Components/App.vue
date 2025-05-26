<template>
  <div class="flex flex-col h-screen">
    <Topbar :toggle-sidebar="toggleSidebar" />

    <div class="flex flex-1 overflow-hidden">
      <Sidebar v-model="isSidebarOpen" :on-option-selected="toggleSidebar" />

      <main class="flex-1 overflow-y-auto p-4">
        <router-view />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import Sidebar from './Sidebar.vue'
import Topbar from './Topbar.vue'
import { onMounted, onUnmounted } from 'vue'

const isSidebarOpen = ref(true)
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
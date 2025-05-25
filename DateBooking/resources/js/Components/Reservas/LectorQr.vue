<template>
  <div>
    <h2>Escáner QR</h2>
    <qrcode-stream @decode="onDecode" @init="onInit" />
    <p v-if="decodedContent">Contenido leído: {{ decodedContent }}</p>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { QrcodeStream } from 'vue-qrcode-reader'

const decodedContent = ref('')

function onDecode(content) {
  decodedContent.value = content
}

function onInit(promise) {
  promise.catch(error => {
    console.error('Error al iniciar la cámara:', error)
  })
}
</script>

<style scoped>
qrcode-stream {
  display: block;
  max-width: 400px;
  margin: 1rem auto;
  border: 2px solid #444;
  border-radius: 10px;
}
</style>
<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { auth } from '../firebase.js'
import { createUserWithEmailAndPassword } from 'firebase/auth'

// Refs para formulario
const correo = ref('')
const contrasena = ref('')
const error = ref('')
const router = useRouter()

// Función de registro
const registrar = async () => {
  error.value = ''
  try {
    const userCredential = await createUserWithEmailAndPassword(auth, correo.value, contrasena.value)
    console.log('Usuario registrado:', userCredential.user)

    // Aquí podrías redirigir o mostrar un mensaje de éxito
    router.push('/bienvenida') // Ejemplo de redirección
  } catch (e) {
    console.error('Error al registrar:', e)
    error.value = 'Ocurrió un error al registrarse: ' + e.message
  }
}

const activeTab = ref('cliente'); // Por defecto, mostrar la pestaña de cliente (aunque este formulario es básico)
</script>

<template>
  <div class="container">
    <h1>¡Te damos la bienvenida a Date-Boking!</h1>
    <h2>Regístrate como Cliente o como Establecimiento</h2>

    <div class="tab-container">
      <button
        class="tab"
        :class="{ active: activeTab === 'cliente' }"
        @click="activeTab = 'cliente'"
      >
        Cliente
      </button>
      <button
        class="tab"
        :class="{ active: activeTab === 'establecimiento' }"
        @click="activeTab = 'establecimiento'"
      >
        Establecimiento
      </button>
    </div>

    <div class="tab-content active">
      <div class="form-layout client">
        <div class="image-placeholder">Imagen</div>
        <div class="form-container">
          <h2 class="form-title">Registro de Cliente</h2>
          <form @submit.prevent="registrar">
            <div class="form-group">
              <label for="correo">Ingresa tu correo</label>
              <input v-model="correo" type="email" id="correo" placeholder="Tu correo" required />
              <span class="checkmark">&#10004;</span>
            </div>
            <div class="form-group">
              <label for="contrasena">Ingresa una contraseña</label>
              <input
                v-model="contrasena"
                type="password"
                id="contrasena"
                placeholder="Tu contraseña"
                required
              />
              <span class="checkmark">&#10004;</span>
            </div>
            <div v-if="error" class="error-message">{{ error }}</div>
            <div class="checkbox-group">
              <input type="checkbox" id="terminos" required />
              <label for="terminos">Acepta los términos y condiciones</label>
            </div>
            <button type="submit" class="register-button">Registrarse</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.container {
  background-color: #f4f4f4; /* Fondo general claro */
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  padding: 30px;
  width: 100%; /* Ocupar el ancho completo del padre */
  max-width: 600px; /* Ancho máximo para centrar el contenido */
  margin: 20px auto; /* Centrar horizontalmente */
  text-align: center;
}

h1 {
  color: #333;
  margin-bottom: 15px;
}

h2 {
  color: #666;
  font-size: 1em;
  margin-bottom: 20px;
}

.tab-container {
  display: flex;
  margin-bottom: 20px;
  border-bottom: 1px solid #ddd;
}

.tab {
  padding: 10px 20px;
  cursor: pointer;
  border: none;
  background-color: transparent;
  font-weight: bold;
  color: #777;
  border-radius: 5px 5px 0 0;
}

.tab.active {
  background-color: #eee;
  color: #333;
}

.tab-content {
  background-color: #fff;
  padding: 20px;
  border-radius: 6px;
  margin-bottom: 20px;
}

.form-layout {
  display: flex;
  gap: 20px;
  align-items: flex-start;
}

.image-placeholder {
  width: 100px;
  height: 100px;
  border: 1px dashed #ccc;
  display: flex;
  justify-content: center;
  align-items: center;
  color: #999;
  border-radius: 5px;
}

.form-container {
  flex-grow: 1;
  text-align: left;
}

.form-title {
  font-size: 1.5em;
  margin-bottom: 15px;
  color: #333;
  text-align: center;
}

.form-group {
  margin-bottom: 15px;
  position: relative;
}

.form-group label {
  display: block;
  margin-bottom: 5px;
  color: #555;
}

.form-group input[type="email"],
.form-group input[type="password"] {
  width: calc(100% - 30px);
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-sizing: border-box;
}

.form-group .checkmark {
  position: absolute;
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
  color: green;
  font-size: 1.2em;
}

.checkbox-group {
  margin-bottom: 15px;
  display: flex;
  align-items: center;
}

.checkbox-group input[type="checkbox"] {
  margin-right: 8px;
}

.checkbox-group label {
  color: #555;
}

.register-button {
  background-color: #007bff;
  color: white;
  padding: 12px 25px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 1em;
  transition: background-color 0.3s ease;
  width: 100%;
}

.register-button:hover {
  background-color: #0056b3;
}

.error-message {
  color: #dc2626; /* Rojo para el mensaje de error */
  margin-bottom: 10px;
}
</style>
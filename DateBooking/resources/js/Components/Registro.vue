<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { auth } from '../firebase.js'
import { createUserWithEmailAndPassword, updateProfile } from 'firebase/auth'
import axios from 'axios'

// Refs para formulario
const nombre = ref('')
const correo = ref('')
const telefono = ref('')
const contrasena = ref('')
const error = ref('')
const loading = ref(false)
const router = useRouter()
const fotoUrl = ref('https://via.placeholder.com/150') // URL por defecto para la foto

// Función de registro
const registrar = async () => {
  error.value = ''
  loading.value = true

  try {
    // 1. Crear usuario en Firebase
    const userCredential = await createUserWithEmailAndPassword(auth, correo.value, contrasena.value)
    const user = userCredential.user

    // 2. Actualizar el perfil del usuario en Firebase con el nombre
    await updateProfile(user, {
      displayName: nombre.value,
      photoURL: fotoUrl.value
    })

    // 3. Guardar datos adicionales en nuestra base de datos
    try {
      const response = await axios.post('/api/usuarios', {
        uid: user.uid,
        nombre: nombre.value,
        email: correo.value,
        telefono: telefono.value,
        foto_url: fotoUrl.value,
        rol: activeTab.value // 'cliente' o 'establecimiento'
      })

      console.log('Usuario guardado en la base de datos:', response.data)
      
      // 4. Redireccionar al dashboard
      router.push('/dashboard')
    } catch (apiError) {
      console.error('Error al guardar en la base de datos:', apiError)
      error.value = 'Error al guardar los datos: ' + (apiError.response?.data?.message || apiError.message)
    }
  } catch (firebaseError) {
    console.error('Error con Firebase:', firebaseError)
    switch (firebaseError.code) {
      case 'auth/email-already-in-use':
        error.value = 'Este correo electrónico ya está registrado'
        break
      case 'auth/invalid-email':
        error.value = 'El correo electrónico no es válido'
        break
      case 'auth/operation-not-allowed':
        error.value = 'El registro con correo y contraseña no está habilitado'
        break
      case 'auth/weak-password':
        error.value = 'La contraseña debe tener al menos 6 caracteres'
        break
      default:
        error.value = 'Error al registrar: ' + firebaseError.message
    }
  } finally {
    loading.value = false
  }
}

const activeTab = ref('cliente') // Por defecto, mostrar la pestaña de cliente

// Validación de contraseña
const validarContrasena = () => {
  if (contrasena.value.length < 6) {
    error.value = 'La contraseña debe tener al menos 6 caracteres'
    return false
  }
  return true
}

// Validación de teléfono
const validarTelefono = () => {
  const telefonoRegex = /^\d{10}$/
  if (!telefonoRegex.test(telefono.value)) {
    error.value = 'El teléfono debe tener 10 dígitos'
    return false
  }
  return true
}
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
              <label for="nombre">Nombre completo</label>
              <input 
                v-model="nombre" 
                type="text" 
                id="nombre" 
                placeholder="Tu nombre completo" 
                required 
              />
              <span class="checkmark">&#10004;</span>
            </div>
            <div class="form-group">
              <label for="correo">Ingresa tu correo</label>
              <input 
                v-model="correo" 
                type="email" 
                id="correo" 
                placeholder="Tu correo" 
                required 
              />
              <span class="checkmark">&#10004;</span>
            </div>
            <div class="form-group">
              <label for="telefono">Teléfono</label>
              <input 
                v-model="telefono" 
                type="tel" 
                id="telefono" 
                placeholder="Tu número de teléfono" 
                required 
              />
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
            <button 
              type="submit" 
              class="register-button"
              :disabled="loading"
            >
              {{ loading ? 'Registrando...' : 'Registrarse' }}
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.container {
  background-color: #f4f4f4;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  padding: 30px;
  width: 100%;
  max-width: 600px;
  margin: 20px auto;
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

.form-group input[type="text"],
.form-group input[type="email"],
.form-group input[type="tel"],
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

.register-button:disabled {
  background-color: #cccccc;
  cursor: not-allowed;
}

.error-message {
  color: #dc2626;
  background-color: #fee2e2;
  padding: 10px;
  border-radius: 4px;
  margin-bottom: 15px;
  text-align: left;
}
</style>
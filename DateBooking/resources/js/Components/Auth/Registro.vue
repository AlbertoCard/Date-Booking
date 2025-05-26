<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { auth } from '../../firebase.js'
import { createUserWithEmailAndPassword, updateProfile } from 'firebase/auth'
import axios from 'axios'

// Refs para formulario
const nombre = ref('')
const correo = ref('')
const telefono = ref('')
const contrasena = ref('')
const loading = ref(false)
const router = useRouter()
const fotoUrl = ref('https://via.placeholder.com/150')
const activeTab = ref('cliente')
const showModal = ref(false)
const modalMessage = ref('')

const showErrorMessage = (message) => {
  modalMessage.value = message;
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  modalMessage.value = '';
};


const validarTelefonoInput = (event) => {
  const valor = event.target.value.replace(/\D/g, '')
  telefono.value = valor.slice(0, 10)
}


const validarNombreInput = (event) => {
  nombre.value = event.target.value.slice(0, 50)
}


const validarCorreoInput = (event) => {
  correo.value = event.target.value.slice(0, 100)
}


const validarContrasenaInput = (event) => {
  contrasena.value = event.target.value.slice(0, 20)
}

// Función de registro
const registrar = async () => {
  loading.value = true

  try {
    // Validaciones básicas
    if (!nombre.value.trim()) {
      showErrorMessage('Por favor, ingresa tu nombre completo');
      loading.value = false;
      return;
    }
    if (!correo.value.trim()) {
      showErrorMessage('Por favor, ingresa tu correo electrónico');
      loading.value = false;
      return;
    }
    if (!telefono.value.trim()) {
      showErrorMessage('Por favor, ingresa tu número de teléfono');
      loading.value = false;
      return;
    }
    if (telefono.value.length !== 10) {
      showErrorMessage('El número de teléfono debe tener 10 dígitos');
      loading.value = false;
      return;
    }
    if (!contrasena.value || contrasena.value.length < 6) {
      showErrorMessage('La contraseña debe tener al menos 6 caracteres');
      loading.value = false;
      return;
    }

    // Crear usuario en Firebase
    const userCredential = await createUserWithEmailAndPassword(auth, correo.value, contrasena.value)
    const user = userCredential.user

    
    await updateProfile(user, {
      displayName: nombre.value,
      photoURL: fotoUrl.value
    })

    // Guardar datos en nuestra base de datos
    try {
      console.log('Enviando datos al servidor:', {
        uid: user.uid,
        nombre: nombre.value,
        email: correo.value,
        telefono: telefono.value,
        foto_url: fotoUrl.value,
        rol: activeTab.value
      })

      const usuarioResponse = await axios.post('/api/usuarios', {
        uid: user.uid,
        nombre: nombre.value,
        email: correo.value,
        telefono: telefono.value,
        foto_url: fotoUrl.value,
        rol: activeTab.value
      })

      console.log('Respuesta del servidor (usuario):', usuarioResponse.data)

      localStorage.setItem('userData', JSON.stringify({
        uid: user.uid,
        nombre: nombre.value,
        email: correo.value,
        telefono: telefono.value,
        foto_url: fotoUrl.value,
        rol: activeTab.value
      }))

      if (activeTab.value === 'establecimiento') {
        console.log('Creando establecimiento...')
        const establecimientoResponse = await axios.post('/api/establecimientos', {
          nombre: nombre.value,
          telefono: telefono.value,
          direccion: 'Sin dirección',
          rfc: 'Sin RFC',
          estado: 'Sin estado',
          codigo_postal: '00000',
          pais: 'México',
          id_estado: 0,
          stripe_account_id: 'Sin cuenta',
          id_usuario: user.uid
        })
        console.log('Respuesta del servidor (establecimiento):', establecimientoResponse.data)
      }

      router.push('/')
    } catch (apiError) {
      console.error('Error al guardar en la base de datos:', apiError)
      if (apiError.response) {
        console.error('Respuesta del servidor:', apiError.response.data)
        showErrorMessage('Lo sentimos, ha ocurrido un error al guardar tus datos. Por favor, intenta nuevamente.');
      } else {
        showErrorMessage('Lo sentimos, ha ocurrido un error al guardar tus datos. Por favor, intenta nuevamente.');
      }

      try {
        await user.delete()
      } catch (deleteError) {
        console.error('Error al eliminar usuario de Firebase:', deleteError)
      }
    }
  } catch (firebaseError) {
    console.error('Error con Firebase:', firebaseError)
    switch (firebaseError.code) {
      case 'auth/email-already-in-use':
        showErrorMessage('Este correo electrónico ya está registrado. Por favor, utiliza otro correo o inicia sesión.');
        break;
      case 'auth/invalid-email':
        showErrorMessage('Por favor, ingresa un correo electrónico válido.');
        break;
      case 'auth/operation-not-allowed':
        showErrorMessage('Lo sentimos, el registro con correo y contraseña no está habilitado en este momento.');
        break;
      case 'auth/weak-password':
        showErrorMessage('Por favor, utiliza una contraseña más segura (mínimo 6 caracteres).');
        break;
      default:
        showErrorMessage('Lo sentimos, ha ocurrido un error al registrar tu cuenta. Por favor, intenta nuevamente.');
    }
  } finally {
    loading.value = false
  }
}

// Validación de contraseña
const validarContrasena = () => {
  if (contrasena.value.length < 6) {
    showErrorMessage('La contraseña debe tener al menos 6 caracteres');
    return false;
  }
  return true;
}

// Validación de teléfono
const validarTelefono = () => {
  const telefonoRegex = /^\d{10}$/;
  if (!telefonoRegex.test(telefono.value)) {
    showErrorMessage('El número de teléfono debe tener 10 dígitos');
    return false;
  }
  return true;
}
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-100 to-white flex items-center justify-center p-6">
    <!-- Modal de error -->
    <Transition name="fade">
      <div v-if="showModal" class="modal-backdrop" @click="closeModal">
        <div class="modal-content" @click.stop>
          <div class="modal-icon" :class="{ 'active': true }">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <h3 class="modal-title">Atención</h3>
          <p class="modal-message">{{ modalMessage }}</p>
          <div class="modal-actions">
            <button class="modal-button confirm" @click="closeModal">
              Entendido
            </button>
          </div>
        </div>
      </div>
    </Transition>

    <div class="container">
      <div class="tab-container">
        <button class="tab" :class="{ active: activeTab === 'cliente' }" @click="activeTab = 'cliente'">
          Cliente
        </button>
        <button class="tab" :class="{ active: activeTab === 'establecimiento' }" @click="activeTab = 'establecimiento'">
          Establecimiento
        </button>
      </div>

      <Transition name="slide-fade" mode="out-in">
        <div :key="activeTab" class="content-wrapper" :class="{ 'reverse': activeTab === 'establecimiento' }">
          <!-- Lado Imagen -->
          <div class="image-side">
            <div class="image-container">
              <img :src="activeTab === 'cliente' ?
                'https://img.freepik.com/free-vector/appointment-booking-with-calendar_23-2148553008.jpg' :
                'https://img.freepik.com/free-vector/shop-with-sign-we-are-open_23-2148547718.jpg'"
                :alt="activeTab === 'cliente' ? 'Cliente Registro' : 'Establecimiento Registro'"
                class="featured-image" />
            </div>
            <h3 class="image-title">
              {{ activeTab === 'cliente' ? 'Reserva tus citas fácilmente' : 'Gestiona tu negocio' }}
            </h3>
            <p class="image-description">
              {{ activeTab === 'cliente' ?
                'Encuentra y agenda citas con tus establecimientos favoritos' :
                'Administra tus reservas y horarios de manera eficiente' }}
            </p>
          </div>

          <!-- Lado Formulario -->
          <div class="form-side">
            <h2 class="form-title">
              {{ activeTab === 'cliente' ? 'Registro de Cliente' : 'Registro de Establecimiento' }}
            </h2>

            <form @submit.prevent="registrar" class="registration-form">
              <div class="form-group">
                <label for="nombre">
                  {{ activeTab === 'cliente' ? 'Nombre completo' : 'Nombre del establecimiento' }}
                </label>
                <input 
                  v-model="nombre" 
                  type="text" 
                  id="nombre"
                  @input="validarNombreInput"
                  :placeholder="activeTab === 'cliente' ? 'Tu nombre completo' : 'Nombre de tu negocio'" 
                  required 
                />
              </div>

              <div class="form-group">
                <label for="correo">Correo electrónico</label>
                <input 
                  v-model="correo" 
                  type="email" 
                  id="correo" 
                  @input="validarCorreoInput"
                  placeholder="correo@ejemplo.com" 
                  required 
                />
              </div>

              <div class="form-group">
                <label for="telefono">Teléfono de contacto</label>
                <input 
                  v-model="telefono" 
                  type="tel" 
                  id="telefono" 
                  @input="validarTelefonoInput"
                  pattern="[0-9]*"
                  inputmode="numeric"
                  placeholder="10 dígitos" 
                  required 
                />
              </div>

              <div class="form-group">
                <label for="contrasena">Contraseña</label>
                <input 
                  v-model="contrasena" 
                  type="password" 
                  id="contrasena" 
                  @input="validarContrasenaInput"
                  placeholder="Mínimo 6 caracteres"
                  required 
                />
              </div>

              <div v-if="error" class="error-message">
                {{ error }}
              </div>

              <div class="terms-group">
                <input type="checkbox" id="terminos" required />
                <label for="terminos">
                  Acepto los términos y condiciones
                </label>
              </div>

              <button type="submit" class="submit-button" :disabled="loading">
                {{ loading ? 'Procesando...' : 'Crear cuenta' }}
              </button>
            </form>
          </div>
        </div>
      </Transition>
    </div>
  </div>
</template>

<style scoped>
.container {
  background-color: white;
  border-radius: 20px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
  width: 100%;
  max-width: 1200px;
  height: 100%;
  margin: 10px;
  margin-top: -20px;
}

.tab-container {
  display: flex;
  justify-content: center;
  gap: 10px;
  padding: 20px;
  background: linear-gradient(to right, #f8fafc, #f1f5f9);
}

.tab {
  padding: 12px 30px;
  border: none;
  border-radius: 30px;
  font-weight: 600;
  font-size: 1rem;
  color: #64748b;
  background-color: transparent;
  transition: all 0.3s ease;
  cursor: pointer;
  position: relative;
  overflow: hidden;
}

.tab.active {
  background-color: #2563eb;
  color: white;
  box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
}

.tab::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  height: 2px;
  background-color: #2563eb;
  transform: scaleX(0);
  transition: transform 0.3s ease;
}

.tab.active::after {
  transform: scaleX(1);
}

.tab:hover:not(.active) {
  background-color: rgba(37, 99, 235, 0.1);
}

.content-wrapper {
  display: flex;
  min-height: 600px;
  transition: all 0.5s ease-in-out;
  position: relative;
}

.content-wrapper.reverse {
  flex-direction: row-reverse;
}

.image-side {
  flex: 1;
  padding: 40px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
}

.image-container {
  width: 100%;
  max-width: 400px;
  margin-bottom: 30px;
  transition: all 0.3s ease;
}

.featured-image {
  width: 100%;
  height: auto;
  border-radius: 12px;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
  transition: transform 0.5s ease, box-shadow 0.5s ease;
}

.image-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1e40af;
  margin-bottom: 10px;
  text-align: center;
  transition: all 0.3s ease;
}

.image-description {
  font-size: 1rem;
  color: #64748b;
  text-align: center;
  max-width: 80%;
  transition: all 0.3s ease;
}

.form-side {
  flex: 1;
  padding: 40px;
  background-color: white;
}

.form-title {
  font-size: 1.8rem;
  font-weight: 700;
  color: #1e293b;
  margin-bottom: 30px;
  text-align: center;
}

.registration-form {
  max-width: 450px;
  margin: 0 auto;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  font-weight: 500;
  color: #334155;
}

.form-group input,
.form-group select {
  width: 100%;
  padding: 12px;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.3s ease;
}

.form-group input:focus,
.form-group select:focus {
  border-color: #2563eb;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
  outline: none;
}

.terms-group {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 20px;
}

.terms-group input[type="checkbox"] {
  width: 18px;
  height: 18px;
  border-radius: 4px;
}

.submit-button {
  width: 100%;
  padding: 14px;
  background: linear-gradient(to right, #2563eb, #1d4ed8);
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.submit-button:hover {
  background: linear-gradient(to right, #1d4ed8, #1e40af);
  transform: translateY(-1px);
}

.submit-button:disabled {
  background: #94a3b8;
  cursor: not-allowed;
  transform: none;
}

.submit-button::after {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 200%;
  height: 100%;
  background: linear-gradient(120deg,
      transparent,
      rgba(255, 255, 255, 0.2),
      transparent);
  transform: translateX(-100%);
}

.submit-button:hover::after {
  transition: transform 0.7s ease;
  transform: translateX(100%);
}

.error-message {
  padding: 12px;
  background-color: #fee2e2;
  border-radius: 8px;
  color: #dc2626;
  margin-bottom: 20px;
  font-size: 0.9rem;
}

@media (max-width: 768px) {
  .content-wrapper {
    flex-direction: column;
  }

  .content-wrapper.reverse {
    flex-direction: column;
  }

  .image-side {
    padding: 20px;
  }

  .form-side {
    padding: 20px;
  }

  .container {
    margin: 10px;
  }
}

/* Animaciones de transición */
.slide-fade-enter-active,
.slide-fade-leave-active {
  transition: all 0.5s ease;
}

.slide-fade-enter-from {
  opacity: 0;
  transform: translateX(-30px);
}

.slide-fade-leave-to {
  opacity: 0;
  transform: translateX(30px);
}

/* Ajuste para la dirección de la animación cuando está en reverse */
.reverse .slide-fade-enter-from {
  transform: translateX(30px);
}

.reverse .slide-fade-leave-to {
  transform: translateX(-30px);
}

/* Mejora en la transición del tab activo */
.tab {
  position: relative;
  overflow: hidden;
}

.tab::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  height: 2px;
  background-color: #2563eb;
  transform: scaleX(0);
  transition: transform 0.3s ease;
}

.tab.active::after {
  transform: scaleX(1);
}

/* Mejora en la transición de hover de los tabs */
.tab:hover:not(.active) {
  background-color: rgba(37, 99, 235, 0.1);
}

/* Animación suave para el contenido interno */
.form-group,
.image-container,
.image-title,
.image-description {
  transition: all 0.3s ease;
}

.content-wrapper {
  position: relative;
  transition: all 0.5s ease;
}

/* Efecto de elevación suave al hover en elementos interactivos */
.form-group input:hover,
.form-group select:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.featured-image {
  transition: transform 0.5s ease, box-shadow 0.5s ease;
}

.image-side:hover .featured-image {
  transform: scale(1.02);
  box-shadow: 0 25px 30px -5px rgba(0, 0, 0, 0.15);
}

/* Animación para el botón de submit */
.submit-button {
  position: relative;
  overflow: hidden;
}

.submit-button::after {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 200%;
  height: 100%;
  background: linear-gradient(120deg,
      transparent,
      rgba(255, 255, 255, 0.2),
      transparent);
  transform: translateX(-100%);
}

.submit-button:hover::after {
  transition: transform 0.7s ease;
  transform: translateX(100%);
}

@media (max-width: 768px) {
  .image-side {
    height: 60vh;
    max-height: 100vh;
    flex: none;
    max-width: 100vw;
    overflow-y: auto;
  }
  .form-side{
    height: 80vh;
    max-height: 100vh;
    flex: none;
    max-width: 100vw;
    overflow-y: auto;
  }
  .content-wrapper {
    flex-direction: column !important;
    min-height: 100vh;
    height: 100vh;
  }
}

/* Estilos del Modal */
.modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
}

.modal-content {
  background-color: white;
  border-radius: 1rem;
  padding: 2rem;
  max-width: 90%;
  width: 400px;
  text-align: center;
  position: relative;
  transform: translateY(0);
  transition: transform 0.3s ease;
  z-index: 10000;
}

.modal-icon {
  margin: 0 auto 1.5rem;
  width: 3.5rem;
  height: 3.5rem;
  background-color: #fee2e2;
  color: #dc2626;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}

.modal-icon.active {
  background-color: #dcfce7;
  color: #16a34a;
}

.modal-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: #1e293b;
  margin-bottom: 1rem;
}

.modal-message {
  color: #64748b;
  margin-bottom: 2rem;
  line-height: 1.5;
}

.modal-actions {
  display: flex;
  gap: 1rem;
  justify-content: center;
}

.modal-button {
  padding: 0.75rem 1.5rem;
  border-radius: 0.5rem;
  font-weight: 600;
  transition: all 0.3s ease;
}

.modal-button.cancel {
  background-color: #e5e7eb;
  color: #374151;
}

.modal-button.cancel:hover {
  background-color: #d1d5db;
}

.modal-button.confirm {
  background-color: #2563eb;
  color: white;
}

.modal-button.confirm:hover {
  background-color: #1d4ed8;
}

/* Animaciones del modal */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.fade-enter-active .modal-content {
  animation: modal-in 0.3s ease-out;
}

.fade-leave-active .modal-content {
  animation: modal-out 0.3s ease-in;
}

@keyframes modal-in {
  0% {
    transform: translateY(20px);
    opacity: 0;
  }
  100% {
    transform: translateY(0);
    opacity: 1;
  }
}

@keyframes modal-out {
  0% {
    transform: translateY(0);
    opacity: 1;
  }
  100% {
    transform: translateY(20px);
    opacity: 0;
  }
}

/* Ajustes responsive para el modal */
@media (max-width: 768px) {
  .modal-content {
    width: 90%;
    padding: 1.5rem;
  }

  .modal-title {
    font-size: 1.25rem;
  }

  .modal-message {
    font-size: 0.875rem;
  }

  .modal-button {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
  }
}
</style>


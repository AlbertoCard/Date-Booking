<template>
  <Loader :visible="loading" />
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

    <div class="container bg-white rounded-2xl shadow-2xl overflow-hidden max-w-6xl mx-auto">
      <div class="flex flex-col md:flex-row">
        <!-- Lado Izquierdo: Imagen y Mensaje -->
        <div class="hidden md:flex w-1/2 bg-gradient-to-br from-blue-50 to-blue-100 p-12 relative overflow-hidden">
          <div class="absolute top-0 left-0 w-full h-full">
            <div
              class="absolute transform -rotate-45 -left-1/4 -top-1/4 w-96 h-96 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full opacity-10">
            </div>
            <div
              class="absolute transform -rotate-45 -right-1/4 -bottom-1/4 w-96 h-96 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full opacity-10">
            </div>
          </div>

          <div class="relative z-10 flex flex-col items-center justify-center w-full space-y-8">
            <div class="transform hover:scale-105 transition-transform duration-500">
              <img src="https://img.freepik.com/free-vector/appointment-booking-with-woman-calendar_23-2148559014.jpg"
                alt="Reservas Online Illustration" class="w-80 h-80 object-cover rounded-2xl shadow-lg" />
            </div>
            <div class="text-center space-y-4">
              <h1 class="text-4xl font-bold text-gray-800 tracking-tight">
                DateBooking
              </h1>
              <p class="text-gray-600 text-lg max-w-sm">
                Gestiona tus citas de manera eficiente y profesional
              </p>
            </div>
          </div>
        </div>

        <!-- Lado Derecho: Formulario -->
        <div class="w-full md:w-1/2 p-8 md:p-12 space-y-6">
          <!-- Notificación -->
          <Transition name="fade">
            <div v-if="showNotification" role="alert"
              class="bg-blue-100 dark:bg-blue-900 border-l-4 border-blue-500 dark:border-blue-700 text-blue-900 dark:text-blue-100 p-4 rounded-lg flex items-center transition duration-300 ease-in-out hover:bg-blue-200 dark:hover:bg-blue-800 transform hover:scale-105 mb-6">
              <svg stroke="currentColor" viewBox="0 0 24 24" fill="none"
                class="h-5 w-5 flex-shrink-0 mr-3 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg">
                <path d="M13 16h-1v-4h1m0-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2"
                  stroke-linejoin="round" stroke-linecap="round"></path>
              </svg>
              <p class="text-sm font-semibold flex-grow">
                Se ha enviado un correo de recuperación a {{ resetEmail }}
              </p>
              <button @click="closeNotification"
                class="ml-3 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors duration-200">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd"
                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                    clip-rule="evenodd"></path>
                </svg>
              </button>
            </div>
          </Transition>

          <div class="text-center space-y-3">
            <h2 class="text-3xl font-bold text-gray-900">¡Bienvenido de nuevo!</h2>
            <p class="text-gray-600">Nos alegra verte otra vez</p>
          </div>

          <!-- Google Button -->
          <button @click="loginWithGoogle"
            class="google-button w-full bg-white hover:bg-gray-50 text-gray-800 font-medium py-3 px-4 border border-gray-300 rounded-lg shadow-sm transition-all duration-300 flex items-center justify-center gap-3 relative overflow-hidden group">
            <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google"
              class="h-5 w-5 transition-transform duration-300 group-hover:scale-110" />
            <span class="transition-colors duration-300">Continuar con Google</span>
            <div
              class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700">
            </div>
          </button>

          <div class="relative">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-t border-gray-200"></div>
            </div>
            <div class="relative flex justify-center text-sm">
              <span class="px-4 bg-white text-gray-500">o continúa con email</span>
            </div>
          </div>

          <!-- Formulario -->
          <form @submit.prevent="login" class="space-y-5">
            <div class="form-group">
              <label class="block text-gray-700 mb-2 text-sm font-medium">Correo electrónico</label>
              <input type="email" v-model="email" required
                class="input-field w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all duration-300"
                placeholder="usuario@ejemplo.com" />
            </div>

            <div class="form-group">
              <label class="block text-gray-700 mb-2 text-sm font-medium">Contraseña</label>
              <input type="password" v-model="password" required
                class="input-field w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all duration-300"
                placeholder="••••••••" />
            </div>

            <button type="submit"
              class="login-button w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-3 rounded-lg transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98] relative overflow-hidden group">
              <span class="relative z-10">Iniciar Sesión</span>
              <div
                class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700">
              </div>
            </button>
          </form>

          <div class="space-y-4">
            <p class="text-center text-gray-600 text-sm hover:text-gray-800 transition-colors duration-300">
              <router-link to="/reset-password"
                class="text-blue-600 hover:text-blue-700 underline-offset-4 hover:underline">
                ¿Olvidaste tu contraseña?
              </router-link>
            </p>

            <div class="flex justify-center">
              <router-link to="/registro" custom v-slot="{ navigate }">
                <button @click="navigate"
                  class="register-button group relative px-6 py-3 text-lg font-semibold text-blue-600 transition-all duration-300 hover:text-blue-700">
                  <span class="relative z-10">¿No tienes cuenta? Regístrate aquí</span>
                  <div
                    class="absolute bottom-0 left-0 w-0 h-0.5 bg-blue-600 group-hover:w-full transition-all duration-300">
                  </div>
                </button>
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { signInWithEmailAndPassword, signInWithPopup, signOut } from 'firebase/auth';
import { auth, googleProvider } from '../../firebase';
import axios from 'axios';
import Loader from '../Loader.vue';

const router = useRouter();
const route = useRoute();
const email = ref('');
const password = ref('');
const showNotification = ref(false);
const resetEmail = ref('');
const showModal = ref(false);
const modalMessage = ref('');

const loading = ref(false);

onMounted(() => {
  // Verificar si hay parámetros de consulta y si el mensaje no se ha mostrado antes
  if (route.query.showNotification === 'true' && route.query.resetEmail) {
    const notificationShown = localStorage.getItem('resetEmailNotificationShown');

    if (!notificationShown) {
      resetEmail.value = route.query.resetEmail;
      showNotification.value = true;

      // Guardar en localStorage que ya se mostró la notificación
      localStorage.setItem('resetEmailNotificationShown', 'true');

      // Ocultar la notificación después de 5 segundos
      setTimeout(() => {
        showNotification.value = false;
        // Limpiar los parámetros de la URL
        router.replace({ query: {} });
      }, 5000);
    } else {
      // Si ya se mostró, solo limpiar los parámetros de la URL
      router.replace({ query: {} });
    }
  }
});

const closeNotification = () => {
  showNotification.value = false;
  // Limpiar los parámetros de la URL al cerrar manualmente
  router.replace({ query: {} });
};

const showErrorMessage = (message) => {
  modalMessage.value = message;
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  modalMessage.value = '';
};

const login = async () => {
  try {
    loading.value = true;
    const userCredential = await signInWithEmailAndPassword(auth, email.value, password.value);
    console.log('Usuario autenticado en Firebase:', userCredential.user.uid);

    try {
      const activarResponse = await axios.put(`/api/usuarios/${userCredential.user.uid}/activo`);
      console.log('Usuario activado:', activarResponse.data);

      const response = await axios.get(`/api/usuarios/obtener/${userCredential.user.uid}`);
      console.log('Datos del usuario obtenidos:', response.data);

      const userData = response.data.usuario;
      localStorage.setItem('userData', JSON.stringify(userData));

      if (userData.rol === 'establecimiento') {
        router.push('/servicio-agregados');
      } else {
        router.push('/');
      }
    } catch (apiError) {
      console.error('Error en la API:', apiError.response?.data || apiError.message);
      showErrorMessage('Lo sentimos, ha ocurrido un error al comunicarnos con el servidor. Por favor, intenta nuevamente.');
    }
  } catch (error) {
    console.error('Error de login:', error);
    if (error.code === 'auth/user-not-found' || error.code === 'auth/wrong-password') {
      showErrorMessage('El correo electrónico o la contraseña que ingresaste no son correctos. Por favor, verifica tus datos e intenta nuevamente.');
    } else if (error.code === 'auth/invalid-email') {
      showErrorMessage('Por favor, ingresa un correo electrónico válido.');
    } else if (error.code === 'auth/too-many-requests') {
      showErrorMessage('Hemos detectado demasiados intentos fallidos. Por favor, espera unos minutos antes de intentar nuevamente.');
    } else {
      showErrorMessage('Lo sentimos, ha ocurrido un error al iniciar sesión. Por favor, intenta nuevamente.');
    }
  } finally {
    loading.value = false;
  }
};

const loginWithGoogle = async () => {
  loading.value = true;
  try {
    const result = await signInWithPopup(auth, googleProvider);
    console.log('Usuario autenticado con Google:', result.user.uid);

    try {
      const response = await axios.get(`/api/usuarios/obtener/${result.user.uid}`);
      console.log('Datos del usuario obtenidos:', response.data);

      const userData = response.data.usuario;
      const activarResponse = await axios.put(`/api/usuarios/${result.user.uid}/activo`);
      console.log('Usuario activado:', activarResponse.data);

      if (userData.rol === 'establecimiento') {
        const estabResponse = await axios.get(`/api/establecimientos/usuario/${result.user.uid}`);
        if (estabResponse.data.establecimientos && estabResponse.data.establecimientos.length > 0) {
          userData.id_establecimiento = estabResponse.data.establecimientos[0].id_establecimiento;
        }
      }

      localStorage.setItem('userData', JSON.stringify(userData));

      if (userData.rol === 'establecimiento') {
        router.push('/servicio-agregados');
      } else {
        router.push('/');
      }
    } catch (apiError) {
      if (apiError.response?.status === 404) {
        console.log('Usuario no encontrado, creando nuevo usuario...');

        const newUserResponse = await axios.post('/api/usuarios', {
          uid: result.user.uid,
          nombre: result.user.displayName || 'Usuario Google',
          email: result.user.email,
          telefono: result.user.phoneNumber || '0000000000',
          foto_url: result.user.photoURL || 'https://via.placeholder.com/150',
          rol: 'cliente'
        });

        console.log('Nuevo usuario creado:', newUserResponse.data);
        const userData = newUserResponse.data.usuario;
        localStorage.setItem('userData', JSON.stringify(userData));
        router.push('/');
      } else {
        console.error('Error en la API:', apiError.response?.data || apiError.message);
        showErrorMessage('Lo sentimos, ha ocurrido un error al comunicarnos con el servidor. Por favor, intenta nuevamente.');
      }
    }
  } catch (error) {
    console.error('Error de Google Sign-In:', error);
    showErrorMessage('Lo sentimos, ha ocurrido un error al iniciar sesión con Google. Por favor, intenta nuevamente.');
  } finally {
    loading.value = false;
  }
};

const cerrarSesion = async () => {
  try {
    await signOut(auth);
    // Limpiar todos los datos del usuario
    localStorage.removeItem('userData');
    router.push('/login');
  } catch (error) {
    console.error('Error al cerrar sesión:', error);
  }
};
</script>

<style scoped>
.container {
  backdrop-filter: blur(10px);
  transform-style: preserve-3d;
  perspective: 1000px;
}

.form-group {
  transform: translateZ(0);
  transition: transform 0.3s ease;
}

.form-group:hover {
  transform: translateZ(10px);
}

.input-field {
  outline: none;
  background: rgba(255, 255, 255, 0.9);
}

.input-field:focus {
  box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2);
  transform: translateY(-1px);
}

.google-button {
  transform-style: preserve-3d;
  transition: all 0.3s ease;
}

.google-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.login-button {
  box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
}

.login-button:hover {
  box-shadow: 0 6px 12px -2px rgba(37, 99, 235, 0.3);
}

.register-button::after {
  content: '';
  position: absolute;
  width: 100%;
  transform: scaleX(0);
  height: 2px;
  bottom: 0;
  left: 0;
  background-color: currentColor;
  transform-origin: bottom right;
  transition: transform 0.3s ease;
}

.register-button:hover::after {
  transform: scaleX(1);
  transform-origin: bottom left;
}

.info {
  background: linear-gradient(to right, #2563eb, #3b82f6);
  transform: translateY(0);
  transition: all 0.3s ease;
}

.info:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
}

.fade-enter-active,
.fade-leave-active {
  transition: all 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(-10px) scale(0.95);
}

@keyframes float {
  0%,
  100% {
    transform: translateY(0);
  }

  50% {
    transform: translateY(-10px);
  }
}

.container img {
  animation: float 6s ease-in-out infinite;
}

@media (max-width: 768px) {
  .container {
    margin: 1rem;
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




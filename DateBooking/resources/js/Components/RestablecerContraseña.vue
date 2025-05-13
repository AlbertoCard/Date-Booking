<template>
  <div class="flex min-h-screen bg-gradient-to-br from-gray-100 to-white items-center justify-center p-6">
    <div class="card">
      <div class="card-inner">
        <!-- Lado izquierdo con imagen decorativa -->
        <div class="card-left">
          <img 
            src="https://img.freepik.com/free-vector/forgot-password-concept-illustration_114360-1123.jpg"
            alt="Restablecer Contraseña Illustration"
            class="w-full h-full object-cover"
          />
        </div>
        
        <!-- Lado derecho con el formulario -->
        <div class="card-right">
          <div class="content">
            <div class="header-section">
              <div class="icon-wrapper">
                <svg viewBox="0 0 24 24" class="lock-icon" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M12 14.5V16.5M7 10.0288C7.47142 10 8.05259 10 8.8 10H15.2C15.9474 10 16.5286 10 17 10.0288M7 10.0288C6.41168 10.0647 5.99429 10.1455 5.63803 10.327C5.07354 10.6146 4.61458 11.0736 4.32698 11.638C4 12.2798 4 13.1198 4 14.8V16.2C4 17.8802 4 18.7202 4.32698 19.362C4.61458 19.9264 5.07354 20.3854 5.63803 20.673C6.27976 21 7.11984 21 8.8 21H15.2C16.8802 21 17.7202 21 18.362 20.673C18.9265 20.3854 19.3854 19.9264 19.673 19.362C20 18.7202 20 17.8802 20 16.2V14.8C20 13.1198 20 12.2798 19.673 11.638C19.3854 11.0736 18.9265 10.6146 18.362 10.327C18.0057 10.1455 17.5883 10.0647 17 10.0288M7 10.0288V8C7 5.23858 9.23858 3 12 3C14.7614 3 17 5.23858 17 8V10.0288" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
              <h2 class="heading">¡Ups!</h2>
              <p class="sub-heading">¿Olvidaste tu contraseña?</p>
              <p class="sub-sub-heading">No te preocupes, te ayudamos a recuperarla</p>
            </div>

            <div class="form-section">
              <div class="input-group">
                <input 
                  v-model="email" 
                  class="email-input" 
                  placeholder="Ingresa tu correo electrónico" 
                  type="email" 
                />
                <span class="input-border"></span>
              </div>

              <button @click="resetPassword" class="reset-button">
                <span>Restablecer Contraseña</span>
                <div class="button-effect"></div>
              </button>

              <p v-if="message" :class="['message', messageType]">
                <span class="message-icon" v-if="messageType === 'success'">✓</span>
                <span class="message-icon" v-else>!</span>
                {{ message }}
              </p>

              <router-link to="/login" class="back-link">
                Volver al inicio de sesión
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { sendPasswordResetEmail } from "firebase/auth";
import { auth } from "../firebase";

const router = useRouter();
const email = ref('');
const message = ref('');
const messageType = ref('error');

const resetPassword = async () => {
  try {
    if (!email.value) {
      message.value = "Por favor, ingresa un correo electrónico.";
      messageType.value = "error";
      return;
    }
    await sendPasswordResetEmail(auth, email.value);
    message.value = "Tu peticion ha sido enviada.";
    messageType.value = "success";
    
    // Limpiar el localStorage para permitir que se muestre la nueva notificación
    localStorage.removeItem('resetEmailNotificationShown');
    
    // Redirigir al login después de 1.5 segundos
    setTimeout(() => {
      router.push({ 
        path: '/login', 
        query: { 
          resetEmail: email.value,
          showNotification: 'true'
        } 
      });
    }, 1500);
  } catch (error) {
    console.error("Error al enviar el correo de restablecimiento: ", error.message);
    message.value = "Error al enviar el correo de restablecimiento.";
    messageType.value = "error";
  }
};
</script>

<style scoped>
.card {
  width: 800px;
  min-height: 500px;
  background: rgba(255, 255, 255, 0.95);
  border-radius: 20px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  overflow: hidden;
  transition: all 0.3s ease;
}

.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.card-inner {
  display: flex;
  height: 100%;
}

.card-left {
  flex: 1;
  padding: 20px;
  background: linear-gradient(135deg, #e0e7ff 0%, #dbeafe 100%);
  display: flex;
  align-items: center;
  justify-content: center;
}

.card-right {
  flex: 1;
  padding: 40px;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.header-section {
  text-align: center;
  margin-bottom: 30px;
}

.icon-wrapper {
  width: 60px;
  height: 60px;
  margin: 0 auto 20px;
  background: #eff6ff;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 15px;
}

.lock-icon {
  width: 100%;
  height: 100%;
}

.heading {
  font-size: 2.5em;
  font-weight: 700;
  color: #1e3a8a;
  margin-bottom: 10px;
  letter-spacing: -0.5px;
}

.sub-heading {
  font-size: 1.5em;
  font-weight: 600;
  color: #1e40af;
  margin-bottom: 5px;
}

.sub-sub-heading {
  font-size: 1em;
  color: #64748b;
  margin-bottom: 30px;
}

.form-section {
  display: flex;
  flex-direction: column;
  gap: 25px;
}

.input-group {
  position: relative;
  margin-bottom: 10px;
}

.email-input {
  width: 100%;
  padding: 15px;
  border: 2px solid #e2e8f0;
  border-radius: 10px;
  font-size: 1em;
  transition: all 0.3s ease;
  background: transparent;
}

.email-input:focus {
  border-color: #2563eb;
  outline: none;
  box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
}

.input-border {
  position: absolute;
  bottom: 0;
  left: 0;
  height: 2px;
  width: 0;
  background: #2563eb;
  transition: all 0.3s ease;
}

.email-input:focus ~ .input-border {
  width: 100%;
}

.reset-button {
  padding: 15px 30px;
  border: none;
  border-radius: 10px;
  background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
  color: white;
  font-size: 1em;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.reset-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
}

.button-effect {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(to right, transparent, rgba(255, 255, 255, 0.2), transparent);
  transform: translateX(-100%);
}

.reset-button:hover .button-effect {
  transform: translateX(100%);
  transition: transform 0.5s ease;
}

.message {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  padding: 12px;
  border-radius: 8px;
  font-size: 0.9em;
  transition: all 0.3s ease;
}

.message.success {
  background: #dcfce7;
  color: #059669;
}

.message.error {
  background: #fee2e2;
  color: #dc2626;
}

.message-icon {
  width: 20px;
  height: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  font-weight: bold;
}

.success .message-icon {
  background: #059669;
  color: white;
}

.error .message-icon {
  background: #dc2626;
  color: white;
}

.back-link {
  text-align: center;
  color: #2563eb;
  text-decoration: none;
  font-size: 0.9em;
  transition: all 0.3s ease;
}

.back-link:hover {
  color: #1d4ed8;
  text-decoration: underline;
}

@media (max-width: 768px) {
  .card {
    width: 90%;
    margin: 20px;
  }

  .card-inner {
    flex-direction: column;
  }

  .card-left {
    display: none;
  }

  .card-right {
    padding: 30px;
  }
}
</style>
  
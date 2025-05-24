<template>
    <div class="stripe-payment">
        <div class="mb-4">
            <h3 class="text-lg font-semibold mb-2">Detalles del pago</h3>
            <p class="text-gray-600">Monto a pagar: $50.00 USD</p>
            <p v-if="!isAuthenticated" class="text-red-500 mt-2">
                Debes iniciar sesión para realizar el pago
            </p>
        </div>
        <button 
            @click="pay" 
            class="btn-pagar"
            :disabled="loading || !isAuthenticated"
        >
            <span v-if="loading">Procesando...</span>
            <span v-else-if="!isAuthenticated">Inicia sesión para pagar</span>
            <span v-else>Pagar ahora</span>
        </button>
    </div>
</template>
  
<script setup>
import { ref, onMounted } from 'vue'
import { loadStripe } from '@stripe/stripe-js'
import axios from 'axios'
import { getAuth, onAuthStateChanged } from 'firebase/auth'
  
const loading = ref(false)
const auth = getAuth()
const isAuthenticated = ref(false)
const userId = ref(null)

onMounted(() => {
    onAuthStateChanged(auth, (user) => {
        if (user) {
            isAuthenticated.value = true
            userId.value = user.uid
            console.log('Usuario autenticado:', user.uid)
        } else {
            isAuthenticated.value = false
            userId.value = null
            console.log('Usuario no autenticado')
        }
    })
})
  
const pay = async () => {
    try {
        if (!isAuthenticated.value || !userId.value) {
            alert('Debes iniciar sesión para realizar el pago')
            return
        }

        loading.value = true
        console.log('Iniciando proceso de pago...')
        
        const stripe = await loadStripe(import.meta.env.VITE_STRIPE_PUBLIC_KEY)
        console.log('Stripe cargado:', !!stripe)
    
        const response = await axios.post('/api/stripe/checkout', {
            userId: userId.value
        })
        console.log('Respuesta del servidor:', response.data)
        
        const sessionId = response.data.id
        const result = await stripe.redirectToCheckout({ sessionId })
        
        if (result.error) {
            throw new Error(result.error.message)
        }
    } catch (error) {
        console.error('Error al procesar el pago:', error)
        if (error.response) {
            console.error('Detalles del error:', error.response.data)
        }
        alert('Hubo un error al procesar el pago. Por favor, intenta de nuevo.')
    } finally {
        loading.value = false
    }
}
</script>

<style scoped>
.stripe-payment {
    padding: 20px;
    max-width: 400px;
    margin: 0 auto;
}

.btn-pagar {
    background-color: #2563eb;
    color: white;
    padding: 12px 24px;
    border-radius: 6px;
    border: none;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    width: 100%;
}

.btn-pagar:hover {
    background-color: #1d4ed8;
    transform: translateY(-1px);
}

.btn-pagar:disabled {
    background-color: #93c5fd;
    cursor: not-allowed;
    transform: none;
}
</style>
  
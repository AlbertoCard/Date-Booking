<template>
    <div class="stripe-payment">
        <div class="mb-4">
            <h3 class="text-lg font-semibold mb-2">Detalles del pago</h3>
            <p class="text-gray-600">Monto a pagar: $50.00 USD</p>
        </div>
        <button 
            @click="pay" 
            class="btn-pagar"
            :disabled="loading"
        >
            <span v-if="loading">Procesando...</span>
            <span v-else>Pagar ahora</span>
        </button>
    </div>
</template>
  
<script setup>
import { ref } from 'vue'
import { loadStripe } from '@stripe/stripe-js'
import axios from 'axios'
  
const loading = ref(false)
  
const pay = async () => {
    try {
        loading.value = true
        const stripe = await loadStripe('pk_test_51RRqn4FT8eXT037JrkUBM8eCnXoE0RxVhAcWog91LJVBUwy2lE2Zchpy107cME7vYCAT8FrkEYwCAZJbDNxRxyQG00VVb0KKAf')
    
        const response = await axios.post('/api/stripe/checkout')
        console.log('Respuesta del servidor:', response.data)
        
        const sessionId = response.data.id
        const result = await stripe.redirectToCheckout({ sessionId })
        
        if (result.error) {
            throw new Error(result.error.message)
        }
    } catch (error) {
        console.error('Error al procesar el pago:', error)
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
  
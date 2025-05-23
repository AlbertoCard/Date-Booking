<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-100 to-white p-6">
        <div class="container bg-white rounded-2xl shadow-2xl overflow-hidden max-w-2xl mx-auto p-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Añadir Reseña</h1>
                <p class="text-gray-600">Comparte tu experiencia con este servicio</p>
            </div>

            <form @submit.prevent="enviarResena" class="space-y-6">
                <!-- Calificación con estrellas -->
                <div class="flex flex-col items-center">
                    <div class="flex gap-2 mb-2">
                        <button v-for="i in 5" :key="i" 
                            type="button"
                            @click="calificacion = i"
                            class="text-3xl transition-transform hover:scale-110"
                            :class="i <= calificacion ? 'text-yellow-400' : 'text-gray-300'">
                            ★
                        </button>
                    </div>
                    <span class="text-sm text-gray-500">Selecciona una calificación</span>
                </div>

                <!-- Comentario -->
                <div>
                    <label for="comentario" class="block text-sm font-medium text-gray-700 mb-2">
                        Tu comentario
                    </label>
                    <textarea
                        id="comentario"
                        v-model="comentario"
                        rows="4"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Cuéntanos sobre tu experiencia..."
                        required
                    ></textarea>
                </div>

                <!-- Botones -->
                <div class="flex gap-4 justify-center">
                    <button
                        type="button"
                        @click="$router.back()"
                        class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all duration-300"
                    >
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        :disabled="!calificacion || !comentario"
                        class="px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Enviar Reseña
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { getAuth } from 'firebase/auth';

export default {
    name: 'AgregarResena',
    data() {
        return {
            calificacion: 0,
            comentario: '',
            cargando: false,
            error: null
        };
    },
    methods: {
        async enviarResena() {
            if (this.cargando) return;
            this.cargando = true;
            this.error = null;

            try {
                const auth = getAuth();
                const user = auth.currentUser;

                if (!user) {
                    throw new Error('Debes iniciar sesión para enviar una reseña');
                }

                const reservaId = this.$route.params.id;
                const resenaData = {
                    id_reserva: reservaId,
                    calificacion: this.calificacion,
                    comentario: this.comentario,
                    user_uid: user.uid
                };

                await axios.post('/api/resenas', resenaData, {
                    headers: {
                        'Authorization': `Bearer ${await user.getIdToken()}`
                    }
                });

                // Mostrar mensaje de éxito y redirigir
                alert('¡Gracias por tu reseña!');
                this.$router.push("/reservas/" + user.uid);
            } catch (error) {
                console.error('Error al enviar la reseña:', error);
                this.error = error.response?.data?.message || 'Error al enviar la reseña';
                alert(this.error);
            } finally {
                this.cargando = false;
            }
        }
    }
};
</script>

<style scoped>
.container {
    backdrop-filter: blur(10px);
    transform-style: preserve-3d;
    perspective: 1000px;
    background: rgba(255, 255, 255, 0.9);
}

button {
    transition: all 0.3s ease;
}

button:active {
    transform: scale(0.95);
}
</style> 
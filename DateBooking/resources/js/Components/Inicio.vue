<template>
    <!-- /resources/js/Components/Inicio.vue -->
    <div class="contenedor">
        <!-- Estado de carga -->
        <div v-if="cargando" class="flex justify-center items-center min-h-[400px]">
            <Loader :visible="true" />
        </div>

        <!-- Error -->
        <div v-else-if="error" class="text-center py-12">
            <div class="text-red-500 text-xl">
                {{ error }}
            </div>
        </div>

        <!-- Contenido principal -->
        <div v-else>
            <!-- Encabezado -->
            <div class="encabezado">
                <h1>Recomendaciones</h1>
            </div>

            <!-- Lista de servicios -->
            <div v-if="servicios.length > 0">
                <div v-for="servicio in servicios" 
                    :key="servicio.id_servicio" 
                    class="tarjeta-servicio"
                    @click="verDetalle(servicio.id_servicio)"
                >
                    <!-- Imagen o ícono -->
                    <div class="imagen">
                        <div class="w-full h-full flex items-center justify-center text-gray-500">
                            <span class="text-4xl">{{ servicio.nombre[0] }}</span>
                        </div>
                    </div>

                    <!-- Contenido -->
                    <div class="info-servicio">
                        <h2>{{ servicio.nombre }}</h2>
                        <p class="descripcion">{{ servicio.descripcion }}</p>
                    </div>

                    <!-- Precio y estrellas -->
                    <div class="precio-estrellas">
                        <p class="precio">${{ servicio.costo }}</p>
                        <p class="estrellas">
                            <span v-for="i in 5" :key="i">
                                {{ i <= Math.round(servicio.promedio_reseñas || 0) ? '★' : '☆' }}
                            </span>
                            <span class="ml-2 text-xs text-gray-500">
                                ({{ servicio.total_reseñas || 0 }})
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Estado vacío -->
            <div v-else class="text-center py-12">
                <div class="text-gray-500 text-xl">
                    No hay servicios disponibles en este momento
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import Loader from './Loader.vue';

const router = useRouter();
const servicios = ref([]);
const cargando = ref(true);
const error = ref(null);

const cargarServicios = async () => {
    try {
        const response = await axios.get('/api/servicios');
        console.log('Respuesta de servicios:', response.data);
        servicios.value = response.data;
    } catch (err) {
        console.error('Error al cargar servicios:', err);
        error.value = 'Error al cargar los servicios. Por favor, intenta más tarde.';
    } finally {
        cargando.value = false;
    }
};

const verDetalle = (id) => {
    console.log('Redirigiendo a servicio con ID:', id);
    router.push(`/servicio/${id}`);
};

onMounted(() => {
    cargarServicios();
});
</script>

<style scoped>
.contenedor {
    max-width: 1200px;
    margin: 10px auto;
    margin-top: -50px;
    padding: 60px;
    font-family: Arial, sans-serif;
    background: #fcfcfc;
}

.encabezado {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}

.encabezado h1 {
    font-size: 24px;
    font-weight: bold;
}

.tarjeta-servicio {
    display: flex;
    align-items: center;
    border: 1px solid #ddd;
    border-radius: 12px;
    padding: 40px;
    margin-bottom: 16px;
    gap: 16px;
    background: white;
    transition: all 0.3s ease;
    cursor: pointer;
}

.tarjeta-servicio:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.imagen {
    width: 100px;
    height: 100px;
    background: #f3f4f6;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.info-servicio {
    flex: 1;
}

.info-servicio h2 {
    margin: 0;
    font-size: 20px;
    font-weight: bold;
    color: #1f2937;
    margin-bottom: 8px;
}

.descripcion {
    font-size: 14px;
    color: #6b7280;
    line-height: 1.5;
}

.precio-estrellas {
    text-align: right;
    min-width: 150px;
}

.precio {
    font-size: 26px;
    font-weight: bold;
    color: #2563eb;
    margin-bottom: 8px;
}

.estrellas {
    color: #fbbf24;
    font-size: 18px;
    margin-bottom: 12px;
}

.btn-reservar {
    background-color: #2563eb;
    color: white;
    padding: 8px 16px;
    border-radius: 6px;
    border: none;
    font-weight: 500;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.btn-reservar:hover {
    background-color: #1d4ed8;
}

/* Responsivo */
@media (max-width: 768px) {
    .contenedor {
        padding: 20px;
        margin-top: 0;
    }

    .tarjeta-servicio {
        flex-direction: column;
        padding: 20px;
        text-align: center;
    }

    .imagen {
        margin-bottom: 16px;
    }

    .info-servicio {
        margin-bottom: 16px;
    }

    .precio-estrellas {
        text-align: center;
    }
}
</style>
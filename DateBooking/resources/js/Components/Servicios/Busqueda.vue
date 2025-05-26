<template>
    <div class="contenedor">
        <Loader :visible="cargando" />

        <div class="encabezado" id="encabezado-principal">
            <h1>"{{ searchText }}"</h1>
            <div class="filtros">
                <fieldset>
                    <div class="button-group">
                        <input type="radio" id="todos" name="categorias" value="todos"
                            v-model="categoriaSeleccionada" />
                        <label for="todos">Todos</label>
                    </div>
                    <div class="button-group">
                        <input type="radio" id="restaurante" name="categorias" value="restaurante"
                            v-model="categoriaSeleccionada" />
                        <label for="restaurante">Restaurante</label>
                    </div>
                    <div class="button-group">
                        <input type="radio" id="hotel" name="categorias" value="hotel"
                            v-model="categoriaSeleccionada" />
                        <label for="hotel">Hotel</label>
                    </div>
                    <div class="button-group">
                        <input type="radio" id="eventos" name="categorias" value="evento"
                            v-model="categoriaSeleccionada" />
                        <label for="eventos">Eventos</label>
                    </div>
                    <div class="button-group">
                        <input type="radio" id="consultorios" name="categorias" value="consultorio"
                            v-model="categoriaSeleccionada" />
                        <label for="consultorios">Consultorios</label>
                    </div>
                </fieldset>
            </div>
        </div>
        <div v-if="!cargando && servicios.length === 0" class="sin-resultados">
            No se encontraron servicios relacionados con "{{ searchText }}".
        </div>
        <div v-for="servicio in servicios" :key="servicio.id_servicio" class="tarjeta-servicio"
            @click="verDetalle(servicio.id_servicio)">
            <div class="imagen">
                <template v-if="servicio.imagen && servicio.imagen.url">
                    <img :src="servicio.imagen.url" :alt="servicio.nombre" class="w-full h-full object-cover rounded-lg"
                        @error="handleImageError" @load="handleImageLoad">
                </template>
                <div v-else
                    class="w-full h-full bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-white" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
            <div class="info-servicio">
                <h2>{{ servicio.nombre }}</h2>
                <p class="descripcion">{{ servicio.descripcion }}</p>
            </div>
            <div class="precio-estrellas">
                <p class="precio">${{ servicio.costo }}</p>
                <p class="estrellas">
                    <span v-for="i in 5" :key="i">
                        {{ i <= servicio.estrellas ? '★' : '☆' }} </span>
                </p>
            </div>
        </div>
        <div v-if="totalPaginasServicios > 1" class="flex justify-center mt-6 gap-2">
            <button class="btn-reservar" :disabled="paginaServicios === 1"
                @click="cambiarPaginaServicios(paginaServicios - 1)">
                Anterior
            </button>
            <span class="px-3 py-2">{{ paginaServicios }} / {{ totalPaginasServicios }}</span>
            <button class="btn-reservar" :disabled="paginaServicios === totalPaginasServicios"
                @click="cambiarPaginaServicios(paginaServicios + 1)">
                Siguiente
            </button>
        </div>
        <div v-if="servicios.length < 10">
            <div class="encabezado" id="encabezado-recomendaciones">
                <h1>Mas opciones</h1>
            </div>
            <div v-for="recomendacion in recomendaciones" :key="recomendacion.id_servicio" class="tarjeta-servicio"
                @click="verDetalle(recomendacion.id_servicio)">
                <div class="imagen">
                    <template v-if="recomendacion.imagen && recomendacion.imagen.url">
                        <img :src="recomendacion.imagen.url" :alt="recomendacion.nombre"
                            class="w-full h-full object-cover rounded-lg" @error="handleImageError"
                            @load="handleImageLoad">
                    </template>
                    <div v-else
                        class="w-full h-full bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-white" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
                <div class="info-servicio">
                    <h2>{{ recomendacion.nombre }}</h2>
                    <p class="descripcion">{{ recomendacion.descripcion }}</p>
                </div>
                <div class="precio-estrellas">
                    <p class="precio">${{ recomendacion.costo }}</p>
                    <p class="estrellas">
                        <span v-for="i in 5" :key="i">
                            {{ i <= recomendacion.estrellas ? '★' : '☆' }} </span>
                    </p>
                </div>
            </div>
            <div v-if="totalPaginasRecomendaciones > 1" class="flex justify-center mt-6 gap-2">
                <button class="btn-reservar" :disabled="paginaRecomendaciones === 1"
                    @click="cambiarPaginaRecomendaciones(paginaRecomendaciones - 1)">
                    Anterior
                </button>
                <span class="px-3 py-2">{{ paginaRecomendaciones }} / {{ totalPaginasRecomendaciones }}</span>
                <button class="btn-reservar" :disabled="paginaRecomendaciones === totalPaginasRecomendaciones"
                    @click="cambiarPaginaRecomendaciones(paginaRecomendaciones + 1)">
                    Siguiente
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import Loader from '../Loader.vue';

export default {
    components: {
        Loader,
    },
    data() {
        return {
            servicios: [],
            recomendaciones: [],
            searchText: this.$route.params.search,
            categoriaSeleccionada: 'todos',
            cargando: true,
            paginaServicios: 1,
            totalPaginasServicios: 1,
            paginaRecomendaciones: 1,
            totalPaginasRecomendaciones: 1,
        };
    },
    mounted() {
        this.cargarServicios();
        this.cargarRecomendaciones();
    },
    watch: {
        categoriaSeleccionada() {
            this.paginaServicios = 1;
            this.cargarServicios();
            this.cargarRecomendaciones();
        }
    },
    methods: {
        verDetalle(id) {
            this.$router.push(`/nodo-servicio/${id}`);
        },
        cargarServicios() {
            this.cargando = true;
            let url = `/api/servicios/search/${this.searchText}?page=${this.paginaServicios}`;
            if (this.categoriaSeleccionada !== 'todos') {
                url = `/api/servicios/categoria/${this.searchText}/${this.categoriaSeleccionada}?page=${this.paginaServicios}`;
            }
            axios.get(url)
                .then(response => {
                    this.servicios = response.data.data;
                    this.totalPaginasServicios = response.data.last_page;
                })
                .catch(() => {
                    this.servicios = [];
                    this.totalPaginasServicios = 1;
                })
                .finally(() => {
                    this.cargando = false;
                    this.$nextTick(() => {
                        const encabezado = document.getElementById('encabezado-principal');
                        if (encabezado) {
                            encabezado.scrollIntoView({ behavior: 'smooth' });
                        }
                    });
                });
        },
        cargarRecomendaciones() {
            if (this.servicios.length <= 10) {
                axios.get(`/api/servicios/paginado?page=${this.paginaRecomendaciones}`)
                    .then(response => {
                        this.recomendaciones = response.data.data;
                        this.totalPaginasRecomendaciones = response.data.last_page;
                    })
                    .catch(() => {
                        this.recomendaciones = [];
                        this.totalPaginasRecomendaciones = 1;
                    })
                    .finally(() => {
                        this.$nextTick(() => {
                            const encabezado = document.getElementById('encabezado-recomendaciones');
                            if (encabezado) {
                                encabezado.scrollIntoView({ behavior: 'smooth' });
                            }
                        });
                    });
            } else {
                this.recomendaciones = [];
                this.totalPaginasRecomendaciones = 1;
            }
        },
        cambiarPaginaServicios(nueva) {
            if (nueva >= 1 && nueva <= this.totalPaginasServicios) {
                this.paginaServicios = nueva;
                this.cargarServicios();
            }
        },
        cambiarPaginaRecomendaciones(nueva) {
            if (nueva >= 1 && nueva <= this.totalPaginasRecomendaciones) {
                this.paginaRecomendaciones = nueva;
                this.cargarRecomendaciones();
            }
        },
        handleImageError(e) {
            e.target.style.display = 'none';
        },
        handleImageLoad(e) {
            e.target.style.opacity = 1;
        }
    }
}
</script>

<style scoped>
.contenedor {
    max-width: 1200px;
    width: 100%;
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
    overflow: hidden;
}

.imagen img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.imagen:hover img {
    transform: scale(1.05);
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

.acciones {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.acciones button {
    padding: 9px 22px;
    font-size: 14px;
    border-radius: 20px;
    border: none;
    cursor: pointer;
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

.estado button {
    width: 60px;
    height: 36px;
    border-radius: 18px;
    font-weight: bold;
    border: none;
    cursor: pointer;
}


.button-group {
    flex-grow: 1;
    margin: auto;
}

.button-group input[type="radio"] {
    display: none;
}

.button-group label {
    display: inline-block;
    padding: 10px 20px;
    cursor: pointer;
    background-color: #2563eb;
    color: white;
    border-radius: 15px;
    transition: all ease 0.2s;
    text-align: center;
    flex-grow: 1;
    flex-basis: 0;
    min-width: fit-content;
    /* Ensure the label adjusts to fit its content */
    font-size: 13px;
    margin: 5px;
    white-space: nowrap;
    /* Prevent text from wrapping */
}

.button-group input[type="radio"]:checked+label {
    background-color: white;
    color: #2563eb;
    border: 2px dashed #2563eb;

}

fieldset {
    border: 0;
    display: flex;
    overflow: hidden;
}

.filtros {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.sin-resultados {
    background: #dbeafe;
    color: #3b82f6;
    border: 1px solid #93c5fd;
    border-radius: 10px;
    padding: 30px 20px;
    margin: 30px 0;
    text-align: center;
    font-size: 18px;
    font-weight: bold;
    box-shadow: 0 2px 8px rgba(220, 53, 69, 0.08);
    letter-spacing: 0.5px;
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
        margin-top: -30px;
    }

    .encabezado {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        margin-bottom: 24px;
        gap: 50px;
        overflow: hidden;
    }

    .filtros {
        overflow-x: scroll;
    }

    .tarjeta-servicio {
        flex-direction: column;
        align-items: center;
    }

    .acciones {
        flex-direction: row;
        gap: 12px;
        margin-top: 8px;
    }

    .precio-estrellas,
    .estado {
        align-self: center;
        margin-top: 8px;
    }

    .imagen {
        margin-bottom: 16px;
    }

    .info-servicio {
        margin-bottom: 16px;
    }

}
</style>
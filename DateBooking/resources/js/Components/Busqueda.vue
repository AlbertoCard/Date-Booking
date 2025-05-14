<template>
    <!-- /resources/js/Components/Inicio.vue -->
    <div class="contenedor">
        <!-- Encabezado -->

        <div class="encabezado">
            <h1>"{{ searchText }}"</h1>
            <!-- select para seleccionar la categoria -->
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
        <!-- Lista de servicios -->
        <div v-for="servicio in servicios" :key="servicio.id_servicio" class="tarjeta-servicio">
            <!-- Imagen o ícono -->
            <div class="imagen"></div>

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
                        {{ i <= servicio.estrellas ? '★' : '☆' }} </span>
                </p>
            </div>
        </div>
        <!-- Encabezado -->
        <div class="encabezado">
            <h1>Mas opciones</h1>
        </div>
        <!-- Lista de servicios -->
        <div v-for="recomendacion in recomendaciones" :key="recomendacion.id_servicio" class="tarjeta-servicio">
            <!-- Imagen o ícono -->
            <div class="imagen"></div>

            <!-- Contenido -->
            <div class="info-servicio">
                <h2>{{ recomendacion.nombre }}</h2>
                <p class="descripcion">{{ recomendacion.descripcion }}</p>
            </div>
            <!-- Precio y estrellas -->
            <div class="precio-estrellas">
                <p class="precio">${{ recomendacion.costo }}</p>
                <p class="estrellas">
                    <span v-for="i in 5" :key="i">
                        {{ i <= recomendacion.estrellas ? '★' : '☆' }} </span>
                </p>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            servicios: [],
            recomendaciones: [],
            searchText: this.$route.params.search, // Obtener el parámetro del URL
            categoriaSeleccionada: 'todos', // Estado para la categoría seleccionada
        };
    },
    mounted() {
        const search = this.$route.params.search; // Obtener el parámetro del URL
        axios.get(`/api/servicios/` + search)
            .then(response => {
                this.servicios = response.data;
            })
            .catch(error => {
                console.error('Error fetching servicios:', error);
            });
        axios.get('/api/servicios')
            .then(response => {
                this.recomendaciones = response.data;
            })
            .catch(error => {
                console.error('Error fetching servicios:', error);
            });
    },
    watch: {
        categoriaSeleccionada(nuevaCategoria) {
            this.obtenerServiciosPorCategoria(nuevaCategoria);
        },
    },
    methods: {
        obtenerServiciosPorCategoria(categoria) {
            if (categoria === 'todos') {
                axios.get(`/api/servicios/${this.searchText}`)
                    .then(response => {
                        this.servicios = response.data;
                        this.categoriaSeleccionada = 'todos';
                    })
                    .catch(error => {
                        console.error('Error fetching todos los servicios:', error);
                    });
                return;
            }
            axios.get(`/api/servicios/categoria/${this.searchText}/${categoria}`)
                .then(response => {
                    this.servicios = response.data;
                    this.categoriaSeleccionada = categoria;
                })
                .catch(error => {
                    console.error('Error fetching servicios por categoria:', error);
                });
            console.log(this.servicios);
            console.log(this.categoriaSeleccionada);
            console.log(this.searchText)
        }
    },

}
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
}

.nuevo-servicio {
    padding: 8px 16px;
    background: black;
    color: white;
    border: none;
    border-radius: 24px;
    cursor: pointer;
}

.tarjeta-servicio {
    display: flex;
    align-items: center;
    border: 1px solid #ddd;
    border-radius: 12px;
    padding: 40px;
    margin-bottom: 16px;
    gap: 16px;
}

.imagen {
    width: 100px;
    height: 100px;
    background: #ccc;
    border-radius: 8px;
}

.info-servicio {
    flex: 1;
}

.info-servicio h2 {
    margin: 0;
    font-size: 18px;
    font-weight: bold;
}

.descripcion {
    font-size: 14px;
    color: #555;
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

.editar {
    background-color: #e5e5e5;
}

.cancelar {
    background-color: #ffe5e5;
    color: #b00;
}

.precio-estrellas {
    text-align: right;
}

.precio {
    font-size: 26px;
    font-weight: bold;
}

.estrellas {
    color: #f5a623;
    font-size: 18px;
}

.estado button {
    width: 60px;
    height: 36px;
    border-radius: 18px;
    font-weight: bold;
    border: none;
    cursor: pointer;
}

.estado .on {
    background-color: #4caf50;
    color: white;
}

.estado .off {
    background-color: #ccc;
    color: black;
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
    border: 1px solid #2b426d;
    background-color: #385c7e;
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
    color: #02375a;
    border: 1px solid #2b426d;
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




/* Responsivo */
@media (max-width: 768px) {

    .encabezado {
        gap: 50px;
        overflow: hidden;
    }

    .filtros {
        overflow: scroll;
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

    .encabezado {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        margin-bottom: 24px;
    }



}
</style>

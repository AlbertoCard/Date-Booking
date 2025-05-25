<template>
    <div class="div_contenedor">
        <Loader :visible="cargando" />

        <div class="div_encabezado">
            <h1 class="bold">Afiliados</h1>
            <button class="btn_nuevoAfiliado" @click="mostrarFormulario = true">Añadir</button>
        </div>
  
        <div v-if="this.afiliados.length == 0" >
            <h1 class="texto_afiliados">No tienes afiliados asociados.</h1>
        </div>

        <!-- Se muestran los afiliados -->
        <div class="div_gridAfiliados">
            <div
            class="div_cardAfiliado"
            v-for="afiliado in afiliados"
            :key="afiliado.uid"
            >

                <div>
                    <template v-if="afiliado.foto_url">
                        <img :src="afiliado.foto_url" alt="Foto" @error="afiliado.foto_url = null" />
                    </template>
                    <template v-else>
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="60"
                            height="60"
                            fill="#ccc"
                            viewBox="0 0 24 24"
                        >
                            <path
                                d="M12 2a5 5 0 0 1 5 5c0 2.761-2.239 5-5 5s-5-2.239-5-5a5 5 0 0 1 5-5zm0 12c4.418 0 8 1.79 8 4v2H4v-2c0-2.21 3.582-4 8-4z"
                            />
                        </svg>
                    </template>
                </div>
                <div class="div_info">
                    <h2>{{ afiliado.nombre }}</h2>
                    <p>{{ afiliado.email }}</p>
                </div>
                <div class="div_acciones">
                    <button class="btn_desafiliar" @click="desafiliarAfiliado(afiliado.uid)">Desafiliar</button>
                </div>
            </div>
        </div>

        <div v-if="mostrarFormulario" class="div_formulario">
            <div class="div_modal">
                <h2>Añadir afiliado</h2>
                <form @submit.prevent="agregarAfiliado">
                    <label>Email:</label>
                    <input type="email" v-model="nuevoAfiliado.email" required />
                    <div class="div_accionesForm">
                        <button type="submit" class="btn_afiliar">Guardar</button>
                        <button type="button" class="btn_cancelar" @click="mostrarFormulario = false">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
  
<script>
  import axios from 'axios';
  import Loader from '../Loader.vue';

  const API_URL = 'http://127.0.0.1:8000/api/afiliados/'
  
  export default {
    components: {
        Loader,
    },
    data() {
        return {
            mostrarFormulario: false,
            nuevoAfiliado: {
                email: '',
            },
            afiliados: [],
            cargando: true,
        };
    },
    mounted() {
        this.cargando = true;
        this.cargarAfiliados();
    },
    methods: {
        cargarAfiliados() {
            const userData = JSON.parse(localStorage.getItem('userData'));
            if (!userData || !userData.uid) {
                alert('Error: No se encontró la información del usuario. Por favor, inicia sesión nuevamente.');
                return;
            }
            axios.get(API_URL + userData.uid)
            .then(response => {
                this.cargando = true;
                this.afiliados = response.data;
                console.log("Mostrando afiliados del establecimiento: " + this.afiliados.length);
            })
            .catch(error => {
                console.error('Error al obtener afiliados:', error);
            })
            .finally(() => {
                    this.cargando = false; // Ocultar el loader al finalizar la carga
            });
        },
        agregarAfiliado() {
            const userData = JSON.parse(localStorage.getItem('userData'));
            if (!userData || !userData.uid) {
                alert('Error: No se encontró la información del usuario. Por favor, inicia sesión nuevamente.');
                return;
            }

            const datosAfiliado = {
                email: this.nuevoAfiliado.email,
                uid: userData.uid
            };

            this.cargando = true;
            axios.post(API_URL + 'agregar', datosAfiliado)
                .then(response => {
                    this.cargando = true;
                    if (response.data.redirect) {
                        if (confirm(response.data.message)) {
                            this.nuevoAfiliado.email = '';
                            // Redirigir a la vista de registro
                            this.$router.push('/registro');
                        }
                    } else {
                        alert(response.data.message);
                        this.mostrarFormulario = false;
                        this.cargando = true;
                        this.nuevoAfiliado.email = '';
                        this.cargarAfiliados();
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert(error.response?.data?.error || "Ocurrió un error al agregar el afiliado.");
                })
        },
        desafiliarAfiliado(uid){
            const confirmar = confirm("¿Estás seguro de que deseas desafiliar al afiliado?");
            if (confirmar) {
                const userData = JSON.parse(localStorage.getItem('userData'));
                if (!userData || !userData.uid) {
                    alert('Error: No se encontró la información del usuario. Por favor, inicia sesión nuevamente.');
                    return;
                }
                const id_userEstablecimiento = userData.uid;

                axios.delete(API_URL + 'desafiliar/' + uid + '/' + id_userEstablecimiento)
                    .then(response => {
                        alert("Afiliado desafiliado correctamente");
                        this.cargando = true;
                        this.cargarAfiliados();
                    })
                    .catch(error => {
                        alert(error.response?.data?.error || "Error al desafiliar.");
                    })
            } else{
                return;
            }
        },      
    }
  };
</script>
  
<style scoped>
    .div_contenedor {
        max-width: 1200px;
        margin: 10px auto;
        padding: 40px;
        font-family: Arial, sans-serif;
        background: #fdfdfd;
        border-radius: 10px;
    }
  
    .div_encabezado {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 32px;
    }
  
    .div_encabezado h1 {
        font-size: 26px;
    }
  
    .btn_nuevoAfiliado {
        padding: 10px 20px;
        background: #0B57D0;
        color: white;
        border: none;
        border-radius: 20px;
        font-size: 14px;
        cursor: pointer;
    }

    .btn_nuevoAfiliado:hover {
        background-color: #0848b1;
    }

    .texto_afiliados{
        font-size: 22px;
    }
  
    .div_gridAfiliados {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 20px;
    } 
  
    .div_cardAfiliado {
        border: 1px solid #ddd;
        border-radius: 12px;
        padding: 20px;
        background-color: #fff;
        box-shadow: 1px 1px 5px rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .foto_afiliado {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 50%;
        margin-bottom: 12px;
        align-self: center;
    }
  
    .div_cardAfiliado .info h2 {
        margin: 0;
        font-size: 20px;
    }
  
    .div_cardAfiliado .info p {
        font-size: 16px;
        color: #555;
    }
  
    .div_acciones {
        display: flex;
        justify-content: flex-end;
        margin-top: 16px;
    }
  
    .btn_desafiliar {
        background-color: #0B57D0;
        color: white;
        border: none;
        padding: 10px 18px;
        font-size: 14px;
        border-radius: 10px;
        cursor: pointer;
    }

    .btn_desafiliar:hover {
        background-color: #0848b1;
    }

    .div_formulario {
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background-color: rgba(0, 0, 0, 0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        animation: fadeIn 0.3s ease;
    }

    .div_modal {
        background: white;
        padding: 30px 25px;
        border-radius: 16px;
        width: 100%;
        max-width: 400px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        animation: slideUp 0.3s ease;
    }

    .div_modal h2 {
        margin-top: 0;
        font-size: 22px;
        color: #333;
    }

    .div_modal label {
        display: block;
        margin-top: 15px;
        margin-bottom: 5px;
        font-weight: bold;
        color: #555;
    }

    .div_modal input {
        width: 100%;
        padding: 10px;
        font-size: 15px;
        border: 1px solid #ccc;
        border-radius: 10px;
        outline: none;
        transition: border-color 0.2s;
    }

    .div_modal input:focus {
        border-color: #0B57D0;
    }

    .div_accionesForm {
        display: flex;
        justify-content: space-between;
        margin-top: 25px;
    }

    .btn_afiliar {
        padding: 10px 18px;
        font-size: 14px;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        transition: background 0.2s ease;
        background-color: #0B57D0;
        color: white;
    }

    .btn_afiliar:hover {
        background-color: #0848b1;
    }

    .btn_cancelar {
        padding: 10px 18px;
        font-size: 14px;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        transition: background 0.2s ease;
        background-color: #e0e0e0;
        color: #333;
    }

    .btn_cancelar:hover {
        background-color: #cfcfcf;
    }

    .bold{
        font-weight: bold;
    }
</style>  
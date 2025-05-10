<template>
    <div class="formulario">
        <h1 class="titulo">Agregar nuevo servicio</h1>
        <div class="formulario-filas">
            <div class="formulario-grupo">
                <label>Nombre del servicio</label>
                <input type="text" />
            </div>
            <div class="formulario-grupo">
                <label>Categoría</label>
                <select>
                    <option disabled value="">Selecciona la categoría</option>
                    <option>Consultoría</option>
                    <option>Mantenimiento</option>
                    <option>Diseño</option>
                    <option>Otro</option>
                </select>
            </div>
        </div>

        <div class="formulario-grupo">
            <label>Añade descripción del servicio</label>
            <textarea></textarea>
        </div>

        <div class="formulario-filas">
            <div class="formulario-grupo">
                <label>Fecha</label>
                <div class="fecha-inputs">
                    <input type="text" placeholder="MM" />
                    <input type="text" placeholder="DD" />
                    <input type="text" placeholder="YYYY" />
                </div>
            </div>

            <div class="formulario-grupo">
                <label>Horario</label>
                <select>
                    <option disabled value="">Elige</option>
                    <option>08:00 - 10:00</option>
                    <option>10:00 - 12:00</option>
                    <option>14:00 - 16:00</option>
                </select>
            </div>

            <div class="formulario-grupo">
                <label>Precio</label>
                <input type="text" class="precio-input" placeholder="$XX.XX" />
            </div>
        </div>

        <div class="formulario-grupo">
            <label>Sube un archivo</label>
            <div class="subida-archivo">
                <input type="file" hidden />
                <p>Upload Media</p>
            </div>
        </div>

        <div class="formulario-grupo center">
            <button>Añadir Servicio</button>
        </div>
    </div>
</template>

<style scoped>
.formulario {
    background: #fcfcfc;
    padding: 48px;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    max-width: 100%;
    width: 90%;
    margin: auto;
    font-family: Arial, sans-serif;
}

.formulario-filas {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
}

.formulario-grupo {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-width: 200px;
    margin-top: 16px;
}

.formulario-grupo label {
    font-weight: bold;
    margin-bottom: 4px;
}

.formulario-grupo input,
.formulario-grupo select,
.formulario-grupo textarea {
    padding: 10px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 14px;
    width: 100%;
}

textarea {
    resize: none;
}

.fecha-inputs {
    display: flex;
    gap: 8px;
}

.fecha-inputs input {
    width: 100%;
}

.precio-input {
    color: #6d28d9;
    font-weight: bold;
}

.subida-archivo {
    border: 2px dashed #aaa;
    padding: 34px;
    text-align: center;
    cursor: pointer;
    border-radius: 8px;
    color: #666;
}

.center {
    text-align: center;
}

.titulo {
    font-size: 1.8em;
    margin-bottom: 16px;
    color: #333;
    font-weight: bold;
    text-align: center;
    margin-bottom: 24px;
    /* border: 2px solid #ad0808; */
    padding: 16px;
}

button {
    background: white;
    padding: 12px 28px;
    border: 1px solid black;
    border-radius: 24px;
    cursor: pointer;
    font-weight: bold;
    transition: background 0.3s;
}

button:hover {
    background: #ddd;
}

@media (max-width: 768px) {
    .formulario {
        padding: 24px;
    }

    .formulario-filas {
        flex-direction: column;
    }

    .formulario-grupo {
        width: 100%;
    }

    .fecha-inputs {
        flex-direction: column;
    }

    .fecha-inputs input {
        width: 100%;
    }
}
</style>

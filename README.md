# Date-Booking
Date-Booking es una plataforma diseñada para optimizar la gestión de reservas y turnos en diversos sectores como salud y entretenimiento. El sistema permite a los usuarios agendar citas y comprar boletos de manera digital, generando códigos QR para validación en puntos de acceso. 

# Proyecto Laravel + Vue

Este repositorio contiene el proyecto base desarrollado con **Laravel 12.8.1** y **Vue 3.5.13**.  
Sigue estos pasos cuidadosamente para poder levantar el entorno de desarrollo correctamente.

---

## Introducción

Este proyecto combina Laravel (backend) con Vue (frontend).  
Asegúrate de seguir estas instrucciones **paso por paso** para evitar errores.  
**Sí, tienes que leer todo. Si no lo haces, no preguntes después.**

---

## Pre-requisitos

Antes de clonar el repositorio, asegúrate de tener lo siguiente instalado:

- [Node.js](https://nodejs.org/) (v16 o superior recomendado)
- [Composer](https://getcomposer.org/)
- Servidor Apache (puedes usar [XAMPP](https://www.apachefriends.org/es/index.html))  
  - O tener habilitado `php` como variable global en tu sistema (`php -v` para verificar)

---

## Instalación y ejecución

1. Clonar el repositorio:
   ```bash
   git clone https://github.com/tu_usuario/tu_repositorio.git
   cd tu_repositorio
2. Descarga y cambiate a tu rama:
   ```
   git checkout -b tu_nombre origin/tu_nombre
3. Instalar dependencias de Laravel:
   ```bash
   composer install
4. Instalar dependencias de Vue:
   ```bash
   npm install
5. Añadir el archivo .env al proyecto
6. Levantar el proyecto (terminales distintas)
   - Laravel
     ```
     php artisan serve
   - Vue
     ```
     php artisan serve
## Uso de ramas
Para mantener el proyecto organizado, se ha creado:
Una rama principal: master
Una rama por cada integrante
Una rama por cada incremento (ej: incremento-1, incremento-2, etc.)

## Instrucciones de uso
1. NUNCA trabajes directamente en la rama master.
2. Siempre cambia a tu propia rama antes de empezar a trabajar:
   ```
     git checkout tu_rama
3. Solo trabaja en tu rama
4. Cuando tu parte este lista se agrega al incremento correspondiente, no a la master

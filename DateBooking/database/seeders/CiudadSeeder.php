<?php

namespace Database\Seeders;

use App\Models\Ciudade;
use App\Models\Estado;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CiudadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        $ciudadesPorEstado = [
    'Aguascalientes' => ['Aguascalientes', 'Jesús María', 'Calvillo', 'Rincón de Romos', 'Pabellón de Arteaga'],
    'Baja California' => ['Tijuana', 'Mexicali', 'Ensenada', 'Tecate', 'Playas de Rosarito'],
    'Baja California Sur' => ['La Paz', 'Los Cabos', 'Comondú', 'Loreto', 'Mulegé'],
    'Campeche' => ['Campeche', 'Ciudad del Carmen', 'Champotón', 'Escárcega', 'Calkiní'],
    'Chiapas' => ['Tuxtla Gutiérrez', 'San Cristóbal de las Casas', 'Tapachula', 'Comitán', 'Palenque'],
    'Chihuahua' => ['Chihuahua', 'Ciudad Juárez', 'Delicias', 'Cuauhtémoc', 'Parral'],
    'Ciudad de México' => ['Iztapalapa', 'Gustavo A. Madero', 'Álvaro Obregón', 'Coyoacán', 'Benito Juárez'],
    'Coahuila' => ['Saltillo', 'Torreón', 'Monclova', 'Piedras Negras', 'Acuña'],
    'Colima' => ['Colima', 'Manzanillo', 'Tecomán', 'Villa de Álvarez', 'Armería'],
    'Durango' => ['Durango', 'Gómez Palacio', 'Lerdo', 'Canatlán', 'Santiago Papasquiaro'],
    'Estado de México' => ['Toluca', 'Ecatepec', 'Naucalpan', 'Nezahualcóyotl', 'Metepec'],
    'Guanajuato' => ['León', 'Irapuato', 'Celaya', 'Salamanca', 'Guanajuato'],
    'Guerrero' => ['Acapulco', 'Chilpancingo', 'Iguala', 'Zihuatanejo', 'Taxco'],
    'Hidalgo' => ['Pachuca', 'Tulancingo', 'Tula de Allende', 'Tepeji del Río', 'Actopan'],
    'Jalisco' => ['Guadalajara', 'Zapopan', 'Tlaquepaque', 'Tonalá', 'Puerto Vallarta'],
    'Michoacán' => ['Morelia', 'Uruapan', 'Zamora', 'Lázaro Cárdenas', 'Apatzingán'],
    'Morelos' => ['Cuernavaca', 'Jiutepec', 'Cuautla', 'Temixco', 'Yautepec'],
    'Nayarit' => ['Tepic', 'Bahía de Banderas', 'Compostela', 'Santiago Ixcuintla', 'Xalisco'],
    'Nuevo León' => ['Monterrey', 'Guadalupe', 'San Nicolás de los Garza', 'Apodaca', 'Santa Catarina'],
    'Oaxaca' => ['Oaxaca de Juárez', 'Salina Cruz', 'Juchitán de Zaragoza', 'Tuxtepec', 'Puerto Escondido'],
    'Puebla' => ['Puebla', 'Tehuacán', 'Atlixco', 'San Martín Texmelucan', 'Cholula'],
    'Querétaro' => ['Santiago de Querétaro', 'San Juan del Río', 'El Marqués', 'Corregidora', 'Pedro Escobedo'],
    'Quintana Roo' => ['Cancún', 'Chetumal', 'Playa del Carmen', 'Cozumel', 'Tulum'],
    'San Luis Potosí' => ['San Luis Potosí', 'Soledad de Graciano Sánchez', 'Matehuala', 'Ciudad Valles', 'Rioverde'],
    'Sinaloa' => ['Culiacán', 'Mazatlán', 'Los Mochis', 'Guasave', 'Navolato'],
    'Sonora' => ['Hermosillo', 'Ciudad Obregón', 'Nogales', 'San Luis Río Colorado', 'Navojoa'],
    'Tabasco' => ['Villahermosa', 'Cárdenas', 'Comalcalco', 'Macuspana', 'Tenosique'],
    'Tamaulipas' => ['Reynosa', 'Matamoros', 'Nuevo Laredo', 'Tampico', 'Ciudad Victoria'],
    'Tlaxcala' => ['Tlaxcala', 'Apizaco', 'Huamantla', 'Chiautempan', 'San Pablo del Monte'],
    'Veracruz' => ['Veracruz', 'Xalapa', 'Coatzacoalcos', 'Poza Rica', 'Orizaba'],
    'Yucatán' => ['Mérida', 'Valladolid', 'Tizimín', 'Progreso', 'Ticul'],
    'Zacatecas' => ['Zacatecas', 'Guadalupe', 'Fresnillo', 'Jerez', 'Río Grande'],
];

        foreach ($ciudadesPorEstado as $estadoNombre => $ciudades) {
            $estado = Estado::where('nombre', $estadoNombre)->first();

            if ($estado) {
                foreach ($ciudades as $ciudad) {
                    Ciudade::create([
                        'nombre' => $ciudad,
                        'id_estado' => $estado->id_estado,
                    ]);
                }
            }
        }
    }
}

<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
// Project implemented by the "Recovery, Transformation and Resilience Plan.
// Funded by the European Union - Next GenerationEU".
//
// Produced by the UNIMOODLE University Group: Universities of
// Valladolid, Complutense de Madrid, UPV/EHU, León, Salamanca,
// Illes Balears, Valencia, Rey Juan Carlos, La Laguna, Zaragoza, Málaga,
// Córdoba, Extremadura, Vigo, Las Palmas de Gran Canaria y Burgos..
/**
 * @package    mod_certifygen
 * @copyright  2024 Proyecto UNIMOODLE
 * @author     UNIMOODLE Group (Coordinator) <direccion.area.estrategia.digital@uva.es>
 * @author     3IPUNT <contacte@tresipunt.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


namespace mod_certifygen\external;


use external_api;
use external_function_parameters;
use external_single_structure;
use external_value;

class getPdfTeaching_external extends external_api {
    public static function getPdfTeaching_parameters(): external_function_parameters {
        return new external_function_parameters(
            [
                'id' => new external_value(PARAM_INT, 'model id'),
                'dni' => new external_value(PARAM_RAW, 'user dni'),
                'courseid' => new external_value(PARAM_INT, 'course id'),
            ]
        );
    }
    public static function getPdfTeaching(int $id, string $dni, int $courseid): array {
        /**
         * Devuelve el PDF del certificado de que el profesor ha impartido docencia en el curso
         * indicado con el detalle del uso que ha realizado de la herramienta que aparecerá en el
         * certificado. Este servicio web llamará a getJsonTeaching para obtener la información a
         * maquetar
         */
        return ['file' => 'The file'];
    }
    public static function getPdfTeaching_returns(): external_single_structure {
        return new external_single_structure(array(
                'file' => new external_value(PARAM_RAW, 'File content on base64'),
            )
        );
    }
}
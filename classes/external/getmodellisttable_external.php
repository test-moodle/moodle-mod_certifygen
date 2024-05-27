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
 * * @copyright  2024 Proyecto UNIMOODLE
 * * @author     UNIMOODLE Group (Coordinator) <direccion.area.estrategia.digital@uva.es>
 * * @author     3IPUNT <contacte@tresipunt.com>
 * * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


namespace mod_certifygen\external;


use external_api;
use mod_certifygen\tables\modellist_table;
use moodle_url;
use external_function_parameters;
use external_single_structure;
use external_value;
class getmodellisttable_external extends external_api {
    public static function getmodellisttable_parameters(): external_function_parameters {
        return new external_function_parameters([]);
    }
    public static function getmodellisttable(): array {
        $tablelist = new modellist_table();
        $tablelist->baseurl = new moodle_url('/mod/certifygen/modelmanager.php');
        ob_start();
        // TODO: optional_params 10 and true
        $tablelist->out(10, true);
        $out1 = ob_get_contents();
        ob_end_clean();
        return [
            'table' => $out1
        ];
    }
    public static function getmodellisttable_returns(): external_single_structure {
        return new external_single_structure(
            [
                'table' => new external_value(PARAM_RAW, 'model list table'),
            ]
        );
    }
}
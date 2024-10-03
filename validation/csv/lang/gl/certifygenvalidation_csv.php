<?php
// This file is part of the mod_certifygen plugin for Moodle - http://moodle.org/
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
// Produced by the UNIMOODLE University Group: Universities of
// Valladolid, Complutense de Madrid, UPV/EHU, León, Salamanca,
// Illes Balears, Valencia, Rey Juan Carlos, La Laguna, Zaragoza, Málaga,
// Córdoba, Extremadura, Vigo, Las Palmas de Gran Canaria y Burgos.

/**
 *
 * @package   certifygenvalidation_csv
 * @copyright  2024 Proyecto UNIMOODLE
 * @author     UNIMOODLE Group (Coordinator) <direccion.area.estrategia.digital@uva.es>
 * @author     3IPUNT <contacte@tresipunt.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// This line protects the file from being accessed by a URL directly.
defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Validación CSV';
$string['enable'] = 'Habilitar';
$string['enable_help'] = 'Se este plugin está habilitado, podes usalo para validar Certificados Unimoodle';
$string['firmacatalogserviceurl'] = 'FirmaCatalogService URL';
$string['firmacatalogserviceurl_help'] = 'Esta é a URL de FirmaCatalogService. <span class="bold">É necesaria en todas as solicitudes.</span>';
$string['firmaquerycatalogserviceurl'] = 'FirmaQueryCatalogService URL';
$string['firmaquerycatalogserviceurl_help'] = 'Esta é a URL de FirmaQueryCatalogService. <span class="bold">É necesaria en todas as solicitudes.</span>';
$string['appID'] = 'ID da Aplicación';
$string['appID_help'] = 'Este é o identificador da aplicación. <span class="bold">É necesario en todas as solicitudes.</span>';
$string['certifygenvalidation_csv_settings'] = 'Configuración de CSV';
$string['csvnotconfigured'] = 'CSV non configurado';
$string['pluginnamesettings'] = 'Configuración da Validación CSV';
$string['csv_result_not_expected'] = 'Resultado do endpoint non esperado';
$string['privacy:metadata'] = 'O plugin de Validación CSV non almacena ningún dato persoal.';
$string['wsoutput'] = 'Saída do servizo web';
$string['wsoutput_help'] = 'Se é verdade, as actividades de certificación relacionadas con este tipo de validación formarán parte da saída de
get_id_instance_certificate_external ws. De ser certo, as solicitudes do profesor con modelos con este tipo de validación formarán parte do
saída de get_courses_as_teacher ws.';

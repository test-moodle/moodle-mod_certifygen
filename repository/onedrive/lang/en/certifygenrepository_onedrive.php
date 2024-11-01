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
 * @package   certifygenrepository_onedrive
 * @copyright  2024 Proyecto UNIMOODLE
 * @author     UNIMOODLE Group (Coordinator) <direccion.area.estrategia.digital@uva.es>
 * @author     3IPUNT <contacte@tresipunt.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// This line protects the file from being accessed by a URL directly.
defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Onedrive Repository';
$string['pluginnamesettings'] = 'Onedrive Repository Settings';
$string['enable'] = 'Enable';
$string['enable_help'] = 'This repository saves the certificates in one of the enabled repositories in the platform';
$string['settings_folder'] = 'Folder';
$string['settings_folder_desc'] = 'OneDrive parent folder where all the reports will be saved';
$string['privacy:metadata'] = 'The Certifygen Onedrive Repository subplugin does not store any personal data.';
$string['privacy:metadata:validationid'] = 'The issue id ';
$string['privacy:metadata:userid'] = 'The ID of the user who owns the certificate.';
$string['privacy:metadata:url'] = 'Certificate link';

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
// Córdoba, Extremadura, Vigo, Las Palmas de Gran Canaria y Burgos.

/**
 * @package    mod_certifygen
 * @copyright  2024 Proyecto UNIMOODLE
 * @author     UNIMOODLE Group (Coordinator) <direccion.area.estrategia.digital@uva.es>
 * @author     3IPUNT <contacte@tresipunt.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// This line protects the file from being accessed by a URL directly.
defined('MOODLE_INTERNAL') || die();

// Enlace principal de settings.
$ADMIN->add('modsettings',
    new admin_category('modsettingcertifygencat',
        get_string('modulename', 'certifygen'),
  $module->is_enabled() === false));

// Certifygen settings.
$settings = new admin_settingpage('modsettingcertifygen', get_string('pluginnamesettings', 'mod_certifygen'), 'moodle/site:config');
$ADMIN->add('modsettingcertifygencat', $settings);

// Model manager page access.
$modelsmanagersettings = new admin_externalpage('certifygenmodelsmanager',
    get_string('modelsmanager', 'mod_certifygen'),
    '/mod/certifygen/modelmanager.php',  'moodle/site:config', $module->is_enabled() === false);
$ADMIN->add('modsettingcertifygencat', $modelsmanagersettings);

$ADMIN->add('modsettingcertifygencat',
    new admin_category('certifygenvalidationplugins',
        get_string('managecertifygenvalidationplugins', 'mod_certifygen'),
        $module->is_enabled() === false));

//$subpluginssettings = new admin_externalpage('certifygenvalidation',
//    get_string('managecertifygenvalidationplugins', 'mod_certifygen'),
//    '/mod/certifygen/adminmanageplugins.php?subtype=certifygenvalidation',
//    'moodle/site:config',
//    $module->is_enabled() === false);
//$ADMIN->add('certifygenvalidationplugins', $subpluginssettings);

unset($settings);




foreach (core_plugin_manager::instance()->get_plugins_of_type('certifygenvalidation') as $plugin) {
    /** @var \mod_certifygen\plugininfo\certifygenvalidation $plugin */
    $plugin->load_settings($ADMIN, 'certifygenvalidationplugins', $hassiteconfig);
}
// TinyMCE does not have standard settings page.
$settings = null;
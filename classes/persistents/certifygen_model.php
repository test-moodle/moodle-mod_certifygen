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
// Project implemented by the "Recovery, Transformation and Resilience Plan.
// Funded by the European Union - Next GenerationEU".
//
// Produced by the UNIMOODLE University Group: Universities of
// Valladolid, Complutense de Madrid, UPV/EHU, León, Salamanca,
// Illes Balears, Valencia, Rey Juan Carlos, La Laguna, Zaragoza, Málaga,
// Córdoba, Extremadura, Vigo, Las Palmas de Gran Canaria y Burgos.

/**
 * certifygen_model
 * @package    mod_certifygen
 * @copyright  2024 Proyecto UNIMOODLE
 * @author     UNIMOODLE Group (Coordinator) <direccion.area.estrategia.digital@uva.es>
 * @author     3IPUNT <contacte@tresipunt.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_certifygen\persistents;
use coding_exception;
use core\invalid_persistent_exception;
use core\persistent;

/**
 * certifygen_model
 * @package    mod_certifygen
 * @copyright  2024 Proyecto UNIMOODLE
 * @author     UNIMOODLE Group (Coordinator) <direccion.area.estrategia.digital@uva.es>
 * @author     3IPUNT <contacte@tresipunt.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class certifygen_model extends persistent {

    /**
     * @var string table
     */
    public const TABLE = 'certifygen_model';
    /** @var int TYPE_ACTIVITY */
    public const TYPE_ACTIVITY = 1;
    /** @var int TYPE_TEACHER_ALL_COURSES_USED */
    public const TYPE_TEACHER_ALL_COURSES_USED = 2;
    /** @var int MODE_UNIQUE */
    public const MODE_UNIQUE = 1;
    /** @var int MODE_PERIODIC */
    public const MODE_PERIODIC = 2;
    /**
     * Define properties
     *
     * @return array[]
     */
    protected static function define_properties(): array {
        return [
            'name' => [
                'type' => PARAM_TEXT,
            ],
            'idnumber' => [
                'type' => PARAM_TEXT,
            ],
            'type' => [
                'type' => PARAM_INT,
            ],
            'mode' => [
                'type' => PARAM_INT,
                'default' => 0,
            ],
            'templateid' => [
                'type' => PARAM_INT,
            ],
            'timeondemmand' => [
                'type' => PARAM_INT,
                'default' => 0,
            ],
            'langs' => [
                'type' => PARAM_TEXT,
                'default' => null,
                'null' => NULL_ALLOWED,
            ],
            'validation' => [
                'type' => PARAM_TEXT,
                'default' => null,
                'null' => NULL_ALLOWED,
            ],
            'report' => [
                'type' => PARAM_TEXT,
                'default' => null,
                'null' => NULL_ALLOWED,
            ],
            'repository' => [
                'type' => PARAM_TEXT,
                'default' => null,
                'null' => NULL_NOT_ALLOWED,
            ],
            'usermodified' => [
                'type' => PARAM_INT,
            ],
        ];
    }

    /**
     * save_model_object
     * @param object $data
     * @return self
     * @throws coding_exception
     * @throws invalid_persistent_exception
     */
    public static function save_model_object(object $data): self {
        global $USER;
        $modeldata = [
            'name' => $data->modelname,
            'idnumber' => $data->modelidnumber,
            'type' => $data->type,
            'mode' => $data->mode,
            'templateid' => $data->templateid ?? 0,
            'timeondemmand' => $data->timeondemmand ?? 0,
            'langs' => empty($data->langs) ? null : implode(',', $data->langs),
            'validation' => empty($data->validation) ? null : $data->validation,
            'report' => empty($data->report) ? null : $data->report,
            'repository' => $data->repository,
            'usermodified' => $USER->id,
            'timecreated' => time(),
            'timemodified' => time(),
        ];
        $id = $data->modelid ?? 0;
        $model = new self($id, (object)$modeldata);
        try {
            if ($id > 0) {
                $model->update();
                return $model;
            }

            return $model->create();
        } catch (\moodle_exception $e) {
            debugging(__FUNCTION__ . ' e: ' . $e->getMessage());
            return false;
        }

    }

    /**
     * get_model_languages
     * @return array
     * @throws coding_exception
     */
    public function get_model_languages(): array {
        $languages = $this->get('langs');
        if (empty($languages)) {
            return [];
        }
        return explode(',', $languages);
    }
}

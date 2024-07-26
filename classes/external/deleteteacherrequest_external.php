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

namespace mod_certifygen\external;

use external_api;
use external_function_parameters;
use external_single_structure;
use external_value;
use invalid_parameter_exception;
use mod_certifygen\interfaces\ICertificateReport;
use mod_certifygen\interfaces\ICertificateValidation;
use mod_certifygen\persistents\certifygen_model;
use mod_certifygen\persistents\certifygen_validations;
use moodle_exception;

class deleteteacherrequest_external extends external_api {
    /**
     * Describes the external function parameters.
     *
     * @return external_function_parameters
     */
    public static function deleteteacherrequest_parameters(): external_function_parameters {
        return new external_function_parameters(
            [
                'id' => new external_value(PARAM_INT, 'request id'),
            ]
        );
    }

    /**
     * @param int $id
     * @return array
     * @throws invalid_parameter_exception
     */
    public static function deleteteacherrequest(int $id): array {

        self::validate_parameters(
            self::deleteteacherrequest_parameters(), ['id' => $id]
        );
        $result = ['result' => true, 'message' => 'OK'];
        try {
            $candelete = true;
            $request = new certifygen_validations($id);
            $model = new certifygen_model($request->get('modelid'));
            $validationplugin = $model->get('validation');
            if (!empty($validationplugin) && $request->get('status') != certifygen_validations::STATUS_NOT_STARTED) {
                $validationpluginclass = $validationplugin . '\\' . $validationplugin;
                if (get_config($validationplugin, 'enabled') === '1') {
                    /** @var ICertificateValidation $subplugin */
                    $subplugin = new $validationpluginclass();
                    if ($subplugin->canRevoke()) {
//                        $code = ICertificateReport::FILE_NAME_STARTSWITH . $id;
                        $output = $subplugin->revoke($request->get('code'));
                        if ($output['haserror']) {
                            $candelete = false;
                            $result['result'] = false;
                            $result['message'] = $output['message'];
                        }
                    }
                } else {
                    $result['result'] = false;
                    $result['message'] = 'plugin_not_enabled';
                }
            }
            if ($candelete) {
                $request->delete();
            }
        } catch (moodle_exception $e) {
            $result['result'] = false;
            $result['message'] = $e->getMessage();
        }

        return $result;
    }
    /**
     * Describes the data returned from the external function.
     *
     * @return external_single_structure
     */
    public static function deleteteacherrequest_returns(): external_single_structure {
        return new external_single_structure(
            [
                'result' => new external_value(PARAM_BOOL, 'request deleted'),
                'message' => new external_value(PARAM_RAW, 'meesage'),
            ]
        );
    }

}
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
use mod_certifygen\external\deletemodel_external;
use mod_certifygen\external\get_courses_as_teacher_external;
use mod_certifygen\external\getcoursesnames_external;
use mod_certifygen\external\getmodellisttable_external;
use mod_certifygen\external\getmycertificatedata_external;
use mod_certifygen\persistents\certifygen_model;
global $CFG;
require_once($CFG->dirroot.'/admin/tool/certificate/tests/generator/lib.php');
require_once($CFG->dirroot.'/lib/externallib.php');

class getmodellisttable_external_test extends advanced_testcase {

    /**
     * Test set up.
     */
    public function setUp(): void {
        $this->resetAfterTest();
    }

    /**
     * @return void
     * @throws invalid_parameter_exception
     */
    public function test_getmodellisttable(): void {

        $result = getmodellisttable_external::getmodellisttable();
        // Tests.
        self::assertIsArray($result);
        self::assertArrayHasKey('table', $result);
        self::assertEquals('-->' . get_string('nothingtodisplay'), trim($result['table']));

        // Create model.
        $templategenerator = $this->getDataGenerator()->get_plugin_generator('tool_certificate');
        $certificate1 = $templategenerator->create_template((object)['name' => 'Certificate 1']);
        $modgenerator = $this->getDataGenerator()->get_plugin_generator('mod_certifygen');
        $modgenerator->create_model_by_name(
            certifygen_model::TYPE_TEACHER_ALL_COURSES_USED,
            $certificate1->get_id(),
            certifygen_model::TYPE_TEACHER_ALL_COURSES_USED
        );

        // Tests.
        $result = getmodellisttable_external::getmodellisttable();
        self::assertIsArray($result);
        self::assertArrayHasKey('table', $result);
        $findme   = '<div class="no-overflow"><table class="flexible table table-striped table-hover generaltable generalbox">';
        $pos = strpos($result['table'], $findme);
        self::assertEquals(0, $pos);
        $count = substr_count($result['table'], '<tr>');
        self::assertEquals(1, $count); // tr on thead
        $count = substr_count($result['table'], '<tr class=""');
        self::assertEquals(1, $count); // tr on tbody

        // Create another model.
        $templategenerator = $this->getDataGenerator()->get_plugin_generator('tool_certificate');
        $certificate1 = $templategenerator->create_template((object)['name' => 'Certificate 1']);
        $modgenerator = $this->getDataGenerator()->get_plugin_generator('mod_certifygen');
        $modgenerator->create_model_by_name(
            certifygen_model::TYPE_TEACHER_ALL_COURSES_USED,
            $certificate1->get_id(),
            certifygen_model::TYPE_TEACHER_ALL_COURSES_USED
        );

        // Tests.
        $result = getmodellisttable_external::getmodellisttable();
        self::assertIsArray($result);
        self::assertArrayHasKey('table', $result);
        $findme   = '<div class="no-overflow"><table class="flexible table table-striped table-hover generaltable generalbox">';
        $pos = strpos($result['table'], $findme);
        self::assertEquals(0, $pos);
        $count = substr_count($result['table'], '<tr>');
        self::assertEquals(1, $count); // tr on thead
        $count = substr_count($result['table'], '<tr class=""');
        self::assertEquals(2, $count); // tr on tbody
    }
}
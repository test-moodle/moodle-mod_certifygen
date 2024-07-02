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

namespace mod_certifygen;

use coding_exception;
use context_course;
use core_course\customfield\course_handler;
use dml_exception;
use moodle_exception;
use stdClass;
use tool_certificate\certificate;
use tool_certificate\permission;

defined('MOODLE_INTERNAL') || die;
global $CFG;
require_once($CFG->dirroot . '/grade/querylib.php');
require_once($CFG->dirroot . '/user/lib.php');
require_once($CFG->libdir . '/gradelib.php');

class certifygen {
//    /**
//     * Gets users who meet access restrictionss and had not been issued.
//     *
//     * @param stdClass $coursecertificate
//     * @param \cm_info $cm
//     * @return array
//     */
//    public static function get_users_to_issue(stdClass $coursecertificate, \cm_info $cm): array {
//        global $DB;
//        return [];
//        $context = \context_course::instance($coursecertificate->course);
//        // Get users already issued subquery.
//        [$usersissuedsql, $usersissuedparams] = self::get_users_issued_select($coursecertificate->course,
//            $coursecertificate->template);
//        // Get users enrolled with receive capabilities subquery.
//        [$enrolledsql, $enrolledparams] = get_enrolled_sql($context, 'mod/coursecertificate:receive', 0, true);
//        $sql  = "SELECT eu.id FROM ($enrolledsql) eu WHERE eu.id NOT IN ($usersissuedsql)";
//        $params = array_merge($enrolledparams, $usersissuedparams);
//        $potentialusers = $DB->get_records_sql($sql, $params);
//
//        // Filter only users with access to the activity {@see info_module::filter_user_list}.
//        $info = new info_module($cm);
//        $filteredusers = $info->filter_user_list($potentialusers);
//
//        // Filter only users without 'viewall' capabilities and with access to the activity.
//        $users = [];
//        foreach ($filteredusers as $filtereduser) {
//            $modinfouser = get_fast_modinfo($cm->get_course(), $filtereduser->id);
//            $cmuser = $modinfouser->get_cms()[$cm->id] ?? null;
//            // Property 'cm_info::uservisible' checks if user has access to the activity - it is visible, in the
//            // correct group, user has capability to view it, is available. However, for teachers it
//            // can return true even if they do not satisfy availability criteria,
//            // therefore we need to additionally check property 'cm_info::available'.
//            if ($cmuser && $cmuser->uservisible && $cmuser->available) {
//                $users[] = $filtereduser;
//            }
//        }
//        return $users;
//    }

    /**
     * Returns the record for the certificate user has in a given course
     *
     * In rare situations (race conditions) there can be more than one certificate, in which case return the last record.
     *
     * @param int $userid
     * @param int $courseid
     * @param int $templateid
     * @param string $lang
     * @return stdClass|null
     * @throws dml_exception
     */
    public static function get_user_certificate(int $userid, int $courseid, int $templateid, string $lang): ?stdClass {

        global $DB;

        $comparelang = $DB->sql_compare_text('cv.lang');
        $comparelangplaceholder = $DB->sql_compare_text(':lang');
        $comparecomp = $DB->sql_compare_text('ci.component');
        $comparecompplaceholder = $DB->sql_compare_text(':component');
        $sql = "SELECT ci.* 
                FROM {tool_certificate_issues} ci
                INNER JOIN {certifygen_validations} cv ON (cv.issueid = ci.id AND cv.userid = ci.userid)
                WHERE $comparecomp = $comparecompplaceholder 
                    AND ci.courseid = :courseid 
                    AND ci.templateid = :templateid 
                    AND ci.userid = :userid
                    AND ci.archived = 0 
                    AND {$comparelang} = {$comparelangplaceholder}
                ORDER BY ci.id DESC";

        $params = [
            'component' => 'mod_certifygen',
            'courseid' => $courseid,
            'templateid' => $templateid,
            'userid' => $userid,
            'lang' => $lang,
        ];
        $records = $DB->get_records_sql($sql, $params);

        return $records ? reset($records) : null;
    }

    /**
     * Issue a course certificate to the user if they don't already have one
     * @param stdClass $user
     * @param int $templateid
     * @param stdClass $course
     * @param string $lang
     * @return int
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public static function issue_certificate(stdClass $user, int $templateid, stdClass $course, string $lang): int {
        if (self::get_user_certificate($user->id, $course->id, $templateid, $lang)) {
            return 0;
        }
        try {
            $template = template::instance($templateid, (object) ['lang' => $lang]);
            $issuedata = self::get_issue_data($course, $user);
            $expirydatetype = $expirydateoffset = 0;
            $expirydate = certificate::calculate_expirydate(
                $expirydatetype,
                $expirydateoffset,
                $expirydateoffset
            );
            return $template->issue_certificate($user->id, $expirydate, $issuedata, 'mod_certifygen', $course->id);
        } catch(moodle_exception $e) {
            error_log(__FUNCTION__ . ' ' . __LINE__. ' ERROR: '. var_export($e->getMessage(), true));
        }
        return 0;
    }

    /**
     * Returns select for the users that have been already issued
     *
     * @param int $courseid
     * @param int $templateid
     * @return array
     */
    private static function get_users_issued_select(int $courseid, int $templateid): array {
        $sql = "SELECT DISTINCT ci.userid FROM {tool_certificate_issues} ci
                WHERE component = :component AND courseid = :courseid AND templateid = :templateid
                      AND archived = 0";
        $params = ['component' => 'mod_certifygen', 'courseid' => $courseid,
            'templateid' => $templateid, ];
        return [$sql, $params];
    }

    /**
     * Get data for the issue. Important course fields (id, shortname, fullname and URL) and course customfields.
     * @param stdClass $course
     * @param stdClass $user
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     */
    public static function get_issue_data(stdClass $course, stdClass $user): array {
        global $DB;

        // Get user course completion date.
        $result = $DB->get_field('course_completions', 'timecompleted',
            ['course' => $course->id, 'userid' => $user->id]);
        $completiondate = $result ? userdate($result, get_string('strftimedatefullshort')) : '';

        // Get user course grade.
        $grade = grade_get_course_grade($user->id, $course->id);
        if ($grade && $grade->grade) {
            $gradestr = $grade->str_grade;
        }

        $issuedata = [
            'courseid' => $course->id,
            'courseshortname' => $course->shortname,
            'coursefullname' => $course->fullname,
            'courseurl' => course_get_url($course)->out(),
            'coursecompletiondate' => $completiondate,
            'coursegrade' => $gradestr ?? '',
        ];
        // Add course custom fields data.
        $handler = course_handler::create();
        foreach ($handler->get_instance_data($course->id, true) as $data) {
            $issuedata['coursecustomfield_' . $data->get_field()->get('shortname')] = $data->export_value();
        }

        return $issuedata;
    }

    /**
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public static function get_user_certificate_file_url(string $templateid, int $userid, int $courseid, string $lang) : string {
        $users = user_get_users_by_id([$userid]);
        $user = reset($users);
        $course = get_course($courseid);
        certifygen::issue_certificate($user, $templateid, $course, $lang);
        $url = "";
        if ($existingcertificate = self::get_user_certificate($userid, $course->id, $templateid, $lang)) {
            $issue = template::get_issue_from_code($existingcertificate->code);
            $context = context_course::instance($issue->courseid, IGNORE_MISSING) ?: null;
            $template = $issue ? template::instance($issue->templateid, (object) ['lang' => $lang]) : null;
            if ($template && (permission::can_verify() ||
                    permission::can_view_issue($template, $issue, $context))) {
                $url = $template->get_issue_file_url($issue);
            } else {
                throw new moodle_exception('certificatenotfound', 'mod_certifygen');
            }
        }
        return $url;
    }

    /**
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public static function get_user_certificate_file(string $templateid, int $userid, int $courseid, string $lang)
    {
        $users = user_get_users_by_id([$userid]);
        $user = reset($users);
        $course = get_course($courseid);
        certifygen::issue_certificate($user, $templateid, $course, $lang);
        if ($existingcertificate = self::get_user_certificate($userid, $course->id, $templateid, $lang)) {

            $issue = template::get_issue_from_code($existingcertificate->code);
            $context = context_course::instance($issue->courseid, IGNORE_MISSING) ?: null;

            $template = $issue ? template::instance($issue->templateid, (object) ['lang' => $lang]) : null;
            if ($template && (permission::can_verify() ||
                    permission::can_view_issue($template, $issue, $context))) {
                return $template->get_issue_file($issue);
            } else {
                throw new moodle_exception('certificatenotfound', 'mod_certifygen');
            }
        }
        return null;
    }

    /**
     * Get groupmode subquery
     *
     * @param int $groupmode
     * @param int $groupid
     * @return array
     * @throws coding_exception
     */
    private static function get_groupmode_subquery(int $groupmode, int $groupid) {
        if (($groupmode != NOGROUPS) && $groupid) {
            [$sql, $params] = groups_get_members_ids_sql($groupid);
            $groupmodequery = "AND u.id IN ($sql)";
            return [$groupmodequery, $params];
        }
        return ['', []];
    }

    /**
     * @param string $lang
     * @param int $templateid
     * @param int $courseid
     * @param string $component
     * @param int|null $userid
     * @param string $tifirst
     * @param string $tilast
     * @param int $limitfrom
     * @param int $limitnum
     * @param string $sort
     * @return array
     * @throws dml_exception
     */
    public static function get_issues_for_course_by_lang(string $lang, int $templateid, int $courseid, string $component,
                                                         int $userid, string $tifirst, string $tilast,
                                                         int $limitfrom, int $limitnum, string $sort = ''): array {
        global $DB;

        if (empty($sort)) {
            $sort = 'ci.timecreated DESC';
        }

        $params = [
            'now' => time(),
            'templateid' => $templateid,
            'courseid' => $courseid,
            'component' => $component,
            'lang' => $lang,
            ];
        $where = "";
        if (!empty($tifirst)) {
            $params['tifirst'] = $tifirst . '%';
            $where .= ' AND ' . $DB->sql_like('u.firstname', ':tifirst');

        }
        if (!empty($tilast)) {
            $params['tilast'] = $tilast . '%';
            $where .= ' AND ' . $DB->sql_like('u.lastname', ':tilast');
        }
        if ($userid) {
            $params['userid'] = $userid;
            $where = ' AND u.id = :userid';
        }

        $sql = "SELECT RAND(), us.id, ci.id as issueid, ci.code, ci.emailed, ci.timecreated, ci.userid, ci.templateid, ci.expires,
       ci.courseid, ci.archived, cv.lang, cv.status, cv.id as validationid, us.*
                    FROM (SELECT u.*, c.id as courseid
                        FROM {user} u
                        INNER JOIN {user_enrolments} ue ON ue.userid = u.id
                        INNER JOIN {enrol} e ON e.id = ue.enrolid
                        INNER JOIN {course} c ON c.id = e.courseid
                        INNER JOIN {context} cont ON (cont.instanceid = c.id AND cont.contextlevel = 50)
                        INNER JOIN {role_assignments} ra ON ( ra.contextid = cont.id AND  ra.userid = u.id)
                        INNER JOIN {role} r ON r.id = ra.roleid
                        WHERE r.shortname = 'student'
                        AND c.id = :courseid $where
                        ) AS us
                    LEFT JOIN {tool_certificate_issues} ci ON (ci.userid = us.id AND ci.courseid = us.courseid AND ci.templateid = :templateid AND ci.component = :component)
                    LEFT JOIN {certifygen_validations} cv ON (cv.userid = us.id AND cv.issueid = ci.id AND cv.lang = :lang)";

        return $DB->get_records_sql($sql, $params, $limitfrom, $limitnum);
    }

    /**
     * Get extra fields for select query of certificates.
     *
     * @param \context $context
     * @return string
     * @throws coding_exception
     */
    public static function get_extra_user_fields(\context $context): string {
        global $CFG;

        if ($CFG->version < 2021050700) {
            // Moodle 3.9-3.10.
            $extrafields = get_extra_user_fields($context);
            $userfields = \user_picture::fields('u', $extrafields);

        } else {
            // Moodle 3.11 and above.
            $extrafields = \core_user\fields::for_identity($context, false)->get_required_fields();
            $userfields = \core_user\fields::for_userpic()->including(...$extrafields)
                ->get_sql('u', false, '', '', false)->selects;
        }

        return str_replace(' ', '', $userfields);
    }
    /**
     * Helps to build SQL to retrieve users that can be displayed to the current user
     *
     * If tool_tenant is installed - adds a tenant filter
     *
     * @uses \tool_tenant\tenancy::get_users_subquery()
     *
     * @param string $usertablealias
     * @param bool $canseeall do not add tenant check if user has capability 'tool/tenant:manage'
     * @return string
     */
    public static function get_users_subquery(string $usertablealias = 'u', bool $canseeall = true): string {
        return component_class_callback('tool_tenant\\tenancy', 'get_users_subquery',
            [$canseeall, false, $usertablealias.'.id'], '1=1');
    }

    /**
     * @param int $courseid
     * @param string $tifirst
     * @param string $tilast
     * @param int $userid
     * @return int
     * @throws dml_exception
     */
    public static function count_issues_for_course_by_lang(int $courseid, string $tifirst, string $tilast, int $userid = 0) {
        global $DB;

        $where = '';
        $params = [
            'courseid' => $courseid,
        ];
        if (!empty($tifirst)) {
            $where .= " AND u.firstname LIKE '$tifirst%'";

        }
        if (!empty($tilast)) {
            $where .= " AND u.firstname LIKE '%$tilast'";
        }

        if ($userid) {
            $params['userid'] = $userid;
            $where = ' AND u.id = :userid';
        }

        $sql = "SELECT COUNT(u.id) as count
                    FROM {user} u
                    INNER JOIN {user_enrolments} ue ON ue.userid = u.id
                    INNER JOIN {enrol} e ON e.id = ue.enrolid
                    INNER JOIN {course} c ON c.id = e.courseid
                    INNER JOIN {context} cont ON (cont.instanceid = c.id AND cont.contextlevel = 50)
                    INNER JOIN {role_assignments} ra ON ( ra.contextid = cont.id AND  ra.userid = u.id)
                    INNER JOIN {role} r ON r.id = ra.roleid
                    WHERE r.shortname = 'student'
                    AND c.id = :courseid $where";

        return $DB->count_records_sql($sql, $params);
    }
}

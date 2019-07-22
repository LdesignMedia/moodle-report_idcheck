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

/**
 * This file contains functions used by the progress report
 *
 * @package    report_idcheck
 * @copyright  2019-07-22 Mfreak.nl | LdesignMedia.nl - Luuk Verhoeven
 * @author     Luuk Verhoeven
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * This function extends the navigation with the report items
 *
 * @param navigation_node $navigation The navigation node to extend
 * @param stdClass        $course     The course to object for the report
 * @param stdClass        $context    The context of the course
 *
 * @throws coding_exception
 * @throws moodle_exception
 */
function report_idcheck_extend_navigation_course($navigation, $course, $context) {
    global $CFG, $DB;

    require_once($CFG->libdir . '/completionlib.php');

    $showonnavigation = has_capability('report/progress:view', $context);
    $group = groups_get_course_group($course, true); // Supposed to verify group
    if ($group === 0 && $course->groupmode == SEPARATEGROUPS) {
        $showonnavigation = ($showonnavigation && has_capability('moodle/site:accessallgroups', $context));
    }

    // Check if there is a 'questionpopup' added to the course.
    if (!$DB->record_exists('block_instances', ['parentcontextid' => $context->id, 'blockname' => 'questionpopup'])) {
        return;
    }

    $completion = new completion_info($course);
    $showonnavigation = ($showonnavigation && $completion->is_enabled() && $completion->has_activities());
    if ($showonnavigation) {
        $url = new moodle_url('/report/idcheck/index.php', ['course' => $course->id]);
        $navigation->add(get_string('pluginname', 'report_idcheck'), $url, navigation_node::TYPE_SETTING,
            null, null, new pix_icon('i/report', ''));
    }
}

/**
 * report_idcheck_questionpopup_answer
 *
 * @param int $userid
 * @param int $courseid
 *
 * @return string
 * @throws dml_exception
 */
function report_idcheck_questionpopup_answer(int $userid, int $courseid = 0) : string {
    global $DB;
    $coursecontext = context_course::instance($courseid);

    $sql = 'SELECT a.answer
            FROM {block_instances} bi 
            JOIN {context} c ON (bi.id = c.instanceid AND c.contextlevel = 80)
            JOIN {block_questionpopup_answer} a ON (a.userid = :userid AND a.contextid = c.id)
            WHERE
                    bi.parentcontextid = :parentcontextid
                AND 
                    bi.blockname = :blockname
                ';

    $answer = $DB->get_record_sql($sql, [
        'userid' => $userid,
        'parentcontextid' => $coursecontext->id,
        'blockname' => 'questionpopup',
    ]);

    return $answer->answer ?? '-';
}

/**
 * Return a list of page types
 *
 * @param string   $pagetype       current page type
 * @param stdClass $parentcontext  Block's parent context
 * @param stdClass $currentcontext Current context of block
 *
 * @return array
 * @throws coding_exception
 */
function report_idcheck_page_type_list($pagetype, $parentcontext, $currentcontext) {
    $array = [
        '*' => get_string('page-x', 'pagetype'),
        'report-*' => get_string('page-report-x', 'pagetype'),
        'report-progress-*' => get_string('page-report-progress-x', 'report_idcheck'),
        'report-progress-index' => get_string('page-report-progress-index', 'report_idcheck'),
    ];

    return $array;
}
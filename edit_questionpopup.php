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
 *
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   report_idcheck
 * @copyright 20/01/2021 Mfreak.nl | LdesignMedia.nl - Luuk Verhoeven
 * @author    Luuk Verhoeven
 **/
require('../../config.php');
require_once('lib.php');

defined('MOODLE_INTERNAL') || die;

// Get course
$id = required_param('course', PARAM_INT);
$userid = required_param('userid', PARAM_INT);
$course = $DB->get_record('course', ['id' => $id]);
if (!$course) {
    print_error('invalidcourseid');
}
$context = context_course::instance($course->id);

$PAGE->set_url(new moodle_url('/report/idcheck/edit_questionpopup.php', [
    'course' => $id,
    'userid' => $userid,
]));

require_login($course);
require_capability('report/idcheck:view', $context);

$form = new \report_idcheck\form\questionpopup($PAGE->url, [
    'answers' => report_idcheck_questionpopup_answer($userid, $course->id),
]);

if (($data = $form->get_data()) != false) {

    unset($data->submitbutton);

    $coursecontext = context_course::instance($course->id);
    $sql = 'SELECT id
            FROM {block_questionpopup_answer} a  
            WHERE
                (a.userid = :userid AND a.contextid = :contextid)';

    $answerrecord = $DB->get_record_sql($sql, [
        'userid' => $userid,
        'contextid' => $coursecontext->id,
    ], MUST_EXIST);

    $questions = [];
    foreach ($data as $question => $answer) {
        $questions[s($question)] = s($answer);
    }

    $DB->update_record('block_questionpopup_answer', [
        'id' => $answerrecord->id,
        'answer' => serialize($questions),
    ]);

    redirect(new moodle_url('/report/idcheck/index.php', ['course' => $id]));
}

echo $OUTPUT->header();
echo $form->render();
echo $OUTPUT->footer();

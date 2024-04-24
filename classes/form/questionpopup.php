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

namespace report_idcheck\form;
global $CFG;
require_once($CFG->libdir . '/formslib.php');
defined('MOODLE_INTERNAL') || die;

/**
 * Class questionpopup
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   report_idcheck
 * @copyright 20/01/2021 Mfreak.nl | LdesignMedia.nl - Luuk Verhoeven
 * @author    Luuk Verhoeven
 */
class questionpopup extends \moodleform {

    /**
     * Form definition.
     */
    protected function definition() {

        $mform = &$this->_form;
        $mform->addElement('header', 'header', get_string('form:questionpopup', 'report_idcheck'));

        foreach ($this->_customdata['answers'] as $question => $answer) {
            $mform->addElement('text', $question);
            $mform->setDefault($question, s($answer));
        }

        $this->add_action_buttons(true, get_string('btn:edit', 'report_idcheck'));
    }

}

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
 * @copyright 27/08/2019 Mfreak.nl | LdesignMedia.nl - Luuk Verhoeven
 * @author    Luuk Verhoeven
 **/

namespace report_idcheck;
defined('MOODLE_INTERNAL') || die;

class helper {

    /**
     * get_answers_forum
     *
     * @param int $activityid
     * @param int $userid
     *
     * @return string
     * @throws \dml_exception
     * @throws \coding_exception
     */
    public static function get_answers_forum(int $activityid, int $userid) {
        global $DB;
        $html = '';

        $discussions = self::get_discussions($activityid);
        if (empty($discussions)) {
            return '';
        }

        list($insql, $params) = $DB->get_in_or_equal($discussions, SQL_PARAMS_NAMED);

        $sql = 'SELECT p.id, p.message 
                FROM {forum_posts} p 
                WHERE p.parent != 0 
                    AND p.userid = :userid 
                    AND p.discussion ' . $insql;

        $results = $DB->get_records_sql($sql, ['userid' => $userid] + $params);
        foreach ($results as $result) {
            $html .= $result->message . '<hr>';
        }

        return $html;
    }

    /**
     * get_discussions
     *
     * @param int $activityid
     *
     * @return mixed
     * @throws \dml_exception
     */
    private static function get_discussions(int $activityid) {
        global $DB;

        static $forums;

        if (isset($forums[$activityid])) {
            return $forums[$activityid];
        }

        $forums[$activityid] = $DB->get_records_menu('forum_discussions', ['forum' => $activityid], '', 'id , id as v');

        return $forums[$activityid];
    }

}
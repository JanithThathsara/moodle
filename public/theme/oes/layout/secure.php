<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Secure exam layout for theme_oes.
 * Used for Safe Exam Browser and secure exam windows.
 *
 * @package    theme_oes
 * @copyright  2026 Mahapola Ports & Maritime Academy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$blockshtml = $OUTPUT->blocks('side-pre');
$hasblocks = strpos($blockshtml, 'data-block=') !== false;
$bodyattributes = $OUTPUT->body_attributes(['pagelayout-secure', 'oes-secure-exam-mode']);

global $USER;
$candidate_name = isloggedin() ? fullname($USER) : 'Candidate';
$candidate_id = isloggedin() ? (!empty($USER->idnumber) ? $USER->idnumber : $USER->username) : 'N/A';

$PAGE->requires->js(new moodle_url('/theme/oes/javascript/exam_nav.js'));

$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'wwwroot' => $CFG->wwwroot,
    'output' => $OUTPUT,
    'bodyattributes' => $bodyattributes,
    'sidepreblocks' => $blockshtml,
    'hasblocks' => $hasblocks,
    'is_secure_exam' => true,
    'exam_secure_badge' => get_string('secure_exam_active', 'theme_oes'),
    'exam_secure_warning' => get_string('secure_exam_warning', 'theme_oes'),
    'candidate_name' => $candidate_name,
    'candidate_id' => $candidate_id,
    'session_time' => userdate(time(), get_string('strftimedatetimeshort', 'langconfig')),
];

if (empty($PAGE->layout_options['noactivityheader'])) {
    $header = $PAGE->activityheader;
    $renderer = $PAGE->get_renderer('core');
    $templatecontext['headercontent'] = $header->export_for_template($renderer);
}

echo $OUTPUT->render_from_template('theme_boost/secure', $templatecontext);

<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Drawer based layout for theme_oes.
 *
 * @package    theme_oes
 * @copyright  2026 Mahapola Ports & Maritime Academy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/behat/lib.php');
require_once($CFG->dirroot . '/course/lib.php');

// Add block button in editing mode.
$addblockbutton = $OUTPUT->addblockbutton();

if (isloggedin()) {
    $courseindexopen = (get_user_preferences('drawer-open-index', true) == true);
    $blockdraweropen = (get_user_preferences('drawer-open-block') == true);
} else {
    $courseindexopen = false;
    $blockdraweropen = false;
}

if (defined('BEHAT_SITE_RUNNING') && get_user_preferences('behat_keep_drawer_closed') != 1) {
    $blockdraweropen = true;
}

$extraclasses = ['uses-drawers', 'oes-theme'];
if ($courseindexopen) {
    $extraclasses[] = 'drawer-open-index';
}

$blockshtml = $OUTPUT->blocks('side-pre');
$hasblocks = (strpos($blockshtml, 'data-block=') !== false || !empty($addblockbutton));
if (!$hasblocks) {
    $blockdraweropen = false;
}

$forceblockdraweropen = false;
if (strpos($PAGE->pagetype, 'mod-quiz-attempt') !== false || strpos($PAGE->pagetype, 'mod-quiz-review') !== false) {
    if ($hasblocks) {
        $forceblockdraweropen = true;
        $blockdraweropen = true;
    }
    $PAGE->requires->js(new moodle_url('/theme/oes/javascript/exam_nav.js'));
}
$courseindex = core_course_drawer();
if (!$courseindex) {
    $courseindexopen = false;
}

$bodyattributes = $OUTPUT->body_attributes($extraclasses);
$forceblockdraweropen = $OUTPUT->firstview_fakeblocks();

$secondarynavigation = false;
$overflow = '';
if ($PAGE->has_secondary_navigation()) {
    $tablistnav = $PAGE->has_tablist_secondary_navigation();
    $moremenu = new \core\navigation\output\more_menu($PAGE->secondarynav, 'nav-tabs', true, $tablistnav);
    $secondarynavigation = $moremenu->export_for_template($OUTPUT);
    $overflowdata = $PAGE->secondarynav->get_overflow_menu_data();
    if (!is_null($overflowdata)) {
        $selectmenu = new \core\output\select_menu(
            'tertiarynavigation',
            $overflowdata->urls,
            $overflowdata->selected,
        );
        $selectmenu->set_label($overflowdata->label, $overflowdata->labelattributes);
        $overflow = $selectmenu->export_for_template($OUTPUT);
    }
}

$primary = new core\navigation\output\primary($PAGE);
$renderer = $PAGE->get_renderer('core');
$primarymenu = $primary->export_for_template($renderer);
$buildregionmainsettings = !$PAGE->include_region_main_settings_in_header_actions() && !$PAGE->has_secondary_navigation();
$regionmainsettingsmenu = $buildregionmainsettings ? $OUTPUT->region_main_settings_menu() : false;

$header = $PAGE->activityheader;
$headercontent = $header->export_for_template($renderer);

$coursefullname = ($PAGE->course?->fullname) ? format_string(
    $PAGE->course->fullname,
    true,
    ['context' => context_course::instance($PAGE->course->id), 'escape' => false],
) : '';
$courseurl = $PAGE->course ? new \core\url('/course/view.php', ['id' => $PAGE->course->id]) : null;

$lockdownurl = get_config('theme_oes', 'lockdown_browser_url');
$islocaldownload = false;
$localfilename = 'SEB_Setup.exe';

// Check if local installer exists in Moodle public/downloads or downloads folder.
$haslocalfile = false;
if (file_exists($CFG->dirroot . '/downloads/' . $localfilename)) {
    $haslocalfile = true;
} else if (file_exists($CFG->dirroot . '/public/downloads/' . $localfilename)) {
    $haslocalfile = true;
} else if (file_exists(dirname($CFG->dirroot) . '/public/downloads/' . $localfilename)) {
    $haslocalfile = true;
}

if ($haslocalfile) {
    // ALWAYS prioritize local offline installer when file exists on localhost/server!
    $lockdownurl = $CFG->wwwroot . '/downloads/' . $localfilename;
    $islocaldownload = true;
} else if (empty($lockdownurl) || strpos($lockdownurl, 'safeexambrowser.org') !== false) {
    $lockdownurl = $CFG->wwwroot . '/downloads/' . $localfilename;
    $islocaldownload = true;
} else {
    if (strpos($lockdownurl, '/') === 0) {
        $lockdownurl = $CFG->wwwroot . $lockdownurl;
        $islocaldownload = true;
    } else if (strpos($lockdownurl, $CFG->wwwroot) !== false || preg_match('/\.(exe|msi|zip)$/i', $lockdownurl)) {
        $islocaldownload = true;
    }
}

$lockdownshow = get_config('theme_oes', 'lockdown_browser_show');
$showlockdown = ($lockdownshow !== '0' && $lockdownshow !== 0);

$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'coursefullname' => $coursefullname,
    'courseurl' => $courseurl ? $courseurl->out(false) : null,
    'output' => $OUTPUT,
    'sidepreblocks' => $blockshtml,
    'hasblocks' => $hasblocks,
    'bodyattributes' => $bodyattributes,
    'courseindexopen' => $courseindexopen,
    'blockdraweropen' => $blockdraweropen,
    'courseindex' => $courseindex,
    'primarymoremenu' => $primarymenu['moremenu'],
    'secondarymoremenu' => $secondarynavigation ?: false,
    'mobileprimarynav' => $primarymenu['mobileprimarynav'],
    'usermenu' => $primarymenu['user'],
    'langmenu' => $primarymenu['lang'],
    'forceblockdraweropen' => $forceblockdraweropen,
    'regionmainsettingsmenu' => $regionmainsettingsmenu,
    'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu),
    'overflow' => $overflow,
    'headercontent' => $headercontent,
    'addblockbutton' => $addblockbutton,
    'lockdown_url' => $lockdownurl,
    'lockdown_show' => $showlockdown,
    'is_local_download' => $islocaldownload,
    'local_installer_name' => $localfilename,
];

echo $OUTPUT->render_from_template('theme_boost/drawers', $templatecontext);

<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Library functions and callbacks for theme_oes.
 *
 * @package    theme_oes
 * @copyright  2026 Mahapola Ports & Maritime Academy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Returns the main SCSS content.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_oes_get_main_scss_content($theme) {
    global $CFG;
    return file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
}

/**
 * Get SCSS to prepend.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_oes_get_pre_scss($theme) {
    $scss = '';
    $brandcolor = !empty($theme->settings->brandcolor) ? $theme->settings->brandcolor : '#002b49';
    $accentcolor = !empty($theme->settings->accentcolor) ? $theme->settings->accentcolor : '#0076a8';

    $scss .= "\$primary: {$brandcolor} !default;\n";
    $scss .= "\$info: {$accentcolor} !default;\n";
    $scss .= "\$success: #107e54 !default;\n";
    $scss .= "\$warning: #c28800 !default;\n";
    $scss .= "\$danger: #c02b2b !default;\n";
    $scss .= "\$light: #f4f8fb !default;\n";
    $scss .= "\$body-bg: #f4f8fb !default;\n";
    $scss .= "\$body-color: #1a2a3a !default;\n";
    $scss .= "\$link-color: #005b8f !default;\n";
    $scss .= "\$link-hover-color: #00385c !default;\n";
    $scss .= "\$course-content-maxwidth: 1400px !default;\n";
    $scss .= "\$navbar-height: 68px !default;\n";

    if (!empty($theme->settings->scsspre)) {
        $scss .= "\n" . $theme->settings->scsspre . "\n";
    }

    return $scss;
}

/**
 * Inject additional SCSS modules.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_oes_get_extra_scss($theme) {
    global $CFG;
    $content = '';

    $modules = [
        'variables',
        'layout',
        'navbar',
        'home',
        'dashboard',
        'mycourses',
        'exam',
        'secure',
        'results',
        'login',
        'footer',
    ];

    foreach ($modules as $module) {
        $file = $CFG->dirroot . '/theme/oes/scss/_' . $module . '.scss';
        if (file_exists($file)) {
            $content .= "\n/* --- OES " . strtoupper($module) . " --- */\n";
            $content .= file_get_contents($file);
        }
    }

    if (!empty($theme->settings->scss)) {
        $content .= "\n/* --- THEME SETTINGS EXTRA SCSS --- */\n" . $theme->settings->scss;
    }

    return $content;
}

/**
 * Serves any files associated with the theme settings.
 */
function theme_oes_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    if ($context->contextlevel == CONTEXT_SYSTEM && ($filearea === 'logo' || $filearea === 'backgroundimage' ||
        $filearea === 'loginbackgroundimage')) {
        $theme = theme_config::load('oes');
        if (!array_key_exists('cacheability', $options)) {
            $options['cacheability'] = 'public';
        }
        return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
    } else {
        send_file_not_found();
    }
}

/**
 * User preferences.
 */
function theme_oes_user_preferences(): array {
    return [
        'drawer-open-block' => [
            'type' => PARAM_BOOL,
            'null' => NULL_NOT_ALLOWED,
            'default' => false,
            'permissioncallback' => [core_user::class, 'is_current_user'],
        ],
        'drawer-open-index' => [
            'type' => PARAM_BOOL,
            'null' => NULL_NOT_ALLOWED,
            'default' => true,
            'permissioncallback' => [core_user::class, 'is_current_user'],
        ],
    ];
}

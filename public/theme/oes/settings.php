<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Settings for theme_oes.
 *
 * @package    theme_oes
 * @copyright  2026 Mahapola Ports & Maritime Academy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    $settings = new admin_settingpage('themesettingoes', get_string('configtitle', 'theme_oes'));

    // Heading: General.
    $settings->add(new admin_setting_heading('theme_oes_general_heading',
        get_string('generalsettings', 'theme_oes'), ''));

    // Brand Primary Color.
    $name = 'theme_oes/brandcolor';
    $title = get_string('brandcolor', 'theme_oes');
    $description = get_string('brandcolor_desc', 'theme_oes');
    $default = '#002b49';
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Brand Accent Color.
    $name = 'theme_oes/accentcolor';
    $title = get_string('accentcolor', 'theme_oes');
    $description = get_string('accentcolor_desc', 'theme_oes');
    $default = '#0076a8';
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Heading: Lockdown Browser.
    $settings->add(new admin_setting_heading('theme_oes_lockdown_heading',
        get_string('lockdownsettings', 'theme_oes'),
        get_string('lockdown_browser_desc', 'theme_oes')));

    // Lockdown Browser Download URL.
    $name = 'theme_oes/lockdown_browser_url';
    $title = get_string('lockdown_browser_url', 'theme_oes');
    $description = get_string('lockdown_browser_url_desc', 'theme_oes');
    $default = 'https://safeexambrowser.org/download_en.html';
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Show Lockdown Browser in Navbar.
    $name = 'theme_oes/lockdown_browser_show';
    $title = get_string('lockdown_browser_show', 'theme_oes');
    $description = get_string('lockdown_browser_show_desc', 'theme_oes');
    $default = 1;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Raw initial SCSS.
    $name = 'theme_oes/scsspre';
    $title = get_string('rawscsspre', 'admin');
    $description = get_string('rawscsspre_desc', 'admin');
    $setting = new admin_setting_scsscode($name, $title, $description, '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Raw SCSS.
    $name = 'theme_oes/scss';
    $title = get_string('rawscss', 'admin');
    $description = get_string('rawscss_desc', 'admin');
    $setting = new admin_setting_scsscode($name, $title, $description, '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);
}

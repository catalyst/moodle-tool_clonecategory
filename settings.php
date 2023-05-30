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
// You should have received a copy of the GNU General Public Licensetool_clonecategory
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
/**
 * Clone category admin settings
 *
 * @package    tool_clonecategory
 * @copyright  2018, tim@avide.com.au, 2023 Matthew Hilton <matthewhilton@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if (has_capability('moodle/site:config', context_system::instance())) {
    $ADMIN->add('tools', new admin_category('tool_clonecategory_pages', get_string('action_link', 'tool_clonecategory')));

    // Main page.
    $ADMIN->add('tool_clonecategory_pages', new admin_externalpage(
        'clonecategory_action',
        get_string('action_link', 'tool_clonecategory'),
        $CFG->wwwroot. '/admin/tool/clonecategory/action.php'));

    // History page.
    $ADMIN->add('tool_clonecategory_pages', new admin_externalpage(
        'clonecategory_history',
        get_string('cloning_history', 'tool_clonecategory'),
        $CFG->wwwroot. '/admin/tool/clonecategory/history.php'));
}

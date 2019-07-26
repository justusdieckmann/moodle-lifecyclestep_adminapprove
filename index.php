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
 * Life Cycle Admin Approve Step
 *
 * @package tool_lifecycle_step
 * @subpackage adminapprove
 * @copyright  2019 Justus Dieckmann WWU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../../config.php');

require_login(null, false);

$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_url(new \moodle_url('/admin/tool/lifecycle/step/adminapprove/index.php'));

require_capability('moodle/site:config', context_system::instance());

$action = optional_param('act', null, PARAM_ALPHA);
$ids = optional_param_array('c', array(), PARAM_INT);
$wid = required_param('wid', PARAM_INT);

$workflow = \tool_lifecycle\manager\workflow_manager::get_workflow($wid);
if (!$workflow) {
    throw new moodle_exception('Workflowid does not correspond to any workflow.');
}

if (count($ids) > 0 && ($action == 'proceed' || $action == 'rollback')) {
    $sql = 'UPDATE {lifecyclestep_adminapprove} ' .
        'SET status = ' . ($action == 'proceed' ? 1 : 2) .
        'WHERE id IN (' . implode(',', $ids) . ') ';
        $DB->execute($sql, array('wid' => $wid));
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname', 'lifecyclestep_adminapprove'));

echo 'These courses are currently waiting in the "Make Decision #1" Step in the "WF#1" Workflow.<br>';
echo '<form action="" method="post" id="adminapprove-action-form"><input type="hidden" name="act" id="act" value="">';

$table = new lifecyclestep_adminapprove\decision_table($wid);
$table->out(30, false);

echo '</form>';

echo 'Bulk actions:<br>';
echo '<div class="btn btn-secondary m-1" id="adminapprove-bulk-proceed">' . get_string('proceedselected', 'lifecyclestep_adminapprove') . '</div>';
echo '<div class="btn btn-secondary m-1" id="adminapprove-bulk-rollback">' . get_string('rollbackselected', 'lifecyclestep_adminapprove') . '</div>';

$PAGE->requires->js_call_amd('lifecyclestep_adminapprove/init', 'init');

echo $OUTPUT->footer();

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
 * Subplugin for the empty course.
 *
 * @package lifecycletrigger_emptycourse
 * @copyright  2022 Jonas Khan HFT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lifecycle\trigger;

use tool_lifecycle\local\manager\settings_manager;
use tool_lifecycle\local\response\trigger_response;
use tool_lifecycle\settings_type;

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/../lib.php');
require_once(__DIR__ . '/../../lib.php');

/**
 * Class which implements the basic methods necessary for a cleanyp courses trigger subplugin
 * @package lifecycletrigger_emptycourse
 * @copyright  2022 Jonas Khan HFT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class emptycourse extends base_automatic {

    private  $options = array(
      	 '1' => 'book',
      	 '2' => 'resource',
      	 '3' => 'folder',
      	 '4' => 'label',
      	 '5' => 'imscp',
      	 '6' => 'url',
      	 '7' => 'page',
      	 '8' => 'assignment',
      	 '9' => 'chat',
      	 '10' => 'choice',
      	 '11' => 'data',
      	 '12' => 'feedback',
      	 '13' => 'glossary',
      	 '14' => 'lesson',
      	 '15' => 'quiz',
      	 '16' => 'scorm',
      	 '17' => 'survey',
      	 '18' => 'wiki',
      	 '19' => 'workshop',
      	 '20'=> 'forum'
         );

    /**
     * Checks the course and returns a repsonse, which tells if the course should be further processed.
     * @param object $course Course to be processed.
     * @param int $triggerid Id of the trigger instance.
     * @return trigger_response
     */
    public function check_course($course, $triggerid) {
        // Everything is already in the sql statement.
        return trigger_response::trigger();
    }

    /**
     * Add sql counting activites and resources of a specific type within a course.
     * @param int $triggerid Id of the trigger.
     * @return array A list containing the constructed sql fragment and an array of parameters.
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function get_course_recordset_where($triggerid) {
        $exclude = settings_manager::get_settings($triggerid, settings_type::TRIGGER)['exclude'];
        $options = $this->options;

        if ($exclude != ''){
            $excludeasarray = explode(',',$exclude);
            foreach($excludeasarray as $key) {
                unset($options[$key]);
            }
	}

        $sql = "1=1";
        foreach ($options as $type) {
            $sql .= " AND {course}.id not in (SELECT course FROM {".$type."})";
        }

        return array($sql, array());
    }

    /**
     * The return value should be equivalent with the name of the subplugin folder.
     * @return string technical name of the subplugin
     */
    public function get_subpluginname() {
        return 'emptycourse';
    }

    /**
     * Defines which settings each instance of the subplugin offers for the user to define.
     * @return instance_setting[] containing settings keys and PARAM_TYPES
     */
    public function instance_settings() {
        return array(
            new instance_setting('exclude', PARAM_SEQUENCE, true)
        );
    }

    /**
     * At the content to be excluded.
     * @param \MoodleQuickForm $mform
     * @throws \coding_exception
     */
    public function extend_add_instance_form_definition($mform) {
        $options = $this->options;
        $mform->addElement('select', 'exclude', get_string('exclude', 'lifecycletrigger_emptycourse'), $options);
        $mform->getElement('exclude')->setMultiple(true);
        $mform->addHelpButton('exclude', 'exclude', 'lifecycletrigger_emptycourse');
    }

    /**
     * Reset the content to be excluded at the add instance form initializiation.
     * @param \MoodleQuickForm $mform
     * @param array $settings array containing the settings from the db.
     */
    public function extend_add_instance_form_definition_after_data($mform, $settings) {
        $default = array( '20', '19');
        $mform->setDefault('exclude', $default);
    }
}

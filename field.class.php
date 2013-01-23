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
 * @package    profilefield
 * @subpackage radio
 * @copyright  2012 onwards Dan Marsden {@link http://danmarsden.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class profile_field_radio extends profile_field_base {
    var $options;
    var $datakey;

    /**
     * Constructor method.
     * Pulls out the options for the radio from the database and sets the
     * the corresponding key for the data if it exists
     */
    function profile_field_radio($fieldid=0, $userid=0) {
        //first call parent constructor
        $this->profile_field_base($fieldid, $userid);

        /// Param 1 for radio type is the options
        $options = explode("\n", $this->field->param1);
        $this->options = array();
        foreach($options as $key => $option) {
            $this->options[$key] = format_string($option);//multilang formatting
        }

        /// Set the data key
        if ($this->data !== NULL) {
            $this->datakey = (int)array_search($this->data, $this->options);
        }
    }

    /**
     * Create the code snippet for this field instance
     * Overwrites the base class method
     * @param   object   moodleform instance
     */
    function edit_field_add(&$mform) {
        $radioarray=array();
        $attributes = array();
        foreach ($this->options as $option) {
            $name = format_string($option);
            if (!empty($this->field->param2)) { //dirty hack
                $name .='<br/>';
            }
            $radioarray[] =& $mform->createElement('radio',  $this->inputname, '', $name, format_string($option), $attributes);
        }
        $mform->addGroup($radioarray, $this->inputname.'_grp', format_string($this->field->name), array(' '), false);

        if ($this->is_required()) {
            $mform->addRule($this->inputname, get_string('required'), 'required', null, 'client');
        }
    }

    /**
     * HardFreeze the field if locked.
     * @param   object   instance of the moodleform class
     */
    function edit_field_set_locked(&$mform) {
        if (!$mform->elementExists($this->inputname)) {
            return;
        }
        if ($this->is_locked() and !has_capability('moodle/user:update', get_context_instance(CONTEXT_SYSTEM))) {
            $mform->hardFreeze($this->inputname);
            $mform->setConstant($this->inputname, $this->datakey);
        }
    }
}



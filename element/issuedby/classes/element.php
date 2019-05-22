<?php
// This file is part of the customcert module for Moodle - http://moodle.org/
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
 * This file contains the customcert element issuedby's core interaction API.
 *
 * @package    customcertelement_issuedby
 * @copyright  2013 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace customcertelement_issuedby;

defined('MOODLE_INTERNAL') || die();

/**
 * The customcert element issuedby's core interaction API.
 *
 * @package    customcertelement_issuedby
 * @copyright  2013 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class element extends \mod_customcert\element {

    /**
     * This function renders the form elements when adding a customcert element.
     *
     * @param \MoodleQuickForm $mform the edit_form instance
     */
    public function render_form_elements($mform) {
        $mform->addElement('text', 'text', get_string('text', 'customcertelement_issuedby'));
        $mform->setType('text', PARAM_RAW);
        $mform->addHelpButton('text', 'text', 'customcertelement_issuedby');

        parent::render_form_elements($mform);
    }

    /**
     * This will handle how form data will be saved into the data column in the
     * customcert_elements table.
     *
     * @param \stdClass $data the form data
     * @return string the text
     */
    public function save_unique_data($data) {
        if (!empty($data->text)) {
            return $data->text;
        }
    }

    public function get_issuer() {
        if ($teachers = $this->get_list_of_teachers()) {
            $teacher = reset($teachers);
        }

        if (!empty($teacher)) {
            $issuedby = $teacher;
        } else {
            $issuedby = $this->get_text();
        }
        return $issuedby;
    }

    /**
     * Handles rendering the element on the pdf.
     *
     * @param \pdf $pdf the pdf object
     * @param bool $preview true if it is a preview, false otherwise
     * @param \stdClass $user the user we are rendering this for
     */
    public function render($pdf, $preview, $user) {
        $issuedby = $this->get_issuer();
        \mod_customcert\element_helper::render_content($pdf, $this, $issuedby);
    }

    /**
     * Render the element in html.
     *
     * This function is used to render the element when we are using the
     * drag and drop interface to position it.
     *
     * @return string the html
     */
    public function render_html() {
        $issuedby = $this->get_issuer();
        return \mod_customcert\element_helper::render_html_content($this, $issuedby);
    }

    /**
     * Helper function to return the teachers for this course.
     *
     * @return array the list of teachers
     */
    protected function get_list_of_teachers() {
        global $PAGE;

        // Return early if we are in a site template.
        if ($PAGE->context->id == \context_system::instance()->id) {
            return [];
        }

        // The list of teachers to return.
        $teachers = array();

        // Now return all users who can manage the customcert in this context.
        if ($users = get_enrolled_users($PAGE->context, 'mod/customcert:manage')) {
            foreach ($users as $user) {
                $teachers[$user->id] = fullname($user);
            }
        }

        return $teachers;
    }

    /**
     * Sets the data on the form when editing an element.
     *
     * @param \MoodleQuickForm $mform the edit_form instance
     */
    public function definition_after_data($mform) {
        if (!empty($this->get_data())) {
            $element = $mform->getElement('text');
            $element->setValue($this->get_data());
        }
        parent::definition_after_data($mform);
    }

    /**
     * Helper function that returns the text.
     *
     * @return string
     */
    protected function get_text() : string {
        $context = \mod_customcert\element_helper::get_context($this->get_id());
        return format_text($this->get_data(), FORMAT_HTML, ['context' => $context]);
    }
}

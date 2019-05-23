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
 * Customcert date element upgrade code.
 *
 * @package    mod_customcert
 * @copyright  2019 MLC
 * @author     David Saylor <david@mylearningconsultants.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Customcert date element upgrade code.
 *
 * @param int $oldversion the version we are upgrading from
 * @return bool always true
 */
function xmldb_customcertelement_date_upgrade($oldversion) {
    global $DB;

    if ($oldversion < 2019052001) {
        $dateelements = $DB->get_records('customcert_elements', array('element' => 'date'));

        foreach ($dateelements as $element) {
            $data = json_decode($element->data);
            if (isset($data->dateitem) && ($data->dateitem == -5)) {
                $data->dateitem = "-100";
                $element->data = json_encode($data);
                $DB->update_record('customcert_elements', $element);
            }
        }

        // Savepoint reached.
        upgrade_plugin_savepoint(true, 2019052001, 'customcertelement', 'date');
    }

    return true;
}

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
 * downloadlicensepdf block caps.
 *
 * @package    block_downloadlicensepdf
 * @copyright  Daniel Neis <danielneis@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_downloadlicensepdf extends block_base {

    function init() {
        $this->title = get_string('pluginname', 'block_downloadlicensepdf');
    }

    function get_content() {
        global $CFG, $OUTPUT, $COURSE;
        if ($this->content !== null) {
            return $this->content;
        }
        $context = get_context_instance(CONTEXT_COURSE, $COURSE->id);
        if (!has_capability('moodle/course:manageactivities', $context)) {
            return;
        }
        $this->content = new stdClass;
        $fs = get_file_storage();
        $mod = get_fast_modinfo($COURSE);
        $sections = $mod->get_sections();
        $this->content->text = html_writer::start_tag('form',array('action' => '/path/file.php', 'method' => 'post'));
        foreach ($sections as $sectionn => $cmids) {
        	   foreach ($cmids as $cmid) {
        	       $cminfo = $mod->get_cm($cmid);
        	       $modn = $cminfo->modname;
        	       $section = $mod->get_section_info($sectionn);
        	       $secdir = sprintf("%02f", $sectionn) . ". " . clean_filename($section->name);
        	       if ($cminfo->uservisible) {
        	           $cm = $cminfo->get_course_module_record(true);
        	           $files = $fs->get_area_files($cminfo->context->id,
        	                                        'mod_'. $modn,
        	                                        'content',
        	                                        false,
        	                                        'itemid, filepath, filename',
        	                                        false);
        	           $dir = $secdir;
        	           if ($modn != 'resource') {
        	               $dir .= '/' . clean_filename($cminfo->get_formatted_name());
        	           }
        	           foreach ($files as $pathha => $file) {
        	               $filename = $file->get_filename();
        	               if ($file->get_mimetype() == 'application/pdf' || substr(strrchr($filename, '.'),1) == 'pdf') {
        	               	 $this->content->text .= html_writer::start_tag('input', array('type' => 'checkbox', 'name' => 'file_ids' , 'value' => $file->get_id()));
        	                   $this->content->text .= $file->get_filename() . html_writer::end_tag('input');
        	               }
        	           }
        	       }
        	   }
        }
        //$this->content->text .= $file->get_filename() . html_writer::empty_tag('br');
        $this->content->text .= html_writer::end_tag('form');
        return $this->content;
    }
    
    // my moodle can only have SITEID and it's redundant here, so take it away
    public function applicable_formats() {
        return array('course-view' => true);
    }
    public function instance_allow_multiple() {
          return true;
    }
    function has_config() {return true;}
    public function cron() {
            mtrace( "Hey, my cron script is running" );
             
                 // do something
                  
                      return true;
    }
}
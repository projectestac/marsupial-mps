<?php

/**
 * Determines if a user is a teacher in any course, or an admin
 *
 * @uses $USER
 * @param int $userid The id of the user that is being tested against. Set this to 0 if you would just like to test against the currently logged in user.
 * @param bool $includeadmin Include anyone wo is an admin as well
 * @return bool
 */
function isteacherinanycourse($userid=0, $includeadmin=true) {
    global $USER, $CFG;

    if (empty($CFG->rolesactive)) {     // Teachers are locked out during an upgrade to 1.7
        return false;
    }

    if (!$userid) {
        if (empty($USER->id)) {
            return false;
        }
        $userid = $USER->id;
    }

    if (!record_exists('role_assignments', 'userid', $userid)) {    // Has no roles anywhere
        return false;
    }

/// If this user is assigned as an editing teacher anywhere then return true
    if ($roles = get_roles_with_capability('moodle/legacy:editingteacher', CAP_ALLOW)) {
        foreach ($roles as $role) {
            if (record_exists('role_assignments', 'roleid', $role->id, 'userid', $userid)) {
                return true;
            }
        }
    }

/// If this user is assigned as a non-editing teacher anywhere then return true
    if ($roles = get_roles_with_capability('moodle/legacy:teacher', CAP_ALLOW)) {
        foreach ($roles as $role) {
            if (record_exists('role_assignments', 'roleid', $role->id, 'userid', $userid)) {
                return true;
            }
        }
    }

/// Include admins if required
    if ($includeadmin) {
        $context = get_context_instance(CONTEXT_SYSTEM);
        if (has_capability('moodle/legacy:admin', $context, $userid, false)) {
            return true;
        }
    }

    return false;
}

/**
 * Unenrols a student from a given course
 *
 * @param int $courseid The id of the course that is being viewed, if any
 * @param int $userid The id of the user that is being tested against.
 * @return bool
 */
function unenrol_student($userid, $courseid=0) {
    global $CFG;

    $status = true;

    if ($courseid) {
        /// First delete any crucial stuff that might still send mail
        if ($forums = get_records('forum', 'course', $courseid)) {
            foreach ($forums as $forum) {
                delete_records('forum_subscriptions', 'forum', $forum->id, 'userid', $userid);
            }
        }
        /// remove from all legacy student roles
        if ($courseid == SITEID) {
            $context = get_context_instance(CONTEXT_SYSTEM);
        } else if (!$context = get_context_instance(CONTEXT_COURSE, $courseid)) {
            return false;
        }
        if (!$roles = get_roles_with_capability('moodle/legacy:student', CAP_ALLOW)) {
            return false;
        }
        foreach($roles as $role) {
            $status = role_unassign($role->id, $userid, 0, $context->id) and $status;
        }
    } else {
        // recursivelly unenroll student from all courses
        if ($courses = get_records('course')) {
            foreach($courses as $course) {
                $status = unenrol_student($userid, $course->id) and $status;
            }
        }
    }

    return $status;
}

/**
 * Add a teacher to a given course
 *
 * @param int $userid The id of the user that is being tested against. Set this to 0 if you would just like to test against the currently logged in user.
 * @param int $courseid The id of the course that is being viewed, if any
 * @param int $editall Can edit the course
 * @param string $role Obsolete
 * @param int $timestart The time they start
 * @param int $timeend The time they end in this role
 * @param string $enrol The type of enrolment this is
 * @return bool
 */
function add_teacher($userid, $courseid, $editall=1, $role='', $timestart=0, $timeend=0, $enrol='manual') {
    global $CFG;

    if (!$user = get_record('user', 'id', $userid)) {        // Check user
        return false;
    }

    $capability = $editall ? 'moodle/legacy:editingteacher' : 'moodle/legacy:teacher';

    if (!$roles = get_roles_with_capability($capability, CAP_ALLOW)) {
        return false;
    }

    $role = array_shift($roles);      // We can only use one, let's use the first one

    if (!$context = get_context_instance(CONTEXT_COURSE, $courseid)) {
        return false;
    }

    $res = role_assign($role->id, $user->id, 0, $context->id, $timestart, $timeend, 0, $enrol);

    return $res;
}

/**
 * Print a message in a standard themed box.
 * This old function used to implement boxes using tables.  Now it uses a DIV, but the old
 * parameters remain.  If possible, $align, $width and $color should not be defined at all.
 * Preferably just use print_box() in weblib.php
 *
 * @param string $align, alignment of the box, not the text (default center, left, right).
 * @param string $width, width of the box, including units %, for example '100%'.
 * @param string $color, background colour of the box, for example '#eee'.
 * @param int $padding, padding in pixels, specified without units.
 * @param string $class, space-separated class names.
 * @param string $id, space-separated id names.
 * @param boolean $return, return as string or just print it
 */
function print_simple_box($message, $align='', $width='', $color='', $padding=5, $class='generalbox', $id='', $return=false) {
    $output = '';
    $output .= print_simple_box_start($align, $width, $color, $padding, $class, $id, true);
    $output .= stripslashes_safe($message);
    $output .= print_simple_box_end(true);

    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}

/**
 * This old function used to implement boxes using tables.  Now it uses a DIV, but the old
 * parameters remain.  If possible, $align, $width and $color should not be defined at all.
 * Even better, please use print_box_start() in weblib.php
 *
 * @param string $align, alignment of the box, not the text (default center, left, right).   DEPRECATED
 * @param string $width, width of the box, including % units, for example '100%'.            DEPRECATED
 * @param string $color, background colour of the box, for example '#eee'.                   DEPRECATED
 * @param int $padding, padding in pixels, specified without units.                          OBSOLETE
 * @param string $class, space-separated class names.
 * @param string $id, space-separated id names.
 * @param boolean $return, return as string or just print it
 */
function print_simple_box_start($align='', $width='', $color='', $padding=5, $class='generalbox', $id='', $return=false) {

    $output = '';

    $divclasses = 'box '.$class.' '.$class.'content';
    $divstyles  = '';

    if ($align) {
        $divclasses .= ' boxalign'.$align;    // Implement alignment using a class
    }
    if ($width) {    // Hopefully we can eliminate these in calls to this function (inline styles are bad)
        if (substr($width, -1, 1) == '%') {    // Width is a % value
            $width = (int) substr($width, 0, -1);    // Extract just the number
            if ($width < 40) {
                $divclasses .= ' boxwidthnarrow';    // Approx 30% depending on theme
            } else if ($width > 60) {
                $divclasses .= ' boxwidthwide';      // Approx 80% depending on theme
            } else {
                $divclasses .= ' boxwidthnormal';    // Approx 50% depending on theme
            }
        } else {
            $divstyles  .= ' width:'.$width.';';     // Last resort
        }
    }
    if ($color) {    // Hopefully we can eliminate these in calls to this function (inline styles are bad)
        $divstyles  .= ' background:'.$color.';';
    }
    if ($divstyles) {
        $divstyles = ' style="'.$divstyles.'"';
    }

    if ($id) {
        $id = ' id="'.$id.'"';
    }

    $output .= '<div'.$id.$divstyles.' class="'.$divclasses.'">';

    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}

/**
 * Print the end portion of a standard themed box.
 * Preferably just use print_box_end() in weblib.php
 */
function print_simple_box_end($return=false) {
    $output = '</div>';
    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}

/** Deprecated function - returns the code of the current charset - originally depended on the selected language pack.
 *
 * @param $ignorecache not used anymore
 * @return string always returns 'UTF-8'
 */
function current_charset($ignorecache = false) {
    return 'UTF-8';
}

/**
 * deprecated - relies on register globals; use new formslib instead
 *
 * Set a variable's value depending on whether or not it already has a value.
 *
 * If variable is set, set it to the set_value otherwise set it to the
 * unset_value.  used to handle checkboxes when you are expecting them from
 * a form
 *
 * @param mixed $var Passed in by reference. The variable to check.
 * @param mixed $set_value The value to set $var to if $var already has a value.
 * @param mixed $unset_value The value to set $var to if $var does not already have a value.
 */
function checked(&$var, $set_value = 1, $unset_value = 0) {

    if (empty($var)) {
        $var = $unset_value;
    } else {
        $var = $set_value;
    }
}

/**
 * Returns the current group mode for a given course or activity module
 *
 * Could be false, SEPARATEGROUPS or VISIBLEGROUPS    (<-- Martin)
 */
function groupmode($course, $cm=null) {

    if (isset($cm->groupmode) && empty($course->groupmodeforce)) {
        return $cm->groupmode;
    }
    return $course->groupmode;
}

/**
 * Print an error page displaying an error message.
 * Old method, don't call directly in new code - use print_error instead.
 *
 *
 * @uses $SESSION
 * @uses $CFG
 * @param string $message The message to display to the user about the error.
 * @param string $link The url where the user will be prompted to continue. If no url is provided the user will be directed to the site index page.
 */
function error ($message, $link='') { 

    global $CFG, $SESSION, $THEME;
    $message = clean_text($message);   // In case nasties are in here

    if (defined('FULLME') && FULLME == 'cron') {
        // Errors in cron should be mtrace'd.
        mtrace($message);
        die;
    }

    if (! defined('HEADER_PRINTED')) {
        //header not yet printed
        @header('HTTP/1.0 404 Not Found');
        print_header(get_string('error'));
    } else {
        print_container_end_all(false, $THEME->open_header_containers);
    }

    echo '<br />';
    print_simple_box($message, '', '', '', '', 'errorbox');

    debugging('Stack trace:', DEBUG_DEVELOPER);

    // in case we are logging upgrade in admin/index.php stop it
    if (function_exists('upgrade_log_finish')) {
        upgrade_log_finish();
    }

    if (empty($link) and !defined('ADMIN_EXT_HEADER_PRINTED')) {
        if ( !empty($SESSION->fromurl) ) {
            $link = $SESSION->fromurl;
            unset($SESSION->fromurl);
        } else {
            $link = $CFG->wwwroot .'/';
        }
    }

    if (!empty($link)) {
        print_continue($link);
    }

    print_footer();

    for ($i=0;$i<512;$i++) {  // Padding to help IE work with 404
        echo ' ';
    }

    die;
}

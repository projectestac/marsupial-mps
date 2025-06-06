<?php

/**
 * Library of functions for database manipulation.
 *
 * Other main libraries:
 * - weblib.php - functions that produce web output
 * - moodlelib.php - general-purpose Moodle functions
 *
 * @author Martin Dougiamas and many others
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */

/// Some constants
define('LASTACCESS_UPDATE_SECS', 60); /// Number of seconds to wait before
/// updating lastaccess information in DB.

/**
 * Escape all dangerous characters in a data record
 *
 * $dataobject is an object containing needed data
 * Run over each field exectuting addslashes() function
 * to escape SQL unfriendly characters (e.g. quotes)
 * Handy when writing back data read from the database
 *
 * @param $dataobject Object containing the database record
 * @return object Same object with neccessary characters escaped
 */
function addslashes_object($dataobject) {
    $a = get_object_vars($dataobject);
    foreach ($a as $key => $value) {
        $a[$key] = addslashes($value);
    }
    return (object)$a;
}

/// USER DATABASE ////////////////////////////////////////////////

/**
 * Returns list of all admins, using 1 DB query. It depends on DB schema v1.7
 * but does not depend on the v1.9 datastructures (context.path, etc).
 *
 * @return object
 * @uses $CFG
 */
function get_admins() {

    global $CFG;

    $sql = "SELECT ra.userid, SUM(rc.permission) AS permission, MIN(ra.id) AS adminid
            FROM " . $CFG->prefix . "role_capabilities rc
            JOIN " . $CFG->prefix . "context ctx
              ON ctx.id=rc.contextid
            JOIN " . $CFG->prefix . "role_assignments ra
              ON ra.roleid=rc.roleid AND ra.contextid=ctx.id
            WHERE ctx.contextlevel=10
              AND rc.capability IN ('moodle/site:config',
                                    'moodle/legacy:admin',
                                    'moodle/site:doanything')       
            GROUP BY ra.userid
            HAVING SUM(rc.permission) > 0";

    $sql = "SELECT u.*, ra.adminid
            FROM  " . $CFG->prefix . "user u
            JOIN ($sql) ra
              ON u.id=ra.userid
            ORDER BY ra.adminid ASC";

    return get_records_sql($sql);
}

function get_courses_in_metacourse($metacourseid) {
    global $CFG;

    $sql = "SELECT c.id,c.shortname,c.fullname FROM {$CFG->prefix}course c, {$CFG->prefix}course_meta mc WHERE mc.parent_course = $metacourseid
        AND mc.child_course = c.id ORDER BY c.shortname";

    return get_records_sql($sql);
}

/**
 * Returns a subset of users
 *
 * @param bool $get If false then only a count of the records is returned
 * @param string $search A simple string to search for
 * @param bool $confirmed A switch to allow/disallow unconfirmed users
 * @param array(int) $exceptions A list of IDs to ignore, eg 2,4,5,8,9,10
 * @param string $sort A SQL snippet for the sorting criteria to use
 * @param string $firstinitial ?
 * @param string $lastinitial ?
 * @param string $page ?
 * @param string $recordsperpage ?
 * @param string $fields A comma separated list of fields to be returned from the chosen table.
 * @return object|false|int  {@link $USER} records unless get is false in which case the integer count of the records found is
 *     returned. False is returned if an error is encountered.
 * @uses $CFG
 */
function get_users($get = true, $search = '', $confirmed = false, $exceptions = '', $sort = 'firstname ASC',
                   $firstinitial = '', $lastinitial = '', $page = '', $recordsperpage = '', $fields = '*', $extraselect = '') {

    global $CFG;

    if ($get && !$recordsperpage) {
        debugging('Call to get_users with $get = true no $recordsperpage limit. ' .
            'On large installations, this will probably cause an out of memory error. ' .
            'Please think again and change your code so that it does not try to ' .
            'load so much data into memory.', DEBUG_DEVELOPER);
    }

    $LIKE = sql_ilike();
    $fullname = sql_fullname();

    $select = 'username <> \'guest\' AND deleted = 0';

    if (!empty($search)) {
        $search = trim($search);
        $select .= " AND ($fullname $LIKE '%$search%' OR email $LIKE '%$search%') ";
    }

    if ($confirmed) {
        $select .= ' AND confirmed = \'1\' ';
    }

    if ($exceptions) {
        $select .= ' AND id NOT IN (' . $exceptions . ') ';
    }

    if ($firstinitial) {
        $select .= ' AND firstname ' . $LIKE . ' \'' . $firstinitial . '%\'';
    }
    if ($lastinitial) {
        $select .= ' AND lastname ' . $LIKE . ' \'' . $lastinitial . '%\'';
    }

    if ($extraselect) {
        $select .= " AND $extraselect ";
    }

    if ($get) {
        return get_records_select('user', $select, $sort, $fields, $page, $recordsperpage);
    } else {
        return count_records_select('user', $select);
    }
}

/// OTHER SITE AND COURSE FUNCTIONS /////////////////////////////////////////////

/**
 * Returns $course object of the top-level site.
 *
 * @return course  A {@link $COURSE} object for the site
 */
function get_site() {

    global $SITE;

    if (!empty($SITE->id)) {   // We already have a global to use, so return that
        return $SITE;
    }

    if ($course = get_record('course', 'category', 0)) {
        return $course;
    } else {
        return false;
    }
}

/**
 * Returns list of courses, for whole site, or category
 *
 * Returns list of courses, for whole site, or category
 * Important: Using c.* for fields is extremely expensive because
 *            we are using distinct. You almost _NEVER_ need all the fields
 *            in such a large SELECT
 *
 * @param type description
 *
 */
function get_courses($categoryid = "all", $sort = "c.sortorder ASC", $fields = "c.*") {

    global $USER, $CFG;

    if ($categoryid != "all" && is_numeric($categoryid)) {
        $categoryselect = "WHERE c.category = '$categoryid'";
    } else {
        $categoryselect = "";
    }

    if (empty($sort)) {
        $sortstatement = "";
    } else {
        $sortstatement = "ORDER BY $sort";
    }

    $visiblecourses = array();

    // pull out all course matching the cat
    if ($courses = get_records_sql("SELECT $fields,
                                    ctx.id AS ctxid, ctx.path AS ctxpath,
                                    ctx.depth AS ctxdepth, ctx.contextlevel AS ctxlevel
                                    FROM {$CFG->prefix}course c
                                    JOIN {$CFG->prefix}context ctx
                                      ON (c.id = ctx.instanceid 
                                          AND ctx.contextlevel=" . CONTEXT_COURSE . ")
                                    $categoryselect
                                    $sortstatement")) {

        // loop throught them
        foreach ($courses as $course) {
            $course = make_context_subobj($course);
            if (isset($course->visible) && $course->visible <= 0) {
                // for hidden courses, require visibility check
                if (has_capability('moodle/course:viewhiddencourses', $course->context)) {
                    $visiblecourses [] = $course;
                }
            } else {
                $visiblecourses [] = $course;
            }
        }
    }
    return $visiblecourses;

    /*
        $teachertable = "";
        $visiblecourses = "";
        $sqland = "";
        if (!empty($categoryselect)) {
            $sqland = "AND ";
        }
        if (!empty($USER->id)) {  // May need to check they are a teacher
            if (!has_capability('moodle/course:create', get_context_instance(CONTEXT_SYSTEM))) {
                $visiblecourses = "$sqland ((c.visible > 0) OR t.userid = '$USER->id')";
                $teachertable = "LEFT JOIN {$CFG->prefix}user_teachers t ON t.course = c.id";
            }
        } else {
            $visiblecourses = "$sqland c.visible > 0";
        }
    
        if ($categoryselect or $visiblecourses) {
            $selectsql = "{$CFG->prefix}course c $teachertable WHERE $categoryselect $visiblecourses";
        } else {
            $selectsql = "{$CFG->prefix}course c $teachertable";
        }
    
        $extrafield = str_replace('ASC','',$sort);
        $extrafield = str_replace('DESC','',$extrafield);
        $extrafield = trim($extrafield);
        if (!empty($extrafield)) {
            $extrafield = ','.$extrafield;
        }
        return get_records_sql("SELECT ".((!empty($teachertable)) ? " DISTINCT " : "")." $fields $extrafield FROM $selectsql ".((!empty($sort)) ? "ORDER BY $sort" : ""));
        */
}

/**
 * Returns a sorted list of categories. Each category object has a context
 * property that is a context object.
 *
 * When asking for $parent='none' it will return all the categories, regardless
 * of depth. Wheen asking for a specific parent, the default is to return
 * a "shallow" resultset. Pass false to $shallow and it will return all
 * the child categories as well.
 *
 *
 * @param string $parent The parent category if any
 * @param string $sort the sortorder
 * @param bool $shallow - set to false to get the children too
 * @return array of categories
 */
function get_categories($parent = 'none', $sort = NULL, $shallow = true) {
    global $CFG;

    if ($sort === NULL) {
        $sort = 'ORDER BY cc.sortorder ASC';
    } elseif ($sort === '') {
        // leave it as empty
    } else {
        $sort = "ORDER BY $sort";
    }

    if ($parent === 'none') {
        $sql = "SELECT cc.*,
                      ctx.id AS ctxid, ctx.path AS ctxpath,
                      ctx.depth AS ctxdepth, ctx.contextlevel AS ctxlevel
                FROM {$CFG->prefix}course_categories cc
                JOIN {$CFG->prefix}context ctx
                  ON cc.id=ctx.instanceid AND ctx.contextlevel=" . CONTEXT_COURSECAT . "
                $sort";
    } elseif ($shallow) {
        $parent = (int)$parent;
        $sql = "SELECT cc.*,
                       ctx.id AS ctxid, ctx.path AS ctxpath,
                       ctx.depth AS ctxdepth, ctx.contextlevel AS ctxlevel
                FROM {$CFG->prefix}course_categories cc
                JOIN {$CFG->prefix}context ctx
                  ON cc.id=ctx.instanceid AND ctx.contextlevel=" . CONTEXT_COURSECAT . "
                WHERE cc.parent=$parent
                $sort";
    } else {
        $parent = (int)$parent;
        $sql = "SELECT cc.*,
                       ctx.id AS ctxid, ctx.path AS ctxpath,
                       ctx.depth AS ctxdepth, ctx.contextlevel AS ctxlevel
                FROM {$CFG->prefix}course_categories cc
                JOIN {$CFG->prefix}context ctx
                  ON cc.id=ctx.instanceid AND ctx.contextlevel=" . CONTEXT_COURSECAT . "
                JOIN {$CFG->prefix}course_categories ccp
                     ON (cc.path LIKE " . sql_concat('ccp.path', "'%'") . ")
                WHERE ccp.id=$parent
                $sort";
    }
    $categories = array();

    if ($rs = get_recordset_sql($sql)) {
        while ($cat = rs_fetch_next_record($rs)) {
            $cat = make_context_subobj($cat);
            if ($cat->visible || has_capability('moodle/category:viewhiddencategories', $cat->context)) {
                $categories[$cat->id] = $cat;
            }
        }
    }
    return $categories;
}

/**
 * Returns an array of category ids of all the subcategories for a given
 * category.
 *
 * @param $catid - The id of the category whose subcategories we want to find.
 * @return array of category ids.
 */
function get_all_subcategories($catid) {

    $subcats = array();

    if ($categories = get_records('course_categories', 'parent', $catid)) {
        foreach ($categories as $cat) {
            array_push($subcats, $cat->id);
            $subcats = array_merge($subcats, get_all_subcategories($cat->id));
        }
    }
    return $subcats;
}

/**
 * This recursive function makes sure that the courseorder is consecutive
 *
 * @param type description
 *
 * $n is the starting point, offered only for compatilibity -- will be ignored!
 * $safe (bool) prevents it from assuming category-sortorder is unique, used to upgrade
 *       safely from 1.4 to 1.5
 */
function fix_course_sortorder($categoryid = 0, $n = 0, $safe = 0, $depth = 0, $path = '') {

    global $CFG;

    $count = 0;

    $catgap = 1000; // "standard" category gap
    $tolerance = 200;  // how "close" categories can get

    if ($categoryid > 0) {
        // update depth and path
        $cat = get_record('course_categories', 'id', $categoryid);
        if ($cat->parent == 0) {
            $depth = 0;
            $path = '';
        } else {
            if ($depth == 0) { // doesn't make sense; get from DB
                // this is only called if the $depth parameter looks dodgy
                $parent = get_record('course_categories', 'id', $cat->parent);
                $path = $parent->path;
                $depth = $parent->depth;
            }
        }
        $path = $path . '/' . $categoryid;
        $depth = $depth + 1;

        if ($cat->path !== $path) {
            set_field('course_categories', 'path', addslashes($path), 'id', $categoryid);
        }
        if ($cat->depth != $depth) {
            set_field('course_categories', 'depth', $depth, 'id', $categoryid);
        }
    }

    // get some basic info about courses in the category
    $info = get_record_sql('SELECT MIN(sortorder) AS min,
                                   MAX(sortorder) AS max,
                                   COUNT(sortorder)  AS count
                            FROM ' . $CFG->prefix . 'course
                            WHERE category=' . $categoryid);
    if (is_object($info)) { // no courses?
        $max = $info->max;
        $count = $info->count;
        $min = $info->min;
        unset($info);
    }

    if ($categoryid > 0 && $n == 0) { // only passed category so don't shift it
        $n = $min;
    }

    // $hasgap flag indicates whether there's a gap in the sequence
    $hasgap = false;
    if ($max - $min + 1 != $count) {
        $hasgap = true;
    }

    // $mustshift indicates whether the sequence must be shifted to
    // meet its range
    $mustshift = false;
    if ($min < $n - $tolerance || $min > $n + $tolerance + $catgap) {
        $mustshift = true;
    }

    // actually sort only if there are courses,
    // and we meet one ofthe triggers:
    //  - safe flag
    //  - they are not in a continuos block
    //  - they are too close to the 'bottom'
    if ($count && ($safe || $hasgap || $mustshift)) {
        // special, optimized case where all we need is to shift
        if ($mustshift && !$safe && !$hasgap) {
            $shift = $n + $catgap - $min;
            if ($shift < $count) {
                $shift = $count + $catgap;
            }
            // UPDATE course SET sortorder=sortorder+$shift
            execute_sql("UPDATE {$CFG->prefix}course
                         SET sortorder=sortorder+$shift
                         WHERE category=$categoryid", 0);
            $n = $n + $catgap + $count;

        } else { // do it slowly
            $n = $n + $catgap;
            // if the new sequence overlaps the current sequence, lack of transactions
            // will stop us -- shift things aside for a moment...
            if ($safe || ($n >= $min && $n + $count + 1 < $min && $CFG->dbfamily === 'mysql')) {
                $shift = $max + $n + 1000;
                execute_sql("UPDATE {$CFG->prefix}course
                         SET sortorder=sortorder+$shift
                         WHERE category=$categoryid", 0);
            }

            $courses = get_courses($categoryid, 'c.sortorder ASC', 'c.id,c.sortorder');
            begin_sql();
            $tx = true; // transaction sanity
            foreach ($courses as $course) {
                if ($tx && $course->sortorder != $n) { // save db traffic
                    $tx = $tx && set_field('course', 'sortorder', $n,
                            'id', $course->id);
                }
                $n++;
            }
            if ($tx) {
                commit_sql();
            } else {
                rollback_sql();
                if (!$safe) {
                    // if we failed when called with !safe, try
                    // to recover calling self with safe=true
                    return fix_course_sortorder($categoryid, $n, true, $depth, $path);
                }
            }
        }
    }
    set_field('course_categories', 'coursecount', $count, 'id', $categoryid);

    // $n could need updating
    $max = get_field_sql("SELECT MAX(sortorder) from {$CFG->prefix}course WHERE category=$categoryid");
    if ($max > $n) {
        $n = $max;
    }

    if ($categories = get_categories($categoryid)) {
        foreach ($categories as $category) {
            $n = fix_course_sortorder($category->id, $n, $safe, $depth, $path);
        }
    }

    return $n + 1;
}

/**
 * This function creates a default separated/connected scale
 *
 * This function creates a default separated/connected scale
 * so there's something in the database.  The locations of
 * strings and files is a bit odd, but this is because we
 * need to maintain backward compatibility with many different
 * existing language translations and older sites.
 *
 * @uses $CFG
 */
function make_default_scale() {

    global $CFG;

    $defaultscale = NULL;
    $defaultscale->courseid = 0;
    $defaultscale->userid = 0;
    $defaultscale->name = get_string('separateandconnected');
    $defaultscale->scale = get_string('postrating1', 'forum') . ',' .
        get_string('postrating2', 'forum') . ',' .
        get_string('postrating3', 'forum');
    $defaultscale->timemodified = time();

    /// Read in the big description from the file.  Note this is not
    /// HTML (despite the file extension) but Moodle format text.
    $parentlang = get_string('parentlanguage');
    if ($parentlang[0] == '[') {
        $parentlang = '';
    }
    if (is_readable($CFG->dataroot . '/lang/' . $CFG->lang . '/help/forum/ratings.html')) {
        $file = file($CFG->dataroot . '/lang/' . $CFG->lang . '/help/forum/ratings.html');
    } else {
        if (is_readable($CFG->dirroot . '/lang/' . $CFG->lang . '/help/forum/ratings.html')) {
            $file = file($CFG->dirroot . '/lang/' . $CFG->lang . '/help/forum/ratings.html');
        } else {
            if ($parentlang and is_readable($CFG->dataroot . '/lang/' . $parentlang . '/help/forum/ratings.html')) {
                $file = file($CFG->dataroot . '/lang/' . $parentlang . '/help/forum/ratings.html');
            } else {
                if ($parentlang and is_readable($CFG->dirroot . '/lang/' . $parentlang . '/help/forum/ratings.html')) {
                    $file = file($CFG->dirroot . '/lang/' . $parentlang . '/help/forum/ratings.html');
                } else {
                    if (is_readable($CFG->dirroot . '/lang/en_utf8/help/forum/ratings.html')) {
                        $file = file($CFG->dirroot . '/lang/en_utf8/help/forum/ratings.html');
                    } else {
                        $file = '';
                    }
                }
            }
        }
    }

    $defaultscale->description = addslashes(implode('', $file));

    if ($defaultscale->id = insert_record('scale', $defaultscale)) {
        execute_sql('UPDATE ' . $CFG->prefix . 'forum SET scale = \'' . $defaultscale->id . '\'', false);
    }
}

/**
 * Returns a menu of all available scales from the site as well as the given course
 *
 * @param int $courseid The id of the course as found in the 'course' table.
 * @return object
 * @uses $CFG
 */
function get_scales_menu($courseid = 0) {

    global $CFG;

    $sql = "SELECT id, name FROM {$CFG->prefix}scale
             WHERE courseid = '0' or courseid = '$courseid'
          ORDER BY courseid ASC, name ASC";

    if ($scales = get_records_sql_menu($sql)) {
        return $scales;
    }

    make_default_scale();

    return get_records_sql_menu($sql);
}

/**
 * Given a set of timezone records, put them in the database,  replacing what is there
 *
 * @param array $timezones An array of timezone records
 * @uses $CFG
 */
function update_timezone_records($timezones) {
/// Given a set of timezone records, put them in the database

    global $CFG;

/// Clear out all the old stuff
    execute_sql('TRUNCATE TABLE ' . $CFG->prefix . 'timezone', false);

/// Insert all the new stuff
    foreach ($timezones as $timezone) {
        if (is_array($timezone)) {
            $timezone = (object)$timezone;
        }
        insert_record('timezone', $timezone);
    }
}

/// LOG FUNCTIONS /////////////////////////////////////////////////////

/**
 * Add an entry to the log table.
 *
 * @param int $categoryid The category id
 * @param string $actionid The module name - e.g. forum, journal, resource, course, user etc
 * @param string $info Additional description information
 * @param bool $error False = normal log, True = error log, save in the other table
 * @uses $CFG
 * @uses $REMOTE_ADDR
 */
function add_to_log($categoryid, $action, $info = '', $error = false) {

    $data = new stdClass();

    // sanitize all incoming data
    $data->time = time();
    $data->categoryid = clean_param($categoryid, PARAM_INT);
    $data->actionid = clean_param($action, PARAM_RAW);
    $data->info = addslashes(clean_param($info, PARAM_RAW));
    $data->ip = getremoteaddr();
    if (empty($data->ip)) {
        $data->ip = '0.0.0.0';
    }
    $data->info = empty($data->info) ? sql_empty() : $data->info; // Use proper empties for each database

    //select table in where register 
    $table = (!$error) ? 'log' : 'log_errors';

    //insert into db
    if (!insert_record($table, $data)) {
        error("Error registering log");
    }
}

/**
 * Returns an object with counts of failed login attempts
 *
 * Returns information about failed login attempts.  If the current user is
 * an admin, then two numbers are returned:  the number of attempts and the
 * number of accounts.  For non-admins, only the attempts on the given user
 * are shown.
 *
 * @param string $mode Either 'admin', 'teacher' or 'everybody'
 * @param string $username The username we are searching for
 * @param string $lastlogin The date from which we are searching
 * @return int
 */
function count_login_failures($mode, $username, $lastlogin) {

    $select = 'module=\'login\' AND action=\'error\' AND time > ' . $lastlogin;

    if (has_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM))) {    // Return information about all accounts
        if ($count->attempts = count_records_select('log', $select)) {
            $count->accounts = count_records_select('log', $select, 'COUNT(DISTINCT info)');
            return $count;
        }
    } else {
        if ($mode == 'everybody' or ($mode == 'teacher' and isteacherinanycourse())) {
            if ($count->attempts = count_records_select('log', $select . ' AND info = \'' . $username . '\'')) {
                return $count;
            }
        }
    }
    return NULL;
}

/// GENERAL HELPFUL THINGS  ///////////////////////////////////

/**
 * Dump a given object's information in a PRE block.
 *
 * Mostly just used for debugging.
 *
 * @param mixed $object The data to be printed
 */
function print_object($object) {
    echo '<pre class="notifytiny">' . htmlspecialchars(print_r($object, true)) . '</pre>';
}

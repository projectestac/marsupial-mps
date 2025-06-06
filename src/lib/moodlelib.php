<?php

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas  http://dougiamas.com     //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/**
 * moodlelib.php - Moodle main library
 *
 * Main library file of miscellaneous general-purpose Moodle functions.
 * Other main libraries:
 *  - weblib.php      - functions that produce web output
 *  - datalib.php     - functions that access the database
 * @author Martin Dougiamas
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */

/// CONSTANTS (Encased in phpdoc proper comments)/////////////////////////

/**
 * Used by some scripts to check they are being called by Moodle
 */
define('MOODLE_INTERNAL', true);

/// Date and time constants ///
/**
 * Time constant - the number of seconds in a year
 */

define('YEARSECS', 31536000);

/**
 * Time constant - the number of seconds in a week
 */
define('WEEKSECS', 604800);

/**
 * Time constant - the number of seconds in a day
 */
define('DAYSECS', 86400);

/**
 * Time constant - the number of seconds in an hour
 */
define('HOURSECS', 3600);

/**
 * Time constant - the number of seconds in a minute
 */
define('MINSECS', 60);

/**
 * Time constant - the number of minutes in a day
 */
define('DAYMINS', 1440);

/**
 * Time constant - the number of minutes in an hour
 */
define('HOURMINS', 60);

/// Parameter constants - every call to optional_param(), required_param()  ///
/// or clean_param() should have a specified type of parameter.  //////////////

/**
 * PARAM_RAW specifies a parameter that is not cleaned/processed in any way;
 * originally was 0, but changed because we need to detect unknown
 * parameter types and swiched order in clean_param().
 */
define('PARAM_RAW', 666);

/**
 * PARAM_CLEAN - obsoleted, please try to use more specific type of parameter.
 * It was one of the first types, that is why it is abused so much ;-)
 */
define('PARAM_CLEAN',    0x0001);

/**
 * PARAM_INT - integers only, use when expecting only numbers.
 */
define('PARAM_INT',      0x0002);

/**
 * PARAM_INTEGER - an alias for PARAM_INT
 */
define('PARAM_INTEGER',  0x0002);

/**
 * PARAM_NUMBER - a real/floating point number.
 */
define('PARAM_NUMBER',  0x000a);

/**
 * PARAM_ALPHA - contains only english letters.
 */
define('PARAM_ALPHA',    0x0004);

/**
 * PARAM_ACTION - an alias for PARAM_ALPHA, use for various actions in formas and urls
 * @TODO: should we alias it to PARAM_ALPHANUM ?
 */
define('PARAM_ACTION',   0x0004);

/**
 * PARAM_FORMAT - an alias for PARAM_ALPHA, use for names of plugins, formats, etc.
 * @TODO: should we alias it to PARAM_ALPHANUM ?
 */
define('PARAM_FORMAT',   0x0004);

/**
 * PARAM_NOTAGS - all html tags are stripped from the text. Do not abuse this type.
 */
define('PARAM_NOTAGS',   0x0008);

 /**
 * PARAM_MULTILANG - alias of PARAM_TEXT.
 */
define('PARAM_MULTILANG',  0x0009);

 /**
 * PARAM_TEXT - general plain text compatible with multilang filter, no other html tags.
 */
define('PARAM_TEXT',  0x0009);

/**
 * PARAM_FILE - safe file name, all dangerous chars are stripped, protects against XSS, SQL injections and directory traversals
 */
define('PARAM_FILE',     0x0010);

/**
 * PARAM_TAG - one tag (interests, blogs, etc.) - mostly international alphanumeric with spaces
 */
define('PARAM_TAG',   0x0011);

/**
 * PARAM_TAGLIST - list of tags separated by commas (interests, blogs, etc.)
 */
define('PARAM_TAGLIST',   0x0012);

/**
 * PARAM_PATH - safe relative path name, all dangerous chars are stripped, protects against XSS, SQL injections and directory traversals
 * note: the leading slash is not removed, window drive letter is not allowed
 */
define('PARAM_PATH',     0x0020);

/**
 * PARAM_HOST - expected fully qualified domain name (FQDN) or an IPv4 dotted quad (IP address)
 */
define('PARAM_HOST',     0x0040);

/**
 * PARAM_URL - expected properly formatted URL. Please note that domain part is required, http://localhost/ is not acceppted but http://localhost.localdomain/ is ok.
 */
define('PARAM_URL',      0x0080);

/**
 * PARAM_LOCALURL - expected properly formatted URL as well as one that refers to the local server itself. (NOT orthogonal to the others! Implies PARAM_URL!)
 */
define('PARAM_LOCALURL', 0x0180);

/**
 * PARAM_CLEANFILE - safe file name, all dangerous and regional chars are removed,
 * use when you want to store a new file submitted by students
 */
define('PARAM_CLEANFILE',0x0200);

/**
 * PARAM_ALPHANUM - expected numbers and letters only.
 */
define('PARAM_ALPHANUM', 0x0400);

/**
 * PARAM_BOOL - converts input into 0 or 1, use for switches in forms and urls.
 */
define('PARAM_BOOL',     0x0800);

/**
 * PARAM_CLEANHTML - cleans submitted HTML code and removes slashes
 * note: do not forget to addslashes() before storing into database!
 */
define('PARAM_CLEANHTML',0x1000);

/**
 * PARAM_ALPHAEXT the same contents as PARAM_ALPHA plus the chars in quotes: "/-_" allowed,
 * suitable for include() and require()
 * @TODO: should we rename this function to PARAM_SAFEDIRS??
 */
define('PARAM_ALPHAEXT', 0x2000);

/**
 * PARAM_SAFEDIR - safe directory name, suitable for include() and require()
 */
define('PARAM_SAFEDIR',  0x4000);

/**
 * PARAM_SEQUENCE - expects a sequence of numbers like 8 to 1,5,6,4,6,8,9.  Numbers and comma only.
 */
define('PARAM_SEQUENCE',  0x8000);

/**
 * PARAM_PEM - Privacy Enhanced Mail format
 */
define('PARAM_PEM',      0x10000);

/**
 * PARAM_BASE64 - Base 64 encoded format
 */
define('PARAM_BASE64',   0x20000);

/// Page types ///
/**
 * PAGE_COURSE_VIEW is a definition of a page type. For more information on the page class see moodle/lib/pagelib.php.
 */
define('PAGE_COURSE_VIEW', 'course-view');

/// Debug levels ///
/** no warnings at all */
define ('DEBUG_NONE', 0);
/** E_ERROR | E_PARSE */
define ('DEBUG_MINIMAL', 5);
/** E_ERROR | E_PARSE | E_WARNING | E_NOTICE */
define ('DEBUG_NORMAL', 15);
/** E_ALL without E_STRICT for now, do show recoverable fatal errors */
define ('DEBUG_ALL', 6143);
/** DEBUG_ALL with extra Moodle debug messages - (DEBUG_ALL | 32768) */
define ('DEBUG_DEVELOPER', 38911);

/**
 * Blog access level constant declaration
 */
define ('BLOG_USER_LEVEL', 1);
define ('BLOG_GROUP_LEVEL', 2);
define ('BLOG_COURSE_LEVEL', 3);
define ('BLOG_SITE_LEVEL', 4);
define ('BLOG_GLOBAL_LEVEL', 5);

/**
 * Tag constanst
 */
//To prevent problems with multibytes strings, this should not exceed the
//length of "varchar(255) / 3 (bytes / utf-8 character) = 85".
define('TAG_MAX_LENGTH', 50);

/**
 * Password policy constants
 */
define ('PASSWORD_LOWER', 'abcdefghijklmnopqrstuvwxyz');
define ('PASSWORD_UPPER', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ');
define ('PASSWORD_DIGITS', '0123456789');
define ('PASSWORD_NONALPHANUM', '.,;:!?_-+/*@#&$');

if (!defined('SORT_LOCALE_STRING')) { // PHP < 4.4.0 - TODO: remove in 2.0
    define('SORT_LOCALE_STRING', SORT_STRING);
}

/// PARAMETER HANDLING ////////////////////////////////////////////////////

/**
 * Returns a particular value for the named variable, taken from
 * POST or GET.  If the parameter doesn't exist then an error is
 * thrown because we require this variable.
 *
 * This function should be used to initialise all required values
 * in a script that are based on parameters.  Usually it will be
 * used like this:
 *    $id = required_param('id');
 *
 * @param string $parname the name of the page parameter we want
 * @param int $type expected type of parameter
 * @return mixed
 */
function required_param($parname, $type=PARAM_CLEAN) {

    // detect_unchecked_vars addition
    global $CFG;
    if (!empty($CFG->detect_unchecked_vars)) {
        global $UNCHECKED_VARS;
        unset ($UNCHECKED_VARS->vars[$parname]);
    }

    if (isset($_POST[$parname])) {       // POST has precedence
        $param = $_POST[$parname];
    } else if (isset($_GET[$parname])) {
        $param = $_GET[$parname];
    } else {
        error('A required parameter ('.$parname.') was missing');
    }

    return clean_param($param, $type);
}

/**
 * Returns a particular value for the named variable, taken from
 * POST or GET, otherwise returning a given default.
 *
 * This function should be used to initialise all optional values
 * in a script that are based on parameters.  Usually it will be
 * used like this:
 *    $name = optional_param('name', 'Fred');
 *
 * @param string $parname the name of the page parameter we want
 * @param mixed  $default the default value to return if nothing is found
 * @param int $type expected type of parameter
 * @return mixed
 */
function optional_param($parname, $default=NULL, $type=PARAM_CLEAN) {

    // detect_unchecked_vars addition
    global $CFG;
    if (!empty($CFG->detect_unchecked_vars)) {
        global $UNCHECKED_VARS;
        unset ($UNCHECKED_VARS->vars[$parname]);
    }

    if (isset($_POST[$parname])) {       // POST has precedence
        $param = $_POST[$parname];
    } else if (isset($_GET[$parname])) {
        $param = $_GET[$parname];
    } else {
        return $default;
    }

    return clean_param($param, $type);
}

/**
 * Used by {@link optional_param()} and {@link required_param()} to
 * clean the variables and/or cast to specific types, based on
 * an options field.
 * <code>
 * $course->format = clean_param($course->format, PARAM_ALPHA);
 * $selectedgrade_item = clean_param($selectedgrade_item, PARAM_CLEAN);
 * </code>
 *
 * @uses $CFG
 * @uses PARAM_RAW
 * @uses PARAM_CLEAN
 * @uses PARAM_CLEANHTML
 * @uses PARAM_INT
 * @uses PARAM_NUMBER
 * @uses PARAM_ALPHA
 * @uses PARAM_ALPHANUM
 * @uses PARAM_ALPHAEXT
 * @uses PARAM_SEQUENCE
 * @uses PARAM_BOOL
 * @uses PARAM_NOTAGS
 * @uses PARAM_TEXT
 * @uses PARAM_SAFEDIR
 * @uses PARAM_CLEANFILE
 * @uses PARAM_FILE
 * @uses PARAM_PATH
 * @uses PARAM_HOST
 * @uses PARAM_URL
 * @uses PARAM_LOCALURL
 * @uses PARAM_PEM
 * @uses PARAM_BASE64
 * @uses PARAM_TAG
 * @uses PARAM_SEQUENCE
 * @param mixed $param the variable we are cleaning
 * @param int $type expected format of param after cleaning.
 * @return mixed
 */
function clean_param($param, $type) {

    global $CFG;

    if (is_array($param)) {              // Let's loop
        $newparam = array();
        foreach ($param as $key => $value) {
            $newparam[$key] = clean_param($value, $type);
        }
        return $newparam;
    }

    switch ($type) {
        case PARAM_RAW:          // no cleaning at all
            return $param;

        case PARAM_CLEAN:        // General HTML cleaning, try to use more specific type if possible
            if (is_numeric($param)) {
                return $param;
            }
            $param = stripslashes($param);   // Needed for kses to work fine
            $param = clean_text($param);     // Sweep for scripts, etc
            return addslashes($param);       // Restore original request parameter slashes

        case PARAM_CLEANHTML:    // prepare html fragment for display, do not store it into db!!
            $param = stripslashes($param);   // Remove any slashes
            $param = clean_text($param);     // Sweep for scripts, etc
            return trim($param);

        case PARAM_INT:
            return (int)$param;  // Convert to integer

        case PARAM_NUMBER:
            return (float)$param;  // Convert to integer

        case PARAM_ALPHA:        // Remove everything not a-z
            return preg_replace('/[^a-zA-Z]/i', '', $param);

        case PARAM_ALPHANUM:     // Remove everything not a-zA-Z0-9
            return preg_replace('/[^A-Za-z0-9]/i', '', $param);

        case PARAM_ALPHAEXT:     // Remove everything not a-zA-Z/_-
            return preg_replace('/[^a-zA-Z\/_-]/i', '', $param);

        case PARAM_SEQUENCE:     // Remove everything not 0-9,
            return preg_replace('/[^0-9,]/i', '', $param);

        case PARAM_BOOL:         // Convert to 1 or 0
            $tempstr = strtolower($param);
            if ($tempstr == 'on' or $tempstr == 'yes' ) {
                $param = 1;
            } else if ($tempstr == 'off' or $tempstr == 'no') {
                $param = 0;
            } else {
                $param = empty($param) ? 0 : 1;
            }
            return $param;

        case PARAM_NOTAGS:       // Strip all tags
            return strip_tags($param);

        case PARAM_TEXT:    // leave only tags needed for multilang
            return clean_param(strip_tags($param, '<lang><span>'), PARAM_CLEAN);

        case PARAM_SAFEDIR:      // Remove everything not a-zA-Z0-9_-
            return preg_replace('/[^a-zA-Z0-9_-]/i', '', $param);

        case PARAM_CLEANFILE:    // allow only safe characters
            return clean_filename($param);

        case PARAM_FILE:         // Strip all suspicious characters from filename
            $param = preg_replace('~[[:cntrl:]]|[&<>"`\|\':\\\\/]~u', '', $param);
            $param = preg_replace('~\.\.+~', '', $param);
            if ($param === '.') {
                $param = '';
            }
            return $param;

        case PARAM_PATH:         // Strip all suspicious characters from file path
            $param = str_replace('\\', '/', $param);
            $param = preg_replace('~[[:cntrl:]]|[&<>"`\|\':]~u', '', $param);
            $param = preg_replace('~\.\.+~', '', $param);
            $param = preg_replace('~//+~', '/', $param);
            return preg_replace('~/(\./)+~', '/', $param);

        case PARAM_HOST:         // allow FQDN or IPv4 dotted quad
            $param = preg_replace('/[^\.\d\w-]/','', $param ); // only allowed chars
            // match ipv4 dotted quad
            if (preg_match('/(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})/',$param, $match)){
                // confirm values are ok
                if ( $match[0] > 255
                     || $match[1] > 255
                     || $match[3] > 255
                     || $match[4] > 255 ) {
                    // hmmm, what kind of dotted quad is this?
                    $param = '';
                }
            }
// IECISA -> MPS ************** DELETED -> just want IPv4 check			
			/* elseif ( preg_match('/^[\w\d\.-]+$/', $param) // dots, hyphens, numbers
                       && !preg_match('/^[\.-]/',  $param) // no leading dots/hyphens
                       && !preg_match('/[\.-]$/',  $param) // no trailing dots/hyphens
                       ) {
                // all is ok - $param is respected
            } */
// *********** END			
			else {
                // all is not ok...
                $param='';
            }
            return $param;

        case PARAM_URL:          // allow safe ftp, http, mailto urls
            include_once($CFG->dirroot . '/lib/validateurlsyntax.php');
            if (!empty($param) && validateUrlSyntax($param, 's?H?S?F?E?u-P-a?I?p?f?q?r?')) {
                // all is ok, param is respected
            } else {
                $param =''; // not really ok
            }
            return $param;

        case PARAM_LOCALURL:     // allow http absolute, root relative and relative URLs within wwwroot
            $param = clean_param($param, PARAM_URL);
            if (!empty($param)) {
                if (preg_match(':^/:', $param)) {
                    // root-relative, ok!
                } elseif (preg_match('/^'.preg_quote($CFG->wwwroot, '/').'/i',$param)) {
                    // absolute, and matches our wwwroot
                } else {
                    // relative - let's make sure there are no tricks
                    if (validateUrlSyntax($param, 's-u-P-a-p-f+q?r?')) {
                        // looks ok.
                    } else {
                        $param = '';
                    }
                }
            }
            return $param;

        case PARAM_PEM:
            $param = trim($param);
            // PEM formatted strings may contain letters/numbers and the symbols
            // forward slash: /
            // plus sign:     +
            // equal sign:    =
            // , surrounded by BEGIN and END CERTIFICATE prefix and suffixes
            if (preg_match('/^-----BEGIN CERTIFICATE-----([\s\w\/\+=]+)-----END CERTIFICATE-----$/', trim($param), $matches)) {
                list($wholething, $body) = $matches;
                unset($wholething, $matches);
                $b64 = clean_param($body, PARAM_BASE64);
                if (!empty($b64)) {
                    return "-----BEGIN CERTIFICATE-----\n$b64\n-----END CERTIFICATE-----\n";
                } else {
                    return '';
                }
            }
            return '';

        case PARAM_BASE64:
            if (!empty($param)) {
                // PEM formatted strings may contain letters/numbers and the symbols
                // forward slash: /
                // plus sign:     +
                // equal sign:    =
                if (0 >= preg_match('/^([\s\w\/\+=]+)$/', trim($param))) {
                    return '';
                }
                $lines = preg_split('/[\s]+/', $param, -1, PREG_SPLIT_NO_EMPTY);
                // Each line of base64 encoded data must be 64 characters in
                // length, except for the last line which may be less than (or
                // equal to) 64 characters long.
                for ($i=0, $j=count($lines); $i < $j; $i++) {
                    if ($i + 1 == $j) {
                        if (64 < strlen($lines[$i])) {
                            return '';
                        }
                        continue;
                    }

                    if (64 != strlen($lines[$i])) {
                        return '';
                    }
                }
                return implode("\n",$lines);
            } else {
                return '';
            }

        case PARAM_TAG:
            //as long as magic_quotes_gpc is used, a backslash will be a
            //problem, so remove *all* backslash.
            $param = str_replace('\\', '', $param);
            //convert many whitespace chars into one
            $param = preg_replace('/\s+/', ' ', $param);
            $textlib = textlib_get_instance();
            $param = $textlib->substr(trim($param), 0, TAG_MAX_LENGTH);
            return $param;


        case PARAM_TAGLIST:
            $tags = explode(',', $param);
            $result = array();
            foreach ($tags as $tag) {
                $res = clean_param($tag, PARAM_TAG);
                if ($res != '') {
                    $result[] = $res;
                }
            }
            if ($result) {
                return implode(',', $result);
            } else {
                return '';
            }

        default:                 // throw error, switched parameters in optional_param or another serious problem
            error("Unknown parameter type: $type");
    }
}

/**
 * Set a key in global configuration
 *
 * Set a key/value pair in both this session's {@link $CFG} global variable
 * and in the 'config' database table for future sessions.
 *
 * Can also be used to update keys for plugin-scoped configs in config_plugin table.
 * In that case it doesn't affect $CFG.
 *
 * A NULL value will delete the entry.
 *
 * @param string $name the key to set
 * @param string $value the value to set (without magic quotes)
 * @param string $plugin (optional) the plugin scope
 * @uses $CFG
 * @return bool
 */
function set_config($name, $value, $plugin=NULL) {
/// No need for get_config because they are usually always available in $CFG

    global $CFG;

    if (empty($plugin)) {
        if (!array_key_exists($name, $CFG->config_php_settings)) {
            // So it's defined for this invocation at least
            if (is_null($value)) {
                unset($CFG->$name);
            } else {
                $CFG->$name = (string)$value; // settings from db are always strings
            }
        }

        if (get_field('config', 'name', 'name', $name)) {
            if ($value===null) {
                return delete_records('config', 'name', $name);
            } else {
                return set_field('config', 'value', addslashes($value), 'name', $name);
            }
        } else {
            if ($value===null) {
                return true;
            }
            $config = new stdClass();
            $config->name = $name;
            $config->value = addslashes($value);
            return insert_record('config', $config);
        }
    } else { // plugin scope
        if ($id = get_field('config_plugins', 'id', 'name', $name, 'plugin', $plugin)) {
            if ($value===null) {
                return delete_records('config_plugins', 'name', $name, 'plugin', $plugin);
            } else {
                return set_field('config_plugins', 'value', addslashes($value), 'id', $id);
            }
        } else {
            if ($value===null) {
                return true;
            }
            $config = new stdClass();
            $config->plugin = addslashes($plugin);
            $config->name   = $name;
            $config->value  = addslashes($value);
            return insert_record('config_plugins', $config);
        }
    }
}

/**
 * Get configuration values from the global config table
 * or the config_plugins table.
 *
 * If called with no parameters it will do the right thing
 * generating $CFG safely from the database without overwriting
 * existing values.
 *
 * If called with 2 parameters it will return a $string single
 * value or false of the value is not found.
 *
 * @param string $plugin
 * @param string $name
 * @uses $CFG
 * @return hash-like object or single value
 *
 */
function get_config($plugin=NULL, $name=NULL) {

    global $CFG;

    if (!empty($name)) { // the user is asking for a specific value
        if (!empty($plugin)) {
            return get_field('config_plugins', 'value', 'plugin' , $plugin, 'name', $name);
        } else {
            return get_field('config', 'value', 'name', $name);
        }
    }

    // the user is after a recordset
    if (!empty($plugin)) {
        if ($configs=get_records('config_plugins', 'plugin', $plugin, '', 'name,value')) {
            $configs = (array)$configs;
            $localcfg = array();
            foreach ($configs as $config) {
                $localcfg[$config->name] = $config->value;
            }
            return (object)$localcfg;
        } else {
            return false;
        }
    } else {
        // this was originally in setup.php
        if ($configs = get_records('config')) {
            $localcfg = (array)$CFG;
            foreach ($configs as $config) {
                if (!isset($localcfg[$config->name])) {
                    $localcfg[$config->name] = $config->value;
                }
                // do not complain anymore if config.php overrides settings from db
            }

            $localcfg = (object)$localcfg;
            return $localcfg;
        } else {
            // preserve $CFG if DB returns nothing or error
            return $CFG;
        }

    }
}

/**
 * Removes a key from global configuration
 *
 * @param string $name the key to set
 * @param string $plugin (optional) the plugin scope
 * @uses $CFG
 * @return bool
 */
function unset_config($name, $plugin=NULL) {

    global $CFG;

    unset($CFG->$name);

    if (empty($plugin)) {
        return delete_records('config', 'name', $name);
    } else {
        return delete_records('config_plugins', 'name', $name, 'plugin', $plugin);
    }
}

/**
 * Removes a single volatile flag
 *
 * @param string $type the "type" namespace for the key
 * @param string $name the key to set
 * @uses $CFG
 * @return bool
 */
function unset_cache_flag($type, $name) {

    return delete_records('cache_flags',
                          'name', addslashes($name),
                          'flagtype', addslashes($type));
}

/**
 * Refresh current $USER session global variable with all their current preferences.
 * @uses $USER
 */
function reload_user_preferences() {

    global $USER;

    //reset preference
    $USER->preference = array();

    if (!isloggedin() or isguestuser()) {
        // no permanent storage for not-logged-in user and guest

    } else if ($preferences = get_records('user_preferences', 'userid', $USER->id)) {
        foreach ($preferences as $preference) {
            $USER->preference[$preference->name] = $preference->value;
        }
    }

    return true;
}

/**
 * Sets a preference for the current user
 * Optionally, can set a preference for a different user object
 * @uses $USER
 * @todo Add a better description and include usage examples. Add inline links to $USER and user functions in above line.

 * @param string $name The key to set as preference for the specified user
 * @param string $value The value to set forthe $name key in the specified user's record
 * @param int $otheruserid A moodle user ID
 * @return bool
 */
function set_user_preference($name, $value, $otheruserid=NULL) {

    global $USER;

    if (!isset($USER->preference)) {
        reload_user_preferences();
    }

    if (empty($name)) {
        return false;
    }

    $nostore = false;

    if (empty($otheruserid)){
        if (!isloggedin() or isguestuser()) {
            $nostore = true;
        }
        $userid = $USER->id;
    } else {
        if (isguestuser($otheruserid)) {
            $nostore = true;
        }
        $userid = $otheruserid;
    }

    $return = true;
    if ($nostore) {
        // no permanent storage for not-logged-in user and guest

    } else if ($preference = get_record('user_preferences', 'userid', $userid, 'name', addslashes($name))) {
        if ($preference->value === $value) {
            return true;
        }
        if (!set_field('user_preferences', 'value', addslashes((string)$value), 'id', $preference->id)) {
            $return = false;
        }

    } else {
        $preference = new stdClass();
        $preference->userid = $userid;
        $preference->name   = addslashes($name);
        $preference->value  = addslashes((string)$value);
        if (!insert_record('user_preferences', $preference)) {
            $return = false;
        }
    }

    // update value in USER session if needed
    if ($userid == $USER->id) {
        $USER->preference[$name] = (string)$value;
    }

    return $return;
}

/**
 * If no arguments are supplied this function will return
 * all of the current user preferences as an array.
 * If a name is specified then this function
 * attempts to return that particular preference value.  If
 * none is found, then the optional value $default is returned,
 * otherwise NULL.
 * @param string $name Name of the key to use in finding a preference value
 * @param string $default Value to be returned if the $name key is not set in the user preferences
 * @param int $otheruserid A moodle user ID
 * @uses $USER
 * @return string
 */
function get_user_preferences($name=NULL, $default=NULL, $otheruserid=NULL) {
    global $USER;

    if (!isset($USER->preference)) {
        reload_user_preferences();
    }

    if (empty($otheruserid)){
        $userid = $USER->id;
    } else {
        $userid = $otheruserid;
    }

    if ($userid == $USER->id) {
        $preference = $USER->preference;

    } else {
        $preference = array();
        if ($prefdata = get_records('user_preferences', 'userid', $userid)) {
            foreach ($prefdata as $pref) {
                $preference[$pref->name] = $pref->value;
            }
        }
    }

    if (empty($name)) {
        return $preference;            // All values

    } else if (array_key_exists($name, $preference)) {
        return $preference[$name];    // The single value

    } else {
        return $default;              // Default value (or NULL)
    }
}

/// FUNCTIONS FOR HANDLING TIME ////////////////////////////////////////////

/**
 * Given date parts in user time produce a GMT timestamp.
 *
 * @param int $year The year part to create timestamp of
 * @param int $month The month part to create timestamp of
 * @param int $day The day part to create timestamp of
 * @param int $hour The hour part to create timestamp of
 * @param int $minute The minute part to create timestamp of
 * @param int $second The second part to create timestamp of
 * @param float $timezone ?
 * @param bool $applydst ?
 * @return int timestamp
 * @todo Finish documenting this function
 */
function make_timestamp($year, $month=1, $day=1, $hour=0, $minute=0, $second=0, $timezone=99, $applydst=true) {

    $strtimezone = NULL;
    if (!is_numeric($timezone)) {
        $strtimezone = $timezone;
    }

    $timezone = get_user_timezone_offset($timezone);

    if (abs($timezone) > 13) {
        $time = mktime((int)$hour, (int)$minute, (int)$second, (int)$month, (int)$day, (int)$year);
    } else {
        $time = gmmktime((int)$hour, (int)$minute, (int)$second, (int)$month, (int)$day, (int)$year);
        $time = usertime($time, $timezone);
        if($applydst) {
            $time -= dst_offset_on($time, $strtimezone);
        }
    }

    return $time;

}

/**
 * Returns a formatted string that represents a date in user time
 * <b>WARNING: note that the format is for strftime(), not date().</b>
 * Because of a bug in most Windows time libraries, we can't use
 * the nicer %e, so we have to use %d which has leading zeroes.
 * A lot of the fuss in the function is just getting rid of these leading
 * zeroes as efficiently as possible.
 *
 * If parameter fixday = true (default), then take off leading
 * zero from %d, else mantain it.
 *
 * @uses HOURSECS
 * @param  int $date timestamp in GMT
 * @param string $format strftime format
 * @param float $timezone
 * @param bool $fixday If true (default) then the leading
 * zero from %d is removed. If false then the leading zero is mantained.
 * @return string
 */
function userdate($date, $format='', $timezone=99, $fixday = true) {

    global $CFG;

    $strtimezone = NULL;
    if (!is_numeric($timezone)) {
        $strtimezone = $timezone;
    }

    if (empty($format)) {
        $format = get_string('strftimedaydatetime');
    }

    if (!empty($CFG->nofixday)) {  // Config.php can force %d not to be fixed.
        $fixday = false;
    } else if ($fixday) {
        $formatnoday = str_replace('%d', 'DD', $format);
        $fixday = ($formatnoday != $format);
    }

    $date += dst_offset_on($date, $strtimezone);

    $timezone = get_user_timezone_offset($timezone);

    if (abs($timezone) > 13) {   /// Server time
        if ($fixday) {
            $datestring = strftime($formatnoday, $date);
            $daystring  = str_replace(' 0', '', strftime(' %d', $date));
            $datestring = str_replace('DD', $daystring, $datestring);
        } else {
            $datestring = strftime($format, $date);
        }
    } else {
        $date += (int)($timezone * 3600);
        if ($fixday) {
            $datestring = gmstrftime($formatnoday, $date);
            $daystring  = str_replace(' 0', '', gmstrftime(' %d', $date));
            $datestring = str_replace('DD', $daystring, $datestring);
        } else {
            $datestring = gmstrftime($format, $date);
        }
    }

/// If we are running under Windows convert from windows encoding to UTF-8
/// (because it's impossible to specify UTF-8 to fetch locale info in Win32)

   if ($CFG->ostype == 'WINDOWS') {
       if ($localewincharset = get_string('localewincharset')) {
           $textlib = textlib_get_instance();
           $datestring = $textlib->convert($datestring, $localewincharset, 'utf-8');
       }
   }

    return $datestring;
}

/**
 * Given a $time timestamp in GMT (seconds since epoch),
 * returns an array that represents the date in user time
 *
 * @uses HOURSECS
 * @param int $time Timestamp in GMT
 * @param float $timezone ?
 * @return array An array that represents the date in user time
 * @todo Finish documenting this function
 */
function usergetdate($time, $timezone=99) {

    $strtimezone = NULL;
    if (!is_numeric($timezone)) {
        $strtimezone = $timezone;
    }

    $timezone = get_user_timezone_offset($timezone);

    if (abs($timezone) > 13) {    // Server time
        return getdate($time);
    }

    // There is no gmgetdate so we use gmdate instead
    $time += dst_offset_on($time, $strtimezone);
    $time += intval((float)$timezone * HOURSECS);

    $datestring = gmstrftime('%B_%A_%j_%Y_%m_%w_%d_%H_%M_%S', $time);

    //be careful to ensure the returned array matches that produced by getdate() above
    list(
        $getdate['month'],
        $getdate['weekday'],
        $getdate['yday'],
        $getdate['year'],
        $getdate['mon'],
        $getdate['wday'],
        $getdate['mday'],
        $getdate['hours'],
        $getdate['minutes'],
        $getdate['seconds']
    ) = explode('_', $datestring);

    return $getdate;
}

/**
 * Given a GMT timestamp (seconds since epoch), offsets it by
 * the timezone.  eg 3pm in India is 3pm GMT - 7 * 3600 seconds
 *
 * @uses HOURSECS
 * @param  int $date Timestamp in GMT
 * @param float $timezone
 * @return int
 */
function usertime($date, $timezone=99) {

    $timezone = get_user_timezone_offset($timezone);

    if (abs($timezone) > 13) {
        return $date;
    }
    return $date - (int)($timezone * HOURSECS);
}

/**
 * Returns a float which represents the user's timezone difference from GMT in hours
 * Checks various settings and picks the most dominant of those which have a value
 *
 * @uses $CFG
 * @uses $USER
 * @param float $tz If this value is provided and not equal to 99, it will be returned as is and no other settings will be checked
 * @return int
 */
function get_user_timezone_offset($tz = 99) {

    global $USER, $CFG;

    $tz = get_user_timezone($tz);

    if (is_float($tz)) {
        return $tz;
    } else {
        $tzrecord = get_timezone_record($tz);
        if (empty($tzrecord)) {
            return 99.0;
        }
        return (float)$tzrecord->gmtoff / HOURMINS;
    }
}

/**
 * Returns a float or a string which denotes the user's timezone
 * A float value means that a simple offset from GMT is used, while a string (it will be the name of a timezone in the database)
 * means that for this timezone there are also DST rules to be taken into account
 * Checks various settings and picks the most dominant of those which have a value
 *
 * @uses $USER
 * @uses $CFG
 * @param float $tz If this value is provided and not equal to 99, it will be returned as is and no other settings will be checked
 * @return mixed
 */
function get_user_timezone($tz = 99) {
    global $USER, $CFG;

    $timezones = array(
        $tz,
        isset($CFG->forcetimezone) ? $CFG->forcetimezone : 99,
        isset($USER->timezone) ? $USER->timezone : 99,
        isset($CFG->timezone) ? $CFG->timezone : 99,
        );

    $tz = 99;

    while(($tz == '' || $tz == 99 || $tz == NULL) && $next = each($timezones)) {
        $tz = $next['value'];
    }

    return is_numeric($tz) ? (float) $tz : $tz;
}

/**
 * ?
 *
 * @uses $CFG
 * @uses $db
 * @param string $timezonename ?
 * @return object
 */
function get_timezone_record($timezonename) {
    global $CFG, $db;
    static $cache = NULL;

    if ($cache === NULL) {
        $cache = array();
    }

    if (isset($cache[$timezonename])) {
        return $cache[$timezonename];
    }

    return $cache[$timezonename] = get_record_sql('SELECT * FROM '.$CFG->prefix.'timezone
                                      WHERE name = '.$db->qstr($timezonename).' ORDER BY year DESC', true);
}

/**
 * ?
 *
 * @uses $CFG
 * @uses $USER
 * @param ? $fromyear ?
 * @param ? $to_year ?
 * @return bool
 */
function calculate_user_dst_table($from_year = NULL, $to_year = NULL, $strtimezone = NULL) {
    global $CFG, $SESSION;

    $usertz = get_user_timezone($strtimezone);

    if (is_float($usertz)) {
        // Trivial timezone, no DST
        return false;
    }

    if (!empty($SESSION->dst_offsettz) && $SESSION->dst_offsettz != $usertz) {
        // We have precalculated values, but the user's effective TZ has changed in the meantime, so reset
        unset($SESSION->dst_offsets);
        unset($SESSION->dst_range);
    }

    if (!empty($SESSION->dst_offsets) && empty($from_year) && empty($to_year)) {
        // Repeat calls which do not request specific year ranges stop here, we have already calculated the table
        // This will be the return path most of the time, pretty light computationally
        return true;
    }

    // Reaching here means we either need to extend our table or create it from scratch

    // Remember which TZ we calculated these changes for
    $SESSION->dst_offsettz = $usertz;

    if(empty($SESSION->dst_offsets)) {
        // If we 're creating from scratch, put the two guard elements in there
        $SESSION->dst_offsets = array(1 => NULL, 0 => NULL);
    }
    if(empty($SESSION->dst_range)) {
        // If creating from scratch
        $from = max((empty($from_year) ? intval(date('Y')) - 3 : $from_year), 1971);
        $to   = min((empty($to_year)   ? intval(date('Y')) + 3 : $to_year),   2035);

        // Fill in the array with the extra years we need to process
        $yearstoprocess = array();
        for($i = $from; $i <= $to; ++$i) {
            $yearstoprocess[] = $i;
        }

        // Take note of which years we have processed for future calls
        $SESSION->dst_range = array($from, $to);
    }
    else {
        // If needing to extend the table, do the same
        $yearstoprocess = array();

        $from = max((empty($from_year) ? $SESSION->dst_range[0] : $from_year), 1971);
        $to   = min((empty($to_year)   ? $SESSION->dst_range[1] : $to_year),   2035);

        if($from < $SESSION->dst_range[0]) {
            // Take note of which years we need to process and then note that we have processed them for future calls
            for($i = $from; $i < $SESSION->dst_range[0]; ++$i) {
                $yearstoprocess[] = $i;
            }
            $SESSION->dst_range[0] = $from;
        }
        if($to > $SESSION->dst_range[1]) {
            // Take note of which years we need to process and then note that we have processed them for future calls
            for($i = $SESSION->dst_range[1] + 1; $i <= $to; ++$i) {
                $yearstoprocess[] = $i;
            }
            $SESSION->dst_range[1] = $to;
        }
    }

    if(empty($yearstoprocess)) {
        // This means that there was a call requesting a SMALLER range than we have already calculated
        return true;
    }

    // From now on, we know that the array has at least the two guard elements, and $yearstoprocess has the years we need
    // Also, the array is sorted in descending timestamp order!

    // Get DB data

    static $presets_cache = array();
    if (!isset($presets_cache[$usertz])) {
        $presets_cache[$usertz] = get_records('timezone', 'name', $usertz, 'year DESC', 'year, gmtoff, dstoff, dst_month, dst_startday, dst_weekday, dst_skipweeks, dst_time, std_month, std_startday, std_weekday, std_skipweeks, std_time');
    }
    if(empty($presets_cache[$usertz])) {
        return false;
    }

    // Remove ending guard (first element of the array)
    reset($SESSION->dst_offsets);
    unset($SESSION->dst_offsets[key($SESSION->dst_offsets)]);

    // Add all required change timestamps
    foreach($yearstoprocess as $y) {
        // Find the record which is in effect for the year $y
        foreach($presets_cache[$usertz] as $year => $preset) {
            if($year <= $y) {
                break;
            }
        }

        $changes = dst_changes_for_year($y, $preset);

        if($changes === NULL) {
            continue;
        }
        if($changes['dst'] != 0) {
            $SESSION->dst_offsets[$changes['dst']] = $preset->dstoff * MINSECS;
        }
        if($changes['std'] != 0) {
            $SESSION->dst_offsets[$changes['std']] = 0;
        }
    }

    // Put in a guard element at the top
    $maxtimestamp = max(array_keys($SESSION->dst_offsets));
    $SESSION->dst_offsets[($maxtimestamp + DAYSECS)] = NULL; // DAYSECS is arbitrary, any "small" number will do

    // Sort again
    krsort($SESSION->dst_offsets);

    return true;
}

function dst_changes_for_year($year, $timezone) {

    if($timezone->dst_startday == 0 && $timezone->dst_weekday == 0 && $timezone->std_startday == 0 && $timezone->std_weekday == 0) {
        return NULL;
    }

    $monthdaydst = find_day_in_month($timezone->dst_startday, $timezone->dst_weekday, $timezone->dst_month, $year);
    $monthdaystd = find_day_in_month($timezone->std_startday, $timezone->std_weekday, $timezone->std_month, $year);

    list($dst_hour, $dst_min) = explode(':', $timezone->dst_time);
    list($std_hour, $std_min) = explode(':', $timezone->std_time);

    $timedst = make_timestamp($year, $timezone->dst_month, $monthdaydst, 0, 0, 0, 99, false);
    $timestd = make_timestamp($year, $timezone->std_month, $monthdaystd, 0, 0, 0, 99, false);

    // Instead of putting hour and minute in make_timestamp(), we add them afterwards.
    // This has the advantage of being able to have negative values for hour, i.e. for timezones
    // where GMT time would be in the PREVIOUS day than the local one on which DST changes.

    $timedst += $dst_hour * HOURSECS + $dst_min * MINSECS;
    $timestd += $std_hour * HOURSECS + $std_min * MINSECS;

    return array('dst' => $timedst, 0 => $timedst, 'std' => $timestd, 1 => $timestd);
}

// $time must NOT be compensated at all, it has to be a pure timestamp
function dst_offset_on($time, $strtimezone = NULL) {
    global $SESSION;

    if(!calculate_user_dst_table(NULL, NULL, $strtimezone) || empty($SESSION->dst_offsets)) {
        return 0;
    }

    reset($SESSION->dst_offsets);
    while(list($from, $offset) = each($SESSION->dst_offsets)) {
        if($from <= $time) {
            break;
        }
    }

    // This is the normal return path
    if($offset !== NULL) {
        return $offset;
    }

    // Reaching this point means we haven't calculated far enough, do it now:
    // Calculate extra DST changes if needed and recurse. The recursion always
    // moves toward the stopping condition, so will always end.

    if($from == 0) {
        // We need a year smaller than $SESSION->dst_range[0]
        if($SESSION->dst_range[0] == 1971) {
            return 0;
        }
        calculate_user_dst_table($SESSION->dst_range[0] - 5, NULL, $strtimezone);
        return dst_offset_on($time, $strtimezone);
    }
    else {
        // We need a year larger than $SESSION->dst_range[1]
        if($SESSION->dst_range[1] == 2035) {
            return 0;
        }
        calculate_user_dst_table(NULL, $SESSION->dst_range[1] + 5, $strtimezone);
        return dst_offset_on($time, $strtimezone);
    }
}

function find_day_in_month($startday, $weekday, $month, $year) {

    $daysinmonth = days_in_month($month, $year);

    if($weekday == -1) {
        // Don't care about weekday, so return:
        //    abs($startday) if $startday != -1
        //    $daysinmonth otherwise
        return ($startday == -1) ? $daysinmonth : abs($startday);
    }

    // From now on we 're looking for a specific weekday

    // Give "end of month" its actual value, since we know it
    if($startday == -1) {
        $startday = -1 * $daysinmonth;
    }

    // Starting from day $startday, the sign is the direction

    if($startday < 1) {

        $startday = abs($startday);
        $lastmonthweekday  = strftime('%w', mktime(12, 0, 0, $month, $daysinmonth, $year, 0));

        // This is the last such weekday of the month
        $lastinmonth = $daysinmonth + $weekday - $lastmonthweekday;
        if($lastinmonth > $daysinmonth) {
            $lastinmonth -= 7;
        }

        // Find the first such weekday <= $startday
        while($lastinmonth > $startday) {
            $lastinmonth -= 7;
        }

        return $lastinmonth;

    }
    else {

        $indexweekday = strftime('%w', mktime(12, 0, 0, $month, $startday, $year, 0));

        $diff = $weekday - $indexweekday;
        if($diff < 0) {
            $diff += 7;
        }

        // This is the first such weekday of the month equal to or after $startday
        $firstfromindex = $startday + $diff;

        return $firstfromindex;

    }
}

/**
 * Calculate the number of days in a given month
 *
 * @param int $month The month whose day count is sought
 * @param int $year The year of the month whose day count is sought
 * @return int
 */
function days_in_month($month, $year) {
   return intval(date('t', mktime(12, 0, 0, $month, 1, $year, 0)));
}

/// USER AUTHENTICATION AND LOGIN ////////////////////////////////////////

/**
 * Makes sure that $USER->sesskey exists, if $USER itself exists. It sets a new sesskey
 * if one does not already exist, but does not overwrite existing sesskeys. Returns the
 * sesskey string if $USER exists, or boolean false if not.
 *
 * @uses $USER
 * @return string
 */
function sesskey() {
    global $USER;

    if(!isset($USER)) {
        return false;
    }

    if (empty($USER->sesskey)) {
        $USER->sesskey = random_string(10);
    }

    return $USER->sesskey;
}

/**
 * For security purposes, this function will check that the currently
 * given sesskey (passed as a parameter to the script or this function)
 * matches that of the current user.
 *
 * @param string $sesskey optionally provided sesskey
 * @return bool
 */
function confirm_sesskey($sesskey=NULL) {
    global $USER;

    if (!empty($USER->ignoresesskey) || !empty($CFG->ignoresesskey)) {
        return true;
    }

    if (empty($sesskey)) {
        $sesskey = required_param('sesskey', PARAM_RAW);  // Check script parameters
    }

    if (!isset($USER->sesskey)) {
        return false;
    }

    return ($USER->sesskey === $sesskey);
}

/**
 * Setup all global $CFG course variables, set locale and also themes
 * This function can be used on pages that do not require login instead of require_login()
 *
 * @param mixed $courseorid id of the course or course object
 */
function course_setup($courseorid=0) {
    global $COURSE, $CFG, $SITE;

/// Redefine global $COURSE if needed
    if (empty($courseorid)) {
        // no change in global $COURSE - for backwards compatibiltiy
        // if require_rogin() used after require_login($courseid);
    } else if (is_object($courseorid)) {
        $COURSE = clone($courseorid);
    } else {
        global $course; // used here only to prevent repeated fetching from DB - may be removed later
        if ($courseorid == SITEID) {
            $COURSE = clone($SITE);
        } else if (!empty($course->id) and $course->id == $courseorid) {
            $COURSE = clone($course);
        } else {
            if (!$COURSE = get_record('course', 'id', $courseorid)) {
                error('Invalid course ID');
            }
        }
    }

/// set locale and themes
    moodle_setlocale();
    theme_setup();

}

/**
 * This function just makes sure a user is logged out.
 *
 * @uses $CFG
 * @uses $USER
 */
function require_logout() {

    global $USER, $CFG, $SESSION;

    if (isloggedin()) {
        add_to_log(SITEID, "user", "logout", "view.php?id=$USER->id&course=".SITEID, $USER->id, 0, $USER->id);

        $authsequence = get_enabled_auth_plugins(); // auths, in sequence
        foreach($authsequence as $authname) {
            $authplugin = get_auth_plugin($authname);
            $authplugin->prelogout_hook();
        }
    }

    if (ini_get_bool("register_globals") and check_php_version("4.3.0")) {
        // This method is just to try to avoid silly warnings from PHP 4.3.0
        session_unregister("USER");
        session_unregister("SESSION");
    }

    // Initialize variable to pass-by-reference to headers_sent(&$file, &$line)
    $file = $line = null;
    if (headers_sent($file, $line)) {
        error_log('MoodleSessionTest cookie could not be set in moodlelib.php:'.__LINE__);
        error_log('Headers were already sent in file: '.$file.' on line '.$line);
    } else {
        if (check_php_version('5.2.0')) {
            setcookie('MoodleSessionTest'.$CFG->sessioncookie, '', time() - 3600, $CFG->sessioncookiepath, $CFG->sessioncookiedomain, $CFG->cookiesecure, $CFG->cookiehttponly);
        } else {
            setcookie('MoodleSessionTest'.$CFG->sessioncookie, '', time() - 3600, $CFG->sessioncookiepath, $CFG->sessioncookiedomain, $CFG->cookiesecure);
        }
    }

    unset($_SESSION['USER']);
    unset($_SESSION['SESSION']);

    unset($SESSION);
    unset($USER);

}

/**
 * Modify the user table by setting the currently logged in user's
 * last login to now.
 *
 * @uses $USER
 * @return bool
 */
function update_user_login_times() {
    global $USER;

    $user = new stdClass();
    $USER->lastlogin = $user->lastlogin = $USER->currentlogin;
    $USER->currentlogin = $user->lastaccess = $user->currentlogin = time();

    $user->id = $USER->id;

    return update_record('user', $user);
}

function over_bounce_threshold($user) {

    global $CFG;

    if (empty($CFG->handlebounces)) {
        return false;
    }

    if (empty($user->id)) { /// No real (DB) user, nothing to do here.
        return false;
    }

    // set sensible defaults
    if (empty($CFG->minbounces)) {
        $CFG->minbounces = 10;
    }
    if (empty($CFG->bounceratio)) {
        $CFG->bounceratio = .20;
    }
    $bouncecount = 0;
    $sendcount = 0;
    if ($bounce = get_record('user_preferences','userid',$user->id,'name','email_bounce_count')) {
        $bouncecount = $bounce->value;
    }
    if ($send = get_record('user_preferences','userid',$user->id,'name','email_send_count')) {
        $sendcount = $send->value;
    }
    return ($bouncecount >= $CFG->minbounces && $bouncecount/$sendcount >= $CFG->bounceratio);
}

/**
 * @param $user - object containing an id
 * @param $reset - will reset the count to 0
 */
function set_send_count($user,$reset=false) {

    if (empty($user->id)) { /// No real (DB) user, nothing to do here.
        return;
    }

    if ($pref = get_record('user_preferences','userid',$user->id,'name','email_send_count')) {
        $pref->value = (!empty($reset)) ? 0 : $pref->value+1;
        update_record('user_preferences',$pref);
    }
    else if (!empty($reset)) { // if it's not there and we're resetting, don't bother.
        // make a new one
        $pref->name = 'email_send_count';
        $pref->value = 1;
        $pref->userid = $user->id;
        insert_record('user_preferences',$pref, false);
    }
}

/**
 * Goes through all enrolment records for the courses inside the metacourse and sync with them.
 *
 * @param mixed $course the metacourse to synch. Either the course object itself, or the courseid.
 */
function sync_metacourse($course) {
    global $CFG;

    // Check the course is valid.
    if (!is_object($course)) {
        if (!$course = get_record('course', 'id', $course)) {
            return false; // invalid course id
        }
    }

    // Check that we actually have a metacourse.
    if (empty($course->metacourse)) {
        return false;
    }

    // Get a list of roles that should not be synced.
    if (!empty($CFG->nonmetacoursesyncroleids)) {
        $roleexclusions = 'ra.roleid NOT IN (' . $CFG->nonmetacoursesyncroleids . ') AND';
    } else {
        $roleexclusions = '';
    }

    // Get the context of the metacourse.
    $context = get_context_instance(CONTEXT_COURSE, $course->id); // SITEID can not be a metacourse

    // We do not ever want to unassign the list of metacourse manager, so get a list of them.
    if ($users = get_users_by_capability($context, 'moodle/course:managemetacourse')) {
        $managers = array_keys($users);
    } else {
        $managers = array();
    }

    // Get assignments of a user to a role that exist in a child course, but
    // not in the meta coure. That is, get a list of the assignments that need to be made.
    if (!$assignments = get_records_sql("
            SELECT
                ra.id, ra.roleid, ra.userid, ra.hidden
            FROM
                {$CFG->prefix}role_assignments ra,
                {$CFG->prefix}context con,
                {$CFG->prefix}course_meta cm
            WHERE
                ra.contextid = con.id AND
                con.contextlevel = " . CONTEXT_COURSE . " AND
                con.instanceid = cm.child_course AND
                cm.parent_course = {$course->id} AND
                $roleexclusions
                NOT EXISTS (
                    SELECT 1 FROM
                        {$CFG->prefix}role_assignments ra2
                    WHERE
                        ra2.userid = ra.userid AND
                        ra2.roleid = ra.roleid AND
                        ra2.contextid = {$context->id}
                )
    ")) {
        $assignments = array();
    }

    // Get assignments of a user to a role that exist in the meta course, but
    // not in any child courses. That is, get a list of the unassignments that need to be made.
    if (!$unassignments = get_records_sql("
            SELECT
                ra.id, ra.roleid, ra.userid
            FROM
                {$CFG->prefix}role_assignments ra
            WHERE
                ra.contextid = {$context->id} AND
                $roleexclusions
                NOT EXISTS (
                    SELECT 1 FROM
                        {$CFG->prefix}role_assignments ra2,
                        {$CFG->prefix}context con2,
                        {$CFG->prefix}course_meta cm
                    WHERE
                        ra2.userid = ra.userid AND
                        ra2.roleid = ra.roleid AND
                        ra2.contextid = con2.id AND
                        con2.contextlevel = " . CONTEXT_COURSE . " AND
                        con2.instanceid = cm.child_course AND
                        cm.parent_course = {$course->id}
                )
    ")) {
        $unassignments = array();
    }

    $success = true;

    // Make the unassignments, if they are not managers.
    foreach ($unassignments as $unassignment) {
        if (!in_array($unassignment->userid, $managers)) {
            $success = role_unassign($unassignment->roleid, $unassignment->userid, 0, $context->id) && $success;
        }
    }

    // Make the assignments.
    foreach ($assignments as $assignment) {
        $success = role_assign($assignment->roleid, $assignment->userid, 0, $context->id, 0, 0, $assignment->hidden) && $success;
    }

    return $success;

// TODO: finish timeend and timestart
// maybe we could rely on cron job to do the cleaning from time to time
}

/**
 * Removes the record from the metacourse table and calls sync_metacourse
 */
function remove_from_metacourse($metacourseid, $courseid) {

    if (delete_records('course_meta','parent_course',$metacourseid,'child_course',$courseid)) {
        return sync_metacourse($metacourseid);
    }
    return false;
}

/**
 * Determines if a user is currently logged in
 *
 * @uses $USER
 * @return bool
 */
function isloggedin() {
    global $USER;

    return (!empty($USER->id));
}

/**
 * Determines if a user is logged in as real guest user with username 'guest'.
 * This function is similar to original isguest() in 1.6 and earlier.
 * Current isguest() is deprecated - do not use it anymore.
 *
 * @param $user mixed user object or id, $USER if not specified
 * @return bool true if user is the real guest user, false if not logged in or other user
 */
function isguestuser($user=NULL) {
    global $USER;
    if ($user === NULL) {
        $user = $USER;
    } else if (is_numeric($user)) {
        $user = get_record('user', 'id', $user, '', '', '', '', 'id, username');
    }

    if (empty($user->id)) {
        return false; // not logged in, can not be guest
    }

    return ($user->username == 'guest');
}

/**
 * Given an object containing firstname and lastname
 * values, this function returns a string with the
 * full name of the person.
 * The result may depend on system settings
 * or language.  'override' will force both names
 * to be used even if system settings specify one.
 *
 * @uses $CFG
 * @uses $SESSION
 * @param object $user A {@link $USER} object to get full name of
 * @param bool $override If true then the name will be first name followed by last name rather than adhering to fullnamedisplay setting.
 */
function fullname($user, $override=false) {

    global $CFG, $SESSION;

    if (!isset($user->firstname) and !isset($user->lastname)) {
        return '';
    }

    if (!$override) {
        if (!empty($CFG->forcefirstname)) {
            $user->firstname = $CFG->forcefirstname;
        }
        if (!empty($CFG->forcelastname)) {
            $user->lastname = $CFG->forcelastname;
        }
    }

    if (!empty($SESSION->fullnamedisplay)) {
        $CFG->fullnamedisplay = $SESSION->fullnamedisplay;
    }

    if ($CFG->fullnamedisplay == 'firstname lastname') {
        return $user->firstname .' '. $user->lastname;

    } else if ($CFG->fullnamedisplay == 'lastname firstname') {
        return $user->lastname .' '. $user->firstname;

    } else if ($CFG->fullnamedisplay == 'firstname') {
        if ($override) {
            return get_string('fullnamedisplay', '', $user);
        } else {
            return $user->firstname;
        }
    }

    return get_string('fullnamedisplay', '', $user);
}

/**
 * Sets a moodle cookie with an encrypted string
 *
 * @uses $CFG
 * @uses DAYSECS
 * @uses HOURSECS
 * @param string $thing The string to encrypt and place in a cookie
 */
function set_moodle_cookie($thing) {
    global $CFG;

    if ($thing == 'guest') {  // Ignore guest account
        return;
    }

    $cookiename = 'MOODLEID_'.$CFG->sessioncookie;

    $days = 60;
    $seconds = DAYSECS*$days;

    setCookie($cookiename, '', time() - HOURSECS, $CFG->sessioncookiepath, $CFG->sessioncookiedomain, $CFG->cookiesecure);
    setCookie($cookiename, rc4encrypt($thing), time()+$seconds, $CFG->sessioncookiepath, $CFG->sessioncookiedomain, $CFG->cookiesecure);
}

/**
 * Returns whether a given authentication plugin exists.
 *
 * @uses $CFG
 * @param string $auth Form of authentication to check for. Defaults to the
 *        global setting in {@link $CFG}.
 * @return boolean Whether the plugin is available.
 */
function exists_auth_plugin($auth) {
    global $CFG;
//IECISA ********** MODIFIED -> change path
    //if (file_exists("{$CFG->dirroot}/auth/$auth/auth.php")) {
    //    return is_readable("{$CFG->dirroot}/auth/$auth/auth.php");
    if (file_exists("{$CFG->dirroot}/application/admin/auth/$auth/auth.php")) {
    	return is_readable("{$CFG->dirroot}/application/admin/auth/$auth/auth.php");
    }
    return false;
}

/**
 * Returns an authentication plugin instance.
 *
 * @uses $CFG
 * @param string $auth name of authentication plugin
 * @return object An instance of the required authentication plugin.
 */
function get_auth_plugin($auth) {
    global $CFG;

    // check the plugin exists first
    if (! exists_auth_plugin($auth)) {
        error("Authentication plugin '$auth' not found.");
    }
    
    // return auth plugin instance
// IECISA *********** MODIFIED -> change path
    //require_once "{$CFG->dirroot}/auth/$auth/auth.php";
    require_once "{$CFG->dirroot}/application/admin/auth/$auth/auth.php";
// ********** END
    $class = "auth_plugin_$auth";
    return new $class;
}

/**
 * Returns array of active auth plugins.
 *
 * @param bool $fix fix $CFG->auth if needed
 * @return array
 */
function get_enabled_auth_plugins($fix=false) {
    global $CFG;

    $default = array('manual', 'nologin');

    if (empty($CFG->auth)) {
        $auths = array();
    } else {
        $auths = explode(',', $CFG->auth);
    }

    if ($fix) {
        $auths = array_unique($auths);
        foreach($auths as $k=>$authname) {
            if (!exists_auth_plugin($authname) or in_array($authname, $default)) {
                unset($auths[$k]);
            }
        }
        $newconfig = implode(',', $auths);
        if (!isset($CFG->auth) or $newconfig != $CFG->auth) {
            set_config('auth', $newconfig);
        }
    }

    return (array_merge($default, $auths));
}

/**
 * Retrieve the guest user object
 *
 * @uses $CFG
 * @return user A {@link $USER} object
 */
function guest_user() {
    global $CFG;

    if ($newuser = get_record('user', 'username', 'guest', 'mnethostid',  $CFG->mnet_localhost_id)) {
        $newuser->confirmed = 1;
        $newuser->lang = $CFG->lang;
        $newuser->lastip = getremoteaddr();
        if (empty($newuser->lastip)) {
            $newuser->lastip = '0.0.0.0';
        }
    }

    return $newuser;
}

/**
 * Call to complete the user login process after authenticate_user_login()
 * has succeeded. It will setup the $USER variable and other required bits
 * and pieces.
 *
 * NOTE:
 * - It will NOT log anything -- up to the caller to decide what to log.
 *
 *
 *
 * @uses $CFG, $USER
 * @param string $user obj
 * @return user|flase A {@link $USER} object or false if error
 */
function complete_user_login($user) {
    global $CFG, $USER;

    $USER = $user; // this is required because we need to access preferences here!

    if (!empty($CFG->regenloginsession)) {
        // please note this setting may break some auth plugins        
        session_regenerate_id();
    }

    reload_user_preferences();

    update_user_login_times();
    if (empty($CFG->nolastloggedin)) {
        set_moodle_cookie($USER->username);
    } else {
        // do not store last logged in user in cookie
        // auth plugins can temporarily override this from loginpage_hook()
        // do not save $CFG->nolastloggedin in database!
        set_moodle_cookie('nobody');
    }
    set_login_session_preferences();

    // Call enrolment plugins
// IECISA ********* DELETED ->
    //check_enrolment_plugins($user);
// ********** END

    /// This is what lets the user do anything on the site :-)
// IECISA ********** DELETED ->
    //load_all_capabilities();
// ********* END

    /// Select password change url
    $userauth = get_auth_plugin($USER->auth);

    /// check whether the user should be changing password
    if (get_user_preferences('auth_forcepasswordchange', false)){
        if ($userauth->can_change_password()) {
            if ($changeurl = $userauth->change_password_url()) {
                redirect($changeurl);
            } else {
                redirect($CFG->httpswwwroot.'/login/change_password.php');
            }
        } else {
            print_error('nopasswordchangeforced', 'auth');
        }
    }
    return $USER;
}

/**
 * Compare password against hash stored in internal user table.
 * If necessary it also updates the stored hash to new format.
 *
 * @param object user
 * @param string plain text password
 * @return bool is password valid?
 */
function validate_internal_user_password(&$user, $password) {
    global $CFG;

    if (!isset($CFG->passwordsaltmain)) {
        $CFG->passwordsaltmain = '';
    }

    $validated = false;

    // get password original encoding in case it was not updated to unicode yet
    $textlib = textlib_get_instance();
    $convpassword = $textlib->convert($password, 'utf-8', get_string('oldcharset'));

    if ($user->password == md5($password.$CFG->passwordsaltmain) or $user->password == md5($password)
        or $user->password == md5($convpassword.$CFG->passwordsaltmain) or $user->password == md5($convpassword)) {
        $validated = true;
    } else {
        for ($i=1; $i<=20; $i++) { //20 alternative salts should be enough, right?
            $alt = 'passwordsaltalt'.$i;
            if (!empty($CFG->$alt)) {
                if ($user->password == md5($password.$CFG->$alt) or $user->password == md5($convpassword.$CFG->$alt)) {
                    $validated = true;
                    break;
                }
            }
        }
    }

    if ($validated) {
        // force update of password hash using latest main password salt and encoding if needed
        update_internal_user_password($user, $password);
    }

    return $validated;
}

/**
 * Calculate hashed value from password using current hash mechanism.
 *
 * @param string password
 * @return string password hash
 */
function hash_internal_user_password($password) {
    global $CFG;

    if (isset($CFG->passwordsaltmain)) {
        return md5($password.$CFG->passwordsaltmain);
    } else {
        return md5($password);
    }
}

/**
 * Update pssword hash in user object.
 *
 * @param object user
 * @param string plain text password
 * @param bool store changes also in db, default true
 * @return true if hash changed
 */
function update_internal_user_password(&$user, $password) {
    global $CFG;

    $authplugin = get_auth_plugin($user->auth);
    if ($authplugin->prevent_local_passwords()) {
        $hashedpassword = 'not cached';
    } else {
        $hashedpassword = hash_internal_user_password($password);
    }

    return set_field('user', 'password',  $hashedpassword, 'id', $user->id);
}

/**
 * Get a complete user record, which includes all the info
 * in the user record
 * Intended for setting as $USER session variable
 *
 * @uses $CFG
 * @uses SITEID
 * @param string $field The user field to be checked for a given value.
 * @param string $value The value to match for $field.
 * @return user A {@link $USER} object.
 */
function get_complete_user_data($field, $value, $mnethostid=null) {

    global $CFG;

    if (!$field || !$value) {
        return false;
    }

/// Build the WHERE clause for an SQL query

    $constraints = $field .' = \''. $value .'\' AND deleted <> \'1\'';

    // If we are loading user data based on anything other than id,
    // we must also restrict our search based on mnet host.
    if ($field != 'id') {
        if (empty($mnethostid)) {
            // if empty, we restrict to local users
            $mnethostid = $CFG->mnet_localhost_id;
        }
    }
    if (!empty($mnethostid)) {
        $mnethostid = (int)$mnethostid;
        $constraints .= ' AND mnethostid = ' . $mnethostid;
    }

/// Get all the basic user data

    if (! $user = get_record_select('user', $constraints)) {
        return false;
    }

/// Get various settings and preferences

    if ($displays = get_records('course_display', 'userid', $user->id)) {
        foreach ($displays as $display) {
            $user->display[$display->course] = $display->display;
        }
    }

    $user->preference = get_user_preferences(null, null, $user->id);

    $user->lastcourseaccess    = array(); // during last session
    $user->currentcourseaccess = array(); // during current session
    if ($lastaccesses = get_records('user_lastaccess', 'userid', $user->id)) {
        foreach ($lastaccesses as $lastaccess) {
            $user->lastcourseaccess[$lastaccess->courseid] = $lastaccess->timeaccess;
        }
    }

    $sql = "SELECT g.id, g.courseid
              FROM {$CFG->prefix}groups g, {$CFG->prefix}groups_members gm
             WHERE gm.groupid=g.id AND gm.userid={$user->id}";

    // this is a special hack to speedup calendar display
    $user->groupmember = array();
    if ($groups = get_records_sql($sql)) {
        foreach ($groups as $group) {
            if (!array_key_exists($group->courseid, $user->groupmember)) {
                $user->groupmember[$group->courseid] = array();
            }
            $user->groupmember[$group->courseid][$group->id] = $group->id;
        }
    }

/// Add the custom profile fields to the user record
    include_once($CFG->dirroot.'/user/profile/lib.php');
    $customfields = (array)profile_user_record($user->id);
    foreach ($customfields as $cname=>$cvalue) {
        if (!isset($user->$cname)) { // Don't overwrite any standard fields
            $user->$cname = $cvalue;
        }
    }

/// Rewrite some variables if necessary
    if (!empty($user->description)) {
        $user->description = true;   // No need to cart all of it around
    }
    if ($user->username == 'guest') {
        $user->lang       = $CFG->lang;               // Guest language always same as site
        $user->firstname  = get_string('guestuser');  // Name always in current language
        $user->lastname   = ' ';
    }

    if (isset($_SERVER['REMOTE_ADDR'])) {
        $user->sesskey  = random_string(10);
        $user->sessionIP = md5(getremoteaddr());   // Store the current IP in the session
    }

    return $user;
}

/**
 * When logging in, this function is run to set certain preferences
 * for the current SESSION
 */
function set_login_session_preferences() {
    global $SESSION, $CFG;

    $SESSION->justloggedin = true;

    unset($SESSION->lang);

    // Restore the calendar filters, if saved
    if (intval(get_user_preferences('calendar_persistflt', 0))) {
        include_once($CFG->dirroot.'/calendar/lib.php');
        calendar_set_filters_status(get_user_preferences('calendar_savedflt', 0xff));
    }
}

function generate_email_processing_address($modid,$modargs) {
    global $CFG;

    $header = $CFG->mailprefix . substr(base64_encode(pack('C',$modid)),0,2).$modargs;
    return $header . substr(md5($header.get_site_identifier()),0,16).'@'.$CFG->maildomain;
}

/// CORRESPONDENCE  ////////////////////////////////////////////////

/**
 * Get mailer instance, enable buffering, flush buffer or disable buffering.
 * @param $action string 'get', 'buffer', 'close' or 'flush'
 * @return reference to mailer instance if 'get' used or nothing
 */
function &get_mailer($action='get') {
    global $CFG;

    static $mailer  = null;
    static $counter = 0;

    if (!isset($CFG->smtpmaxbulk)) {
        $CFG->smtpmaxbulk = 1;
    }

    if ($action == 'get') {
        $prevkeepalive = false;

        if (isset($mailer) and $mailer->Mailer == 'smtp') {
            if ($counter < $CFG->smtpmaxbulk and empty($mailer->error_count)) {
                $counter++;
                // reset the mailer
                $mailer->Priority         = 3;
                $mailer->CharSet          = 'UTF-8'; // our default
                $mailer->ContentType      = "text/plain";
                $mailer->Encoding         = "8bit";
                $mailer->From             = "root@localhost";
                $mailer->FromName         = "Root User";
                $mailer->Sender           = "";
                $mailer->Subject          = "";
                $mailer->Body             = "";
                $mailer->AltBody          = "";
                $mailer->ConfirmReadingTo = "";

                $mailer->ClearAllRecipients();
                $mailer->ClearReplyTos();
                $mailer->ClearAttachments();
                $mailer->ClearCustomHeaders();
                return $mailer;
            }

            $prevkeepalive = $mailer->SMTPKeepAlive;
            get_mailer('flush');
        }

        include_once($CFG->libdir.'/phpmailer/class.phpmailer.php');
        $mailer = new phpmailer();

        $counter = 1;

        $mailer->Version   = 'Moodle '.$CFG->version;         // mailer version
        $mailer->PluginDir = $CFG->libdir.'/phpmailer/';      // plugin directory (eg smtp plugin)
        $mailer->CharSet   = 'UTF-8';

        // some MTAs may do double conversion of LF if CRLF used, CRLF is required line ending in RFC 822bis
        // hmm, this is a bit hacky because LE should be private
        if (isset($CFG->mailnewline) and $CFG->mailnewline == 'CRLF') {
            $mailer->LE = "\r\n";
        } else {
            $mailer->LE = "\n";
        }

        if ($CFG->smtphosts == 'qmail') {
            $mailer->IsQmail();                              // use Qmail system

        } else if (empty($CFG->smtphosts)) {
            $mailer->IsMail();                               // use PHP mail() = sendmail

        } else {
            $mailer->IsSMTP();                               // use SMTP directly
            if (!empty($CFG->debugsmtp)) {
                $mailer->SMTPDebug = true;
            }
            $mailer->Host          = $CFG->smtphosts;        // specify main and backup servers
            $mailer->SMTPKeepAlive = $prevkeepalive;         // use previous keepalive

            if ($CFG->smtpuser) {                            // Use SMTP authentication
                $mailer->SMTPAuth = true;
                $mailer->Username = $CFG->smtpuser;
                $mailer->Password = $CFG->smtppass;
            }
        }

        return $mailer;
    }

    $nothing = null;

    // keep smtp session open after sending
    if ($action == 'buffer') {
        if (!empty($CFG->smtpmaxbulk)) {
            get_mailer('flush');
            $m =& get_mailer();
            if ($m->Mailer == 'smtp') {
                $m->SMTPKeepAlive = true;
            }
        }
        return $nothing;
    }

    // close smtp session, but continue buffering
    if ($action == 'flush') {
        if (isset($mailer) and $mailer->Mailer == 'smtp') {
            if (!empty($mailer->SMTPDebug)) {
                echo '<pre>'."\n";
            }
            $mailer->SmtpClose();
            if (!empty($mailer->SMTPDebug)) {
                echo '</pre>';
            }
        }
        return $nothing;
    }

    // close smtp session, do not buffer anymore
    if ($action == 'close') {
        if (isset($mailer) and $mailer->Mailer == 'smtp') {
            get_mailer('flush');
            $mailer->SMTPKeepAlive = false;
        }
        $mailer = null; // better force new instance
        return $nothing;
    }
}

/**
 * Send an email to a specified user
 *
 * @uses $CFG
 * @uses $FULLME
 * @uses $MNETIDPJUMPURL IdentityProvider(IDP) URL user hits to jump to mnet peer.
 * @uses SITEID
 * @param user $user  A {@link $USER} object
 * @param user $from A {@link $USER} object
 * @param string $subject plain text subject line of the email
 * @param string $messagetext plain text version of the message
 * @param string $messagehtml complete html version of the message (optional)
 * @param string $attachment a file on the filesystem, relative to $CFG->dataroot
 * @param string $attachname the name of the file (extension indicates MIME)
 * @param bool $usetrueaddress determines whether $from email address should
 *          be sent out. Will be overruled by user profile setting for maildisplay
 * @param int $wordwrapwidth custom word wrap width
 * @return bool|string Returns "true" if mail was sent OK, "emailstop" if email
 *          was blocked by user and "false" if there was another sort of error.
 */
function email_to_user($user, $from, $subject, $messagetext, $messagehtml='', $attachment='', $attachname='', $usetrueaddress=true, $replyto='', $replytoname='', $wordwrapwidth=79) {

    global $CFG, $FULLME, $MNETIDPJUMPURL;
    static $mnetjumps = array();

    if (empty($user) || empty($user->email)) {
        return false;
    }

    if (!empty($user->deleted)) {
        // do not mail delted users
        return false;
    }

    if (!empty($CFG->noemailever)) {
        // hidden setting for development sites, set in config.php if needed
        return true;
    }

    if (!empty($CFG->divertallemailsto)) {
        $subject = "[DIVERTED {$user->email}] $subject";
        $user = clone($user);
        $user->email = $CFG->divertallemailsto;
    }

    // skip mail to suspended users
    if (isset($user->auth) && $user->auth=='nologin') {
        return true;
    }

    if (!empty($user->emailstop)) {
        return 'emailstop';
    }

    if (over_bounce_threshold($user)) {
        error_log("User $user->id (".fullname($user).") is over bounce threshold! Not sending.");
        return false;
    }

    // If the user is a remote mnet user, parse the email text for URL to the
    // wwwroot and modify the url to direct the user's browser to login at their
    // home site (identity provider - idp) before hitting the link itself
    if (is_mnet_remote_user($user)) {
        require_once($CFG->dirroot.'/mnet/lib.php');
        // Form the request url to hit the idp's jump.php
        if (isset($mnetjumps[$user->mnethostid])) {
            $MNETIDPJUMPURL = $mnetjumps[$user->mnethostid];
        } else {
            $idp = mnet_get_peer_host($user->mnethostid);
            $idpjumppath = '/auth/mnet/jump.php';
            $MNETIDPJUMPURL = $idp->wwwroot . $idpjumppath . '?hostwwwroot=' . $CFG->wwwroot . '&wantsurl=';
            $mnetjumps[$user->mnethostid] = $MNETIDPJUMPURL;
        }

        $messagetext = preg_replace_callback("%($CFG->wwwroot[^[:space:]]*)%",
                'mnet_sso_apply_indirection',
                $messagetext);
        $messagehtml = preg_replace_callback("%href=[\"'`]($CFG->wwwroot[\w_:\?=#&@/;.~-]*)[\"'`]%",
                'mnet_sso_apply_indirection',
                $messagehtml);
    }
    $mail =& get_mailer();

    if (!empty($mail->SMTPDebug)) {
        echo '<pre>' . "\n";
    }

/// We are going to use textlib services here
    $textlib = textlib_get_instance();

    $supportuser = generate_email_supportuser();

    // make up an email address for handling bounces
    if (!empty($CFG->handlebounces)) {
        $modargs = 'B'.base64_encode(pack('V',$user->id)).substr(md5($user->email),0,16);
        $mail->Sender = generate_email_processing_address(0,$modargs);
    } else {
        $mail->Sender = $supportuser->email;
    }

    if (is_string($from)) { // So we can pass whatever we want if there is need
        $mail->From     = $CFG->noreplyaddress;
        $mail->FromName = $from;
    } else if ($usetrueaddress and $from->maildisplay) {
        $mail->From     = stripslashes($from->email);
        $mail->FromName = fullname($from);
    } else {
        $mail->From     = $CFG->noreplyaddress;
        $mail->FromName = fullname($from);
        if (empty($replyto)) {
            $mail->AddReplyTo($CFG->noreplyaddress,get_string('noreplyname'));
        }
    }

    if (!empty($replyto)) {
        $mail->AddReplyTo($replyto,$replytoname);
    }

    $mail->Subject = substr(stripslashes($subject), 0, 900);

    $mail->AddAddress(stripslashes($user->email), fullname($user) );

    $mail->WordWrap = $wordwrapwidth;                   // set word wrap

    if (!empty($from->customheaders)) {                 // Add custom headers
        if (is_array($from->customheaders)) {
            foreach ($from->customheaders as $customheader) {
                $mail->AddCustomHeader($customheader);
            }
        } else {
            $mail->AddCustomHeader($from->customheaders);
        }
    }

    if (!empty($from->priority)) {
        $mail->Priority = $from->priority;
    }

    if ($messagehtml && $user->mailformat == 1) { // Don't ever send HTML to users who don't want it
        $mail->IsHTML(true);
        $mail->Encoding = 'quoted-printable';           // Encoding to use
        $mail->Body    =  $messagehtml;
        $mail->AltBody =  "\n$messagetext\n";
    } else {
        $mail->IsHTML(false);
        $mail->Body =  "\n$messagetext\n";
    }

    if ($attachment && $attachname) {
        if (ereg( "\\.\\." ,$attachment )) {    // Security check for ".." in dir path
            $mail->AddAddress($supportuser->email, fullname($supportuser, true) );
            $mail->AddStringAttachment('Error in attachment.  User attempted to attach a filename with a unsafe name.', 'error.txt', '8bit', 'text/plain');
        } else {
            require_once($CFG->libdir.'/filelib.php');
            $mimetype = mimeinfo('type', $attachname);
            $mail->AddAttachment($CFG->dataroot .'/'. $attachment, $attachname, 'base64', $mimetype);
        }
    }



/// If we are running under Unicode and sitemailcharset or allowusermailcharset are set, convert the email
/// encoding to the specified one
    if ((!empty($CFG->sitemailcharset) || !empty($CFG->allowusermailcharset))) {
    /// Set it to site mail charset
        $charset = $CFG->sitemailcharset;
    /// Overwrite it with the user mail charset
        if (!empty($CFG->allowusermailcharset)) {
            if ($useremailcharset = get_user_preferences('mailcharset', '0', $user->id)) {
                $charset = $useremailcharset;
            }
        }
    /// If it has changed, convert all the necessary strings
        $charsets = get_list_of_charsets();
        unset($charsets['UTF-8']);
        if (in_array($charset, $charsets)) {
        /// Save the new mail charset
            $mail->CharSet = $charset;
        /// And convert some strings
            $mail->FromName = $textlib->convert($mail->FromName, 'utf-8', $mail->CharSet); //From Name
            foreach ($mail->ReplyTo as $key => $rt) {                                      //ReplyTo Names
                $mail->ReplyTo[$key][1] = $textlib->convert($rt[1], 'utf-8', $mail->CharSet);
            }
            $mail->Subject = $textlib->convert($mail->Subject, 'utf-8', $mail->CharSet);   //Subject
            foreach ($mail->to as $key => $to) {
                $mail->to[$key][1] = $textlib->convert($to[1], 'utf-8', $mail->CharSet);      //To Names
            }
            $mail->Body = $textlib->convert($mail->Body, 'utf-8', $mail->CharSet);         //Body
            $mail->AltBody = $textlib->convert($mail->AltBody, 'utf-8', $mail->CharSet);   //Subject
        }
    }

    if ($mail->Send()) {
        set_send_count($user);
        $mail->IsSMTP();                               // use SMTP directly
        if (!empty($mail->SMTPDebug)) {
            echo '</pre>';
        }
        return true;
    } else {
        mtrace('ERROR: '. $mail->ErrorInfo);
        add_to_log(SITEID, 'library', 'mailer', $FULLME, 'ERROR: '. $mail->ErrorInfo);
        if (!empty($mail->SMTPDebug)) {
            echo '</pre>';
        }
        return false;
    }
}

/**
 * Generate a fake user for emails based on support settings
 *
 */
function generate_email_supportuser() {

    global $CFG;

    static $supportuser;

    if (!empty($supportuser)) {
        return $supportuser;
    }

    $supportuser = new stdClass;
    $supportuser->email = $CFG->supportemail ? $CFG->supportemail : $CFG->noreplyaddress;
    $supportuser->firstname = $CFG->supportname ? $CFG->supportname : get_string('noreplyname');
    $supportuser->lastname = '';
    $supportuser->maildisplay = true;

    return $supportuser;
}

/// FILE HANDLING  /////////////////////////////////////////////

/**
 * Makes a directory for a particular user.
 *
 * @uses $CFG
 * @param int $userid The id of the user in question - maps to id field of 'user' table.
 * @param bool $test Whether we are only testing the return value (do not create the directory)
 * @return string|false Returns full path to directory if successful, false if not
 */
function make_user_directory($userid, $test=false) {
    global $CFG;

    if (is_bool($userid) || $userid < 0 || !ereg('^[0-9]{1,10}$', $userid) || $userid > 2147483647) {
        if (!$test) {
            notify("Given userid was not a valid integer! (" . gettype($userid) . " $userid)");
        }
        return false;
    }

    // Generate a two-level path for the userid. First level groups them by slices of 1000 users, second level is userid
    $level1 = floor($userid / 1000) * 1000;

    $userdir = "user/$level1/$userid";
    if ($test) {
        return $CFG->dataroot . '/' . $userdir;
    } else {
        return make_upload_directory($userdir);
    }
}

/**
 * Returns an array with all the filenames in
 * all subdirectories, relative to the given rootdir.
 * If excludefile is defined, then that file/directory is ignored
 * If getdirs is true, then (sub)directories are included in the output
 * If getfiles is true, then files are included in the output
 * (at least one of these must be true!)
 *
 * @param string $rootdir  ?
 * @param string $excludefile  If defined then the specified file/directory is ignored
 * @param bool $descend  ?
 * @param bool $getdirs  If true then (sub)directories are included in the output
 * @param bool $getfiles  If true then files are included in the output
 * @return array An array with all the filenames in
 * all subdirectories, relative to the given rootdir
 * @todo Finish documenting this function. Add examples of $excludefile usage.
 */
function get_directory_list($rootdir, $excludefiles='', $descend=true, $getdirs=false, $getfiles=true) {

    $dirs = array();

    if (!$getdirs and !$getfiles) {   // Nothing to show
        return $dirs;
    }

    if (!is_dir($rootdir)) {          // Must be a directory
        return $dirs;
    }

    if (!$dir = opendir($rootdir)) {  // Can't open it for some reason
        return $dirs;
    }

    if (!is_array($excludefiles)) {
        $excludefiles = array($excludefiles);
    }

    while (false !== ($file = readdir($dir))) {
        $firstchar = substr($file, 0, 1);
        if ($firstchar == '.' or $file == 'CVS' or in_array($file, $excludefiles)) {
            continue;
        }
        $fullfile = $rootdir .'/'. $file;
        if (filetype($fullfile) == 'dir') {
            if ($getdirs) {
                $dirs[] = $file;
            }
            if ($descend) {
                $subdirs = get_directory_list($fullfile, $excludefiles, $descend, $getdirs, $getfiles);
                foreach ($subdirs as $subdir) {
                    $dirs[] = $file .'/'. $subdir;
                }
            }
        } else if ($getfiles) {
            $dirs[] = $file;
        }
    }
    closedir($dir);

    asort($dirs);

    return $dirs;
}

/**
 * Adds up all the files in a directory and works out the size.
 *
 * @param string $rootdir  ?
 * @param string $excludefile  ?
 * @return array
 * @todo Finish documenting this function
 */
function get_directory_size($rootdir, $excludefile='') {

    global $CFG;

    // do it this way if we can, it's much faster
    if (!empty($CFG->pathtodu) && is_executable(trim($CFG->pathtodu))) {
        $command = trim($CFG->pathtodu).' -sk '.escapeshellarg($rootdir);
        $output = null;
        $return = null;
        exec($command,$output,$return);
        if (is_array($output)) {
            return get_real_size(intval($output[0]).'k'); // we told it to return k.
        }
    }

    if (!is_dir($rootdir)) {          // Must be a directory
        return 0;
    }

    if (!$dir = @opendir($rootdir)) {  // Can't open it for some reason
        return 0;
    }

    $size = 0;

    while (false !== ($file = readdir($dir))) {
        $firstchar = substr($file, 0, 1);
        if ($firstchar == '.' or $file == 'CVS' or $file == $excludefile) {
            continue;
        }
        $fullfile = $rootdir .'/'. $file;
        if (filetype($fullfile) == 'dir') {
            $size += get_directory_size($fullfile, $excludefile);
        } else {
            $size += filesize($fullfile);
        }
    }
    closedir($dir);

    return $size;
}

/**
 * Converts bytes into display form
 *
 * @param string $size  ?
 * @return string
 * @staticvar string $gb Localized string for size in gigabytes
 * @staticvar string $mb Localized string for size in megabytes
 * @staticvar string $kb Localized string for size in kilobytes
 * @staticvar string $b Localized string for size in bytes
 * @todo Finish documenting this function. Verify return type.
 */
function display_size($size) {

    static $gb, $mb, $kb, $b;

    if (empty($gb)) {
        $gb = get_string('sizegb');
        $mb = get_string('sizemb');
        $kb = get_string('sizekb');
        $b  = get_string('sizeb');
    }

    if ($size >= 1073741824) {
        $size = round($size / 1073741824 * 10) / 10 . $gb;
    } else if ($size >= 1048576) {
        $size = round($size / 1048576 * 10) / 10 . $mb;
    } else if ($size >= 1024) {
        $size = round($size / 1024 * 10) / 10 . $kb;
    } else {
        $size = $size .' '. $b;
    }
    return $size;
}

/**
 * Cleans a given filename by removing suspicious or troublesome characters
 * Only these are allowed: alphanumeric _ - .
 * Unicode characters can be enabled by setting $CFG->unicodecleanfilename = true in config.php
 *
 * WARNING: unicode characters may not be compatible with zip compression in backup/restore,
 *          because native zip binaries do weird character conversions. Use PHP zipping instead.
 *
 * @param string $string  file name
 * @return string cleaned file name
 */
function clean_filename($string) {
    global $CFG;
    if (empty($CFG->unicodecleanfilename)) {
        $textlib = textlib_get_instance();
        $string = $textlib->specialtoascii($string);
        $string = preg_replace('/[^\.a-zA-Z\d\_-]/','_', $string ); // only allowed chars
    } else {
        //clean only ascii range
        $string = preg_replace("/[\\000-\\x2c\\x2f\\x3a-\\x40\\x5b-\\x5e\\x60\\x7b-\\177]/s", '_', $string);
    }
    $string = preg_replace("/_+/", '_', $string);
    $string = preg_replace("/\.\.+/", '.', $string);
    return $string;
}


/// STRING TRANSLATION  ////////////////////////////////////////

/**
 * Returns the code for the current language
 *
 * @uses $CFG
 * @param $USER
 * @param $SESSION
 * @return string
 */
function current_language() {
    global $CFG, $USER, $SESSION, $COURSE;

    if (!empty($COURSE->id) and $COURSE->id != SITEID and !empty($COURSE->lang)) {    // Course language can override all other settings for this page
        $return = $COURSE->lang;

    } else if (!empty($SESSION->lang)) {    // Session language can override other settings
        $return = $SESSION->lang;

    } else if (!empty($USER->lang)) {
        $return = $USER->lang;

    } else {
        $return = $CFG->lang;
    }

    if ($return == 'en') {
        $return = 'en_utf8';
    }

    return $return;
}

/**
 * Prints out a translated string.
 *
 * Prints out a translated string using the return value from the {@link get_string()} function.
 *
 * Example usage of this function when the string is in the moodle.php file:<br/>
 * <code>
 * echo '<strong>';
 * print_string('wordforstudent');
 * echo '</strong>';
 * </code>
 *
 * Example usage of this function when the string is not in the moodle.php file:<br/>
 * <code>
 * echo '<h1>';
 * print_string('typecourse', 'calendar');
 * echo '</h1>';
 * </code>
 *
 * @param string $identifier The key identifier for the localized string
 * @param string $module The module where the key identifier is stored. If none is specified then moodle.php is used.
 * @param mixed $a An object, string or number that can be used
 * within translation strings
 */
function print_string($identifier, $module='', $a=NULL) {
    echo get_string($identifier, $module, $a);
}

/**
 * fix up the optional data in get_string()/print_string() etc
 * ensure possible sprintf() format characters are escaped correctly
 * needs to handle arbitrary strings and objects
 * @param mixed $a An object, string or number that can be used
 * @return mixed the supplied parameter 'cleaned'
 */
function clean_getstring_data( $a ) {
    if (is_string($a)) {
        return str_replace( '%','%%',$a );
    }
    elseif (is_object($a)) {
        $a_vars = get_object_vars( $a );
        $new_a_vars = array();
        foreach ($a_vars as $fname => $a_var) {
            $new_a_vars[$fname] = clean_getstring_data( $a_var );
        }
        return (object)$new_a_vars;
    }
    else {
        return $a;
    }
}

/**
 * @return array places to look for lang strings based on the prefix to the
 * module name. For example qtype_ in question/type. Used by get_string and
 * help.php.
 */
function places_to_search_for_lang_strings() {
    global $CFG;

    return array(
        '__exceptions' => array('moodle', 'langconfig'),
        'assignment_' => array('mod/assignment/type'),
        'auth_' => array('auth'),
        'block_' => array('blocks'),
        'datafield_' => array('mod/data/field'),
        'datapreset_' => array('mod/data/preset'),
        'enrol_' => array('enrol'),
        'filter_' => array('filter'),
        'format_' => array('course/format'),
        'qtype_' => array('question/type'),
        'report_' => array($CFG->admin.'/report', 'course/report', 'mod/quiz/report'),
        'resource_' => array('mod/resource/type'),
        'gradereport_' => array('grade/report'),
        'gradeimport_' => array('grade/import'),
        'gradeexport_' => array('grade/export'),
        'qformat_' => array('question/format'),
        'profilefield_' => array('user/profile/field'),
        '' => array('mod')
    );
}

/**
 * Returns a localized string.
 *
 * Returns the translated string specified by $identifier as
 * for $module.  Uses the same format files as STphp.
 * $a is an object, string or number that can be used
 * within translation strings
 *
 * eg "hello \$a->firstname \$a->lastname"
 * or "hello \$a"
 *
 * If you would like to directly echo the localized string use
 * the function {@link print_string()}
 *
 * Example usage of this function involves finding the string you would
 * like a local equivalent of and using its identifier and module information
 * to retrive it.<br/>
 * If you open moodle/lang/en/moodle.php and look near line 1031
 * you will find a string to prompt a user for their word for student
 * <code>
 * $string['wordforstudent'] = 'Your word for Student';
 * </code>
 * So if you want to display the string 'Your word for student'
 * in any language that supports it on your site
 * you just need to use the identifier 'wordforstudent'
 * <code>
 * $mystring = '<strong>'. get_string('wordforstudent') .'</strong>';
or
 * </code>
 * If the string you want is in another file you'd take a slightly
 * different approach. Looking in moodle/lang/en/calendar.php you find
 * around line 75:
 * <code>
 * $string['typecourse'] = 'Course event';
 * </code>
 * If you want to display the string "Course event" in any language
 * supported you would use the identifier 'typecourse' and the module 'calendar'
 * (because it is in the file calendar.php):
 * <code>
 * $mystring = '<h1>'. get_string('typecourse', 'calendar') .'</h1>';
 * </code>
 *
 * As a last resort, should the identifier fail to map to a string
 * the returned string will be [[ $identifier ]]
 *
 * @uses $CFG
 * @param string $identifier The key identifier for the localized string
 * @param string $module The module where the key identifier is stored, usually expressed as the filename in the language pack without the .php on the end but can also be written as mod/forum or grade/export/xls.  If none is specified then moodle.php is used.
 * @param mixed $a An object, string or number that can be used
 * within translation strings
 * @param array $extralocations An array of strings with other locations to look for string files
 * @return string The localized string.
 */
function get_string($identifier, $module='', $a=NULL, $extralocations=NULL) {

    global $CFG;

/// originally these special strings were stored in moodle.php now we are only in langconfig.php
    $langconfigstrs = array('alphabet', 'backupnameformat', 'decsep', 'firstdayofweek', 'listsep', 'locale',
                            'localewin', 'localewincharset', 'oldcharset',
                            'parentlanguage', 'strftimedate', 'strftimedateshort', 'strftimedatetime',
                            'strftimedaydate', 'strftimedaydatetime', 'strftimedayshort', 'strftimedaytime',
                            'strftimemonthyear', 'strftimerecent', 'strftimerecentfull', 'strftimetime',
                            'thischarset', 'thisdirection', 'thislanguage', 'strftimedatetimeshort', 'thousandssep');

    $filetocheck = 'langconfig.php';
    $defaultlang = 'en_utf8';
    if (in_array($identifier, $langconfigstrs)) {
        $module = 'langconfig';  //This strings are under langconfig.php for 1.6 lang packs
    }

    $lang = current_language();

    if ($module == '') {
        $module = 'moodle';
    }

/// If the "module" is actually a pathname, then automatically derive the proper module name
    if (strpos($module, '/') !== false) {
        $modulepath = split('/', $module);

        switch ($modulepath[0]) {

            case 'mod':
                $module = $modulepath[1];
            break;

            case 'blocks':
            case 'block':
                $module = 'block_'.$modulepath[1];
            break;

            case 'enrol':
                $module = 'enrol_'.$modulepath[1];
            break;

            case 'format':
                $module = 'format_'.$modulepath[1];
            break;

            case 'grade':
                $module = 'grade'.$modulepath[1].'_'.$modulepath[2];
            break;
        }
    }

/// if $a happens to have % in it, double it so sprintf() doesn't break
    if ($a) {
        $a = clean_getstring_data( $a );
    }

/// Define the two or three major locations of language strings for this module
    $locations = array();

    if (!empty($extralocations)) {   // Calling code has a good idea where to look
        if (is_array($extralocations)) {
            $locations += $extralocations;
        } else if (is_string($extralocations)) {
            $locations[] = $extralocations;
        } else {
            debugging('Bad lang path provided');
        }
    }

    if (isset($CFG->running_installer)) {
        $module = 'installer';
        $filetocheck = 'installer.php';
        $locations[] = $CFG->dirroot.'/install/lang/';
        $locations[] = $CFG->dataroot.'/lang/';
        $locations[] = $CFG->dirroot.'/lang/';
        $defaultlang = 'en_utf8';
    } else {
        $locations[] = $CFG->dataroot.'/lang/';
        $locations[] = $CFG->dirroot.'/lang/';
    }

/// Add extra places to look for strings for particular plugin types.
    $rules = places_to_search_for_lang_strings();
    $exceptions = $rules['__exceptions'];
    unset($rules['__exceptions']);

    if (!in_array($module, $exceptions)) {
        $dividerpos = strpos($module, '_');
        if ($dividerpos === false) {
            $type = '';
            $plugin = $module;
        } else {
            $type = substr($module, 0, $dividerpos + 1);
            $plugin = substr($module, $dividerpos + 1);
        }
        if ($module == 'local') {
            $locations[] = $CFG->dirroot . '/local/lang/';
        } if (!empty($rules[$type])) {
            foreach ($rules[$type] as $location) {
                $locations[] = $CFG->dirroot . "/$location/$plugin/lang/";
            }
        }
    }

/// First check all the normal locations for the string in the current language
    $resultstring = '';
    foreach ($locations as $location) {
        $locallangfile = $location.$lang.'_local'.'/'.$module.'.php';    //first, see if there's a local file
        if (file_exists($locallangfile)) {
            if ($result = get_string_from_file($identifier, $locallangfile, "\$resultstring")) {
                if (eval($result) === FALSE) {
                    trigger_error('Lang error: '.$identifier.':'.$locallangfile, E_USER_NOTICE);
                }
                return $resultstring;
            }
        }
        //if local directory not found, or particular string does not exist in local direcotry
        $langfile = $location.$lang.'/'.$module.'.php';
        if (file_exists($langfile)) {
            if ($result = get_string_from_file($identifier, $langfile, "\$resultstring")) {
                if (eval($result) === FALSE) {
                    trigger_error('Lang error: '.$identifier.':'.$langfile, E_USER_NOTICE);
                }
                return $resultstring;
            }
       }
    }

/// If the preferred language was English (utf8) we can abort now
/// saving some checks beacuse it's the only "root" lang
    if ($lang == 'en_utf8') {
        return '[['. $identifier .']]';
    }

/// Is a parent language defined?  If so, try to find this string in a parent language file

    foreach ($locations as $location) {
        $langfile = $location.$lang.'/'.$filetocheck;
        if (file_exists($langfile)) {
            if ($result = get_string_from_file('parentlanguage', $langfile, "\$parentlang")) {
                if (eval($result) === FALSE) {
                    trigger_error('Lang error: '.$identifier.':'.$langfile, E_USER_NOTICE);
                }
                if (!empty($parentlang)) {   // found it!

                    //first, see if there's a local file for parent
                    $locallangfile = $location.$parentlang.'_local'.'/'.$module.'.php';
                    if (file_exists($locallangfile)) {
                        if ($result = get_string_from_file($identifier, $locallangfile, "\$resultstring")) {
                            if (eval($result) === FALSE) {
                                trigger_error('Lang error: '.$identifier.':'.$locallangfile, E_USER_NOTICE);
                            }
                            return $resultstring;
                        }
                    }

                    //if local directory not found, or particular string does not exist in local direcotry
                    $langfile = $location.$parentlang.'/'.$module.'.php';
                    if (file_exists($langfile)) {
                        if ($result = get_string_from_file($identifier, $langfile, "\$resultstring")) {
                            eval($result);
                            return $resultstring;
                        }
                    }
                }
            }
        }
    }

/// Our only remaining option is to try English

    foreach ($locations as $location) {
        $locallangfile = $location.$defaultlang.'_local/'.$module.'.php';    //first, see if there's a local file
        if (file_exists($locallangfile)) {
            if ($result = get_string_from_file($identifier, $locallangfile, "\$resultstring")) {
                eval($result);
                return $resultstring;
            }
        }

        //if local_en not found, or string not found in local_en
        $langfile = $location.$defaultlang.'/'.$module.'.php';

        if (file_exists($langfile)) {
            if ($result = get_string_from_file($identifier, $langfile, "\$resultstring")) {
                eval($result);
                return $resultstring;
            }
        }
    }

/// And, because under 1.6 en is defined as en_utf8 child, me must try
/// if it hasn't been queried before.
    if ($defaultlang  == 'en') {
        $defaultlang = 'en_utf8';
        foreach ($locations as $location) {
            $locallangfile = $location.$defaultlang.'_local/'.$module.'.php';    //first, see if there's a local file
            if (file_exists($locallangfile)) {
                if ($result = get_string_from_file($identifier, $locallangfile, "\$resultstring")) {
                    eval($result);
                    return $resultstring;
                }
            }

            //if local_en not found, or string not found in local_en
            $langfile = $location.$defaultlang.'/'.$module.'.php';

            if (file_exists($langfile)) {
                if ($result = get_string_from_file($identifier, $langfile, "\$resultstring")) {
                    eval($result);
                    return $resultstring;
                }
            }
        }
    }

    return '[['.$identifier.']]';  // Last resort
}

/**
 * This function is only used from {@link get_string()}.
 *
 * @internal Only used from get_string, not meant to be public API
 * @param string $identifier ?
 * @param string $langfile ?
 * @param string $destination ?
 * @return string|false ?
 * @staticvar array $strings Localized strings
 * @access private
 * @todo Finish documenting this function.
 */
function get_string_from_file($identifier, $langfile, $destination) {

    static $strings;    // Keep the strings cached in memory.

    if (empty($strings[$langfile])) {
        $string = array();
        include ($langfile);
        $strings[$langfile] = $string;
    } else {
        $string = &$strings[$langfile];
    }

    if (!isset ($string[$identifier])) {
        return false;
    }

    return $destination .'= sprintf("'. $string[$identifier] .'");';
}

/**
 * Returns a list of language codes and their full names
 * hides the _local files from everyone.
 * @param bool refreshcache force refreshing of lang cache
 * @param bool returnall ignore langlist, return all languages available
 * @return array An associative array with contents in the form of LanguageCode => LanguageName
 */
function get_list_of_languages($refreshcache=false, $returnall=false) {

    global $CFG;

    $languages = array();

    $filetocheck = 'langconfig.php';

    if (!$refreshcache && !$returnall && !empty($CFG->langcache) && file_exists($CFG->dataroot .'/cache/languages')) {
/// read available langs from cache

        $lines = file($CFG->dataroot .'/cache/languages');
        foreach ($lines as $line) {
            $line = trim($line);
            if (preg_match('/^(\w+)\s+(.+)/', $line, $matches)) {
                $languages[$matches[1]] = $matches[2];
            }
        }
        unset($lines); unset($line); unset($matches);
        return $languages;
    }

    if (!$returnall && !empty($CFG->langlist)) {
/// return only languages allowed in langlist admin setting

        $langlist = explode(',', $CFG->langlist);
        // fix short lang names first - non existing langs are skipped anyway...
        foreach ($langlist as $lang) {
            if (strpos($lang, '_utf8') === false) {
                $langlist[] = $lang.'_utf8';
            }
        }
        // find existing langs from langlist
        foreach ($langlist as $lang) {
            $lang = trim($lang);   //Just trim spaces to be a bit more permissive
            if (strstr($lang, '_local')!==false) {
                continue;
            }
            if (substr($lang, -5) == '_utf8') {   //Remove the _utf8 suffix from the lang to show
                $shortlang = substr($lang, 0, -5);
            } else {
                $shortlang = $lang;
            }
        /// Search under dirroot/lang
            if (file_exists($CFG->dirroot .'/lang/'. $lang .'/'. $filetocheck)) {
                include($CFG->dirroot .'/lang/'. $lang .'/'. $filetocheck);
                if (!empty($string['thislanguage'])) {
                    $languages[$lang] = $string['thislanguage'].' ('. $shortlang .')';
                }
                unset($string);
            }
        /// And moodledata/lang
            if (file_exists($CFG->dataroot .'/lang/'. $lang .'/'. $filetocheck)) {
                include($CFG->dataroot .'/lang/'. $lang .'/'. $filetocheck);
                if (!empty($string['thislanguage'])) {
                    $languages[$lang] = $string['thislanguage'].' ('. $shortlang .')';
                }
                unset($string);
            }
        }

    } else {
/// return all languages available in system
    /// Fetch langs from moodle/lang directory
        $langdirs = get_list_of_plugins('lang');
    /// Fetch langs from moodledata/lang directory
        $langdirs2 = get_list_of_plugins('lang', '', $CFG->dataroot);
    /// Merge both lists of langs
        $langdirs = array_merge($langdirs, $langdirs2);
    /// Sort all
        asort($langdirs);
    /// Get some info from each lang (first from moodledata, then from moodle)
        foreach ($langdirs as $lang) {
            if (strstr($lang, '_local')!==false) {
                continue;
            }
            if (substr($lang, -5) == '_utf8') {   //Remove the _utf8 suffix from the lang to show
                $shortlang = substr($lang, 0, -5);
            } else {
                $shortlang = $lang;
            }
        /// Search under moodledata/lang
            if (file_exists($CFG->dataroot .'/lang/'. $lang .'/'. $filetocheck)) {
                include($CFG->dataroot .'/lang/'. $lang .'/'. $filetocheck);
                if (!empty($string['thislanguage'])) {
                    $languages[$lang] = $string['thislanguage'] .' ('. $shortlang .')';
                }
                unset($string);
            }
        /// And dirroot/lang
            if (file_exists($CFG->dirroot .'/lang/'. $lang .'/'. $filetocheck)) {
                include($CFG->dirroot .'/lang/'. $lang .'/'. $filetocheck);
                if (!empty($string['thislanguage'])) {
                    $languages[$lang] = $string['thislanguage'] .' ('. $shortlang .')';
                }
                unset($string);
            }
        }
    }

    if ($refreshcache && !empty($CFG->langcache)) {
        if ($returnall) {
            // we have a list of all langs only, just delete old cache
            @unlink($CFG->dataroot.'/cache/languages');

        } else {
            // store the list of allowed languages
            if ($file = fopen($CFG->dataroot .'/cache/languages', 'w')) {
                foreach ($languages as $key => $value) {
                    fwrite($file, "$key $value\n");
                }
                fclose($file);
            }
        }
    }

    return $languages;
}

/**
 * Returns a list of charset codes. It's hardcoded, so they should be added manually
 * (cheking that such charset is supported by the texlib library!)
 *
 * @return array And associative array with contents in the form of charset => charset
 */
function get_list_of_charsets() {

    $charsets = array(
        'EUC-JP'     => 'EUC-JP',
        'ISO-2022-JP'=> 'ISO-2022-JP',
        'ISO-8859-1' => 'ISO-8859-1',
        'SHIFT-JIS'  => 'SHIFT-JIS',
        'GB2312'     => 'GB2312',
        'GB18030'    => 'GB18030', // gb18030 not supported by typo and mbstring
        'UTF-8'      => 'UTF-8');

    asort($charsets);

    return $charsets;
}

/**
 * Returns a list of picture names in the current or specified language
 *
 * @uses $CFG
 * @return array
 */
function get_list_of_pixnames($lang = '') {
    global $CFG;

    if (empty($lang)) {
        $lang = current_language();
    }

    $string = array();

    $path = $CFG->dirroot .'/lang/en_utf8/pix.php'; // always exists

    if (file_exists($CFG->dataroot .'/lang/'. $lang .'_local/pix.php')) {
        $path = $CFG->dataroot .'/lang/'. $lang .'_local/pix.php';

    } else if (file_exists($CFG->dirroot .'/lang/'. $lang .'/pix.php')) {
        $path = $CFG->dirroot .'/lang/'. $lang .'/pix.php';

    } else if (file_exists($CFG->dataroot .'/lang/'. $lang .'/pix.php')) {
        $path = $CFG->dataroot .'/lang/'. $lang .'/pix.php';

    } else if ($parentlang = get_string('parentlanguage') and $parentlang != '[[parentlanguage]]') {
        return get_list_of_pixnames($parentlang); //return pixnames from parent language instead
    }

    include($path);

    return $string;
}

/// ENCRYPTION  ////////////////////////////////////////////////

/**
 * rc4encrypt
 *
 * @param string $data ?
 * @return string
 * @todo Finish documenting this function
 */
function rc4encrypt($data) {
    $password = 'nfgjeingjk';
    return endecrypt($password, $data, '');
}

/**
 * Based on a class by Mukul Sabharwal [mukulsabharwal @ yahoo.com]
 *
 * @param string $pwd ?
 * @param string $data ?
 * @param string $case ?
 * @return string
 * @todo Finish documenting this function
 */
function endecrypt ($pwd, $data, $case) {

    if ($case == 'de') {
        $data = urldecode($data);
    }

    $key[] = '';
    $box[] = '';
    $temp_swap = '';
    $pwd_length = 0;

    $pwd_length = strlen($pwd);

    for ($i = 0; $i <= 255; $i++) {
        $key[$i] = ord(substr($pwd, ($i % $pwd_length), 1));
        $box[$i] = $i;
    }

    $x = 0;

    for ($i = 0; $i <= 255; $i++) {
        $x = ($x + $box[$i] + $key[$i]) % 256;
        $temp_swap = $box[$i];
        $box[$i] = $box[$x];
        $box[$x] = $temp_swap;
    }

    $temp = '';
    $k = '';

    $cipherby = '';
    $cipher = '';

    $a = 0;
    $j = 0;

    for ($i = 0; $i < strlen($data); $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $temp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $temp;
        $k = $box[(($box[$a] + $box[$j]) % 256)];
        $cipherby = ord(substr($data, $i, 1)) ^ $k;
        $cipher .= chr($cipherby);
    }

    if ($case == 'de') {
        $cipher = urldecode(urlencode($cipher));
    } else {
        $cipher = urlencode($cipher);
    }

    return $cipher;
}

/// ENVIRONMENT CHECKING  ////////////////////////////////////////////////////////////

/**
 * Lists plugin directories within some directory
 *
 * @uses $CFG
 * @param string $plugin dir under we'll look for plugins (defaults to 'mod')
 * @param string $exclude dir name to exclude from the list (defaults to none)
 * @param string $basedir full path to the base dir where $plugin resides (defaults to $CFG->dirroot)
 * @return array of plugins found under the requested parameters
 */
function get_list_of_plugins($plugin='mod', $exclude='', $basedir='') {

    global $CFG;

    $plugins = array();

    if (empty($basedir)) {

        # This switch allows us to use the appropiate theme directory - and potentialy alternatives for other plugins
        switch ($plugin) {
        case "theme":
            $basedir = $CFG->themedir;
            break;

        default:
            $basedir = $CFG->dirroot .'/'. $plugin;
        }

    } else {
        $basedir = $basedir .'/'. $plugin;
    }

    if (file_exists($basedir) && filetype($basedir) == 'dir') {
        $dirhandle = opendir($basedir);
        while (false !== ($dir = readdir($dirhandle))) {
            $firstchar = substr($dir, 0, 1);
            if ($firstchar == '.' or $dir == 'CVS' or $dir == '_vti_cnf' or $dir == 'simpletest' or $dir == $exclude) {
                continue;
            }
            if (filetype($basedir .'/'. $dir) != 'dir') {
                continue;
            }
            $plugins[] = $dir;
        }
        closedir($dirhandle);
    }
    if ($plugins) {
        asort($plugins);
    }
    return $plugins;
}

/**
 * Returns true if the current version of PHP is greater that the specified one.
 *
 * @param string $version The version of php being tested.
 * @return bool
 */
function check_php_version($version='4.1.0') {
    return (version_compare(phpversion(), $version) >= 0);
}

/**
 * Checks to see if is the browser operating system matches the specified
 * brand.
 *
 * Known brand: 'Windows','Linux','Macintosh','SGI','SunOS','HP-UX'
 *
 * @uses $_SERVER
 * @param string $brand The operating system identifier being tested
 * @return bool true if the given brand below to the detected operating system
 */
 function check_browser_operating_system($brand) {
    if (empty($_SERVER['HTTP_USER_AGENT'])) {
        return false;
    }

    if (preg_match("/$brand/i", $_SERVER['HTTP_USER_AGENT'])) {
        return true;
    }

    return false;
 }

/**
 * Checks to see if is a browser matches the specified
 * brand and is equal or better version.
 *
 * @uses $_SERVER
 * @param string $brand The browser identifier being tested
 * @param int $version The version of the browser
 * @return bool true if the given version is below that of the detected browser
 */
 function check_browser_version($brand='MSIE', $version=5.5) {
    if (empty($_SERVER['HTTP_USER_AGENT'])) {
        return false;
    }

    $agent = $_SERVER['HTTP_USER_AGENT'];

    switch ($brand) {

      case 'Camino':   /// Mozilla Firefox browsers

              if (preg_match("/Camino\/([0-9\.]+)/i", $agent, $match)) {
                  if (version_compare($match[1], $version) >= 0) {
                      return true;
                  }
              }
              break;


      case 'Firefox':   /// Mozilla Firefox browsers

          if (preg_match("/Firefox\/([0-9\.]+)/i", $agent, $match)) {
              if (version_compare($match[1], $version) >= 0) {
                  return true;
              }
          }
          break;


      case 'Gecko':   /// Gecko based browsers

          if (substr_count($agent, 'Camino')) {
              // MacOS X Camino support
              $version = 20041110;
          }

          // the proper string - Gecko/CCYYMMDD Vendor/Version
          // Faster version and work-a-round No IDN problem.
          if (preg_match("/Gecko\/([0-9]+)/i", $agent, $match)) {
              if ($match[1] > $version) {
                      return true;
                  }
              }
          break;


      case 'MSIE':   /// Internet Explorer

          if (strpos($agent, 'Opera')) {     // Reject Opera
              return false;
          }
          $string = explode(';', $agent);
          if (!isset($string[1])) {
              return false;
          }
          $string = explode(' ', trim($string[1]));
          if (!isset($string[0]) and !isset($string[1])) {
              return false;
          }
          if ($string[0] == $brand and (float)$string[1] >= $version ) {
              return true;
          }
          break;

      case 'Opera':  /// Opera

          if (preg_match("/Opera\/([0-9\.]+)/i", $agent, $match)) {
              if (version_compare($match[1], $version) >= 0) {
                  return true;
              }
          }
          break;

      case 'Safari':  /// Safari
          // Look for AppleWebKit, excluding strings with OmniWeb, Shiira and SimbianOS
          if (strpos($agent, 'OmniWeb')) { // Reject OmniWeb
              return false;
          } elseif (strpos($agent, 'Shiira')) { // Reject Shiira
              return false;
          } elseif (strpos($agent, 'SimbianOS')) { // Reject SimbianOS
              return false;
          }

          if (preg_match("/AppleWebKit\/([0-9]+)/i", $agent, $match)) {
              if (version_compare($match[1], $version) >= 0) {
                  return true;
              }
          }

          break;

    }

    return false;
}

/**
 * This function makes the return value of ini_get consistent if you are
 * setting server directives through the .htaccess file in apache.
 * Current behavior for value set from php.ini On = 1, Off = [blank]
 * Current behavior for value set from .htaccess On = On, Off = Off
 * Contributed by jdell @ unr.edu
 *
 * @param string $ini_get_arg ?
 * @return bool
 * @todo Finish documenting this function
 */
function ini_get_bool($ini_get_arg) {
    $temp = ini_get($ini_get_arg);

    if ($temp == '1' or strtolower($temp) == 'on') {
        return true;
    }
    return false;
}

/**
 * Hack to find out the GD version by parsing phpinfo output
 *
 * @return int GD version (1, 2, or 0)
 */
function check_gd_version() {
    $gdversion = 0;

    if (function_exists('gd_info')){
        $gd_info = gd_info();
        if (substr_count($gd_info['GD Version'], '2.')) {
            $gdversion = 2;
        } else if (substr_count($gd_info['GD Version'], '1.')) {
            $gdversion = 1;
        }

    } else {
        ob_start();
        phpinfo(INFO_MODULES);
        $phpinfo = ob_get_contents();
        ob_end_clean();

        $phpinfo = explode("\n", $phpinfo);


        foreach ($phpinfo as $text) {
            $parts = explode('</td>', $text);
            foreach ($parts as $key => $val) {
                $parts[$key] = trim(strip_tags($val));
            }
            if ($parts[0] == 'GD Version') {
                if (substr_count($parts[1], '2.0')) {
                    $parts[1] = '2.0';
                }
                $gdversion = intval($parts[1]);
            }
        }
    }

    return $gdversion;   // 1, 2 or 0
}

/// MISCELLANEOUS ////////////////////////////////////////////////////////////////////

/**
 * moodle_setlocale
 *
 * @uses $CFG
 * @param string $locale ?
 * @todo Finish documenting this function
 */
function moodle_setlocale($locale='') {

    global $CFG;

    static $currentlocale = ''; // last locale caching

    $oldlocale = $currentlocale;

/// Fetch the correct locale based on ostype
    if($CFG->ostype == 'WINDOWS') {
        $stringtofetch = 'localewin';
    } else {
        $stringtofetch = 'locale';
    }

/// the priority is the same as in get_string() - parameter, config, course, session, user, global language
    if (!empty($locale)) {
        $currentlocale = $locale;
    } else if (!empty($CFG->locale)) { // override locale for all language packs
        $currentlocale = $CFG->locale;
    } else {
        $currentlocale = get_string($stringtofetch);
    }

/// do nothing if locale already set up
    if ($oldlocale == $currentlocale) {
        return;
    }

/// Due to some strange BUG we cannot set the LC_TIME directly, so we fetch current values,
/// set LC_ALL and then set values again. Just wondering why we cannot set LC_ALL only??? - stronk7
/// Some day, numeric, monetary and other categories should be set too, I think. :-/

/// Get current values
    $monetary= setlocale (LC_MONETARY, 0);
    $numeric = setlocale (LC_NUMERIC, 0);
    $ctype   = setlocale (LC_CTYPE, 0);
    if ($CFG->ostype != 'WINDOWS') {
        $messages= setlocale (LC_MESSAGES, 0);
    }
/// Set locale to all
    setlocale (LC_ALL, $currentlocale);
/// Set old values
    setlocale (LC_MONETARY, $monetary);
    setlocale (LC_NUMERIC, $numeric);
    if ($CFG->ostype != 'WINDOWS') {
        setlocale (LC_MESSAGES, $messages);
    }
    if ($currentlocale == 'tr_TR' or $currentlocale == 'tr_TR.UTF-8') { // To workaround a well-known PHP problem with Turkish letter Ii
        setlocale (LC_CTYPE, $ctype);
    }
}

/**
 * Converts string to lowercase using most compatible function available.
 *
 * @param string $string The string to convert to all lowercase characters.
 * @param string $encoding The encoding on the string.
 * @return string
 * @todo Add examples of calling this function with/without encoding types
 * @deprecated Use textlib->strtolower($text) instead.
 */
function moodle_strtolower ($string, $encoding='') {

    //If not specified use utf8
    if (empty($encoding)) {
        $encoding = 'UTF-8';
    }
    //Use text services
    $textlib = textlib_get_instance();

    return $textlib->strtolower($string, $encoding);
}

/**
 * Generate and return a random string of the specified length.
 *
 * @param int $length The length of the string to be created.
 * @return string
 */
function random_string ($length=15) {
    $pool  = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $pool .= 'abcdefghijklmnopqrstuvwxyz';
    $pool .= '0123456789';
    $poollen = strlen($pool);
    mt_srand ((double) microtime() * 1000000);
    $string = '';
    for ($i = 0; $i < $length; $i++) {
        $string .= substr($pool, (mt_rand()%($poollen)), 1);
    }
    return $string;
}

/**
 * microtime_diff
 *
 * @param string $a ?
 * @param string $b ?
 * @return string
 * @todo Finish documenting this function
 */
function microtime_diff($a, $b) {
    list($a_dec, $a_sec) = explode(' ', $a);
    list($b_dec, $b_sec) = explode(' ', $b);
    return $b_sec - $a_sec + $b_dec - $a_dec;
}

/**
 * For outputting debugging info
 *
 * @uses STDOUT
 * @param string $string ?
 * @param string $eol ?
 * @todo Finish documenting this function
 */
function mtrace($string, $eol="\n", $sleep=0) {

    if (defined('STDOUT')) {
        fwrite(STDOUT, $string.$eol);
    } else {
        echo $string . $eol;
    }

    flush();

    //delay to keep message on user's screen in case of subsequent redirect
    if ($sleep) {
        sleep($sleep);
    }
}

//Replace 1 or more slashes or backslashes to 1 slash
function cleardoubleslashes ($path) {
    return preg_replace('/(\/|\\\){1,}/','/',$path);
}

function unzip_file ($zipfile, $destination = '', $showstatus = true) {
//Unzip one zip file to a destination dir
//Both parameters must be FULL paths
//If destination isn't specified, it will be the
//SAME directory where the zip file resides.

    global $CFG;

    //Extract everything from zipfile
    $path_parts = pathinfo(cleardoubleslashes($zipfile));
    $zippath = $path_parts["dirname"];       //The path of the zip file
    $zipfilename = $path_parts["basename"];  //The name of the zip file
    $extension = $path_parts["extension"];    //The extension of the file

    //If no file, error
    if (empty($zipfilename)) {
        return false;
    }

    //If no extension, error
    if (empty($extension)) {
        return false;
    }

    //Clear $zipfile
    $zipfile = cleardoubleslashes($zipfile);

    //Check zipfile exists
    if (!file_exists($zipfile)) {
        return false;
    }

    //If no destination, passed let's go with the same directory
    if (empty($destination)) {
        $destination = $zippath;
    }

    //Clear $destination
    $destpath = rtrim(cleardoubleslashes($destination), "/");

    //Check destination path exists
    if (!is_dir($destpath)) {
        return false;
    }

    //Check destination path is writable. TODO!!

    //Everything is ready:
    //    -$zippath is the path where the zip file resides (dir)
    //    -$zipfilename is the name of the zip file (without path)
    //    -$destpath is the destination path where the zip file will uncompressed (dir)

    $list = array();

    require_once("$CFG->libdir/filelib.php");

    do {
        $temppath = "$CFG->dataroot/temp/unzip/".random_string(10);
    } while (file_exists($temppath));
    if (!check_dir_exists($temppath, true, true)) {
        return false;
    }

    if (empty($CFG->unzip)) {    // Use built-in php-based unzip function

        include_once("$CFG->libdir/pclzip/pclzip.lib.php");
        $archive = new PclZip(cleardoubleslashes("$zippath/$zipfilename"));
        if (!$list = $archive->extract(PCLZIP_OPT_PATH, $temppath,
                                       PCLZIP_CB_PRE_EXTRACT, 'unzip_cleanfilename',
                                       PCLZIP_OPT_EXTRACT_DIR_RESTRICTION, $temppath)) {
            if (!empty($showstatus)) {
                notice($archive->errorInfo(true));
            }
            return false;
        }

    } else {                     // Use external unzip program

        $separator = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? ' &' : ' ;';
        $redirection = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? '' : ' 2>&1';

        $command = 'cd '.escapeshellarg($zippath).$separator.
                    escapeshellarg($CFG->unzip).' -o '.
                    escapeshellarg(cleardoubleslashes("$zippath/$zipfilename")).' -d '.
                    escapeshellarg($temppath).$redirection;
        //All converted to backslashes in WIN
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $command = str_replace('/','\\',$command);
        }
        Exec($command,$list);
    }

    unzip_process_temp_dir($temppath, $destpath);
    fulldelete($temppath);

    //Display some info about the unzip execution
    if ($showstatus) {
        unzip_show_status($list, $temppath, $destpath);
    }

    return true;
}

/**
 * Sanitize temporary unzipped files and move to target dir.
 * @param string $temppath path to temporary dir with unzip output
 * @param string $destpath destination path
 * @return void
 */
function unzip_process_temp_dir($temppath, $destpath) {
    global $CFG;

    $filepermissions = ($CFG->directorypermissions & 0666); // strip execute flags

    if (check_dir_exists($destpath, true, true)) {
        $currdir = opendir($temppath);
        while (false !== ($file = readdir($currdir))) {
            if ($file <> ".." && $file <> ".") {
                $fullfile = "$temppath/$file";
                if (is_link($fullfile)) {
                    //somebody tries to sneak in symbolik link - no way!
                    continue;
                }
                $cleanfile = clean_param($file, PARAM_FILE); // no dangerous chars
                if ($cleanfile === '') {
                    // invalid file name
                    continue;
                }
                if ($cleanfile !== $file and file_exists("$temppath/$cleanfile")) {
                    // eh, weird chars collision detected
                    continue;
                }
                $descfile = "$destpath/$cleanfile";
                if (is_dir($fullfile)) {
                    // recurse into subdirs
                    unzip_process_temp_dir($fullfile, $descfile);
                }
                if (is_file($fullfile)) {
                    // rename and move the file
                    if (file_exists($descfile)) {
                        //override existing files
                        unlink($descfile);
                    }
                    rename($fullfile, $descfile);
                    chmod($descfile, $filepermissions);
                }
            }
        }
        closedir($currdir);
    }
}

function unzip_show_status($list, $removepath, $removepath2) {
//This function shows the results of the unzip execution
//depending of the value of the $CFG->zip, results will be
//text or an array of files.

    global $CFG;

    if (empty($CFG->unzip)) {    // Use built-in php-based zip function
        $strname = get_string("name");
        $strsize = get_string("size");
        $strmodified = get_string("modified");
        $strstatus = get_string("status");
        echo "<table width=\"640\">";
        echo "<tr><th class=\"header\" scope=\"col\">$strname</th>";
        echo "<th class=\"header\" align=\"right\" scope=\"col\">$strsize</th>";
        echo "<th class=\"header\" align=\"right\" scope=\"col\">$strmodified</th>";
        echo "<th class=\"header\" align=\"right\" scope=\"col\">$strstatus</th></tr>";
        foreach ($list as $item) {
            echo "<tr>";
            $item['filename'] = str_replace(cleardoubleslashes($removepath).'/', "", $item['filename']);
            $item['filename'] = str_replace(cleardoubleslashes($removepath2).'/', "", $item['filename']);
            echo '<td align="left" style="white-space:nowrap ">'.s(clean_param($item['filename'], PARAM_PATH)).'</td>';
            if (! $item['folder']) {
                echo '<td align="right" style="white-space:nowrap ">'.display_size($item['size']).'</td>';
            } else {
                echo "<td>&nbsp;</td>";
            }
            $filedate  = userdate($item['mtime'], get_string("strftimedatetime"));
            echo '<td align="right" style="white-space:nowrap ">'.$filedate.'</td>';
            echo '<td align="right" style="white-space:nowrap ">'.$item['status'].'</td>';
            echo "</tr>";
        }
        echo "</table>";

    } else {                   // Use external zip program
        print_simple_box_start("center");
        echo "<pre>";
        foreach ($list as $item) {
            $item = str_replace(cleardoubleslashes($removepath.'/'), '', $item);
            $item = str_replace(cleardoubleslashes($removepath2.'/'), '', $item);
            echo s($item).'<br />';
        }
        echo "</pre>";
        print_simple_box_end();
    }
}

/**
 * Returns most reliable client address
 *
 * @return string The remote IP address
 */
define('GETREMOTEADDR_SKIP_HTTP_CLIENT_IP', '1');
define('GETREMOTEADDR_SKIP_HTTP_X_FORWARDED_FOR', '2');
function getremoteaddr() {
    global $CFG;

    if (empty($CFG->getremoteaddrconf)) {
        // This will happen, for example, before just after the upgrade, as the
        // user is redirected to the admin screen.
        $variablestoskip = 0;
    } else {
        $variablestoskip = $CFG->getremoteaddrconf;
    }
    if (!($variablestoskip & GETREMOTEADDR_SKIP_HTTP_CLIENT_IP)) {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return cleanremoteaddr($_SERVER['HTTP_CLIENT_IP']);
        }
    }
    if (!($variablestoskip & GETREMOTEADDR_SKIP_HTTP_X_FORWARDED_FOR)) {
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return cleanremoteaddr($_SERVER['HTTP_X_FORWARDED_FOR']);
        }
    }
    if (!empty($_SERVER['REMOTE_ADDR'])) {
        return cleanremoteaddr($_SERVER['REMOTE_ADDR']);
    } else {
        return null;
    }
}

/**
 * Cleans a remote address ready to put into the log table
 */
function cleanremoteaddr($addr) {
    $originaladdr = $addr;
    $matches = array();
    // first get all things that look like IP addresses.
    if (!preg_match_all('/(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})/',$addr,$matches,PREG_SET_ORDER)) {
        return '';
    }
    $goodmatches = array();
    $lanmatches = array();
    foreach ($matches as $match) {
        //        print_r($match);
        // check to make sure it's not an internal address.
        // the following are reserved for private lans...
        // 10.0.0.0 - 10.255.255.255
        // 172.16.0.0 - 172.31.255.255
        // 192.168.0.0 - 192.168.255.255
        // 169.254.0.0 -169.254.255.255
        $bits = explode('.',$match[0]);
        if (count($bits) != 4) {
            // weird, preg match shouldn't give us it.
            continue;
        }
        if (($bits[0] == 10)
            || ($bits[0] == 172 && $bits[1] >= 16 && $bits[1] <= 31)
            || ($bits[0] == 192 && $bits[1] == 168)
            || ($bits[0] == 169 && $bits[1] == 254)) {
            $lanmatches[] = $match[0];
            continue;
        }
        // finally, it's ok
        $goodmatches[] = $match[0];
    }
    if (!count($goodmatches)) {
        // perhaps we have a lan match, it's probably better to return that.
        if (!count($lanmatches)) {
            return '';
        } else {
            return array_pop($lanmatches);
        }
    }
    if (count($goodmatches) == 1) {
        return $goodmatches[0];
    }
    //Commented out following because there are so many, and it clogs the logs   MDL-13544
    //error_log("NOTICE: cleanremoteaddr gives us something funny: $originaladdr had ".count($goodmatches)." matches");

    // We need to return something, so return the first
    return array_pop($goodmatches);
}

/**
 * file_put_contents is only supported by php 5.0 and higher
 * so if it is not predefined, define it here
 *
 * @param $file full path of the file to write
 * @param $contents contents to be sent
 * @return number of bytes written (false on error)
 */
if(!function_exists('file_put_contents')) {
    function file_put_contents($file, $contents) {
        $result = false;
        if ($f = fopen($file, 'w')) {
            $result = fwrite($f, $contents);
            fclose($f);
        }
        return $result;
    }
}

/**
 * The clone keyword is only supported from PHP 5 onwards.
 * The behaviour of $obj2 = $obj1 differs fundamentally
 * between PHP 4 and PHP 5. In PHP 4 a copy of $obj1 was
 * created, in PHP 5 $obj1 is referenced. To create a copy
 * in PHP 5 the clone keyword was introduced. This function
 * simulates this behaviour for PHP < 5.0.0.
 * See also: http://mjtsai.com/blog/2004/07/15/php-5-object-references/
 *
 * Modified 2005-09-29 by Eloy (from Julian Sedding proposal)
 * Found a better implementation (more checks and possibilities) from PEAR:
 * http://cvs.php.net/co.php/pear/PHP_Compat/Compat/Function/clone.php
 *
 * @param object $obj
 * @return object
 */
if(!check_php_version('5.0.0')) {
// the eval is needed to prevent PHP 5 from getting a parse error!
eval('
    function clone($obj) {
    /// Sanity check
        if (!is_object($obj)) {
            user_error(\'clone() __clone method called on non-object\', E_USER_WARNING);
            return;
        }

    /// Use serialize/unserialize trick to deep copy the object
        $obj = unserialize(serialize($obj));

    /// If there is a __clone method call it on the "new" class
        if (method_exists($obj, \'__clone\')) {
            $obj->__clone();
        }

        return $obj;
    }

    // Supply the PHP5 function scandir() to older versions.
    function scandir($directory) {
        $files = array();
        if ($dh = opendir($directory)) {
            while (($file = readdir($dh)) !== false) {
               $files[] = $file;
            }
            closedir($dh);
        }
        return $files;
    }

    // Supply the PHP5 function array_combine() to older versions.
    function array_combine($keys, $values) {
        if (!is_array($keys) || !is_array($values) || count($keys) != count($values)) {
            return false;
        }
        reset($values);
        $result = array();
        foreach ($keys as $key) {
            $result[$key] = current($values);
            next($values);
        }
        return $result;
    }
');
}

/*
 * This function expects to called during shutdown
 * should be set via register_shutdown_function()
 * in lib/setup.php .
 *
 * Right now we do it only if we are under apache, to
 * make sure apache children that hog too much mem are
 * killed.
 *
 */
function moodle_request_shutdown() {

    global $CFG;

    // initially, we are only ever called under apache
    // but check just in case
    if (function_exists('apache_child_terminate')
        && function_exists('memory_get_usage')
        && ini_get_bool('child_terminate')) {
        if (empty($CFG->apachemaxmem)) {
            $CFG->apachemaxmem = 25000000; // default 25MiB
        }
        if (memory_get_usage() > (int)$CFG->apachemaxmem) {
            trigger_error('Mem usage over $CFG->apachemaxmem: marking child for reaping.');
            @apache_child_terminate();
        }
    }
    if (defined('MDL_PERF') || (!empty($CFG->perfdebug) and $CFG->perfdebug > 7)) {
        if (defined('MDL_PERFTOLOG')) {
            $perf = get_performance_info();
            error_log("PERF: " . $perf['txt']);
        }
        if (defined('MDL_PERFINC')) {
            $inc = get_included_files();
            $ts  = 0;
            foreach($inc as $f) {
                if (preg_match(':^/:', $f)) {
                    $fs  =  filesize($f);
                    $ts  += $fs;
                    $hfs =  display_size($fs);
                    error_log(substr($f,strlen($CFG->dirroot)) . " size: $fs ($hfs)"
                              , NULL, NULL, 0);
                } else {
                    error_log($f , NULL, NULL, 0);
                }
            }
            if ($ts > 0 ) {
                $hts = display_size($ts);
                error_log("Total size of files included: $ts ($hts)");
            }
        }
    }
}

/**
 * If new messages are waiting for the current user, then return
 * Javascript code to create a popup window
 *
 * @return string Javascript code
 */
function message_popup_window() {
    global $USER;

    $popuplimit = 30;     // Minimum seconds between popups

    if (!defined('MESSAGE_WINDOW')) {
        if (!empty($USER->id) and !isguestuser()) {
            if (!isset($USER->message_lastpopup)) {
                $USER->message_lastpopup = 0;
            }
            if ((time() - $USER->message_lastpopup) > $popuplimit) {  /// It's been long enough
                if (get_user_preferences('message_showmessagewindow', 1) == 1) {
                    if (count_records_select('message', 'useridto = \''.$USER->id.'\' AND timecreated > \''.$USER->message_lastpopup.'\'')) {
                        $USER->message_lastpopup = time();
                        return '<script type="text/javascript">'."\n//<![CDATA[\n openpopup('/message/index.php', 'message',
                        'menubar=0,location=0,scrollbars,status,resizable,width=400,height=500', 0);\n//]]>\n</script>";
                    }
                }
            }
        }
    }

    return '';
}

/**
 *** get_performance_info() pairs up with init_performance_info()
 *** loaded in setup.php. Returns an array with 'html' and 'txt'
 *** values ready for use, and each of the individual stats provided
 *** separately as well.
 ***
 **/
function get_performance_info() {
    global $CFG, $PERF, $rcache;

    $info = array();
    $info['html'] = '';         // holds userfriendly HTML representation
    $info['txt']  = me() . ' '; // holds log-friendly representation

    $info['realtime'] = microtime_diff($PERF->starttime, microtime());

    $info['html'] .= '<span class="timeused">'.$info['realtime'].' secs</span> ';
    $info['txt'] .= 'time: '.$info['realtime'].'s ';

    if (function_exists('memory_get_usage')) {
        $info['memory_total'] = memory_get_usage();
        $info['memory_growth'] = memory_get_usage() - $PERF->startmemory;
        $info['html'] .= '<span class="memoryused">RAM: '.display_size($info['memory_total']).'</span> ';
        $info['txt']  .= 'memory_total: '.$info['memory_total'].'B (' . display_size($info['memory_total']).') memory_growth: '.$info['memory_growth'].'B ('.display_size($info['memory_growth']).') ';
    }

    if (function_exists('memory_get_peak_usage')) {
        $info['memory_peak'] = memory_get_peak_usage();
        $info['html'] .= '<span class="memoryused">RAM peak: '.display_size($info['memory_peak']).'</span> ';
        $info['txt']  .= 'memory_peak: '.$info['memory_peak'].'B (' . display_size($info['memory_peak']).') ';
    }

    $inc = get_included_files();
    //error_log(print_r($inc,1));
    $info['includecount'] = count($inc);
    $info['html'] .= '<span class="included">Included '.$info['includecount'].' files</span> ';
    $info['txt']  .= 'includecount: '.$info['includecount'].' ';

    if (!empty($PERF->dbqueries)) {
        $info['dbqueries'] = $PERF->dbqueries;
        $info['html'] .= '<span class="dbqueries">DB queries '.$info['dbqueries'].'</span> ';
        $info['txt'] .= 'dbqueries: '.$info['dbqueries'].' ';
    }

    if (!empty($PERF->logwrites)) {
        $info['logwrites'] = $PERF->logwrites;
        $info['html'] .= '<span class="logwrites">Log writes '.$info['logwrites'].'</span> ';
        $info['txt'] .= 'logwrites: '.$info['logwrites'].' ';
    }

    if (!empty($PERF->profiling) && $PERF->profiling) {
        require_once($CFG->dirroot .'/lib/profilerlib.php');
        $info['html'] .= '<span class="profilinginfo">'.Profiler::get_profiling(array('-R')).'</span>';
    }

    if (function_exists('posix_times')) {
        $ptimes = posix_times();
        if (is_array($ptimes)) {
            foreach ($ptimes as $key => $val) {
                $info[$key] = $ptimes[$key] -  $PERF->startposixtimes[$key];
            }
            $info['html'] .= "<span class=\"posixtimes\">ticks: $info[ticks] user: $info[utime] sys: $info[stime] cuser: $info[cutime] csys: $info[cstime]</span> ";
            $info['txt'] .= "ticks: $info[ticks] user: $info[utime] sys: $info[stime] cuser: $info[cutime] csys: $info[cstime] ";
        }
    }

    // Grab the load average for the last minute
    // /proc will only work under some linux configurations
    // while uptime is there under MacOSX/Darwin and other unices
    if (is_readable('/proc/loadavg') && $loadavg = @file('/proc/loadavg')) {
        list($server_load) = explode(' ', $loadavg[0]);
        unset($loadavg);
    } else if ( function_exists('is_executable') && is_executable('/usr/bin/uptime') && $loadavg = `/usr/bin/uptime` ) {
        if (preg_match('/load averages?: (\d+[\.,:]\d+)/', $loadavg, $matches)) {
            $server_load = $matches[1];
        } else {
            trigger_error('Could not parse uptime output!');
        }
    }
    if (!empty($server_load)) {
        $info['serverload'] = $server_load;
        $info['html'] .= '<span class="serverload">Load average: '.$info['serverload'].'</span> ';
        $info['txt'] .= "serverload: {$info['serverload']} ";
    }

    if (isset($rcache->hits) && isset($rcache->misses)) {
        $info['rcachehits'] = $rcache->hits;
        $info['rcachemisses'] = $rcache->misses;
        $info['html'] .= '<span class="rcache">Record cache hit/miss ratio : '.
            "{$rcache->hits}/{$rcache->misses}</span> ";
        $info['txt'] .= 'rcache: '.
            "{$rcache->hits}/{$rcache->misses} ";
    }
    $info['html'] = '<div class="performanceinfo">'.$info['html'].'</div>';
    return $info;
}

/**
 * Delete directory or only it's content
 * @param string $dir directory path
 * @param bool $content_only
 * @return bool success, true also if dir does not exist
 */
function remove_dir($dir, $content_only=false) {
    if (!file_exists($dir)) {
        // nothing to do
        return true;
    }
    $handle = opendir($dir);
    $result = true;
    while (false!==($item = readdir($handle))) {
        if($item != '.' && $item != '..') {
            if(is_dir($dir.'/'.$item)) {
                $result = remove_dir($dir.'/'.$item) && $result;
            }else{
                $result = unlink($dir.'/'.$item) && $result;
            }
        }
    }
    closedir($handle);
    if ($content_only) {
        return $result;
    }
    return rmdir($dir); // if anything left the result will be false, noo need for && $result
}

/**
 * Function to check if a directory exists and optionally create it.
 *
 * @param string absolute directory path (must be under $CFG->dataroot)
 * @param boolean create directory if does not exist
 * @param boolean create directory recursively
 *
 * @return boolean true if directory exists or created
 */
function check_dir_exists($dir, $create=false, $recursive=false) {

    global $CFG;

    if (strstr(cleardoubleslashes($dir), cleardoubleslashes($CFG->dataroot.'/')) === false) {
        debugging('Warning. Wrong call to check_dir_exists(). $dir must be an absolute path under $CFG->dataroot ("' . $dir . '" is incorrect)', DEBUG_DEVELOPER);
    }

    $status = true;

    if(!is_dir($dir)) {
        if (!$create) {
            $status = false;
        } else {
            umask(0000);
            if ($recursive) {
            /// We are going to make it recursive under $CFG->dataroot only
            /// (will help sites running open_basedir security and others)
                $dir = str_replace(cleardoubleslashes($CFG->dataroot . '/'), '', cleardoubleslashes($dir));
            /// PHP 5.0 has recursive mkdir parameter, but 4.x does not :-(
                $dirs = explode('/', $dir); /// Extract path parts
            /// Iterate over each part with start point $CFG->dataroot
                $dir = $CFG->dataroot . '/';
                foreach ($dirs as $part) {
                    if ($part == '') {
                        continue;
                    }
                    $dir .= $part.'/';
                    if (!is_dir($dir)) {
                        if (!mkdir($dir, $CFG->directorypermissions)) {
                            $status = false;
                            break;
                        }
                    }
                }
            } else {
                $status = mkdir($dir, $CFG->directorypermissions);
            }
        }
    }
    return $status;
}

function report_session_error() {
    global $CFG, $FULLME;

    if (empty($CFG->lang)) {
        $CFG->lang = "en";
    }
    // Set up default theme and locale
    theme_setup();
    moodle_setlocale();

    //clear session cookies
    if (check_php_version('5.2.0')) {
        //PHP 5.2.0
        setcookie('MoodleSession'.$CFG->sessioncookie, '', time() - 3600, $CFG->sessioncookiepath, $CFG->sessioncookiedomain, $CFG->cookiesecure, $CFG->cookiehttponly);
        setcookie('MoodleSessionTest'.$CFG->sessioncookie, '', time() - 3600, $CFG->sessioncookiepath, $CFG->sessioncookiedomain, $CFG->cookiesecure, $CFG->cookiehttponly);
    } else {
        setcookie('MoodleSession'.$CFG->sessioncookie, '', time() - 3600, $CFG->sessioncookiepath, $CFG->sessioncookiedomain, $CFG->cookiesecure);
        setcookie('MoodleSessionTest'.$CFG->sessioncookie, '', time() - 3600, $CFG->sessioncookiepath, $CFG->sessioncookiedomain, $CFG->cookiesecure);
    }
    //increment database error counters
    if (isset($CFG->session_error_counter)) {
        set_config('session_error_counter', 1 + $CFG->session_error_counter);
    } else {
        set_config('session_error_counter', 1);
    }
    redirect($FULLME, get_string('sessionerroruser2', 'error'), 5);
}

/**
 * Detect a custom script replacement in the data directory that will
 * replace an existing moodle script
 * @param string $urlpath path to the original script
 * @return string full path name if a custom script exists
 * @return bool false if no custom script exists
 */
function custom_script_path($urlpath='') {
    global $CFG;

    // set default $urlpath, if necessary
    if (empty($urlpath)) {
        $urlpath = qualified_me(); // e.g. http://www.this-server.com/moodle/this-script.php
    }

    // $urlpath is invalid if it is empty or does not start with the Moodle wwwroot
    if (empty($urlpath) or (strpos($urlpath, $CFG->wwwroot) === false )) {
        return false;
    }

    // replace wwwroot with the path to the customscripts folder and clean path
    $scriptpath = $CFG->customscripts . clean_param(substr($urlpath, strlen($CFG->wwwroot)), PARAM_PATH);

    // remove the query string, if any
    if (($strpos = strpos($scriptpath, '?')) !== false) {
        $scriptpath = substr($scriptpath, 0, $strpos);
    }

    // remove trailing slashes, if any
    $scriptpath = rtrim($scriptpath, '/\\');

    // append index.php, if necessary
    if (is_dir($scriptpath)) {
        $scriptpath .= '/index.php';
    }

    // check the custom script exists
    if (file_exists($scriptpath)) {
        return $scriptpath;
    } else {
        return false;
    }
}

/**
 * Returns whether or not the user object is a remote MNET user. This function
 * is in moodlelib because it does not rely on loading any of the MNET code.
 *
 * @param object $user A valid user object
 * @return bool        True if the user is from a remote Moodle.
 */
function is_mnet_remote_user($user) {
    global $CFG;

    if (!isset($CFG->mnet_localhost_id)) {
        include_once $CFG->dirroot . '/mnet/lib.php';
        $env = new mnet_environment();
        $env->init();
        unset($env);
    }

    return (!empty($user->mnethostid) && $user->mnethostid != $CFG->mnet_localhost_id);
}

/**
 * This function will search for browser prefereed languages, setting Moodle
 * to use the best one available if $SESSION->lang is undefined
 */
function setup_lang_from_browser() {

    global $CFG, $SESSION, $USER;

    if (!empty($SESSION->lang) or !empty($USER->lang) or empty($CFG->autolang)) {
        // Lang is defined in session or user profile, nothing to do
        return;
    }

    if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) { // There isn't list of browser langs, nothing to do
        return;
    }

/// Extract and clean langs from headers
    $rawlangs = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
    $rawlangs = str_replace('-', '_', $rawlangs);         // we are using underscores
    $rawlangs = explode(',', $rawlangs);                  // Convert to array
    $langs = array();

    $order = 1.0;
    foreach ($rawlangs as $lang) {
        if (strpos($lang, ';') === false) {
            $langs[(string)$order] = $lang;
            $order = $order-0.01;
        } else {
            $parts = explode(';', $lang);
            $pos = strpos($parts[1], '=');
            $langs[substr($parts[1], $pos+1)] = $parts[0];
        }
    }
    krsort($langs, SORT_NUMERIC);

    $langlist = get_list_of_languages();

/// Look for such langs under standard locations
    foreach ($langs as $lang) {
        $lang = strtolower(clean_param($lang.'_utf8', PARAM_SAFEDIR)); // clean it properly for include
        if (!array_key_exists($lang, $langlist)) {
            continue; // language not allowed, try next one
        }
        if (file_exists($CFG->dataroot .'/lang/'. $lang) or file_exists($CFG->dirroot .'/lang/'. $lang)) {
            $SESSION->lang = $lang; /// Lang exists, set it in session
            break; /// We have finished. Go out
        }
    }
    return;
}

////////////////////////////////////////////////////////////////////////////////

function is_newnav($navigation) {
    if (is_array($navigation) && !empty($navigation['newnav'])) {
        return true;
    } else {
        return false;
    }
}

/**
 * @return string $CFG->siteidentifier, first making sure it is properly initialised.
 */
function get_site_identifier() {
    global $CFG;
    // Check to see if it is missing. If so, initialise it.
    if (empty($CFG->siteidentifier)) {
        set_config('siteidentifier', random_string(32) . $_SERVER['HTTP_HOST']);
    }
    // Return it.
    return $CFG->siteidentifier;
}

<?php

/// check if the application is already installed
if (!file_exists('../../../config.php')) {
    header('Location: ../../install.php');
    die;
}

/// load libraries
require_once('../../../config.php');

//initialize variables
$errormsg = '';

/// Check for timed out sessions
if (!empty($SESSION->has_timed_out)) {
    $session_has_timed_out = true;
    $SESSION->has_timed_out = false;
} else {
    $session_has_timed_out = false;
}

/// check if there are data to autheticate
if ($frm = data_submitted()) {
    $frm->username = trim(moodle_strtolower($frm->username));
    if (!$user = get_record('user', 'username', $frm->username, 'password', md5($frm->password))) {
        $errormsg = get_string("errorloggin");
    } else {
        $USER = complete_user_login($user);
        header ('Location:' . $CFG->wwwroot . '/application/admin/');
        exit;
    }
}

print_header($CFG->sitename, $CFG->sitename);

/// print page content header
print_container_start(true, 'content-header', 'content-header');
corner_left_top();
corner_left_bottom();
corner_right_top();
corner_right_bottom();
echo "<br>";
print_container_end();

/// print page content
print_container_start(true, 'content-body', 'content-body');
corner_left_top();
corner_left_bottom();
corner_right_top();
corner_right_bottom();

echo '
    <div class="loginform">' . $errormsg . '
    <br><br>
    <form action="login.php" method="post" id="login">
        <label for="username">' . get_string("username") . '</label><br>
        <input type="text" name="username" id="username" size="15" value="' . $frm->username . '" /><br><br>
        <label for="password">' . get_string("password") . '</label><br>
        <input type="password" name="password" id="password" size="15" value="" /><br><br>
        <input type="submit" class="boton_75" value="' . get_string("login") . '" />
        <input type="hidden" name="testcookies" value="1" />
    </form>
    </div>';

print_container_end();
print_footer();

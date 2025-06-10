<?php

require_once("../../../config.php");

$redirect = $CFG->wwwroot;

$sesskey = optional_param('sesskey', '__notpresent__', PARAM_RAW); // we want not null default to prevent required sesskey warning

if (!isloggedin()) {
    // no confirmation, user has already logged out
    require_logout();
    header ('Location:' . $CFG->wwwroot);
} else if (!confirm_sesskey($sesskey)) {
    print_header($CFG->sitename, $CFG->sitename);

    /// print page content header
    print_container_start(true, 'content-header', 'content-header');
    corner_left_top();
    corner_left_bottom();
    corner_right_top();
    corner_right_bottom();
    echo "<br>";
    print_container_end();

    /// print page content body
    print_container_start(true, 'content-body', 'content-body');
    corner_left_top();
    corner_left_bottom();
    corner_right_top();
    corner_right_bottom();
    notice_yesno(get_string('logoutconfirm'), 'logout.php', $redirect, array('sesskey' => sesskey()), null, 'post', 'get');
    print_container_end();
    print_footer();
}

$authsequence = get_enabled_auth_plugins(); // auths, in sequence

foreach ($authsequence as $authname) {
    $authplugin = get_auth_plugin($authname);
    $authplugin->logoutpage_hook();
}

require_logout();
header('Location: ' . $redirect);

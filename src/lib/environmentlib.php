<?php

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas     http://dougiamas.com  //
//           (C) 2001-3001 Eloy Lafuente (stronk7) http://contiento.com  //
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

require_once($CFG->libdir . '/xmlize.php');

/// Define a buch of XML processing errors
define('NO_ERROR', 0);
define('NO_VERSION_DATA_FOUND', 1);
define('NO_DATABASE_SECTION_FOUND', 2);
define('NO_DATABASE_VENDORS_FOUND', 3);
define('NO_DATABASE_VENDOR_MYSQL_FOUND', 4);
define('NO_DATABASE_VENDOR_POSTGRES_FOUND', 5);
define('NO_PHP_SECTION_FOUND', 6);
define('NO_PHP_VERSION_FOUND', 7);
define('NO_PHP_EXTENSIONS_SECTION_FOUND', 8);
define('NO_PHP_EXTENSIONS_NAME_FOUND', 9);
define('NO_DATABASE_VENDOR_VERSION_FOUND', 10);
define('NO_UNICODE_SECTION_FOUND', 11);
define('NO_CUSTOM_CHECK_FOUND', 12);
define('CUSTOM_CHECK_FILE_MISSING', 13);
define('CUSTOM_CHECK_FUNCTION_MISSING', 14);

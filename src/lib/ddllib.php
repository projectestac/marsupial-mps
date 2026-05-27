<?php

/// Add required XMLDB constants
require_once($CFG->libdir . '/xmldb/classes/XMLDBConstants.php');

/// Add main XMLDB Generator
require_once($CFG->libdir . '/xmldb/classes/generators/XMLDBGenerator.class.php');

/// Add required XMLDB DB classes
require_once($CFG->libdir . '/xmldb/classes/XMLDBObject.class.php');
require_once($CFG->libdir . '/xmldb/classes/XMLDBFile.class.php');
require_once($CFG->libdir . '/xmldb/classes/XMLDBStructure.class.php');
require_once($CFG->libdir . '/xmldb/classes/XMLDBTable.class.php');
require_once($CFG->libdir . '/xmldb/classes/XMLDBField.class.php');
require_once($CFG->libdir . '/xmldb/classes/XMLDBKey.class.php');
require_once($CFG->libdir . '/xmldb/classes/XMLDBIndex.class.php');
require_once($CFG->libdir . '/xmldb/classes/XMLDBStatement.class.php');

/// Based on $CFG->dbtype, add the proper generator class
if (!file_exists($CFG->libdir . '/xmldb/classes/generators/' . $CFG->dbtype . '/' . $CFG->dbtype . '.class.php')) {
    error('DB Type: ' . $CFG->dbtype . ' not supported by XMLDB');
}
require_once($CFG->libdir . '/xmldb/classes/generators/' . $CFG->dbtype . '/' . $CFG->dbtype . '.class.php');

/// Add other libraries
require_once($CFG->libdir . '/xmlize.php');
/**
 * Add a new field to a table, or modify an existing one (if oldfield is defined).
 *
 * WARNING: This function is deprecated and will be removed in future versions.
 * Please use XMLDB (see http://docs.moodle.org/en/Development:DDL_functions ).
 *
 * Warning: Please be careful on primary keys, as this function will eat auto_increments
 *
 * @param string $table the name of the table to modify. (Without the prefix.)
 * @param string $oldfield If changing an existing column, the name of that column.
 * @param string $field The name of the column at the end of the operation.
 * @param string $type The type of the column at the end of the operation. TEXT, VARCHAR, CHAR, INTEGER, REAL, or TINYINT
 * @param string $size The size of that column type. As in VARCHAR($size), or INTEGER($size).
 * @param string $signed For numeric column types, whether that column is 'signed' or 'unsigned'.
 * @param string $default The new default value for the column.
 * @param string $null 'not null', or '' to allow nulls.
 * @param string $after Which column to insert this one after. Not supported on Postgres.
 *
 * @return boolean Wheter the operation succeeded.
 * @uses $CFG
 * @uses $db
 */
function table_column($table, $oldfield, $field, $type = 'integer', $size = '10',
                      $signed = 'unsigned', $default = '0', $null = 'not null', $after = '')
{
    global $CFG, $db, $empty_rs_cache;

    if (!empty($empty_rs_cache[$table])) {  // Clear the recordset cache because it's out of date
        unset($empty_rs_cache[$table]);
    }

    switch (strtolower($CFG->dbtype)) {

        case 'mysql':
        case 'mysqlt':

            switch (strtolower($type)) {
                case 'text':
                    $type = 'TEXT';
                    $signed = '';
                    break;
                case 'integer':
                    $type = 'INTEGER(' . $size . ')';
                    break;
                case 'varchar':
                    $type = 'VARCHAR(' . $size . ')';
                    $signed = '';
                    break;
                case 'char':
                    $type = 'CHAR(' . $size . ')';
                    $signed = '';
                    break;
            }

            if (!empty($oldfield)) {
                $operation = 'CHANGE ' . $oldfield . ' ' . $field;
            } else {
                $operation = 'ADD ' . $field;
            }

            $default = 'DEFAULT \'' . $default . '\'';

            if (!empty($after)) {
                $after = 'AFTER `' . $after . '`';
            }

            return execute_sql('ALTER TABLE ' . $CFG->prefix . $table . ' ' . $operation . ' ' . $type . ' ' . $signed . ' ' . $default . ' ' . $null . ' ' . $after);

        case 'postgres7':        // From Petri Asikainen
            //Check db-version
            $dbinfo = $db->ServerInfo();
            $dbver = substr($dbinfo['version'], 0, 3);

            //to prevent conflicts with reserved words
            $realfield = '"' . $field . '"';
            $field = '"' . $field . '_alter_column_tmp"';
            $oldfield = '"' . $oldfield . '"';

            switch (strtolower($type)) {
                case 'tinyint':
                case 'integer':
                    if ($size <= 4) {
                        $type = 'INT2';
                    }
                    if ($size <= 10) {
                        $type = 'INT';
                    }
                    if ($size > 10) {
                        $type = 'INT8';
                    }
                    break;
                case 'varchar':
                    $type = 'VARCHAR(' . $size . ')';
                    break;
                case 'char':
                    $type = 'CHAR(' . $size . ')';
                    $signed = '';
                    break;
            }

            $default = '\'' . $default . '\'';

            //After is not implemented in postgesql
            //if (!empty($after)) {
            //    $after = "AFTER '$after'";
            //}

            //Use transactions
            execute_sql('BEGIN');

            //Always use temporary column
            execute_sql('ALTER TABLE ' . $CFG->prefix . $table . ' ADD COLUMN ' . $field . ' ' . $type);
            //Add default values
            execute_sql('UPDATE ' . $CFG->prefix . $table . ' SET ' . $field . '=' . $default);


            if ($dbver >= '7.3') {
                // modifying 'not null' is posible before 7.3
                //update default values to table
                if (strtoupper($null) == 'NOT NULL') {
                    execute_sql('UPDATE ' . $CFG->prefix . $table . ' SET ' . $field . '=' . $default . ' WHERE ' . $field . ' IS NULL');
                    execute_sql('ALTER TABLE ' . $CFG->prefix . $table . ' ALTER COLUMN ' . $field . ' SET ' . $null);
                } else {
                    execute_sql('ALTER TABLE ' . $CFG->prefix . $table . ' ALTER COLUMN ' . $field . ' DROP NOT NULL');
                }
            }

            execute_sql('ALTER TABLE ' . $CFG->prefix . $table . ' ALTER COLUMN ' . $field . ' SET DEFAULT ' . $default);

            if ($oldfield != '""') {

                // We are changing the type of a column. This may require doing some casts...
                $casting = '';
                $oldtype = column_type($table, $oldfield);
                $newtype = column_type($table, $field);

                // Do we need a cast?
                if ($newtype == 'N' && $oldtype == 'C') {
                    $casting = 'CAST(CAST(' . $oldfield . ' AS TEXT) AS REAL)';
                } else {
                    if ($newtype == 'I' && $oldtype == 'C') {
                        $casting = 'CAST(CAST(' . $oldfield . ' AS TEXT) AS INTEGER)';
                    } else {
                        $casting = $oldfield;
                    }
                }

                // Run the update query, casting as necessary
                execute_sql('UPDATE ' . $CFG->prefix . $table . ' SET ' . $field . ' = ' . $casting);
                execute_sql('ALTER TABLE  ' . $CFG->prefix . $table . ' DROP COLUMN ' . $oldfield);
            }

            execute_sql('ALTER TABLE ' . $CFG->prefix . $table . ' RENAME COLUMN ' . $field . ' TO ' . $realfield);

            return execute_sql('COMMIT');

        default:
            switch (strtolower($type)) {
                case 'integer':
                    $type = 'INTEGER';
                    break;
                case 'varchar':
                    $type = 'VARCHAR';
                    break;
            }

            $default = 'DEFAULT \'' . $default . '\'';

            if (!empty($after)) {
                $after = 'AFTER ' . $after;
            }

            if (!empty($oldfield)) {
                execute_sql('ALTER TABLE ' . $CFG->prefix . $table . ' RENAME COLUMN ' . $oldfield . ' ' . $field);
            } else {
                execute_sql('ALTER TABLE ' . $CFG->prefix . $table . ' ADD COLUMN ' . $field . ' ' . $type);
            }

            execute_sql('ALTER TABLE ' . $CFG->prefix . $table . ' ALTER COLUMN ' . $field . ' SET ' . $null);
            return execute_sql('ALTER TABLE ' . $CFG->prefix . $table . ' ALTER COLUMN ' . $field . ' SET ' . $default);
    }
}

/**
 * Given one XMLDBTable, check if it exists in DB (true/false)
 *
 * @param XMLDBTable table to be searched for
 * @return boolean true/false
 */
function table_exists($table)
{

    global $CFG, $db;

    $exists = true;

/// Do this function silenty (to avoid output in install/upgrade process)
    $olddbdebug = $db->debug;
    $db->debug = false;

/// Load the needed generator
    $classname = 'XMLDB' . $CFG->dbtype;
    $generator = new $classname();
    $generator->setPrefix($CFG->prefix);
/// Calculate the name of the table
    $tablename = $generator->getTableName($table, false);

/// Search such tablename in DB
    $metatables = $db->MetaTables();
    $metatables = array_flip($metatables);
    $metatables = array_change_key_case($metatables, CASE_LOWER);
    if (!array_key_exists($tablename, $metatables)) {
        $exists = false;
    }

/// Re-set original debug 
    $db->debug = $olddbdebug;

    return $exists;
}

/**
 * Given one XMLDBField, check if it exists in DB (true/false)
 *
 * @param XMLDBTable the table
 * @param XMLDBField the field to be searched for
 * @return boolean true/false
 * @uses, $db
 */
function field_exists($table, $field)
{

    global $CFG, $db;

    $exists = true;

/// Do this function silenty (to avoid output in install/upgrade process)
    $olddbdebug = $db->debug;
    $db->debug = false;

/// Check the table exists
    if (!table_exists($table)) {
        $db->debug = $olddbdebug; //Re-set original $db->debug
        return false;
    }

/// Load the needed generator
    $classname = 'XMLDB' . $CFG->dbtype;
    $generator = new $classname();
    $generator->setPrefix($CFG->prefix);
/// Calculate the name of the table
    $tablename = $generator->getTableName($table, false);

/// Get list of fields in table
    $fields = null;
    if ($fields = $db->MetaColumns($tablename)) {
        $fields = array_change_key_case($fields, CASE_LOWER);
    }

    if (!array_key_exists($field->getName(), $fields)) {
        $exists = false;
    }

/// Re-set original debug
    $db->debug = $olddbdebug;

    return $exists;
}

/**
 * Given one XMLDBIndex, check if it exists in DB (true/false)
 *
 * @param XMLDBTable the table
 * @param XMLDBIndex the index to be searched for
 * @return boolean true/false
 * @uses, $db
 */
function index_exists($table, $index)
{

    global $CFG, $db;

    $exists = true;

/// Do this function silenty (to avoid output in install/upgrade process)
    $olddbdebug = $db->debug;
    $db->debug = false;

/// Wrap over find_index_name to see if the index exists
    if (!find_index_name($table, $index)) {
        $exists = false;
    }

/// Re-set original debug
    $db->debug = $olddbdebug;

    return $exists;
}

/**
 * This function IS NOT IMPLEMENTED. ONCE WE'LL BE USING RELATIONAL
 * INTEGRITY IT WILL BECOME MORE USEFUL. FOR NOW, JUST CALCULATE "OFFICIAL"
 * KEY NAMES WITHOUT ACCESSING TO DB AT ALL.
 * Given one XMLDBKey, the function returns the name of the key in DB (if exists)
 * of false if it doesn't exist
 *
 * @param XMLDBTable the table to be searched
 * @param XMLDBKey the key to be searched
 * @return string key name of false
 * @uses, $db
 */
function find_key_name($table, $xmldb_key)
{

    global $CFG, $db;

/// Extract key columns
    $keycolumns = $xmldb_key->getFields();

/// Get list of keys in table
/// first primaries (we aren't going to use this now, because the MetaPrimaryKeys is awful)
    ///TODO: To implement when we advance in relational integrity
/// then uniques (note that Moodle, for now, shouldn't have any UNIQUE KEY for now, but unique indexes)
    ///TODO: To implement when we advance in relational integrity (note that AdoDB hasn't any MetaXXX for this.
/// then foreign (note that Moodle, for now, shouldn't have any FOREIGN KEY for now, but indexes)
    ///TODO: To implement when we advance in relational integrity (note that AdoDB has one MetaForeignKeys()
    ///but it's far from perfect.
/// TODO: To create the proper functions inside each generator to retrieve all the needed KEY info (name
///       columns, reftable and refcolumns

/// So all we do is to return the official name of the requested key without any confirmation!)
    $classname = 'XMLDB' . $CFG->dbtype;
    $generator = new $classname();
    $generator->setPrefix($CFG->prefix);
/// One exception, harcoded primary constraint names
    if ($generator->primary_key_name && $xmldb_key->getType() == XMLDB_KEY_PRIMARY) {
        return $generator->primary_key_name;
    } else {
        /// Calculate the name suffix
        switch ($xmldb_key->getType()) {
            case XMLDB_KEY_PRIMARY:
                $suffix = 'pk';
                break;
            case XMLDB_KEY_UNIQUE:
                $suffix = 'uk';
                break;
            case XMLDB_KEY_FOREIGN_UNIQUE:
            case XMLDB_KEY_FOREIGN:
                $suffix = 'fk';
                break;
        }
        /// And simply, return the oficial name
        return $generator->getNameForObject($table->getName(), implode(', ', $xmldb_key->getFields()), $suffix);
    }
}

/**
 * Given one XMLDBIndex, the function returns the name of the index in DB (if exists)
 * of false if it doesn't exist
 *
 * @param XMLDBTable the table to be searched
 * @param XMLDBIndex the index to be searched
 * @return string index name of false
 * @uses, $db
 */
function find_index_name($table, $index)
{

    global $CFG, $db;

/// Do this function silenty (to avoid output in install/upgrade process)
    $olddbdebug = $db->debug;
    $db->debug = false;

/// Extract index columns
    $indcolumns = $index->getFields();

/// Check the table exists
    if (!table_exists($table)) {
        $db->debug = $olddbdebug; //Re-set original $db->debug
        return false;
    }

/// Load the needed generator
    $classname = 'XMLDB' . $CFG->dbtype;
    $generator = new $classname();
    $generator->setPrefix($CFG->prefix);
/// Calculate the name of the table
    $tablename = $generator->getTableName($table, false);

/// Get list of indexes in table
    $indexes = null;
    if ($indexes = $db->MetaIndexes($tablename)) {
        $indexes = array_change_key_case($indexes, CASE_LOWER);
    }

/// Iterate over them looking for columns coincidence
    if ($indexes) {
        foreach ($indexes as $indexname => $index) {
            $columns = $index['columns'];
            /// Lower case column names
            $columns = array_flip($columns);
            $columns = array_change_key_case($columns, CASE_LOWER);
            $columns = array_flip($columns);
            /// Check if index matchs queried index
            $diferences = array_merge(array_diff($columns, $indcolumns), array_diff($indcolumns, $columns));
            /// If no diferences, we have find the index
            if (empty($diferences)) {
                $db->debug = $olddbdebug; //Re-set original $db->debug
                return $indexname;
            }
        }
    }
/// Arriving here, index not found
    $db->debug = $olddbdebug; //Re-set original $db->debug
    return false;
}

/**
 * Given one XMLDBField, the function returns the name of the check constraint in DB (if exists)
 * of false if it doesn't exist. Note that XMLDB limits the number of check constrainst per field
 * to 1 "enum-like" constraint. So, if more than one is returned, only the first one will be
 * retrieved by this funcion.
 *
 * @param XMLDBTable the table to be searched
 * @param XMLDBField the field to be searched
 * @return string check consrtaint name or false
 * @uses, $db
 */
function find_check_constraint_name($table, $field)
{

    global $CFG, $db;

/// Do this function silenty (to avoid output in install/upgrade process)
    $olddbdebug = $db->debug;
    $db->debug = false;

/// Check the table exists
    if (!table_exists($table)) {
        $db->debug = $olddbdebug; //Re-set original $db->debug
        return false;
    }

/// Check the field exists
    if (!field_exists($table, $field)) {
        $db->debug = $olddbdebug; //Re-set original $db->debug
        return false;
    }

/// Load the needed generator
    $classname = 'XMLDB' . $CFG->dbtype;
    $generator = new $classname();
    $generator->setPrefix($CFG->prefix);
/// Calculate the name of the table
    $tablename = $generator->getTableName($table, false);

/// Get list of check_constraints in table/field
    $checks = null;
    if ($objchecks = $generator->getCheckConstraintsFromDB($table, $field)) {
        /// Get only the 1st element. Shouldn't be more than 1 under XMLDB
        $objcheck = array_shift($objchecks);
        if ($objcheck) {
            $checks = strtolower($objcheck->name);
        }
    }

/// Arriving here, check not found
    $db->debug = $olddbdebug; //Re-set original $db->debug
    return $checks;
}

/**
 * Given one XMLDBTable, the function returns the name of its sequence in DB (if exists)
 * of false if it doesn't exist
 *
 * @param XMLDBTable the table to be searched
 * @return string sequence name of false
 */
function find_sequence_name($table)
{

    global $CFG, $db;

    $sequencename = false;

/// Do this function silenty (to avoid output in install/upgrade process)
    $olddbdebug = $db->debug;
    $db->debug = false;

    if (strtolower(get_class($table)) != 'xmldbtable') {
        $db->debug = $olddbdebug; //Re-set original $db->debug
        return false;
    }

/// Check table exists
    if (!table_exists($table)) {
        debugging('Table ' . $table->getName() .
            ' does not exist. Sequence not found', DEBUG_DEVELOPER);
        $db->debug = $olddbdebug; //Re-set original $db->debug
        return false; //Table doesn't exist, nothing to do
    }

    $sequencename = $table->getSequenceFromDB($CFG->dbtype, $CFG->prefix);

    $db->debug = $olddbdebug; //Re-set original $db->debug
    return $sequencename;
}

/**
 * This function will create the table passed as argument with all its
 * fields/keys/indexes/sequences, everything based in the XMLDB object
 *
 * @param XMLDBTable table object (full specs are required)
 * @param boolean continue to specify if must continue on error (true) or stop (false)
 * @param boolean feedback to specify to show status info (true) or not (false)
 * @return boolean true on success, false on error
 * @uses $CFG, $db
 */
function create_table($table, $continue = true, $feedback = true)
{

    global $CFG, $db;

    $status = true;

    if (strtolower(get_class($table)) != 'xmldbtable') {
        return false;
    }

/// Check table doesn't exist
    if (table_exists($table)) {
        debugging('Table ' . $table->getName() .
            ' already exists. Create skipped', DEBUG_DEVELOPER);
        return true; //Table exists, nothing to do
    }

    if (!$sqlarr = $table->getCreateTableSQL($CFG->dbtype, $CFG->prefix, false)) {
        return true; //Empty array = nothing to do = no error
    }

    return execute_sql_arr($sqlarr, $continue, $feedback);
}

/**
 * This function will drop the table passed as argument
 * and all the associated objects (keys, indexes, constaints, sequences, triggers)
 * will be dropped too.
 *
 * @param XMLDBTable table object (just the name is mandatory)
 * @param boolean continue to specify if must continue on error (true) or stop (false)
 * @param boolean feedback to specify to show status info (true) or not (false)
 * @return boolean true on success, false on error
 * @uses $CFG, $db
 */
function drop_table($table, $continue = true, $feedback = true)
{

    global $CFG, $db;

    $status = true;

    if (strtolower(get_class($table)) != 'xmldbtable') {
        return false;
    }

/// Check table exists
    if (!table_exists($table)) {
        debugging('Table ' . $table->getName() .
            ' does not exist. Delete skipped', DEBUG_DEVELOPER);
        return true; //Table don't exist, nothing to do
    }

    if (!$sqlarr = $table->getDropTableSQL($CFG->dbtype, $CFG->prefix, false)) {
        return true; //Empty array = nothing to do = no error
    }

    return execute_sql_arr($sqlarr, $continue, $feedback);
}

/**
 * This function will rename the table passed as argument
 * Before renaming the index, the function will check it exists
 *
 * @param XMLDBTable table object (just the name is mandatory)
 * @param string new name of the index
 * @param boolean continue to specify if must continue on error (true) or stop (false)
 * @param boolean feedback to specify to show status info (true) or not (false)
 * @return boolean true on success, false on error
 * @uses $CFG, $db
 */
function rename_table($table, $newname, $continue = true, $feedback = true)
{

    global $CFG, $db;

    $status = true;

    if (strtolower(get_class($table)) != 'xmldbtable') {
        return false;
    }

/// Check table exists
    if (!table_exists($table)) {
        debugging('Table ' . $table->getName() .
            ' does not exist. Rename skipped', DEBUG_DEVELOPER);
        return true; //Table doesn't exist, nothing to do
    }

/// Check new table doesn't exist
    $check = new XMLDBTable($newname);
    if (table_exists($check)) {
        debugging('Table ' . $check->getName() .
            ' already exists. Rename skipped', DEBUG_DEVELOPER);
        return true; //Table exists, nothing to do
    }

/// Check newname isn't empty
    if (!$newname) {
        debugging('New name for table ' . $table->getName() .
            ' is empty! Rename skipped', DEBUG_DEVELOPER);
        return true; //Table doesn't exist, nothing to do
    }

    if (!$sqlarr = $table->getRenameTableSQL($CFG->dbtype, $CFG->prefix, $newname, false)) {
        return true; //Empty array = nothing to do = no error
    }

    return execute_sql_arr($sqlarr, $continue, $feedback);
}

/**
 * This function will add the field to the table passed as arguments
 *
 * @param XMLDBTable table object (just the name is mandatory)
 * @param XMLDBField field object (full specs are required)
 * @param boolean continue to specify if must continue on error (true) or stop (false)
 * @param boolean feedback to specify to show status info (true) or not (false)
 * @return boolean true on success, false on error
 * @uses $CFG, $db
 */
function add_field($table, $field, $continue = true, $feedback = true)
{

    global $CFG, $db;

    $status = true;

    if (strtolower(get_class($table)) != 'xmldbtable') {
        return false;
    }
    if (strtolower(get_class($field)) != 'xmldbfield') {
        return false;
    }

/// Load the needed generator
    $classname = 'XMLDB' . $CFG->dbtype;
    $generator = new $classname();
    $generator->setPrefix($CFG->prefix);

/// Check the field doesn't exist
    if (field_exists($table, $field)) {
        debugging('Field ' . $table->getName() . '->' . $field->getName() .
            ' already exists. Create skipped', DEBUG_DEVELOPER);
        return true;
    }

/// If NOT NULL and no default given (we ask the generator about the
/// *real* default that will be used) check the table is empty
    if ($field->getNotNull() && $generator->getDefaultValue($field) === NULL && count_records($table->getName())) {
        debugging('Field ' . $table->getName() . '->' . $field->getName() .
            ' cannot be added. Not null fields added to non empty tables require default value. Create skipped', DEBUG_DEVELOPER);
        return true;
    }

    if (!$sqlarr = $table->getAddFieldSQL($CFG->dbtype, $CFG->prefix, $field, false)) {
        return true; //Empty array = nothing to do = no error
    }

    return execute_sql_arr($sqlarr, $continue, $feedback);
}

/**
 * This function will drop the field from the table passed as arguments
 *
 * @param XMLDBTable table object (just the name is mandatory)
 * @param XMLDBField field object (just the name is mandatory)
 * @param boolean continue to specify if must continue on error (true) or stop (false)
 * @param boolean feedback to specify to show status info (true) or not (false)
 * @return boolean true on success, false on error
 * @uses $CFG, $db
 */
function drop_field($table, $field, $continue = true, $feedback = true)
{

    global $CFG, $db;

    $status = true;

    if (strtolower(get_class($table)) != 'xmldbtable') {
        return false;
    }
    if (strtolower(get_class($field)) != 'xmldbfield') {
        return false;
    }

/// Check the field exists
    if (!field_exists($table, $field)) {
        debugging('Field ' . $table->getName() . '->' . $field->getName() .
            ' does not exist. Delete skipped', DEBUG_DEVELOPER);
        return true;
    }

    if (!$sqlarr = $table->getDropFieldSQL($CFG->dbtype, $CFG->prefix, $field, false)) {
        return true; //Empty array = nothing to do = no error
    }

    return execute_sql_arr($sqlarr, $continue, $feedback);
}

/**
 * This function will change the type of the field in the table passed as arguments
 *
 * @param XMLDBTable table object (just the name is mandatory)
 * @param XMLDBField field object (full specs are required)
 * @param boolean continue to specify if must continue on error (true) or stop (false)
 * @param boolean feedback to specify to show status info (true) or not (false)
 * @return boolean true on success, false on error
 * @uses $CFG, $db
 */
function change_field_type($table, $field, $continue = true, $feedback = true)
{

    global $CFG, $db;

    $status = true;

    if (strtolower(get_class($table)) != 'xmldbtable') {
        return false;
    }
    if (strtolower(get_class($field)) != 'xmldbfield') {
        return false;
    }

    if (!$sqlarr = $table->getAlterFieldSQL($CFG->dbtype, $CFG->prefix, $field, false)) {
        return true; //Empty array = nothing to do = no error
    }

    return execute_sql_arr($sqlarr, $continue, $feedback);
}

/**
 * This function will change the precision of the field in the table passed as arguments
 *
 * @param XMLDBTable table object (just the name is mandatory)
 * @param XMLDBField field object (full specs are required)
 * @param boolean continue to specify if must continue on error (true) or stop (false)
 * @param boolean feedback to specify to show status info (true) or not (false)
 * @return boolean true on success, false on error
 * @uses $CFG, $db
 */
function change_field_precision($table, $field, $continue = true, $feedback = true)
{

/// Just a wrapper over change_field_type. Does exactly the same processing
    return change_field_type($table, $field, $continue, $feedback);
}

/**
 * This function will change the unsigned/signed of the field in the table passed as arguments
 *
 * @param XMLDBTable table object (just the name is mandatory)
 * @param XMLDBField field object (full specs are required)
 * @param boolean continue to specify if must continue on error (true) or stop (false)
 * @param boolean feedback to specify to show status info (true) or not (false)
 * @return boolean true on success, false on error
 * @uses $CFG, $db
 */
function change_field_unsigned($table, $field, $continue = true, $feedback = true)
{

/// Just a wrapper over change_field_type. Does exactly the same processing
    return change_field_type($table, $field, $continue, $feedback);
}

/**
 * This function will change the nullability of the field in the table passed as arguments
 *
 * @param XMLDBTable table object (just the name is mandatory)
 * @param XMLDBField field object (full specs are required)
 * @param boolean continue to specify if must continue on error (true) or stop (false)
 * @param boolean feedback to specify to show status info (true) or not (false)
 * @return boolean true on success, false on error
 * @uses $CFG, $db
 */
function change_field_notnull($table, $field, $continue = true, $feedback = true)
{

/// Just a wrapper over change_field_type. Does exactly the same processing
    return change_field_type($table, $field, $continue, $feedback);
}

/**
 * This function will change the default of the field in the table passed as arguments
 * One null value in the default field means delete the default
 *
 * @param XMLDBTable table object (just the name is mandatory)
 * @param XMLDBField field object (full specs are required)
 * @param boolean continue to specify if must continue on error (true) or stop (false)
 * @param boolean feedback to specify to show status info (true) or not (false)
 * @return boolean true on success, false on error
 * @uses $CFG, $db
 */
function change_field_default($table, $field, $continue = true, $feedback = true)
{

    global $CFG, $db;

    $status = true;

    if (strtolower(get_class($table)) != 'xmldbtable') {
        return false;
    }
    if (strtolower(get_class($field)) != 'xmldbfield') {
        return false;
    }

    if (!$sqlarr = $table->getModifyDefaultSQL($CFG->dbtype, $CFG->prefix, $field, false)) {
        return true; //Empty array = nothing to do = no error
    }

    return execute_sql_arr($sqlarr, $continue, $feedback);
}

/**
 * This function will rename the field in the table passed as arguments
 * Before renaming the field, the function will check it exists
 *
 * @param XMLDBTable table object (just the name is mandatory)
 * @param XMLDBField index object (full specs are required)
 * @param string new name of the field
 * @param boolean continue to specify if must continue on error (true) or stop (false)
 * @param boolean feedback to specify to show status info (true) or not (false)
 * @return boolean true on success, false on error
 * @uses $CFG, $db
 */
function rename_field($table, $field, $newname, $continue = true, $feedback = true)
{

    global $CFG, $db;

    $status = true;

    if (strtolower(get_class($table)) != 'xmldbtable') {
        return false;
    }
    if (strtolower(get_class($field)) != 'xmldbfield') {
        return false;
    }

/// Check we have included full field specs
    if (!$field->getType()) {
        debugging('Field ' . $table->getName() . '->' . $field->getName() .
            ' must contain full specs. Rename skipped', DEBUG_DEVELOPER);
        return false;
    }

/// Check field isn't id. Renaming over that field is not allowed
    if ($field->getName() == 'id') {
        debugging('Field ' . $table->getName() . '->' . $field->getName() .
            ' cannot be renamed. Rename skipped', DEBUG_DEVELOPER);
        return true; //Field is "id", nothing to do
    }

/// Check field exists
    if (!field_exists($table, $field)) {
        debugging('Field ' . $table->getName() . '->' . $field->getName() .
            ' does not exist. Rename skipped', DEBUG_DEVELOPER);
        return true; //Field doesn't exist, nothing to do
    }

/// Check newname isn't empty
    if (!$newname) {
        debugging('New name for field ' . $table->getName() . '->' . $field->getName() .
            ' is empty! Rename skipped', DEBUG_DEVELOPER);
        return true; //Field doesn't exist, nothing to do
    }

    if (!$sqlarr = $table->getRenameFieldSQL($CFG->dbtype, $CFG->prefix, $field, $newname, false)) {
        return true; //Empty array = nothing to do = no error
    }

    return execute_sql_arr($sqlarr, $continue, $feedback);
}

/**
 * This function will create the key in the table passed as arguments
 *
 * @param XMLDBTable table object (just the name is mandatory)
 * @param XMLDBKey index object (full specs are required)
 * @param boolean continue to specify if must continue on error (true) or stop (false)
 * @param boolean feedback to specify to show status info (true) or not (false)
 * @return boolean true on success, false on error
 * @uses $CFG, $db
 */
function add_key($table, $key, $continue = true, $feedback = true)
{

    global $CFG, $db;

    $status = true;

    if (strtolower(get_class($table)) != 'xmldbtable') {
        return false;
    }
    if (strtolower(get_class($key)) != 'xmldbkey') {
        return false;
    }
    if ($key->getType() == XMLDB_KEY_PRIMARY) { // Prevent PRIMARY to be added (only in create table, being serious  :-P)
        debugging('Primary Keys can be added at table create time only', DEBUG_DEVELOPER);
        return true;
    }

    if (!$sqlarr = $table->getAddKeySQL($CFG->dbtype, $CFG->prefix, $key, false)) {
        return true; //Empty array = nothing to do = no error
    }

    return execute_sql_arr($sqlarr, $continue, $feedback);
}

/**
 * This function will drop the key in the table passed as arguments
 *
 * @param XMLDBTable table object (just the name is mandatory)
 * @param XMLDBKey key object (full specs are required)
 * @param boolean continue to specify if must continue on error (true) or stop (false)
 * @param boolean feedback to specify to show status info (true) or not (false)
 * @return boolean true on success, false on error
 * @uses $CFG, $db
 */
function drop_key($table, $key, $continue = true, $feedback = true)
{

    global $CFG, $db;

    $status = true;

    if (strtolower(get_class($table)) != 'xmldbtable') {
        return false;
    }
    if (strtolower(get_class($key)) != 'xmldbkey') {
        return false;
    }
    if ($key->getType() == XMLDB_KEY_PRIMARY) { // Prevent PRIMARY to be dropped (only in drop table, being serious  :-P)
        debugging('Primary Keys can be deleted at table drop time only', DEBUG_DEVELOPER);
        return true;
    }

    if (!$sqlarr = $table->getDropKeySQL($CFG->dbtype, $CFG->prefix, $key, false)) {
        return true; //Empty array = nothing to do = no error
    }

    return execute_sql_arr($sqlarr, $continue, $feedback);
}

/**
 * This function will create the index in the table passed as arguments
 * Before creating the index, the function will check it doesn't exists
 *
 * @param XMLDBTable table object (just the name is mandatory)
 * @param XMLDBIndex index object (full specs are required)
 * @param boolean continue to specify if must continue on error (true) or stop (false)
 * @param boolean feedback to specify to show status info (true) or not (false)
 * @return boolean true on success, false on error
 * @uses $CFG, $db
 */
function add_index($table, $index, $continue = true, $feedback = true)
{

    global $CFG, $db;

    $status = true;

    if (strtolower(get_class($table)) != 'xmldbtable') {
        return false;
    }
    if (strtolower(get_class($index)) != 'xmldbindex') {
        return false;
    }

/// Check index doesn't exist
    if (index_exists($table, $index)) {
        debugging('Index ' . $table->getName() . '->' . $index->getName() .
            ' already exists. Create skipped', DEBUG_DEVELOPER);
        return true; //Index exists, nothing to do
    }

    if (!$sqlarr = $table->getAddIndexSQL($CFG->dbtype, $CFG->prefix, $index, false)) {
        return true; //Empty array = nothing to do = no error
    }

    return execute_sql_arr($sqlarr, $continue, $feedback);
}

/**
 * This function will drop the index in the table passed as arguments
 * Before dropping the index, the function will check it exists
 *
 * @param XMLDBTable table object (just the name is mandatory)
 * @param XMLDBIndex index object (full specs are required)
 * @param boolean continue to specify if must continue on error (true) or stop (false)
 * @param boolean feedback to specify to show status info (true) or not (false)
 * @return boolean true on success, false on error
 * @uses $CFG, $db
 */
function drop_index($table, $index, $continue = true, $feedback = true)
{

    global $CFG, $db;

    $status = true;

    if (strtolower(get_class($table)) != 'xmldbtable') {
        return false;
    }
    if (strtolower(get_class($index)) != 'xmldbindex') {
        return false;
    }

/// Check index exists
    if (!index_exists($table, $index)) {
        debugging('Index ' . $table->getName() . '->' . $index->getName() .
            ' does not exist. Delete skipped', DEBUG_DEVELOPER);
        return true; //Index doesn't exist, nothing to do
    }

    if (!$sqlarr = $table->getDropIndexSQL($CFG->dbtype, $CFG->prefix, $index, false)) {
        return true; //Empty array = nothing to do = no error
    }

    return execute_sql_arr($sqlarr, $continue, $feedback);
}

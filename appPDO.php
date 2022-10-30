<?php
include_once('Crud.php');
class appPDO
{
    /**
     * Takes an array of PDO params and converts and arrays to in() clauses with the values exploded
     *
     * @param $sql string
     * @param $pdo_params array
     */
    public function explode_pdo_arrays(&$sql, &$pdo_params)
    {
        if (!is_array($pdo_params)) {
            return;
        }

        foreach ($pdo_params as $k => $v) {
            //Ignore numeric keys and non-arrays
            if (is_array($v)) {
                $in_array = [];
                foreach (array_values($v) as $vk => $vv) {
                    $in_array[] = ":{$k}{$vk}";
                    $pdo_params["{$k}{$vk}"] = $vv;
                }

                /**
                 * Modify the sql to remove the single param and replace it with the inpara list
                 */
                $pat = '/\(\s*:' . $k . '\s*\)/i';
                $replace = "(" . implode(", ", $in_array) . ")";
                $sql = preg_replace($pat, $replace, $sql);

                /**
                 * Unset the original array param
                 */
                unset($pdo_params[$k]);
            }
        }
    }

    public function filter_pdo_params($sql, $passed_params) {
        if (empty($passed_params)) {
            return;
        }

        /**
         * Build array of params to actually send to prepared query and ignore all other values
         */
        $pdo_params = [];

        /**
         * Keep track of missing PDO params from the SQL query to alert the user in the exception
         */
        $missing_params = [];

        if (preg_match_all('/(?<!\w):([\w\.]+)/is', $sql, $matches, PREG_SET_ORDER)) {
            /**
             * Grab just the unique keys from the returned regex
             */
            $matches = array_map(function ($item) {
                return $item[1];
            }, $matches);

            $matches = array_unique($matches);

            /**
             * Loop through matched keys and figure out what's missing from passed params
             */
            foreach ($matches as $key) {
                if (!array_key_exists($key, $passed_params)) {
                    $missing_params[] = $key;
                } else {
                    $pdo_params[$key] = $passed_params[$key];
                }
            }
        } else { //just count the unnamed params...
            preg_match_all('/\?/', $sql, $matches, PREG_SET_ORDER);
            if (count($matches) <> count($passed_params)) {
                throw new Exception("Unnamed parameter count mismatch. In query: " . count($matches) . " provided: " . count($passed_params) . print_r($matches) . ".{$sql}");
            } else {
                $pdo_params = $passed_params;
            }
        }

        if (count($missing_params)) {
            /*$this->app->queries[] = [
                'query' => $sql,
                'params' => $passed_params,
                'sql' => $this->pdo_prepare_debug($sql, $passed_params),
            ];*/
            throw new Exception('Missing required query input: ' . implode(', ', $missing_params));
        }
        return $pdo_params;
    }

    /**
     * Query wrapper for database updates
     *
     * @param $db string The name of the database connection in config
     * @param $table string Name of the database table to insert into
     * @param $params array Column names and values to insert into the specified table
     * @param $allowedparams array If passed, then only columns in the array may be updated.
     *
     * @return bool|int Returns inserted key on success or false
     */
    public function pdo_update($db, $table, $params, $allowedparams = []) {
        /**
         * Get table columns from DB schema and also primary key(s) if exist
         */
        [$primary_keys, $table_cols] = $this->pdo_get_columns($db, $table);

        // Get existing row
        $read_sql = "select * from {$table} where ";

        $read_sql .= implode(' and ', array_map(function ($key) {
            return $key . " = :" . $key;
        }, $primary_keys));

        // Select row to be updated by selecting against only primary keys
        $crud = new Crud();
        $object_row = $crud->read($read_sql, array_intersect_key($params, array_flip($primary_keys)), 'row');

        // Filter table columns that are allowed to be updated (default is all).
        $allowed_table_cols = empty($allowedparams) ? $table_cols : array_values(array_intersect($table_cols, $allowedparams));

        /**
         * Set allowed PDO params and sanitize for DB
         */
        $pdo_params = [];
        foreach ($params as $key => $val) {
            if (in_array($key, $table_cols)) {
                // If the field is a valid table column, but not a PK nor in the allowed updatable fields then throw an error
                if (!in_array($key, $primary_keys) && !in_array($key, $allowed_table_cols)) {
                    throw new Exception(strtr("Trying to update restricted column '{table}.{column}'", [
                        '{table}' => $table,
                        '{column}' => $key,
                    ]));
                }
                $pdo_params[$key] = $val;
            }
        }

        /**
         * Extract the column names as the filtered param keys
         */
        $pdo_keys = array_keys($pdo_params);

        /**
         * Create SQL insert by exploding our pdo_param keys into columns and values
         */
        $sql = "update {$table} set ";

        /**
         * Remove primary key for duplicate key update statement
         */
        $pdo_keys = array_filter($pdo_keys, function ($key) use ($primary_keys) {
            return !in_array($key, $primary_keys);
        });

        /**
         * Explode remaining pdo_param keys into columns and values
         */
        $sql .= implode(', ', array_map(function ($key) {
            return $key . " = :" . $key;
        }, $pdo_keys));

        /**
         * Add where clause with primary key
         */
        $sql .= " where ";

        $sql .= implode(' and ', array_map(function ($key) {
            return $key . " = :" . $key;
        }, $primary_keys));

        /**
         * Run insert/update
         */
        $res = $crud->update($sql, $pdo_params, 'query');

        /**
         * Return false if query fails.
         * If query is successful, return either primary key value if passed or last insert Id
         */
        if (!$res) {
            return false;

        } else {
            return is_array($primary_keys) && !empty($params[$primary_keys[0]]) ? $params[$primary_keys[0]] : $res;
        }
    }

    /**
     * Lookup a database table's information schema to find available columns and any primary keys
     *
     * @param $db string The name of the database connection in config
     * @param $table string Name of the database table to lookup schema
     *
     * @return array With primary keys and column names
     */
    public function pdo_get_columns($db, $table) {

        /**
         * Get all table columns
         */
        $sql = "select column_name, column_key
				from information_schema.columns
				where table_schema = database() and table_name = :table_name";
        $crud = new Crud();
        $columns = $crud->read($sql, ['table_name' => $table], 'all');

        /**
         * Filter out just the column names into array
         */
        $column_names = array_map(function ($item) {
            return $item['column_name'];
        }, $columns);

        /**
         * Find primary key columns and create array of keys
         */
        $primary_keys = array_values(array_map(function ($item) {
            return $item['column_name'];
        }, array_filter($columns, function ($item) {
            return strtolower($item['column_key']) === 'pri';
        })));

        return [$primary_keys, $column_names];
    }

}
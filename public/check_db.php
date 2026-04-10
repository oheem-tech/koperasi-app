<?php
// Dump database content to find fire emoji
$db = \Config\Database::connect();
$tables = $db->listTables();
foreach($tables as $table) {
    if(in_array($table, ['migrations'])) continue;
    $builder = $db->table($table);
    $results = $builder->get()->getResultArray();
    foreach($results as $row) {
        foreach($row as $key => $value) {
            if (is_string($value) && (strpos($value, '🔥') !== false || strpos($value, '128293') !== false)) {
                echo "Found in Table: $table, Column: $key, Value: $value\n";
            }
        }
    }
}
echo "Done checking DB.\n";

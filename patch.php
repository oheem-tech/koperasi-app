<?php
$db = new mysqli('localhost', 'root', '', 'koperasi');
$res = $db->query("SELECT * FROM roles WHERE name = 'admin'");
if ($res && $res->num_rows > 0) {
    $row = $res->fetch_assoc();
    $perms = json_decode($row['permissions'], true);
    if (!in_array('manage_backup', $perms)) {
        $perms[] = 'manage_backup';
        $permsJson = json_encode($perms);
        $db->query("UPDATE roles SET permissions = '$permsJson' WHERE name = 'admin'");
        echo "Updated";
    }
}

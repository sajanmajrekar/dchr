<?php
require_once '../php_actions/db_connect.php';
$out = "=== tblleads ===\n";
$res = $connect->query("DESCRIBE tblleads");
if ($res) {
    while ($r = $res->fetch_assoc()) {
        $out .= $r['Field'] . ", ";
    }
}
file_put_contents('describe.txt', $out);

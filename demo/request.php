<?php
require_once "header.php";
global $DB_conn;
if (isset($_GET['id'])) {
    $id = $DB_conn->real_escape_string($_GET['id']);
    header('Content-Type:application/json; charset=utf-8');
    $record = $DB_conn->query("select * from submit where submitid='{$id}'");
    if ($record->num_rows == 0)
        die(json_encode(array("error"=>"record not found")));
    exit($record->fetch_assoc()['info']);
}
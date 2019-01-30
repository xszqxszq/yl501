<?php
// DB Settings
$DB_servername = "localhost";
$DB_username = "kksk";
$DB_password = "kkskkksk";
$DB_database = "OJ";
$DB_conn = new mysqli($DB_servername, $DB_username, $DB_password, $DB_database);
if ($DB_conn->connect_error) {
    die("连接服务器失败：".$DB_conn->connect_error);
}
$DB_conn->query("set names 'utf8'");

// Site Settings
$GLOBALS['site_name'] = "YALI-501";
$GLOBALS['page_limit'] = 20;

function illegal($user, $password) {
    global $DB_conn;
    $result = $DB_conn->query("select * from user where user='".$user."' and password='".$password."'");
    if ($result->num_rows == 0)
        return false;
    return true;
}

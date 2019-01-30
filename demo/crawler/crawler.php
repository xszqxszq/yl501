<?php
require_once "header.php";
global $DB_conn;
$arr = array("1381", "1664", "1804", "2045");
foreach ($arr as $i) {
    $url = "http://poj.org/problem?id=".$i;
    $content = file_get_contents($url);
    
    $content = substr($content, strpos($content, "<td>"));
    
    $problemName = $content;
    $problemName = substr($problemName, strpos($problemName, "<div class=\"ptt\""));
    $problemName = substr($problemName, strpos($problemName, ">")+1);
    $problemName = substr($problemName, 0, strpos($problemName, "</div>"));
    
    $content = substr($content, strpos($content, "<p class=\"pst\">"));
    $content = substr($content, 0, strpos($content, "<font color=#333399 size=3>"));
    $problemName = $DB_conn->real_escape_string($problemName);
    $content = $DB_conn->real_escape_string($content);
    
    $DB_conn->query("update rproblem set oj='POJ', problem='{$i}', name='{$problemName}', content='{$content}' where problem='{$i}'");
}

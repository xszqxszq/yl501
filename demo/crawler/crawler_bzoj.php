<?php
require_once "header.php";
global $DB_conn;
for ($i=4748;$i<=4748;$i++) {
    $url = "https://darkbzoj.cf/problem/".$i;
    $content = file_get_contents($url);
    
    $content = substr($content, strpos($content, '<h1 class="page-header text-center">'));

    $problemName = $content;
    $problemName = substr($problemName, strpos($problemName, '<h1 class="page-header text-center">'));
    $problemName = substr($problemName, strpos($problemName, ". ")+2);
    $problemName = substr($problemName, 0, strpos($problemName, "</h1>"));
    
    $content = substr($content, strpos($content, "<article"));
    $content = substr($content, 0, strpos($content, "</article>")+10);
    $problemName = $DB_conn->real_escape_string($problemName);
    $content = $DB_conn->real_escape_string($content);
    
    $DB_conn->query("insert into lydsy (problem,name,content) values ('{$i}','{$problemName}','{$content}')");
}

<?php include "header.php"; $GLOBALS['page_name']="重新评测"; showHeader();?>

<?php
global $DB_conn;
if (isset($_GET['id'])) {
    $submitId = $DB_conn->real_escape_string($_GET['id']);
    $query = $DB_conn->query("select * from submit where submitid='{$submitId}'")->fetch_assoc();
    $code = base64_decode($query['code']);
    $language = $query['language'];
    $codeFile = fopen("/var/www/judge/submit/".$submitId.".json", "w") or die("Failed to open file");
    $info = array();
    $info["submitid"] = $submitId;
    $info["language"] = $language;
    $info["problem"] = $query['problem'];
    $info["oj"] = "oj";
    $info["code"] = $code;
    $code = base64_encode($_POST['code']);
    fwrite($codeFile, json_encode($info));
    $DB_conn->query("update submit set status=-1,info='' where submitid='{$submitId}'");
    if ($DB_conn->connect_error) {
        exit($DB_conn->connect_errno);
    }
    header('Location: ./record.php?id='.$submitId);
    exit(0);
}
?>

<?php include "footer.php";?>
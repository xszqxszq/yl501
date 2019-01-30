<?php include "header.php"; $GLOBALS['page_name']="提交"; showHeader();?>

<?php
global $DB_conn, $user_id;
if (empty($_COOKIE['user']) || empty($_COOKIE['password']))
    header('Location: ./login.php');
if (isset($_POST['id']) && isset($_POST['code']) && isset($_POST['language'])) {
    $problemId = $_POST['id'];
    $submitId = (string) ($DB_conn->query("select count(1) from submit")->fetch_row()[0] + 1);
    $code = $_POST['code'];
    $language = addslashes($_POST['language']);
    $codeFile = fopen("/var/www/judge/submit/" . $submitId . ".json", "w") or die("Failed to open file");
    $info = array();
    $info["submitid"] = $submitId;
    $info["language"] = $language;
    $info["problem"] = $problemId;
    $info["oj"] = "oj";
    $info["code"] = $code;
    $code = base64_encode($_POST['code']);
    fwrite($codeFile, json_encode($info));

    $info = json_decode(file_get_contents("/var/www/judge/data/" . $problemId . "/datalist.json"), true);
    $default = array(
        "status" => 8,
        "time" => 0,
        "memory" => 0,
        "testpoints" => array(),
        "compileinfo" => ""
    );
    if ($info['auto']) {
        for ($i = $info['begin']; $i <= $info['end']; $i ++) {
            array_push($default['testpoints'], array(
                "status" => 8,
                "time" => 0,
                "memory" => 0,
                "info" => ""
            ));
        }
    } else {
        foreach ($info['testpoints'] as $i) {
            array_push($default['testpoints'], array(
                "status" => 8,
                "time" => $i["time"],
                "memory" => $i["memory"],
                "info" => ""
            ));
        }
    }
    $defaultContent = json_encode($default);

    $DB_conn->query("insert into submit (submitid,user,problem,code,language,info) values ('{$submitId}','{$user_id}','{$problemId}','{$code}','{$language}','{$defaultContent}')");
    if ($DB_conn->connect_error) {
        exit($DB_conn->connect_errno);
    }
    header('Location: ./record.php?id=' . $submitId);
    exit(0);
}
?>
<?php
$self = htmlspecialchars($_SERVER["PHP_SELF"]);
if (isset($_GET['id']))
    $problem = $_GET['id'];
else
    $problem = "";
$form = <<<EOF
                    <form action="{$self}" method="post">
						题目: <input type="text" name="id" value="{$problem}"><br>
                        语言: 
                        <select name="language">
                            <option value="c++98">c++98</option>
                            <option value="c++11">c++11</option>
                            <option value="c++17">c++17</option>
                            <option value="python2">python2</option>
                        </select><br>
                        代码:<br>
                        <textarea name="code" style="height:433px; width:100%;"></textarea><br>
                        <input value="提交" type="submit">
                    </form>
EOF;
showBlock($form);
?>
<?php include "footer.php";?>
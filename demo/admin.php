<?php include "header.php"; $GLOBALS['page_name'] = "管理"; showHeader();?>

<?php
global $DB_conn;
if ($GLOBALS['user_group'] != 99) {
    header("Location: ./");
}
if (isset($_POST['id']) && isset($_POST['name']) && isset($_POST['content']) && isset($_POST['status']) && isset($_POST['author'])) {
    $name = $DB_conn->real_escape_string($_POST['name']);
    $content = $DB_conn->real_escape_string($_POST['content']);
    $DB_conn->query("update problem set status='{$_POST['status']}',name='{$name}',content='{$content}',author='{$_POST['author']}' where problem='{$_POST['id']}'");
    if ($DB_conn->error) {
        showBlock($DB_conn->errno);
        die();
    } else {
        header("Location: ./admin.php?function=problem&id=" . $_POST['id'] . "&success=true");
        exit();
    }
}
if (isset($_GET['function'])) {
    if ($_GET['function'] === "problem") {
        if (! empty($_GET['id'])) {
            if (! empty($_GET['success']) && $_GET['success'] === "true")
                showBlock("提交成功");
            $prob = $DB_conn->query("select * from problem where problem='{$_GET['id']}'");
            if ($prob->num_rows == 0)
                header('Location: ./admin.php');
            $prob = get_problem($_GET['id']);
            $self = htmlspecialchars($_SERVER["PHP_SELF"]);
            $left = <<<s
<div class="li">
<form method="post" action={$self} id="problem">
<input type="hidden" name="id" value="{$_GET['id']}">
<p><div class="listLeft" style="color: #000000; font-weight:bold;">题目名称</div><div class="textframe" style="display:inline-block; width: 60%;"><input style="width: 100%" type="text" name="name" value="{$prob['name']}"></div></p>
<p><div class="listLeft" style="color: #000000; font-weight:bold;">题目提供</div><div class="textframe" style="display:inline-block; width: 60%;"><input style="width: 100%" type="text" name="author" value="{$prob['author']}"></div></p>
<p><div class="listLeft" style="color: #000000; font-weight:bold;">题目内容</div><div class="textframe" style="display:inline-block; width: 60%; vertical-align:top;"><textarea name="content" style="max-width: 100%; min-width: 100%; min-height: 100px;" form="problem">{$prob['content']}</textarea></div></p>
<p><div class="listLeft" style="color: #000000; font-weight:bold;">题目状态</div><div class="textframe" style="display:inline-block; width: 60%; vertical-align:top;">
<select name="status">
s;
            if ($prob['status'] == "1")
                $left .= "<option value=\"1\" selected=\"selected\">正常显示</option>";
            else
                $left .= "<option value=\"1\">正常显示</option>";
            if ($prob['status'] == "0")
                $left .= "<option value=\"0\" selected=\"selected\">无数据状态</option>";
            else
                $left .= "<option value=\"0\">无数据状态</option>";
            if ($prob['status'] == "-1")
                $left .= "<option value=\"-1\" selected=\"selected\">隐藏状态</option>";
            else
                $left .= "<option value=\"-1\">隐藏状态</option>";
            $left .= <<<s
</select>
</div>
</p>
</form>
<p style="width: 40%">
    <a onclick="problem.submit();"><button class="blue" style="float: left; width: 30%;">保存</button></a>
</p>
</div>
s;
            $right = <<<s
    
s;
            showDiv(array(
                $left,
                $right
            ));
            exit(0);
        }
    }
}
$left = <<<s
<div class="li">
<h3>公告</h3>
<p>暂无</p>
</div>
s;
$right = <<<s
<div class="li">
<h3 class="title">快速入口</h3>
<p><b>实用工具</b></p>
<div>
    <a href="http://172.45.33.100/phpmyadmin/index.php"><button class="blue">phpMyAdmin</button></a>
</div>
</div>
s;
showDiv(array(
    $left,
    $right
));
?>

<?php include "footer.php";?>

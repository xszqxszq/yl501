<?php $GLOBALS['isproblempage'] = true; include "header.php";?>

<?php
global $DB_conn;
if (! empty($_GET['id'])) {
    $prob_id = $DB_conn->real_escape_string($_GET['id']);
    $prob = $DB_conn->query("select * from problem where problem='{$prob_id}'");
    if ($prob->num_rows == 0)
        header('Location: ./problemlist.php');
    $prob = $prob->fetch_assoc();
    $prob_status = $prob['status'];
    if ($prob_status == "0" && (! isset($GLOBALS['user_group']) || $GLOBALS['user_group'] != 99)) {
        $GLOBALS['page_name'] = "错误";
        showHeader();
        showBlock("此题暂未添加数据");
        include "footer.php";
        exit(0);
    }
    if ($prob_status == "-1" && (! isset($GLOBALS['user_group']) || $GLOBALS['user_group'] != 99)) {
        $GLOBALS['page_name'] = "错误";
        showHeader();
        showBlock("隐藏题目，暂不可见");
        include "footer.php";
        exit(0);
    }
    $prob_name = $prob['name'];
    if ($prob['oj'] == "POJ")
        exit(0);
    if ($prob['oj'] == "oj")
        $GLOBALS['page_name'] = $prob_name;
    else
        $GLOBALS['page_name'] = $prob_id . ' ' . $prob_name;
    showHeader();

    $prob_info = "";
    $prob_info .= "<h3>信息</h3>";
    $prob_info .= '<div style="font-size:17px;">' . '<div class="listLeft">ID</div>' . $prob['problem'] . '</div>';
    if (strpos($prob['author'], '!') !== FALSE) {
        $prob_info .= '<div style="font-size:17px;">' . '<div class="listLeft">题目提供者</div><a href="./userinfo.php?id=' . get_user(substr($prob['author'], 1))['user'] . '">' . get_user(substr($prob['author'], 1))['nickname'] . '</a></div>';
    } else {
        $prob_info .= '<div style="font-size:17px;">' . '<div class="listLeft">题目提供者</div>' . $prob['author'] . '</div>';
    }
    if ($DB_conn->query("select count(1) from submit where problem={$prob['problem']}") !== FALSE) {
        $submited = ($DB_conn->query("select count(1) from submit where problem={$prob['problem']}")->fetch_row()[0]);
        $accepted = ($DB_conn->query("select count(1) from submit where problem={$prob['problem']} and status=\"1\"")->fetch_row()[0]);
    } else {
        $submited = $accepted = 0;
    }

    $prob_info .= '<div style="font-size:17px;">' . '<div class="listLeft">通过数</div>' . $accepted . '</div>';
    $prob_info .= '<div style="font-size:17px;">' . '<div class="listLeft">提交数</div>' . $submited . '</div>';
    $prob_info .= '<div style="font-size:17px;">' . '<div class="listLeft">通过率</div>' . ($submited == 0 ? 0 : round(100.0 * $accepted / $submited)) . '%</div>';
} else {
    header('Location: ./problemlist.php');
    exit(0);
}
?>

<?php
$left = '<div class="li"><div id="content">' . $prob['content'] . '</div><script>document.getElementById("content").innerHTML = marked(document.getElementById("content").innerHTML);</script></div>';
$right = '<div class="li">' . $prob_info . "</div><div class=\"li\"><a href=\"./submit.php?id={$prob_id}\"><button class=\"blue\">提交</button></a> " . "<a href=\"./recordlist.php?problem={$prob_id}\"><button class=\"blue\">记录</button></a> ";
if (isset($GLOBALS['user_group']) && $GLOBALS['user_group'] == 99)
    $right .= "<a href=\"./admin.php?function=problem&id={$prob_id}\"><button class=\"blue\">编辑</button></a>";
$right .= '</div>';
showDiv(array(
    $left,
    $right
));
?>

<?php include "footer.php";?>

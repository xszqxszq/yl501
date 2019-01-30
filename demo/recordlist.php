<?php include "header.php"; $GLOBALS['page_name']="记录"; showHeader();?>

<?php
global $DB_conn;
if (! empty($_GET['oj'])) {
    $oj = $_GET['oj'];
} else {
    $oj = "oj";
}
if (! empty($_GET['problem'])) {
    $problem = $_GET['problem'];
    $problemCode = "and problem='" . $problem . "'";
} else {
    $problem = "";
    $problemCode = 'and 1';
}
if (! empty($_GET['user'])) {
    $currUser = get_user_byName($_GET['user']);
    if ($currUser === FALSE) {
        $user = "";
        $userCode = 'and 1';
    } else {
        $user = $_GET['user'];
        $userCode = 'and user=' . $currUser['id'];
    }
} else {
    $user = "";
    $userCode = 'and 1';
}
if (! empty($_GET['page']) && is_numeric($_GET['page'])) {
    $page = $_GET['page'];
} else {
    $page = 1;
}
$currURI = $_SERVER['PHP_SELF'] . "?oj={$oj}";
if (! empty($problem))
    $currURI .= "&problem={$problem}";
if (! empty($user))
    $currURI .= "&user={$user}";
$pageLimit = $GLOBALS['page_limit'];
$recordCount = ($DB_conn->query("select count(1) from submit where 1 {$problemCode} {$userCode}")->fetch_row()[0]);
$pageCount = ceil((float) $recordCount / (float) $pageLimit);
$limit = ($page - 1) * $pageLimit;
$submit = $DB_conn->query("select * from submit where 1 {$problemCode} {$userCode} order by submittime desc limit {$limit},{$pageLimit}");
$probList = array();
while ($row = $submit->fetch_assoc()) {
    $currUser = get_user($row['user']);
    $currProb = get_problem($row['problem']);
    array_push($probList, '<div style="display: inline-block;min-width:220px;"><a href="./userinfo.php?id=' . $currUser['user'] . '"><img style="width:25px;border-radius:3px;position:relative;top:15px" src="' . get_gravatar($currUser['email']) . '"></img></a> ' . status_color($row['status'], "16") . "</div> <div style=\"display:inline-block;min-width:250px;\"><a href=\"./record.php?id={$row['submitid']}\">". ($currProb['oj'] == 'oj' ? 'P' : '') . $currProb['problem'] . " "  . $currProb['name'] . "</a></div>" . '<div style="display:inline-block;min-width:80px;">' . $row['time'] . 'ms</div> ' . '<div style="display:inline-block;min-width:100px;">' . $row['memory'] . 'KB</div> ' . '<div style="display:inline-block;min-width:479px;position:relative;left:33px;">' . '<a href="./userinfo.php?id=' . $currUser['user'] . '">' . $currUser['nickname'] . '</a></div>' . '<div style="display:inline-block;min-width:10px;">' . $row['submittime'] . '</div>');
}
if (1 <= $page && $page <= $pageCount) {
    $button = "<div class=\"ul pagination\"><div class=\"frame pagination\">";
    if ($page == 1)
        $prepage = 1;
    else
        $prepage = $page - 1;
    if ($page == $pageCount)
        $nextpage = $page;
    else
        $nextpage = $page + 1;
    $button = $button . <<<s
<a class="li" href="{$currURI}&page=1"><span class="fa fa-angle-double-left fa-fw" style="color: #3399FF"></span></a>
s;
    $button = $button . <<<s
<a class="li" href="{$currURI}&page={$prepage}"><span class="fa fa-angle-left fa-fw" style="color: #3399FF"></span></a>
s;
    $pageOffset = $pageCount - $page;
    if ($page <= 4)
        $pageOffset = 9 - $page;
    elseif ($pageOffset > 4)
        $pageOffset = 4;
    for ($i = $page - (8 - $pageOffset); $i <= $page + $pageOffset; $i ++) {
        if ($i <= 0) {
            continue;
        }
        if ($i > $pageCount)
            break;
        if ($i == $page)
            $button = $button . <<<s
    <a class="li active" href="{$currURI}&page={$i}"><span>{$i}</span></a>
s;
        else
            $button = $button . <<<s
    <a class="li" href="{$currURI}&page={$i}"><span>{$i}</span></a>
s;
    }
    $button = $button . <<<s
<a class="li" href="{$currURI}&page={$nextpage}"><span class="fa fa-angle-right fa-fw" style="color: #3399FF"></span></a>
s;
    $button = $button . <<<s
<a class="li" href="{$currURI}&page={$pageCount}"><span class="fa fa-angle-double-right fa-fw" style="color: #3399FF"></span></a>
s;
    $button = $button . "</div></div>";
    array_push($probList, $button);
}
$aside = <<<s
<aside>
<div class="ul table">
<div class="frame table">
<div class="li">
<p><div class="listLeft" style="color: #000000; font-weight:bold;">用户名</div><div class="textframe" style="display:inline-block; width: 60%;"><input style="width: 100%" type="text" name="user" value="{$user}"></div></p>
<p><div class="listLeft" style="color: #000000; font-weight:bold;">题目编号</div><div class="textframe" style="display:inline-block; width: 60%;"><input style="width: 100%" type="text" name="problem" value="{$problem}"></div></p>
<button class="blue" style="float: left; width: 30%;" id="submit">查询</button>
<script>
$("#submit").click(function(){
    window.location.replace("./recordlist.php?user="+$("input[name=user]").val()+"&problem="+$("input[name=problem]").val());
});
$("input").keydown(function(event){
  if(event.keyCode == 13){
    $("#submit").trigger("click");
  }
});
</script>
</div>
</div>
</div>
</aside>
s;
array_push($probList, $aside);

showProblemList($probList);
?>

<?php include "footer.php";?>

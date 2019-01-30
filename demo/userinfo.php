<?php include "header.php";?>

<?php
global $DB_conn;
if (isset($_GET['id'])) {
    $username = $DB_conn->real_escape_string($_GET['id']);
    $userinfo = $DB_conn->query("select * from user where user='{$username}'");
    if ($userinfo->num_rows == 0) {
        $GLOBALS['page_name'] = "错误";
        showBlock("用户未找到！");
        include "footer.php";
        exit(0);
    }
    $userinfo = $userinfo->fetch_assoc();
    $GLOBALS['page_name'] = $userinfo['nickname'];
    $nickname = $userinfo['nickname'];
    $userId = $userinfo['id'];
    $userIp = $userinfo['ip'];
    $regtime = $userinfo['regtime'];
    if ($userinfo['group'] == 99) {
        $group = "管理员";
    } elseif ($userinfo['group'] == 0) {
        $group = "封禁用户";
    } else {
        $group = "普通用户";
    }
    
    $submited = $DB_conn->query("select count(1) from submit where user={$userId}")->fetch_row()[0];
    $accepted = $DB_conn->query("select count(distinct problem) from submit where user={$userId} and status=1")->fetch_row()[0];
    $accepted_array = $DB_conn->query("select distinct problem from submit where user={$userId} and status=1 order by problem");
    
    $accepted_list = "";
    while($i = $accepted_array->fetch_assoc()) {
        $accepted_list .= "<a href=\"./problem.php?id={$i["problem"]}\">[".$i["problem"] . ']</a>';
    }
    
    $avatar = get_gravatar(get_user($userId)['email']);
    
    $left = <<<EOF
<div class="li">
<h3>已通过题目</h3>
<p>{$accepted_list}</p>
</div>
EOF;
    $right = <<<EOF
<div class="li" style="text-align:center;">
<img style="width:30%; border-radius:100%;" src="{$avatar}">
<div style="text-align:left;">
<div style="font-size:20px; text-align:center; font-weight:bold;">{$nickname}</div>
<br>
<div style="font-size:17px;"><div class="listLeft">ID</div>{$userId}</div>
<div style="font-size:17px;"><div class="listLeft">用户状态</div>{$group}</div>
<div style="font-size:17px;"><div class="listLeft">注册时间</div>{$regtime}</div>
<div style="font-size:17px;"><div class="listLeft">通过数</div>{$accepted}</div>
<div style="font-size:17px;"><div class="listLeft">提交数</div>{$submited}</div>
<div style="font-size:17px;"><div class="listLeft">IP</div>{$userIp}</div>
</div>
</div>
EOF;
    showHeader();
    showDiv(array(
        0 => $left,
        1 => $right
    ), "100%", "", "width: 100%");
}
?>

<?php include "footer.php";?>
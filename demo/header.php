<?php
require_once "conf.php";
require_once "function.php";
global $DB_conn;
if (isset($_COOKIE['user']) || isset($_COOKIE['password'])) {
    if (($USER_INFO = $DB_conn->query("select * from user where user='{$_COOKIE['user']}' and password='{$_COOKIE['password']}'"))->num_rows == 0 || ($USER_INFO = $USER_INFO->fetch_assoc())['group'] == "0") {
        setcookie("user", NULL);
        setcookie("password", NULL);
        header("Location: " . $_SERVER['PHP_SELF']);
    }
}

if (isset($_COOKIE['user']) && isset($_COOKIE['password'])) {
    $GLOBALS['user_id'] = $USER_INFO['id'];
    $GLOBALS['user_group'] = $USER_INFO['group'];
    $GLOBALS['user_nickname'] = $USER_INFO['nickname'];
    $GLOBALS['user_email'] = $USER_INFO['email'];
    $GLOBALS['user_user'] = $_COOKIE['user'];
    $GLOBALS['user_password'] = $_COOKIE['password'];
    $GLOBALS['user_ip'] = get_ip();
    $DB_conn->query("update user set ip='{$GLOBALS['user_ip']}' where user='{$_COOKIE['user']}'");
}

function showHeader()
{
    global $DB_conn;
    echo <<<Header
<!DOCTYPE html>
<html>
	<head>
    <link rel="stylesheet" href="https://cdn.staticfile.org/font-awesome/4.7.0/css/font-awesome.css"/>
    <link rel="stylesheet" href="./css/main.css"/>
    <link rel="stylesheet" href="./css/index.css"/>
    <link rel="stylesheet" href="./css/form.css"/>
    <link rel="stylesheet" href="./css/problem.css"/>
    <link rel="stylesheet" href="./css/prism.css"/>
    <script src="./js/node_modules/marked/lib/marked.js"></script>
    <script src="//cdn.bootcss.com/mathjax/2.7.0/MathJax.js?config=TeX-AMS-MML_HTMLorMML"></script>
    <script type="text/x-mathjax-config">
    MathJax.Hub.Config({
        tex2jax: {
            inlineMath: [ ['$','$'] ],
            displayMath: [ ['$$','$$'] ]
        }
    });
    </script>
    <link rel="dns-prefetch" href="//cdn.mathjax.org" />
    <script src="https://cdn.staticfile.org/jquery/1.10.2/jquery.min.js"></script>
    <script src="./js/main.js"></script>
    <script src="./js/index.js"></script>
    <script src="./js/node_modules/clipboard/dist/clipboard.min.js"></script>
    <script src="./js/prism.js"></script>
    <meta name="description" content="YALI-501"/>
    <meta name="keywords" content="YALI-501"/>
    <meta charset="UTF-8"/>
	<title>{$GLOBALS['page_name']} - {$GLOBALS['site_name']}</title>
	</head>
	<body>
    <nav>
		<div>
        <div><a href="./">{$GLOBALS['site_name']}</a></div>
        <div><a href="./problemlist.php">题目</a></div>
        <div><a href="./recordlist.php">记录</a></div>
Header;
    if (isset($_COOKIE['user']) && isset($_COOKIE['password'])) {
        $userinfo = $DB_conn->query("select * from user where user='{$_COOKIE['user']}'")->fetch_assoc();
        echo "<div style=\"float: right;\"><a href=\"./logout.php\">注销</a></div>";
        if ($GLOBALS['user_group'] == 99)
            echo "<div style=\"float: right;\"><a href=\"./admin.php\">管理</a></div>";
        echo "<div style=\"float: right;\"><a href=\"./userinfo.php?id={$_COOKIE['user']}\">{$userinfo['nickname']}</a></div>";
    } else {
        echo "<div style=\"float: right;\"><a href=\"./register.php\">注册</a></div>";
        echo "<div style=\"float: right;\"><a href=\"./login.php\">登录</a></div>";
    }
    echo <<<Header
		</div>
    </nav>
    <header><h1>{$GLOBALS['page_name']}</h1></header>
Header;
}
?>

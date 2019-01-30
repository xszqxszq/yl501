<?php require_once "conf.php"; ?>

<?php

function showBlock($target)
{
    echo <<<EOF
<main>
    <article class="index">
        <div class="ul list">
            <div class="frame list">
                <div class="li">
{$target}
                </div>
            </div>
        </div>
    </article>
</main>
EOF;
}

function showDiv($target, $width = "60%", $style1 = "", $style2 = "")
{
    echo <<<EOF
<main class="problem">
    <article style="width: {$width}; {$style1}">
        <div class="ul table text">
{$target[0]}
        </div>
    </article>
    <aside>
        <div class="ul list" style=" {$style2}">
{$target[1]}
        </div>
    </aside>
</main>
EOF;
}

function showProblemList($target, $width = "80%")
{
    $aside = "";
    echo <<<EOF
<main class="problem">
    <article style="width: {$width}">
        <div class="ul list transition">
            <div class="frame list transition">
EOF;
    foreach ($target as $i) {
        if (strpos($i, "<aside>") !== false) {
            $aside = $i;
            continue;
        }
        if (strpos($i, "<div class=\"frame pagination\">") !== false) {
            echo $i;
            continue;
        }
        echo "<div class=\"li\">{$i}</div>";
    }
    echo <<<EOF
            </div>
        </div>
    </article>
{$aside}
</main>
EOF;
}

function bzoj_replace($str)
{
    $str .= "<h3>";
    $str = preg_replace('/<\/h3>(.*?)<h3>/', '<div class="li"><b class="title">${1}</b>${2}</div><h3>', $str);
    return $str;
}

function status_color($status, $size = "20")
{
    $return = "";
    switch ($status) {
        case "-1":
            $return = "<font style=\"color: #646464; font-size: " . $size . "px;\">Waiting</font>";
            break;
        case "8":
            $return = "<font style=\"color: #646464; font-size: " . $size . "px;\">Judging</font>";
            break;
        case "0":
            $return = "<font style=\"color: #646464; font-size: " . $size . "px;\">Unknown Error</font>";
            break;
        case "1":
            $return = "<font style=\"color: #009900; font-size: " . $size . "px;\">Accepted</font>";
            break;
        case "2":
            $return = "<font style=\"color: #FF3300; font-size: " . $size . "px;\">Wrong Answer</font>";
            break;
        case "3":
            $return = "<font style=\"color: #FF3366; font-size: " . $size . "px;\">Runtime Error</font>";
            break;
        case "4":
            $return = "<font style=\"color: #FF9933; font-size: " . $size . "px;\">Compile Error</font>";
            break;
        case "5":
            $return = "<font style=\"color: #0000FF; font-size: " . $size . "px;\">Time Limit Exceeded</font>";
            break;
        case "6":
            $return = "<font style=\"color: #0000FF; font-size: " . $size . "px;\">Memory Limit Exceeded</font>";
            break;
        case "7":
            $return = "<font style=\"color: #0000FF; font-size: " . $size . "px;\">Output Limit Exceeded</font>";
            break;
    }
    return $return;
}

// GRAVATAR
function get_gravatar($email, $s = 80, $d = 'mp', $r = 'g', $img = false, $atts = array())
{
    $url = 'https://www.gravatar.com/avatar/';
    $url .= md5(strtolower(trim($email)));
    $url .= "?s=$s&d=$d&r=$r";
    if ($img) {
        $url = '<img src="' . $url . '"';
        foreach ($atts as $key => $val)
            $url .= ' ' . $key . '="' . $val . '"';
        $url .= ' />';
    }
    return $url;
}

function get_user($user_id)
{
    $ret = $GLOBALS['DB_conn']->query("select * from user where id='{$user_id}'");
    if ($ret->num_rows)
        return $ret->fetch_assoc();
    else
        return FALSE;
}

function get_user_byName($user)
{
    $ret = $GLOBALS['DB_conn']->query("select * from user where user='{$user}'");
    if ($ret->num_rows)
        return $ret->fetch_assoc();
    else
        return FALSE;
}

function get_problem($problem)
{
    $ret = $GLOBALS['DB_conn']->query("select * from problem where problem='{$problem}'");
    if ($ret->num_rows)
        return $ret->fetch_assoc();
    else
        return FALSE;
}

function get_ip()
{
    if (isset($_SERVER["HTTP_CDN_SRC_IP"])) {
        $realip = $_SERVER["HTTP_CDN_SRC_IP"];
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $realip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $realip = $_SERVER['HTTP_CLIENT_IP'];
    } else {
        $realip = $_SERVER['REMOTE_ADDR'];
    }

    // 如果是代理服务器，有可能返回两个IP,这是取第一个即可
    if (stristr($realip, ','))
        $realip = strstr($realip, ',', true);
    return (str_replace('#', '', $realip));
}
?>

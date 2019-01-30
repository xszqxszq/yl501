<?php include "header.php"; $GLOBALS['page_name']="登录"; showHeader();?>

<?php
global $DB_conn;
if (isset($_COOKIE['user']) && isset($_COOKIE['password'])) {
    if ($DB_conn->query("select * from user where user='{$_COOKIE['user']}' and password='{$_COOKIE['password']}'")->num_rows == 0) {
        setcookie("user", NULL);
        setcookie("password", NULL);
    } else {
        header("Location: ./");
    }
}
if (isset($_POST['username']) && isset($_POST['password'])) {
    $password = md5(md5(md5($_POST['password'].'233')));
    $DB_command = "select * from user where user='{$_POST['username']}' and password='{$password}'";
    $DB_result = $DB_conn->query($DB_command);
    if ($DB_result->num_rows == 0) {
        showBlock("密码错误");
        include "footer.php";
    } else {
        setcookie("user", $_POST['username']);
        setcookie("password", $password);
        header("Location: ./");
    }
}

$self = htmlspecialchars($_SERVER["PHP_SELF"]);
$form =
<<<EOF
	<form method="post" action={$self} name=login>
		<p><div class="textframe"><input type="text" name="username" placeholder="用户名" ></div></p>
		<p><div class="textframe"><input type="password" name="password" placeholder="密码" ></div></p>
	</form>
    <p style="width: 40%">
        <a onclick="login.submit();"><button class="blue" style="float: left; width: 49%;" id="login">登录</button></a>
    	<a href="./register.php"><button class="blue" style="float: right; width: 49%;">注册</button></a>
    </p>
<script>
$("input").keydown(function(event){
  if(event.keyCode == 13){
    $("#login").trigger("click");
  }
});
</script>
EOF;
showBlock($form);
?>
	
<?php include "footer.php";?>
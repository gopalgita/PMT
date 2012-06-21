<?php
include "includes/FusionCharts.php";
include "includes/config.php";
include "includes/functions.php";
include "includes/connect.php";
include "includes/user.php";
include "includes/init.php";
menuHeader("users");
session_start();
if (empty($_SESSION["Eid"]))
    do_redirect('login.php');
include_once ('menu.php');
?>
<?php
if (!empty($_POST["Eid"])) {
    $newEid = $_POST["Eid"];
    $newEmpid = $_POST["empname"];
    $newDateOfJoining = $_POST["Dateofjoining"];
    $email = getEmailId($newEid);
    $name = explode(".", $email);
    $password = "$newEid" . "$name[0]";
    $password = md5($password);
    $sql = "INSERT INTO pmdb_user (EmployeeName, ID, IsAdmin) Values(\"$newEmpid\",\"$newEid\", \"1\")";
    $res = dbi_query($sql);
    if ($res)
        echo "Success fully add the $newEmpid";
    else {
        echo "Error in Inertsting";
    }
}
echo "<br><br><br>";
?>
<script type="text/javascript">
    function validate(form) {
        var Eid = form.Eid.value;
        var empname = form.empname.value;
        if(form.Eid.disabled == false && Eid === "") {
            inlineMsg('Eid', 'You must enter the User ID of the user.', 2);
            return false;
        }
        if(form.empname.disabled == false && empname === "") {
            inlineMsg('empname', 'You must enter the Employee Name of the user.', 2);
            return false;
        }

        return true;
    }

    var MSGTIMER = 20;
    var MSGSPEED = 5;
    var MSGOFFSET = 3;
    var MSGHIDE = 3;

    // build out the divs, set attributes and call the fade function //
    function inlineMsg(target, string, autohide) {
        var msg;
        var msgcontent;
        if(!document.getElementById('msg')) {
            msg = document.createElement('div');
            msg.id = 'msg';
            msgcontent = document.createElement('div');
            msgcontent.id = 'msgcontent';
            document.body.appendChild(msg);
            msg.appendChild(msgcontent);
            msg.style.filter = 'alpha(opacity=0)';
            msg.style.opacity = 0;
            msg.alpha = 0;
        } else {
            msg = document.getElementById('msg');
            msgcontent = document.getElementById('msgcontent');
        }
        msgcontent.innerHTML = string;
        msg.style.display = 'block';
        var msgheight = msg.offsetHeight;
        var targetdiv = document.getElementById(target);
        targetdiv.focus();
        var targetheight = targetdiv.offsetHeight;
        var targetwidth = targetdiv.offsetWidth;
        var topposition = topPosition(targetdiv) - ((msgheight - targetheight) / 2);
        var leftposition = leftPosition(targetdiv) + targetwidth + MSGOFFSET;
        msg.style.top = topposition + 'px';
        msg.style.left = leftposition + 'px';
        clearInterval(msg.timer);
        msg.timer = setInterval("fadeMsg(1)", MSGTIMER);
        if(!autohide) {
            autohide = MSGHIDE;
        }
        window.setTimeout("hideMsg()", (autohide * 1000));
    }

    // hide the form alert //
    function hideMsg(msg) {
        var msg = document.getElementById('msg');
        if(!msg.timer) {
            msg.timer = setInterval("fadeMsg(0)", MSGTIMER);
        }
    }

    // face the message box //
    function fadeMsg(flag) {
        if(flag === null) {
            flag = 1;
        }
        var msg = document.getElementById('msg');
        var value;
        if(flag == 1) {
            value = msg.alpha + MSGSPEED;
        } else {
            value = msg.alpha - MSGSPEED;
        }
        msg.alpha = value;
        msg.style.opacity = (value / 100);
        msg.style.filter = 'alpha(opacity=' + value + ')';
        if(value >= 99) {
            clearInterval(msg.timer);
            msg.timer = null;
        } else if(value <= 1) {
            msg.style.display = "none";
            clearInterval(msg.timer);
        }
    }

    // calculate the position of the element in relation to the left of the browser //
    function leftPosition(target) {
        var left = 0;
        if(target.offsetParent) {
            while(1) {
                left += target.offsetLeft;
                if(!target.offsetParent) {
                    break;
                }
                target = target.offsetParent;
            }
        } else if(target.x) {
            left += target.x;
        }
        return left;
    }

    // calculate the position of the element in relation to the top of the browser window //
    function topPosition(target) {
        var top = 0;
        if(target.offsetParent) {
            while(1) {
                top += target.offsetTop;
                if(!target.offsetParent) {
                    break;
                }
                target = target.offsetParent;
            }
        } else if(target.y) {
            top += target.y;
        }
        return top;
    }

    // preload the arrow //
    if(document.images) {
        arrow = new Image(7, 80);
        arrow.src = "./images/msg_arrow.gif";
    }
</script>
<div id="user">
	<form name="form" id="form" class="form" action="users.php" onsubmit="return validate(this)" method="post">
		<?php
        inputDashboardTemplate("Eid", "User ID", "INPUT");
        inputDashboardTemplate("empname", "EmployeeName", "INPUT");
		?>
		<input type="submit" value="Add User" />
	</form>
</div>
<?php
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"./css/framework.css\" />";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"./css/menu.css\" />";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"./css/dashboard.css\" />";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"./css/style.css\" />";
menuTrailer();
?>
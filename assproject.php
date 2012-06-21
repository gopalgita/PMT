<?php
include_Once "includes/FusionCharts.php";
include_Once "includes/config.php";
include_Once "includes/functions.php";
include_Once "includes/connect.php";
include_Once "includes/user.php";
include_Once "includes/init.php";
menuHeader("users");
session_start();
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"./css/framework.css\" />";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"./css/menu.css\" />";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"./css/dashboard.css\" />";
if (empty($_SESSION["Eid"]))
    do_redirect('login.php');
include_once ('menu.php');
$user = $_GET["emp_id"];
if ($_POST["toBox"] != "") {
    $t = $_POST["toBox"];
    $sql = "DELETE FROM pmdb_projectuser WHERE UserId = '" . $_GET["emp_id"] . "'";
    $res = dbi_query($sql);    
    for ($i = 0; $i < count($t); $i++) {        
        $sql = "INSERT INTO pmdb_projectuser VALUES ('$t[$i]', '" . $_GET["emp_id"] . "')";
        $res = dbi_query($sql);
    }
}
?>
<style type="text/css">
.multipleSelectBoxControl input { /* Small butons */
    width: 25px;
}

multipleSelectBoxControl span { /* Labels above select boxes*/
    font-family: arial;
    font-size: 11px;
    font-weight: bold;
}

.multipleSelectBoxControl div option { /* Select box layout */
    font-family: arial;
}

.multipleSelectBoxControl div {
    float: left;
}

fieldset {
    width: 500px;
    allgin-left: 100px;
    float: Center;
}

img {
    border: 0px;
}

.form {
    margin-top: 10px;
}
.form label{
width :200px;
}

</style>
<script type="text/javascript"> 
        
    var fromBoxArray = new Array();
    var toBoxArray = new Array();
    var selectBoxIndex = 0;
    var arrayOfItemsToSelect = new Array();
    
    
    function moveSingleElement()
    {
        var selectBoxIndex = this.parentNode.parentNode.id.replace(/[^\d]/g,'');
        var tmpFromBox;
        var tmpToBox;
        if(this.tagName.toLowerCase()=='select'){           
            tmpFromBox = this;
            if(tmpFromBox==fromBoxArray[selectBoxIndex])tmpToBox = toBoxArray[selectBoxIndex]; else tmpToBox = fromBoxArray[selectBoxIndex];
        }else{
        
            if(this.value.indexOf('>')>=0){
                tmpFromBox = fromBoxArray[selectBoxIndex];
                tmpToBox = toBoxArray[selectBoxIndex];          
            }else{
                tmpFromBox = toBoxArray[selectBoxIndex];
                tmpToBox = fromBoxArray[selectBoxIndex];    
            }
        }
        
        for(var no=0;no<tmpFromBox.options.length;no++){
            if(tmpFromBox.options[no].selected){
                tmpFromBox.options[no].selected = false;
                tmpToBox.options[tmpToBox.options.length] = new Option(tmpFromBox.options[no].text,tmpFromBox.options[no].value);
                
                for(var no2=no;no2<(tmpFromBox.options.length-1);no2++){
                    tmpFromBox.options[no2].value = tmpFromBox.options[no2+1].value;
                    tmpFromBox.options[no2].text = tmpFromBox.options[no2+1].text;
                    tmpFromBox.options[no2].selected = tmpFromBox.options[no2+1].selected;
                }
                no = no -1;
                tmpFromBox.options.length = tmpFromBox.options.length-1;
                                            
            }           
        }
        
        
        var tmpTextArray = new Array();
        for(var no=0;no<tmpFromBox.options.length;no++){
            tmpTextArray.push(tmpFromBox.options[no].text + '___' + tmpFromBox.options[no].value);          
        }
//      tmpTextArray.sort();
        var tmpTextArray2 = new Array();
        for(var no=0;no<tmpToBox.options.length;no++){
            tmpTextArray2.push(tmpToBox.options[no].text + '___' + tmpToBox.options[no].value);     
        }       
//      tmpTextArray2.sort();
        
        for(var no=0;no<tmpTextArray.length;no++){
            var items = tmpTextArray[no].split('___');
            tmpFromBox.options[no] = new Option(items[0],items[1]);
            
        }       
        
        for(var no=0;no<tmpTextArray2.length;no++){
            var items = tmpTextArray2[no].split('___');
            tmpToBox.options[no] = new Option(items[0],items[1]);           
        }
    }
    
//  function sortAllElement(boxRef)
//  {
//      var tmpTextArray2 = new Array();
//      for(var no=0;no<boxRef.options.length;no++){
//          tmpTextArray2.push(boxRef.options[no].text + '___' + boxRef.options[no].value);         
//      }       
//      tmpTextArray2.sort();       
//      for(var no=0;no<tmpTextArray2.length;no++){
//          var items = tmpTextArray2[no].split('___');
//          boxRef.options[no] = new Option(items[0],items[1]);         
//      }       
//      
//  }
    function moveAllElements()
    {
        var selectBoxIndex = this.parentNode.parentNode.id.replace(/[^\d]/g,'');
        var tmpFromBox;
        var tmpToBox;       
        if(this.value.indexOf('>')>=0){
            tmpFromBox = fromBoxArray[selectBoxIndex];
            tmpToBox = toBoxArray[selectBoxIndex];          
        }else{
            tmpFromBox = toBoxArray[selectBoxIndex];
            tmpToBox = fromBoxArray[selectBoxIndex];    
        }
        
        for(var no=0;no<tmpFromBox.options.length;no++){
            tmpToBox.options[tmpToBox.options.length] = new Option(tmpFromBox.options[no].text,tmpFromBox.options[no].value);           
        }   
        
        tmpFromBox.options.length=0;
//      sortAllElement(tmpToBox);
        
    }
    
    
    /* This function highlights options in the "to-boxes". It is needed if the values should be remembered after submit. Call this function onsubmit for your form */
    function multipleSelectOnSubmit()
    {
        
        
        for(var no=0;no<arrayOfItemsToSelect.length;no++){
            var obj = arrayOfItemsToSelect[no];
            for(var no2=0;no2<obj.options.length;no2++){
                obj.options[no2].selected = true;
            }
        }
        
    }
    
function createMovableOptions(fromBox,toBox,totalWidth,totalHeight,labelLeft,labelRight){       
        fromObj = document.getElementById(fromBox);
        toObj = document.getElementById(toBox);
        
        arrayOfItemsToSelect[arrayOfItemsToSelect.length] = toObj;

        
        fromObj.ondblclick = moveSingleElement;
        toObj.ondblclick = moveSingleElement;

        
        fromBoxArray.push(fromObj);
        toBoxArray.push(toObj);
        
        var parentEl = fromObj.parentNode;
        
        var parentDiv = document.createElement('DIV');
        parentDiv.className='multipleSelectBoxControl';
        parentDiv.id = 'selectBoxGroup' + selectBoxIndex;
        parentDiv.style.width = totalWidth + 'px';
        parentDiv.style.height = totalHeight + 'px';
        parentEl.insertBefore(parentDiv,fromObj);
        
        
        var subDiv = document.createElement('DIV');
        subDiv.style.width = (Math.floor(totalWidth/2) - 15) + 'px';
        fromObj.style.width = (Math.floor(totalWidth/2) - 15) + 'px';

        var label = document.createElement('SPAN');
        label.innerHTML = labelLeft;
        subDiv.appendChild(label);
        
        subDiv.appendChild(fromObj);
        subDiv.className = 'multipleSelectBoxDiv';
        parentDiv.appendChild(subDiv);
        
        
        var buttonDiv = document.createElement('DIV');
        buttonDiv.style.verticalAlign = 'middle';
        buttonDiv.style.paddingTop = (totalHeight/2) - 50 + 'px';
        buttonDiv.style.width = '30px';
        buttonDiv.style.textAlign = 'center';
        parentDiv.appendChild(buttonDiv);
        
        var buttonRight = document.createElement('INPUT');
        buttonRight.type='button';
        buttonRight.value = '>';
        buttonDiv.appendChild(buttonRight); 
        buttonRight.onclick = moveSingleElement;    
        
        var buttonAllRight = document.createElement('INPUT');
        buttonAllRight.type='button';
        buttonAllRight.value = '>>';
        buttonAllRight.onclick = moveAllElements;
        buttonDiv.appendChild(buttonAllRight);      
        
        var buttonLeft = document.createElement('INPUT');
        buttonLeft.style.marginTop='10px';
        buttonLeft.type='button';
        buttonLeft.value = '<';
        buttonLeft.onclick = moveSingleElement;
        buttonDiv.appendChild(buttonLeft);      
        
        var buttonAllLeft = document.createElement('INPUT');
        buttonAllLeft.type='button';
        buttonAllLeft.value = '<<';
        buttonAllLeft.onclick = moveAllElements;
        buttonDiv.appendChild(buttonAllLeft);
        
        var subDiv = document.createElement('DIV');
        subDiv.style.width = (Math.floor(totalWidth/2) - 15) + 'px';
        toObj.style.width = (Math.floor(totalWidth/2) - 15) + 'px';

        var label = document.createElement('SPAN');
        label.innerHTML = labelRight;
        subDiv.appendChild(label);
                
        subDiv.appendChild(toObj);
        parentDiv.appendChild(subDiv);      
        
        toObj.style.height = (totalHeight - label.offsetHeight) + 'px';
        fromObj.style.height = (totalHeight - label.offsetHeight) + 'px';

            
        selectBoxIndex++;
        
    }   
</script>
<?php
$notAssociated = "";
$sql = "SELECT ProjectName FROM pmdb_project q WHERE ProjectName NOT IN (SELECT ProjectName FROM pmdb_projectuser p WHERE UserId = '" . $user ."');";
$res = dbi_query($sql);
$i = 0;
while ($row = dbi_fetch_row($res)) {
    $notAssociated[$i] = $row[0];
    $i++;
}
dbi_free_result($res);

$sql = "SELECT ProjectName FROM pmdb_projectuser q WHERE q.UserId = '$user'";
$res = dbi_query($sql);
$i = 0;
while ($row = dbi_fetch_row($res)) {
    $Associated[$i] = $row[0];
    $i++;
}
dbi_free_result($res);         
?>
<fieldset><legend>Select Project Selection</legend>
<div id="users">
<form name="form" id="form" class="form" method="post" action="assproject.php?emp_id=<?php echo $user; ?>" onsubmit="multipleSelectOnSubmit()">
<select multiple name="fromBox[]" id="fromBox">
<?php
for($i=0;$i<count($notAssociated);$i++){
    echo "<option value=\"$notAssociated[$i]\">$notAssociated[$i]</option>";
}   
?>
</select> <select multiple name="toBox[]" id="toBox">
<?php 
for($i=0;$i<count($Associated);$i++) {
    echo "<option value=\"$Associated[$i]\">$Associated[$i]</option>";
}
?>
</select> <input type="submit" value="Update"></form>
</div>
</fieldset>

<script type="text/javascript">
createMovableOptions("fromBox","toBox",500,500,'Available Projects','Associated Projects');
</script>
<script type="text/javascript">
    var infolink_pid = 8714;
    var infolink_wsid = 0;  
    var infolink_link_color = '009900';
    var infolink_title_color = '252667';
    var infolink_text_color = '000000';
    var infolink_ad_link_color = '24951E';
    var infolink_ad_effect_type = 0;
    var infolink_cat = 'software development';
</script>
</body>



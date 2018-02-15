<!DOCTYPE html>
<html>
    <?php if(!function_exists("getDatabase")) include "functions.php"; ?>
    <?php getHead("Antwort löschen", "survey.css"); ?>
    <body>
        <div class="CONTAINER">
            <?php
                //print_r($_GET);
                if(!checkForAdmin()){
                    echo '<h1>Error 400: Access restricted</h1>';
                    return;
                }
                if(isset($_GET["ID"]) && isset($_GET["QID"])){
                    $con = getDatabase();
                    if($con == null) return;
                    $aid = $_GET["ID"];
                    $aid = $con->real_escape_string($aid);
                    $uid = $_GET["QID"];
                    $uid = $con->real_escape_string($uid);
                    if(!is_numeric($aid)) die("'ID' hat das falsche Format");
                    if(!is_numeric($uid)) die("'QID' hat das falsche Format");
                    $sqlq = "DELETE FROM Antworten WHERE AID='$aid'";
                    $res = $con->query($sqlq);
                    $sqlq = "DELETE FROM besitzt WHERE AID='$aid' AND UID='$uid'";
                    $res2 = $con->query($sqlq);
                    
                    if($res && $res2){
                        echo "<h1>Löschung erfolgreich</h1>";
                    }
                    else{
                        echo "<h1>Die Operation ist leider fehlgeschlagen.</h1>";
                    }
                    return;
                }
                else{
                    echo "<h1>Error: Fehlende Parameter</h1>";
                }
            ?>
		</div>
	</body>
</html>
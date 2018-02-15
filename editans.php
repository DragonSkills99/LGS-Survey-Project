<!DOCTYPE html>
<html>
    <?php if(!function_exists("getDatabase")) include "functions.php"; ?>
    <?php getHead("Antwort bearbeiten", "survey.css"); ?>
    <body>
        <div class="CONTAINER">
            <?php
                //print_r($_GET);
                if(!checkForAdmin()){
                    echo '<h1>Error 400: Access restricted</h1>';
                    return;
                }
                if(isset($_POST["edit"]) && isset($_GET["ID"])){
                    $con = getDatabase();
                    if($con == null) return;
                    $txt = $_POST["text"];
                    $txt = $con->real_escape_string($txt);
                    $id = $_GET["ID"];
                    $id = $con->real_escape_string($id);
                    if(!is_numeric($id)) die("'ID' hat das falsche Format");
                    $sqlq = "UPDATE Antworten SET Text='$txt' WHERE AID='$id'";
                    $res = $con->query($sqlq);
                    
                    if($res){
                        echo "<h1>Eintrag erfolgreich</h1>";
                    }
                    else{
                        echo "<h1>Die Operation ist leider fehlgeschlagen.</h1>";
                    }
                    return;
                }
                else if(isset($_POST["edit"]) && isset($_GET["QID"])){
                    $con = getDatabase();
                    if($con == null) return;
                    $txt = $_POST["text"];
                    $txt = $con->real_escape_string($txt);
                    $sqlq = "INSERT INTO Antworten(Text, Zähler) VALUES ('$txt', 0)";
                    $res = $con->query($sqlq);
                    $aid = $con->insert_id;
                    $uid = $_GET["QID"];
                    $uid = $con->real_escape_string($uid);
                    if(!is_numeric($uid)) die("'QID' hat das falsche Format");
                    $sqlq = "INSERT INTO besitzt(UID, AID) VALUES ('$uid', '$aid')";
                    $res = $con->query($sqlq);
                    
                    if($res){
                        echo "<h1>Eintrag erfolgreich</h1>";
                    }
                    else{
                        echo "<h1>Die Operation ist leider fehlgeschlagen.</h1>";
                    }
                    return;
                }
                else if(isset($_GET["ID"]))
                {
                    $con = getDatabase();
                    if($con == null) return;
                    $id = $_GET["ID"];
                    $id = $con->real_escape_string($id);
                    if(!is_numeric($id)) die("'ID' hat das falsche Format");
                    $sqlq = "SELECT Text FROM Antworten WHERE AID='$id'";
                    $res = $con->query($sqlq);
                    if($res->num_rows > 0)
                        $obj = $res->fetch_object();
                    ?>
                    <?php
                }
                else{
                    //echo "<h1>Error: Es fehlen Parameter";
                }
            ?>
            <form action="?<?php if(isset($_GET["ID"])) echo "ID=".$_GET["ID"]."&"; ?><?php if(isset($_GET["QID"])) echo "QID=".$_GET["QID"]; ?>" method="post">
                <table>
                <tr>
                    <td>Text:</td>
                    <td><input type="text" name="text" value="<?php if(isset($obj)) echo htmlspecialchars($obj->Text); ?>"</td>
                </tr>
                <tr>
                    <td colspan="2" style="align-content: center;text-align: center;vertical-align: center;"><input type="submit" name="edit" value="Speichern"></td>
                </tr>
            </table>
            </form>
		</div>
	</body>
</html>
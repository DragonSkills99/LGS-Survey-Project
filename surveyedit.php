<!DOCTYPE html>
<html>
    <?php if(!function_exists("getDatabase")) include "functions.php"; ?>
    <?php getHead("Umfragenbearbeitung", "survey.css"); ?>
    <body>
	    <?php include "menu.php"; ?>
        <div class="CONTAINER">
            <?php
            if(!checkForAdmin()){
                echo '<h1>Error 400: Access restricted</h1>';
                return;
            }
            
            if(isset($_POST["submit"]) && isset($_POST["id"])){
                $con = getDatabase();
                if($con == null) return;
                $id = $_POST["id"];
                $q = $_POST["q"];
                $q = $con->real_escape_string($q);
                $id = $con->real_escape_string($id);
                if(!is_numeric($id)) die("'id' hat das falsche Format");
                $sqlq = "UPDATE Umfrage SET Frage='$q' WHERE UID='$id'";
                $res = $con->query($sqlq);
                if($res){
                    echo '<a href="?ID='.$id.'"><h1>Frage erfolgreich erstellt</h1></a>';
                    ?>
                    <script>
                        window.location = "?ID=<?php echo $id; ?>";
                    </script>
                    <?php
                }
                else{
                    echo '<h1>Das erstellen der Frage ist leider Fehlgeschlagen</h1>';
                }
                return;
            }
            else if(isset($_POST["submit"])){
                $con = getDatabase();
                if($con == null) return;
                $q = $_POST["q"];
                $q = $con->real_escape_string($q);
                $sqlq = "INSERT INTO Umfrage(Frage) VALUES ('$q')";
                $res = $con->query($sqlq);
                if($res){
                    echo '<a href="?ID='.$con->insert_id.'"><h1>Frage erfolgreich erstellt</h1></a>';
                    ?>
                    <script>
                        window.location = "?ID=<?php echo $con->insert_id; ?>";
                    </script>
                    <?php
                }
                else{
                    echo '<h1>Das erstellen der Frage ist leider Fehlgeschlagen</h1>';
                }
                return;
            }
            
            if(isset($_GET["ID"]) || isset($_GET["NEW"])){
                $pid = null;
                $text = null;
                $answers = null;
                $con = getDatabase();
                if($con == null) return;
                if(isset($_GET["ID"])){
                    $id = $_GET["ID"];
                    $pid = $id;
                    if(!is_numeric($id)) die("'ID' hat das falsche Format");
                    $sqlq = "SELECT Frage FROM Umfrage WHERE UID='$id'";
                    $res = $con->query($sqlq);
                    if($res->num_rows > 0){
                        $text = $res->fetch_object()->Frage;
                    }
                    $sqlq = "SELECT besitzt.AID, Antworten.Text FROM besitzt LEFT JOIN Antworten ON Antworten.AID = besitzt.AID WHERE UID='$id'";
                    $res = $con->query($sqlq);
                    $answers = array();
                    while($obj = $res->fetch_object()){
                        $answers[$obj->AID] = $obj->Text;
                    }
                }
                else{
                    
                }
                
                ?>
                <form method="post">
                    <table>
                    <tr>
                        <td>Frage:</td>
                        <td>
                            <input type="text" name="q" value="<?php if($text != null) echo $text; ?>">
                        </td>
                    </tr>
                    <tr>
                        <td>Antworten:</td>
                        <td>
                            <table>
                                <?php
                                if($answers != null){
                                    foreach ($answers as $key => $value) {
                                        echo '<tr>';
                                        echo '<td>'.$value.'</td>';
                                        echo '<td><button class="cbtns" onclick="editans('.$key.')">Bearbeiten</button>';
                                        echo '<button class="cbtns" onclick="delans('.$key.')">Löschen</button></td>';
                                        echo '<tr>';
                                    }
                                }
                                if($pid != null){
                                    ?>
                                    <tr>
                                        <td colspan="2">
                                            <button class="cbtns" onclick="createans()">Hinzufügen</button>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </table>
                        </td>
                        
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input type="submit" name="submit" value="Frage erstellen/bearbeiten">
                            <?php
                            if($pid != null){
                                ?>
                                <input type="hidden" name="id" value="<?php echo $pid; ?>">
                                <?php
                            }
                            ?>
                        </td>
                    </tr>
                </table>
                </form>
                <script type="text/javascript">
                    function editans(id){
                        document.body.innerHTML += getPage("popupiframe.php?url=" + "editans.php%3FID=" + id + "%26QID=<?php echo $pid; ?>");
                    }
                    function delans(id){
                        document.body.innerHTML += getPage("popupiframe.php?url=" + "delans.php%3FID=" + id + "%26QID=<?php echo $pid; ?>");
                    }
                    function createans(){
                        document.body.innerHTML += getPage("popupiframe.php?url=" + "editans.php%3FQID=<?php echo $pid; ?>");
                    }
                    function closeiframe(){
                        var ifs = document.getElementsByClassName("popup");
                        for(var i = 0; i < ifs.length; i++){
                            var ifr = ifs[i];
                            ifr.parentNode.removeChild(ifr);
                        }
                    }
                    
                    function getPage(url) {
                        return $.ajax({
                            url: url,
                            async: false,
                            success: function(data) {
                                return data;
                            }
                        }).responseText;
                    }
                </script>
            <?php
            }
            else{
                $con = getDatabase();
                $sqlq = "SELECT * FROM Umfrage";
                $res = $con->query($sqlq);
                if(!$res) die("Abfrage fehlgeschlagen");
                echo '<table style="width: 100%;">';
                echo '<tr><td><a style="text-decoration: none;" href="/sp/survey/surveyedit.php?NEW"><h2 style="margin-left: 15px;">Neue Frage erstellen</h2></a></td></tr>';
                while($obj = $res->fetch_object()){
                    echo '<tr><td>';
                    echo '<a style="text-decoration: none;" href="/sp/survey/surveyedit.php?ID='.$obj->UID.'"><h2 style="margin-left: 15px;">'.$obj->Frage.'</h2></a>';
                    echo '</td></tr>';
                }
                echo '</table>';
            }
            ?>
        </div>
    </body>
</html>
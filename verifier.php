<!DOCTYPE html>
<html>
    <?php if(!function_exists("getDatabase")) include "functions.php"; ?>
    <?php getHead("E-Mail Validierung", "survey.css"); ?>
    <body>
        <div class="CONTAINER">
            <?php
            if(isset($_GET["key"]) && isset($_GET["email"])){
                $con = getDatabase();
                if($con == null) return;
                $key = $_GET["key"];
                $email = $_GET["email"];
                $key = $con->real_escape_string($key);
                $email = $con->real_escape_string($email);
                $sqlq = "UPDATE Users SET valid='1' WHERE email='$email' AND vkey='$key'";
                $res = $con->query($sqlq);
                //print_r($res);
                //print_r($con);
                if($con->affected_rows > 0){
                    if(loggedIn()){
                        openSession();
                        $_SESSION["valid"] = 1;
                    }
                    echo '<h1>Ihre E-Mail-Adresse wurde erfolgreich bestätigt</h1>';
                }
                else
                {
                    echo '<h1>Ihre E-Mail-Adresse konnte leider nicht bestätigt werden.</h1>';
                }
            }
            else{
                echo '<h1>Ihre E-Mail-Adresse konnte leider nicht bestätigt werden.</h1>';
            }
            ?>
        </div>
    </body>
</html>
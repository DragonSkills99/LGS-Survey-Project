<!DOCTYPE html>
<html>
    <?php if(!function_exists("getDatabase")) include "functions.php"; ?>
    <?php getHead("Registration", "survey.css"); ?>
    <body>
        <?php include "menu.php"; ?>
        <div class="CONTAINER">
        <?php
            if(isset($_POST["submit"])){
                $username = $_POST["username"];
                $password1 = $_POST["password1"];
                $password2 = $_POST["password2"];
                $pwhash = md5($password1);
                $email = $_POST["email"];
                
                $con = getDatabase();
                if($con != null){
                    if(checkPasswords($password1, $password2) && checkUsername($con, $username) && checkEmail($con, $email)){
                        $username = $con->real_escape_string($username);
                        $email = $con->real_escape_string($email);
                        $vkey = getRandomString();
                        $sqlq = "INSERT INTO Users(username, password, email, vkey) VALUES ('$username', '$pwhash', '$email', '$vkey')";
                        $res = $con->query($sqlq);
                        if($res){
                            $msg = 
"
Sehr geehrter Kunde dies sind Ihre Anmeldedaten:<br>
<br>
Nutzername:     $username<br>
Passwort:       $password1<br>
<br>
Bitte geben sie die folgende Address in Ihrem Browser ein, um Ihr Konto zu bestätigen:<br>
<br>
<a href=\"https://an140134.lgsit.de/sp/survey/verifier.php?key=".urlencode($vkey)."&email=".urlencode($email)."\">https://an140134.lgsit.de/sp/survey/verifier.php?key=".urlencode($vkey)."&email=".urlencode($email)."</a><br>
<br>
Anschließend können Sie auf <a href=\"https://an140134.lgsit.de/sp/survey\">https://an140134.lgsit.de/sp/survey</a> an Umfragen teilnehmen
<br>
Ich danke Ihnen vielmals,<br>
DragonSkills99
";
                            if(mmail($email, "Registration bei DragonSurvey", $msg) === true){
                                ?><a href="login.php" style="color: cyan;"><h1 style="color: cyan;">Sie haben die Registration erfolgreich abgeschlossen, nun müssen Sie nur noch Ihre E-Mail-Adresse bestätigen um an Umfragen teilnehmen zu können.</h1></a><br><?php
                            }
                            else{
                                ?><a href="register.php" style="color: cyan;"><h1 style="color: cyan;">Das senden der Bestätigungsmail ist leider fehlgeschlagen, bitte wenden Sie sich an den Administrator.</h1></a><br><?php
                            }
                        }
                        else{
                            ?><a href="register.php" style="color: cyan;"><h1 style="color: cyan;">Der Eintrag in die Datenbank ist leider fehlgeschlagen.</h1></a><br><?php
                        }
                    }
                }
            }
            else{
                ?>
                <form method="post">
                    <table>
                        <tr>
                            <td>Nutzername:</td>
                            <td><input type="text" name="username"></td>
                        </tr>
                        <tr>
                            <td>Passwort:</td>
                            <td><input type="password" name="password1"></td>
                        </tr>
                        <tr>
                            <td>Passwort wiederholen:</td>
                            <td><input type="password" name="password2"></td>
                        </tr>
                        <tr>
                            <td>E-Mail:</td>
                            <td><input type="email" name="email"></td>
                        </tr>
                        <tr>
                            <td colspan="2"><a style="color: orange;">Die E-Mail-Adresse <b>muss</b> auf <b><i>@lgs-hu.eu</i></b> enden.</a></td>
                        </tr>
                        <tr>
                            <td colspan="2" style="align-content: center;text-align: center;vertical-align: center;"><input type="submit" name="submit" value="Registrieren"></td>
                        </tr>
                    </table>
                </form>
                <a href="login.php" style="color: cyan;"><h1>Schon registriert?</h1></a>
                <?php
            }
        ?>
        </div>
    </body>
</html>
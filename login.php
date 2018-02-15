<!DOCTYPE html>
<html>
    <?php if(!function_exists("getDatabase")) include "functions.php"; ?>
    <?php getHead("Anmeldung", "survey.css"); ?>
    <body>
        <?php include "menu.php"; ?>
        <div class="CONTAINER">
        <?php
            if(isset($_POST["submit"])){
                $username = $_POST["username"];
                $password = $_POST["password"];
                $con = getDatabase();
                if($con != null){
                    $username = $con->real_escape_string($username);
                    $password = md5($password);
                    //echo $username." ; ".$password;
                    $sqlq = "SELECT * FROM Users WHERE username='$username' AND password='$password'";
                    $res = $con->query($sqlq);
                    //print_r($res);
                    if($res->num_rows == 1){
                        $user = $res->fetch_object();
                        openSession();
                        $_SESSION["ID"] = $user->UID;
                        $_SESSION["UN"] = $user->username;
                        $_SESSION["valid"] = $user->valid;
                        ?><a href="." style="color: cyan;"><h1 style="color: cyan;">Herzlichen Glückwunsch, Sie haben sich erfolgreich eingeloggt.</h1></a><br><?php
                    }
                    else{
                        ?><a href="login.php" style="color: cyan;"><h1 style="color: cyan;">Falscher Nutzername oder Passwort.</h1></a><br><?php
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
                            <td><input type="password" name="password"></td>
                        </tr>
                        <tr>
                            <td colspan="2" style="align-content: center;text-align: center;vertical-align: center;"><input type="submit" name="submit" value="Anmelden"></td>
                        </tr>
                    </table>
                </form>
                <a href="register.php" style="color: cyan;"><h1>Noch kein Konto?</h1></a>
                <?php
            }
        ?>
        </div>
    </body>
</html>
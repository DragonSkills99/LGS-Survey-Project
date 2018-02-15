<!DOCTYPE html>
<html>
    <?php if(!function_exists("getDatabase")) include "functions.php"; ?>
    <?php getHead("Logout", "survey.css"); ?>
    <body>
        <?php include "menu.php"; ?>
        <div class="CONTAINER">
            <?php destroySession() ?>
            <a href="/sp/survey"><h1>Erfolgreich ausgeloggt.</h1></a>
        </div>
    </body>
</html>
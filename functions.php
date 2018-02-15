<?php
function getHead($title, $stylesheet, $favicon = "survey.ico", $charset = "UTF-8"){
    ?>
    <head>
		<link href="<?php echo $stylesheet; ?>" rel="stylesheet" type="text/css">
        <title><?php echo $title; ?></title>
        <meta charset="<?php echo $charset; ?>">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <link rel="icon" type="img/ico" href="<?php echo $favicon; ?>">
    </head>
    <?php
}

function mmail($sendto, $subject, $message){
    if(!class_exists("PHPMailer")) include '/usr/share/php/libphp-phpmailer/PHPMailerAutoload.php';

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->CharSet = 'UTF-8';
    
    $mail->Host       = "lgs-hu.eu"; // SMTP server example
    $mail->SMTPDebug  = 0;                     // enables SMTP debug information (for testing)
    $mail->SMTPAuth   = true;                  // enable SMTP authentication
    $mail->Username   = "vorname.nachname"; // SMTP account username example
    $mail->Password   = "passwort";
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;                    // set the SMTP port for the GMAIL server
    
    $mail->setFrom('benjamin.muenz@lgs-hu.eu', 'Dragon-Survey');
    $mail->addAddress($sendto);
    
    $mail->isHTML(false);
    $mail->Subject = $subject;
    $mail->Body = $message;
    $mail->AltBody = $message;
    
    if($mail->send()){
        return true;
    }
    else{
        return 'An error occured: '.$mail->ErrorInfo;
    }
}

function getDatabase(){
    $con = mysqli_connect("localhost", "Umfrage", parsePassword(file_get_contents("/var/www/html/sp/survey/pw.php")), "Umfrage");
    if (mysqli_connect_errno())
	{
	    echo "<h1>Verbindung zur Datenbank fehlgeschlagen: " . mysqli_connect_error()."</h1>";
	    $con = null;
	}
	$con->set_charset("utf8");
	return $con;
}

function parsePassword($filecontent){
    return substr($filecontent, 8, strlen($filecontent) - 11);
}

function checkPasswords($password1, $password2){
    $password1hash = md5($password1);
    $password2hash = md5($password2);
    if($password1hash != $password2hash){
        ?>
        <a href="register.php" style="color: cyan;"><h1 style="color: cyan;">Die Passwörter stimmen nicht überein.</h1></a><br>
        <?php
        return false;
    }
    return true;
}

function checkUsername($con, $username){
    $username = $con->real_escape_string($username);
    $sqlq = "SELECT * FROM Users WHERE username='$username'";
    $res = $con->query($sqlq);
    if($res->num_rows > 0){
        ?>
        <a href="register.php" style="color: cyan;"><h1 style="color: cyan;">Der Nutzername ist schon vergeben.</h1></a><br>
        <?php
        return false;
    }
    return true;
}

function startsWith($haystack, $needle)
{
     $length = strlen($needle);
     return (substr($haystack, 0, $length) === $needle);
}

function endsWith($haystack, $needle)
{
    $length = strlen($needle);

    return $length === 0 || 
    (substr($haystack, -$length) === $needle);
}

function checkEmail($con, $email){
    /*//if(!class_exists("EmailChecker"))
        use EmailChecker\EmailChecker;//use "/var/www/html/EmailChecker/src/EmailChecker/";
    
    $ec = new EmailChecker();
    if(!$ec->isValid($email)) {
        ?>
        <a href="register.php" style="color: cyan;"><h1 style="color: cyan;">Diese E-Mail-Adresse ist nicht gültig.</h1></a><br>
        <?php
        return false;
    }*/
    
    $email = $con->real_escape_string($email);
    
    if(!endsWith($email, "@lgs-hu.eu")){
        ?>
        <a href="register.php" style="color: cyan;"><h1 style="color: cyan;">Es sind nur lgs-hu.eu E-Mail-Adressen erlaubt.</h1></a><br>
        <?php
        return false;
    }
    
    $sqlq = "SELECT * FROM Users WHERE email='$email'";
    $res = $con->query($sqlq);
    if($res->num_rows > 0){
        ?>
        <a href="register.php" style="color: cyan;"><h1 style="color: cyan;">Es existiert bereits ein Nutzer mit dieser E-Mail.</h1></a><br>
        <?php
        return false;
    }
    return true;
}

function getRandomChar(){
    $strkey = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    return substr( $strkey ,mt_rand( 0 ,strlen($strkey) - 1 ) ,1 );
}

function getRandomString($lenght = 32){
    $ret = "";
    for($i = 0; $i < $lenght; $i++){
        $ret .= getRandomChar();
    }
    return $ret;
}

global $admins;
$admins = array(1);

function openSession(){
    if(session_status() != PHP_SESSION_ACTIVE){
        session_start();
    }
}

function destroySession(){
    openSession();
    session_destroy();
}

function checkForAdmin(){
    global $admins; 
    openSession();
    if(!isset($_SESSION["ID"])) return false;
    return in_array($_SESSION["ID"], $admins);
}

function loggedIn(){
    openSession();
    return isset($_SESSION["ID"]);
}

function validUser(){
    openSession();
    return isset($_SESSION["valid"]) && $_SESSION["valid"];
}

function isQuestioner(){
    return loggedIn() && validUser();
}

function getuserid(){
    openSession();
    if(!isset($_SESSION["ID"])) return -1;
    return $_SESSION["ID"];
}

function alreadyAnswered($con, $id){
    $UmID = $id;
    if(!is_numeric($id)) die("'\$id' hat das falsche Format");
    $UsID = getuserid();
    $sqlq = "SELECT * FROM beantwortet WHERE UmID='$UmID' AND UsID='$UsID'";
    $res = $con->query($sqlq);
    return $res->num_rows > 0;
}
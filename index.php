<!DOCTYPE html>
<html>
    <?php if(!function_exists("getDatabase")) include "functions.php"; ?>
    <?php getHead("Umfrage", "survey.css"); ?>
	<body>
	    <?php include "menu.php"; ?>
        <div style="height: 30px;"></div>
		<div class="CONTAINER">
			<?php 
			if(!loggedIn() && isset($_GET["ID"]) && !isset($_GET["prev"])){
			    include "popupiframe.php";
			    unset($_POST["save_survey"]);
			}
			if(isset($_POST["save_survey"])){
                if(!isQuestioner()){
                    if(!validUser() && loggedIn()){
                        echo '<h1>Sie müssen Ihre E-Mail Adresse bestätigen, bevor sie Umfragen beantworten können.</h1>';
                    }
                    else{
                        ?>
        			    <script type="text/javascript">
        			        window.location = "login.php";
        			    </script>
        			    <a href="login.php" style="color: cyan; font-weight: bold;">Sollten Sie nicht weitergeleitet werden, so klicken sie bitte hier.</a>
        			    <?php
                    }
    			    return;
                }
			    $con =  getDatabase();
				if($con != null) {
				    $id = $_GET["ID"];
                    if(alreadyAnswered($con, $id)){
                        ?><a href="." style="color: cyan;"><h1 style="color: cyan;">Sie haben diese Umfrage bereits beantwortet.</h1></a><br><?php
                        return;
                    }
    			    $ans = $_POST["ans"];
    			    $ans = $con->real_escape_string($ans);
    			    if(!is_numeric($ans)) die("'ans' hat das falsche Format");
    			    $sqlq = "SELECT Zähler FROM Antworten WHERE AID='$ans'";
    			    $res = $con->query($sqlq);
    			    if($res){
    			        $i = $res->fetch_object()->Zähler;
    			        $i++;
    			        $sqlq = "UPDATE Antworten SET Zähler='$i' WHERE AID='$ans'";
    			        $res = $con->query($sqlq);
    			        if($res){
    			            $UsID = getuserid();
    			            if(!is_numeric($id)) die("'ID' hat das falsche Format");
    			            $sqlq = "INSERT INTO beantwortet(UmID, UsID) VALUES ('$id', '$UsID')";
    			            $con->query($sqlq);
    			            ?>
    			            <script type="text/javascript">
    			                window.location = "?ID=<?php echo $id; ?>&prev";
    			            </script>
    			            <a href="?ID=<?php echo $id; ?>&prev" style="color: cyan; font-weight: bold;">Sollten Sie nicht weitergeleitet werden, so klicken sie bitte hier.</a>
    			            <?php
    			            //header("Location: ?ID=$id&prev");
    			        }
    			        else{
    			            echo "Der Eintrag ist leider fehlgeschlagen: ".$con->error;
    			        }
    			    }
    			    else{
    			        echo $con->error;
    			    }
				}
			}
			else if(isset($_GET["ID"]) && isset($_GET["prev"])){
			    $con =  getDatabase();
				if($con != null) {
					$id = $_GET["ID"];
					$id = $con->real_escape_string($id);
					if(!is_numeric($id)) die("'ID' hat das falsche Format");
					$sqlq = "SELECT * FROM Umfrage WHERE UID='$id'";
					$res = $con->query($sqlq);
					//print_r($res);
					if($res->num_rows > 0){
						$row = $res->fetch_row();
						echo "<h1>".$row[1]."</h1>";
						?>
						<table style="width: 100%">
						    <tr>
						        <th>Antwortmöglichkeit</th>
						        <th>Antworten</th>
						        <th>Statistik</th>
						    </tr>
						<?php
						if(!is_numeric($id)) die("'ID' hat das falsche Format");
					    $sqlq = "SELECT SUM(Antworten.Zähler) FROM besitzt LEFT JOIN Antworten ON Antworten.AID = besitzt.AID WHERE UID='$id'";
					    $res = $con->query($sqlq);
					    echo $con->error;
					    if(!$res) die("Abfrage fehlgeschlagen");
					    $sum = $res->fetch_row()[0];
					    
						$sqlq = "SELECT besitzt.AID, Antworten.Text, Antworten.Zähler FROM besitzt LEFT JOIN Antworten ON Antworten.AID = besitzt.AID WHERE UID='$id'";
						
						$res = $con->query($sqlq);
						if(!$res) die("Abfrage fehlgeschlagen");
						$i = 0;
						//print_r($res);
						while($row = $res->fetch_object()){
						    $i++;
						    if($sum != 0) $percentage = ($row->Zähler / $sum) * 100; else $percentage = 0;
							?>
							<tr>
							    <td><span><?php echo $row->Text; ?></span></td>
							    <td style="text-align: center;"><span class="animcounter"><?php echo $row->Zähler; ?></span></td>
							    <td style="text-align: center;"><span style="position: relative; z-index: 1;"><a class="animnum"><?php echo number_format($percentage, 2, ".", ""); ?></a> %</span><div class="statistic" style="text-align: center; width: <?php echo $percentage ?>%;"></div></td>
							</tr>
							<?php
							//print_r($r);
							//print_r($row);echo "<hr>";
						}
						?>
						<script>
						    $('.animnum').each(function() {
                              $(this).prop('Counter', 0).animate({
                                Counter: $(this).text()
                              }, {
                                duration: 3000,
                                easing: 'swing',
                                step: function(now) {
                                  $(this).text((Math.ceil(now * 100) / 100).toFixed(2));
                                }
                              });
                            });
						    $('.animcounter').each(function() {
                              $(this).prop('Counter', 0).animate({
                                Counter: $(this).text()
                              }, {
                                duration: 3000,
                                easing: 'swing',
                                step: function(now) {
                                  $(this).text((Math.ceil(now)));
                                }
                              });
                            });
						</script>
						<?php
						echo '</table>';
					}
					else{
						echo "<h1>Error 404: Diese Umfrage ist nicht vorhanden.";
					}
				}
			}
			else if(isset($_GET["ID"])){
				$con =  getDatabase();
				if($con != null) {
					$id = $_GET["ID"];
				    if(alreadyAnswered($con, $id)){
				        ?><script type="text/javascript">
    			            window.location = ".?ID=<?php echo $id; ?>&prev";
    			        </script><?php
				    }
					$id = $con->real_escape_string($id);
					if(!is_numeric($id)) die("'ID' hat das falsche Format");
					$sqlq = "SELECT * FROM Umfrage WHERE UID='$id'";
					$res = $con->query($sqlq);
					//print_r($res);
					if($res->num_rows > 0){
						$row = $res->fetch_row();
						echo "<h1>".$row[1]."</h1>";
						?><form method="post" onsubmit="return validateForm()"><?php
					
						$sqlq = "SELECT besitzt.AID, Antworten.Text FROM besitzt LEFT JOIN Antworten ON Antworten.AID = besitzt.AID WHERE UID='$id'";
						
						$res = $con->query($sqlq);
						//echo $con->error;
						$i = 0;
						//print_r($res);
						while($row = $res->fetch_object()){
						    $i++;
							?>
							<p>
                              <input type="radio" id="ans<?php echo $i; ?>" name="ans" value="<?php echo $row->AID; ?>">
                              <label for="ans<?php echo $i; ?>"><?php echo $row->Text; ?></label>
                            </p>
							<?php
							//print_r($r);
							//print_r($row);echo "<hr>";
						}
						
						echo '<input name="save_survey" type="submit" value="Antwort absenden">';
						echo '</form>';
					}
					else{
						echo "<h1>Error 404: Diese Umfrage ist nicht vorhanden.";
					}
				}
			}
			else{
			    $con = getDatabase();
			    if($con == null) return;
			    $sqlq = "SELECT * FROM Umfrage";
			    $res = $con->query($sqlq);
			    echo '<ul>';
			    while($obj = $res->fetch_object()){
                    echo '<a href="?ID='.$obj->UID.'"><li>'.$obj->Frage.'</li></a>';   
			    }
			    echo '</ul>';
			}

			?>
			<script type="application/javascript">
				function validateForm(){
					var ip = document.getElementsByName("ans");
					for(var i=0; i < ip.length; i++){
					    var e = ip[i];
					    if(e.checked){
					        return true;
					    }
					}
					alert("Bitte wählen Sie eine Option, vor dem Absenden dieses Formulars.")
					return false;
				}
			</script>
		</div>
	</body>
</html>
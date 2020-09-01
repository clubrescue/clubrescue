<?php

	/*** PREVENT THE PAGE FROM BEING CACHED BY THE WEB BROWSER ***/
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Wed, 9 Apr 1986 21:00:00 GMT");
	require_once("../../../wp-authenticate.php");
	/*** REQUIRE USER AUTHENTICATION ***/
	login();
	/*** RETRIEVE LOGGED IN USER INFORMATION ***/
	$user = wp_get_current_user();
	
	
	// Include database class
	include '../../../util/utility.class.php';
	include '../../../util/database.class.php';
	
	if(current_user_can('contributor') ||  current_user_can('author') ||  current_user_can('editor') || current_user_can('administrator') ) {	
    
        $tableQuery =  'SELECT * FROM `cr_diplomabeheer` ORDER BY `Volgordenr` ASC';
                    
        $database = new Database();        
        $database->query($tableQuery);	
        $tableResult = $database->resultset();
		
		$tableQuery2 =  'SELECT DISTINCT `Soort` FROM `cr_diplomabeheer` ORDER BY `Soort` ASC';
                    
        $database2 = new Database();        
        $database2->query($tableQuery2);	
        $tableResult2 = $database2->resultset();

		echo '</br>';
        echo '<form action="diplomabeheer_update.php" method="post" name="diplomabeheer" id="diplomabeheer">';
        echo '<table class="striped">';
            echo '<tr>';
				echo '<th></th>';
				echo '<th>Naam</th>';
				echo '<th>Soort</th>';
				echo '<th>Omschrijving</th>';
				echo '<th>Extra omschrijving</th>';
				echo '<th>Volgordenr</th>';
				echo '<th>Maanden geldig</th>';
				echo '<th>Afkorting</th>';
            echo '<tr>';

            foreach ($tableResult as $key => $value) {
                echo '<tr>';
				echo '<td><input name="id[]" type="hidden" value="'.$value["id"] . '"></td>';
				echo '<td><input name="naam[]" type="text" value="'.$value["Naam"] . '"></td>';		
				echo '<td><select class="form-control" name="soort">';
				foreach ($tableResult2 as $key2 => $value2) {
					if ($value["Soort"] === $value2["Soort"]){
						echo '<option value="'.$value2["Soort"].'" selected="selected">'.$value2["Soort"].'</option>';
					}else{
						echo '<option value="'.$value2["Soort"].'">'.$value2["Soort"].'</option>';	
					}
				}
				echo '</select></td>';
				if ($value["Soort"] === 'Bondsdiploma'){
					echo '<td>'.$value["Omschrijving"].'</td>';
					echo '<td>'.$value["Extraomschrijving"].'</td>';
					echo '<td><input name="volgordenr[]" type="number" value="'.$value["Volgordenr"].'"></td>';
					echo '<td>'.$value["Maandengeldig"].'</td>';
					echo '<td><input name="afkorting[]" type="text" value="'.$value["Afkorting"].'"></td>';
				}else{
					echo '<td><input name="omschrijving[]" type="text" value="'.$value["Omschrijving"] . '"></td>';
					echo '<td><input name="extraomschrijving[]" type="text" value="'.$value["Extraomschrijving"] . '"></td>';
					echo '<td><input name="volgordenr[]" type="number" value="'.$value["Volgordenr"] . '"></td>';
					echo '<td><input name="maandengeldig[]" type="number" value="'.$value["Maandengeldig"] . '"></td>';
					echo '<td><input name="afkorting[]" type="text" value="'.$value["Afkorting"] . '"></td>';	
				}
				echo '</tr>';
            }
            echo '</table>
					</br>
                    <button class="btn waves-effect waves-light" type="submit" name="action">Update de diplomas
                    </button>
                </form>';
        }
		echo '<pre>';var_dump($queries);echo '</pre>';
?>
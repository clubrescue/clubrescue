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

    if(current_user_can('administrator') || $user->user_login === 'D271RRP') {	
    
        $tableQuery =  'SELECT * FROM `cr_diplomabeheer` ';	
        $tableQuery .= 'ORDER BY `id` DESC';
                    
        $database = new Database();        
        $database->query($tableQuery);	
        $tableResult = $database->resultset();	

        echo '<form action="alt-diplomabeheer_update.php" method="post" name="diplomabeheer" id="diplomabeheer">';
        echo '<table class="striped">';
            echo '<tr>';
				echo '<td>Naam</td>';
				echo '<td>Soort</td>';
				echo '<td>Omschrijving</td>';
				echo '<td>Extra omschrijving</td>';
				echo '<td>Volgorde nummer</td>';
				echo '<td>Maanden geldig</td>';
            echo '<tr>';

            foreach ($tableResult as $key => $value) {
                echo '<tr>';
				echo '<td><input name="id[]"type="hidden" value="'.$value["id"] . '"></td>';
				echo '<td><input name="naam[]"type="text" value="'.$value["Naam"] . '"></td>';
				echo '<td><input name="soort[]" type="text" value="'.$value["Soort"] . '"></td>';
				echo '<td><input name="omschrijving[]" value="'.$value["Omschrijving"] . '"></td>';
				echo '<td><input name="extraomschrijving[]"type="text" value="'.$value["Extraomschrijving"] . '"></td>';
				echo '<td><input name="volgnr[]" type="number" value="'.$value["Volgnr"] . '"></td>';
				echo '<td><input name="maandengeldig[]" type="number" value="'.$value["Maandengeldig"] . '"></td>';
				echo '</tr>';
            }
            
            echo '</table>
                    <button class="btn waves-effect waves-light" type="submit" name="action">Update het TCB systeem
                    </button>
                </form>';
        }

?>
<?php //include __DIR__ . '/../../../header.php'; ?>
<main>
  <div class="container"><br>
	<div class="row">
      <div class="col s12">
        <ul class="tabs">
          <li class="tab"><a class="active" href="#log">TCB diploma's en zelf beheerde bondsdiploma's</a></li>
        </ul>
      </div>
      <div id="log" class="col s12"><?php echo $tableQuery ?></div>
	</div>
  </div>
</main>
<?php //include '../../../footer.php'; ?>
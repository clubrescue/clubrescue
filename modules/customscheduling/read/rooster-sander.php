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

    $where4kader = 'BWK%';
    $where4ASCOPL = 'BWK'.Date("Y").'%';
    $whereIsNot = '%NI';
    
    $locations = ['','19','20','21','28','PC19','PC20','PC21','PC28','ASC','OPL','CVD','CVDS'];

    if (current_user_can('author') ||  current_user_can('editor') || current_user_can('administrator') || current_user_can('contributor')) {
        $query = "SELECT distinct `Activiteit` FROM  `cr_activiteiten` WHERE `Activiteit` LIKE '$where4kader' AND `Activiteit` NOT LIKE '$whereIsNot' ORDER BY `ACTIVITEIT` DESC";
    } else {
        $query = "SELECT distinct `Activiteit` FROM  `cr_activiteiten` WHERE  `Relatienr` =  '$user->user_login' AND  `Locatie` IN ('OPL',  'ASC') AND `Activiteit` LIKE '$where4ASCOPL' AND `Activiteit` NOT LIKE '$whereIsNot' ORDER BY `ACTIVITEIT` ASC";
    }
    $database = new Database();
    $database->query($query);
    $results = $database->resultset();
    
    if ($database->RowCount() > 0) {
        echo '<form action="" method="POST"><select class="form-control" name="week">';
        foreach ($results as $key => $value) {
            if ($_POST["week"] && $_POST["week"] === $value["Activiteit"]) {
                echo '<option value="'.$value["Activiteit"].'" selected="selected">'.$value["Activiteit"].'</option>';
            } else {
                echo '<option value="'.$value["Activiteit"].'">'.$value["Activiteit"].'</option>';
            }
        }
        echo '</select>
				<button class="btn waves-effect waves-light" type="submit" name="action">Open de geselecteerde week
					<i class="material-icons right">send</i>
				</button>
			  </form>';
        
        if (isset($_POST["week"])) {
            //SORTERING IN ROOSTER IS OP BASIS VAN:
            // - Meeste ervaring (Berekend vanuit cr_activiteiten - aantal * Relatienr en Activiteit BWK* zonder BWK*NI t/m de BWK* voorafgaand aan de te roosteren week, dus ervaring bij aanvang van de week)
            // - Leeftijd (Bij gelijke ervaring oudste bewaker eerst, berekend vanuit cr_leden GeboorteDatum - in dagen)
            // - Volgorde van database resultaten (Bij gelijke ervaring en leeftijd in dagen komt eerst de bewaker die als eerste door de query op de database is gevonden gevolgd door de eerst volgende en zo verder)																																																															\''.$_POST["week"].'\'																																	\''.$_POST["week"].'\'
            //		$tableQuery = ' SELECT leden.`RelatieNr`, leden.`VolledigeNaam`, act.`Locatie`, ervaring.`WekenErvaring`, leden.`GeboorteDatum`, FLOOR(DATEDIFF(NOW(),leden.`GeboorteDatum`)/\'365.242199\') as Leeftijd,
            //						ROW_NUMBER() OVER(PARTITION BY act.`Locatie` ORDER BY ervaring.`WekenErvaring` DESC, leden.`GeboorteDatum` ASC) `PostPositie` FROM `cr_leden` leden join `cr_activiteiten` act on leden.`RelatieNr` = act.`RelatieNr` and act.`Activiteit` = \''.$_POST["week"].'\' join (SELECT Relatienr, count(*) as `WekenErvaring` FROM `cr_activiteiten` WHERE `Activiteit` not like \'%NI\' AND `Activiteit` < \''.$_POST["week"].'\' GROUP BY Relatienr) ervaring on leden.`RelatieNr` = ervaring.`RelatieNr` ';

            $tableQuery = 'SELECT *,
								-- INDEX (=ROW_NUMBER) PER LOCATIE GESORTEERD OP WEKENERVARING DESC en GEBOORTEDATUM ASC INDIEN GELIJK
								ROW_NUMBER() OVER(PARTITION BY `Locatie` ORDER BY `WekenErvaring` DESC, `GeboorteDatum` ASC) `PostPositie`,
								-- AFRONDEN NAAR HELE WEKEN NAAR BOVEN PER POST OBV CASE STATEMENT HIERONDER
                                CEILING(SUM(`WekenErvaring`) OVER(PARTITION BY `Post`)/COUNT(`RelatieNr`) OVER(PARTITION BY `Post`)) `PostErvaring`
							FROM (
								SELECT 
									leden.`RelatieNr`,
									leden.`VolledigeNaam`,
									act.`Locatie`,
									ervaring.`WekenErvaring`,
									leden.`GeboorteDatum`,
									CASE 
										WHEN act.`Locatie` IN (\'19\', \'PC19\') THEN \'19\' 
										WHEN act.`Locatie` IN (\'20\', \'PC20\') THEN \'20\' 
										WHEN act.`Locatie` IN (\'21\', \'PC21\', \'CVDS\') THEN \'21\' 
										WHEN act.`Locatie` IN (\'28\', \'PC28\', \'CVD\') THEN \'28\' 
										WHEN act.`Locatie` IN (\'ASC\', \'OPL\') THEN \'ASCOPL\' 
									END `Post`,
									regel1.`CompetentiesRegel1`,
									regel2.`CompetentiesRegel2`,
									FLOOR(DATEDIFF(NOW(),leden.`GeboorteDatum`)/\'365.242199\') as Leeftijd
								FROM `cr_leden` leden
								join `cr_activiteiten` act 
									-- UPDATE HIER WELKE WEEK JE WILT HEBBEN
									on leden.`RelatieNr` = act.`RelatieNr` and act.`Activiteit` = \''.$_POST["week"].'\'
								left join (
									-- UPDATE HIER WELKE WEEK JE WILT HEBBEN
									SELECT Relatienr, count(*) as `WekenErvaring` FROM `cr_activiteiten` WHERE `Activiteit` not like \'%NI\' AND `Activiteit` < \''.$_POST["week"].'\' GROUP BY Relatienr
								) ervaring on leden.`RelatieNr` = ervaring.`RelatieNr`
								left join (
									SELECT diplomas.`Relatienr`, GROUP_CONCAT(DISTINCT beheer.`Afkorting` ORDER BY beheer.`Volgordenr` SEPARATOR \' \') as `CompetentiesRegel1`
									FROM `cr_diplomas` diplomas
									JOIN `cr_diplomabeheer` beheer
										ON beheer.`Naam` = diplomas.`Diploma` 
									WHERE 
										-- UPDATE HIER WELKE SOORTEN DIPLOMAS ER OP REGEL 1 MOETEN, DIPLOMA MOET GELDIG ZIJN OF GEEN EINDDATUM HEBBEN
										diplomas.`Soort` IN (\'PvB\', \'Niveau\') and (`EindDatum` = \'0000-00-00\' OR `EindDatum` >= NOW()) and beheer.`Afkorting` <> \'\'
									GROUP BY diplomas.`Relatienr`
								) regel1 ON regel1.`Relatienr` = leden.`RelatieNr`
								left join (
									SELECT diplomas.`Relatienr`, GROUP_CONCAT(DISTINCT beheer.`Afkorting` ORDER BY beheer.`Volgordenr` SEPARATOR \' \') as `CompetentiesRegel2`
									FROM `cr_diplomas` diplomas
									JOIN `cr_diplomabeheer` beheer
										ON beheer.`Naam` = diplomas.`Diploma` 
									WHERE 
										-- UPDATE HIER WELKE SOORTEN DIPLOMAS ER OP REGEL 2 MOETEN, DIPLOMA MOET GELDIG ZIJN OF GEEN EINDDATUM HEBBEN
										diplomas.`Soort` IN (\'Bondsdiploma\', \'EvC\') and (`EindDatum` = \'0000-00-00\' OR `EindDatum` >= NOW()) and beheer.`Afkorting` <> \'\'
									GROUP BY diplomas.`Relatienr`
								) regel2 ON regel2.`Relatienr` = leden.`RelatieNr`
							) AS BASE ';
    
            $database->query($tableQuery);
            $tableResult = $database->resultset();

            $formattedArray = [];
            foreach ($tableResult as $bewaker) {
                $formattedArray[$bewaker["Locatie"]][$bewaker["PostPositie"]] = $bewaker;
            }
            echo '<pre>';
            var_dump($formattedArray['CVD']);
        }
    } else {
        // Geen roosters om in te laden.
        echo 'Je hebt helaas geen roosters om in te laden!';
    }
?>
<?php
    if (isset($_POST["week"])) {
        ?>
<style type="text/css">
    .tg {
        border-collapse: collapse;
        border-spacing: 0;
    }

    .tg td {
        font-family: Calibri, Candara, Segoe, "Segoe UI", Optima, Arial, sans-serif;
        font-size: 15px;
        padding: 10px 5px;
        border-style: solid;
        border-width: 1px;
        overflow: hidden;
        word-break: normal;
        border-color: transparent
    }

    .tg th {
        font-family: Calibri, Candara, Segoe, "Segoe UI", Optima, Arial, sans-serif;
        font-size: 15px;
        padding: 10px 5px;
        border-style: solid;
        border-width: 1px;
        overflow: hidden;
        word-break: normal;
        border-color: transparent;
        font-weight: normal
    }

    .tg .tg-grijzebalk-textcenter {
        border-color: transparent;
        text-align: center;
        vertical-align: top;
        font-weight: bold;
        background-color: #d9d9d9
    }

    .tg .tg-grijzebalk-textleft {
        border-color: transparent;
        text-align: left;
        vertical-align: top;
        font-weight: bold;
        background-color: #d9d9d9
    }

    .tg .tg-grijzebalk-textright {
        border-color: transparent;
        text-align: right;
        vertical-align: top;
        font-weight: bold;
        background-color: #d9d9d9
    }

    .tg .tg-logobalk {
        border-color: transparent;
        text-align: center;
        vertical-align: top
    }

    .tg .tg-foto {
        border-color: transparent;
        text-align: left;
        vertical-align: top;
        background-color: transparent< !--#f2f2f2-->;
        width: 79px;
        height: 90px
    }

    .tg .tg-naam {
        border-color: transparent;
        text-align: center;
        vertical-align: top;
        font-weight: bold
    }

    .tg .tg-leeftijd {
        border-color: transparent;
        text-align: left;
        vertical-align: top
    }

    .tg .tg-ervaring {
        border-color: transparent;
        text-align: right;
        vertical-align: top
    }

    .tg .tg-competenties {
        border-color: transparent;
        text-align: left;
        vertical-align: top;
        font-size: 11px
    }

    .tg .tg-spacing {
        border-color: transparent;
        text-align: left;
        vertical-align: top
    }
</style>
<table class="tg">
    <tr>
        <th class="tg-grijzebalk-textleft">ASC</th>
        <th class="tg-grijzebalk-textright" colspan="2">Gem. erv.: <?php echo $formattedArray['ASC'][1]["PostErvaring"]; ?>
            weken</th>
        <th class="tg-spacing" rowspan="34"></th>
        <th class="tg-grijzebalk-textcenter" colspan="7"><?php echo 'Postindeling '.substr($_POST["week"], -6, 4).'-'.substr($_POST["week"], -2).' Texel'; ?>
        </th>
        <th class="tg-spacing" rowspan="34"></th>
        <th class="tg-grijzebalk-textleft">OPL</th>
        <th class="tg-grijzebalk-textright" colspan="2">Gem. erv.: <?php echo $formattedArray['OPL'][1]["PostErvaring"]; ?>
            weken</th>
    </tr>
    <tr>
        <td class="tg-foto" rowspan="3"><img
                src="<?php echo getProfilePictureSource($formattedArray['ASC'][1]["RelatieNr"]); ?>"
                style="display:block;width:100%;height:100%;" /></td>
        <td class="tg-naam" colspan="2"><?php echo $formattedArray['ASC'][1]["VolledigeNaam"]; ?>
        </td>
        <td class="tg-logobalk" colspan="7" rowspan="4"><img src="../../../images/logo-custom.svg" alt="TRB Logo"></td>
        <td class="tg-foto" rowspan="3"><img
                src="<?php echo getProfilePictureSource($formattedArray['OPL'][1]["RelatieNr"]); ?>"
                style="display:block;width:100%;height:100%;" /></td>
        <td class="tg-naam" colspan="2"><?php echo $formattedArray['OPL'][1]["VolledigeNaam"]; ?>
        </td>
    </tr>
    <tr>
        <td class="tg-leeftijd"><?php echo $formattedArray['ASC'][1]["Leeftijd"]; ?>
            jaar</td>
        <td class="tg-ervaring"><?php echo $formattedArray['ASC'][1]["WekenErvaring"]; ?>
            <?php if (1 > $formattedArray['ASC'][1]["WekenErvaring"]) {
            echo 'Geen';
        } elseif ($formattedArray['ASC'][1]["WekenErvaring"] == 1) {
            echo 'week';
        } else {
            echo 'weken';
        } ?>
        </td>
        <td class="tg-leeftijd"><?php echo $formattedArray['OPL'][1]["Leeftijd"]; ?>
            jaar</td>
        <td class="tg-ervaring"><?php echo $formattedArray['OPL'][1]["WekenErvaring"]; ?>
            <?php if (1 > $formattedArray['OPL'][1]["WekenErvaring"]) {
            echo 'Geen';
        } elseif ($formattedArray['OPL'][1]["WekenErvaring"] == 1) {
            echo 'week';
        } else {
            echo 'weken';
        } ?>
        </td>
        </td>
    </tr>
    <tr>
        <td class="tg-competenties" colspan="2" rowspan="2"><?php echo $formattedArray['ASC'][1]["CompetentiesRegel1"] . ' ' . $formattedArray['ASC'][1]["CompetentiesRegel2"]; ?>
        </td>
        <td class="tg-competenties" colspan="2" rowspan="2"><?php echo $formattedArray['OPL'][1]["CompetentiesRegel1"] . ' ' . $formattedArray['OPL'][1]["CompetentiesRegel2"]; ?>
        </td>
    </tr>
    <tr>
        <td class="tg-spacing"></td>
        <td class="tg-spacing"></td>
    </tr>
    <tr>
        <td class="tg-grijzebalk-textleft">Post 19</td>
        <td class="tg-grijzebalk-textright" colspan="2">Gem. erv.: <?php echo $formattedArray['PC19'][1]["PostErvaring"]; ?>
            weken</td>
        <td class="tg-grijzebalk-textleft">Post 20</td>
        <td class="tg-grijzebalk-textright" colspan="2">Gem. erv.: <?php echo $formattedArray['PC20'][1]["PostErvaring"]; ?>
            weken</td>
        <td class="tg-spacing" rowspan="29"></td>
        <td class="tg-grijzebalk-textleft">Post 21</td>
        <td class="tg-grijzebalk-textright" colspan="2">Gem. erv.: <?php echo $formattedArray['PC21'][1]["PostErvaring"]; ?>
            weken</td>
        <td class="tg-grijzebalk-textleft">Post 28</td>
        <td class="tg-grijzebalk-textright" colspan="2">Gem. erv.: <?php echo $formattedArray['PC28'][1]["PostErvaring"]; ?>
            weken</td>
    </tr>
    <tr>
        <td class="tg-foto" rowspan="3"><img
                src="<?php echo getProfilePictureSource($formattedArray['PC19'][1]["RelatieNr"]); ?>"
                style="display:block;width:100%;height:100%;" /></td>
        <td class="tg-naam" colspan="2"><?php echo $formattedArray['PC19'][1]["VolledigeNaam"]; ?>
        </td>
        <td class="tg-foto" rowspan="3"><img
                src="<?php echo getProfilePictureSource($formattedArray['PC20'][1]["RelatieNr"]); ?>"
                style="display:block;width:100%;height:100%;" /></td>
        <td class="tg-naam" colspan="2"><?php echo $formattedArray['PC20'][1]["VolledigeNaam"]; ?>
        </td>
        <td class="tg-foto" rowspan="3"><img
                src="<?php echo getProfilePictureSource($formattedArray['PC21'][1]["RelatieNr"]); ?>"
                style="display:block;width:100%;height:100%;" /></td>
        <td class="tg-naam" colspan="2"><?php echo $formattedArray['PC21'][1]["VolledigeNaam"]; ?>
        </td>
        <td class="tg-foto" rowspan="3"><img
                src="<?php echo getProfilePictureSource($formattedArray['PC28'][1]["RelatieNr"]); ?>"
                style="display:block;width:100%;height:100%;" /></td>
        <td class="tg-naam" colspan="2"><?php echo $formattedArray['PC28'][1]["VolledigeNaam"]; ?>
        </td>
    </tr>
    <tr>
        <td class="tg-leeftijd"><?php echo $formattedArray['PC19'][1]["Leeftijd"]; ?>
            jaar</td>
        <td class="tg-ervaring"><?php echo $formattedArray['PC19'][1]["WekenErvaring"]; ?>
            <?php if (1 > $formattedArray['PC19'][1]["WekenErvaring"]) {
            echo 'Geen';
        } elseif ($formattedArray['PC19'][1]["WekenErvaring"] == 1) {
            echo 'week';
        } else {
            echo 'weken';
        } ?>
        </td>
        <td class="tg-leeftijd"><?php echo $formattedArray['PC20'][1]["Leeftijd"]; ?>
            jaar</td>
        <td class="tg-ervaring"><?php echo $formattedArray['PC20'][1]["WekenErvaring"]; ?>
            <?php if (1 > $formattedArray['PC20'][1]["WekenErvaring"]) {
            echo 'Geen';
        } elseif ($formattedArray['PC20'][1]["WekenErvaring"] == 1) {
            echo 'week';
        } else {
            echo 'weken';
        } ?>
        </td>
        <td class="tg-leeftijd"><?php echo $formattedArray['PC21'][1]["Leeftijd"]; ?>
            jaar</td>
        <td class="tg-ervaring"><?php echo $formattedArray['PC21'][1]["WekenErvaring"]; ?>
            <?php if (1 > $formattedArray['PC21'][1]["WekenErvaring"]) {
            echo 'Geen';
        } elseif ($formattedArray['PC21'][1]["WekenErvaring"] == 1) {
            echo 'week';
        } else {
            echo 'weken';
        } ?>
        </td>
        <td class="tg-leeftijd"><?php echo $formattedArray['PC28'][1]["Leeftijd"]; ?>
            jaar</td>
        <td class="tg-ervaring"><?php echo $formattedArray['PC28'][1]["WekenErvaring"]; ?>
            <?php if (1 > $formattedArray['PC28'][1]["WekenErvaring"]) {
            echo 'Geen';
        } elseif ($formattedArray['PC28'][1]["WekenErvaring"] == 1) {
            echo 'week';
        } else {
            echo 'weken';
        } ?>
        </td>

    </tr>
    <tr>
        <td class="tg-competenties" colspan="2" rowspan="2"><?php echo $formattedArray['PC19'][1]["CompetentiesRegel1"] . ' ' . $formattedArray['PC19'][1]["CompetentiesRegel2"]?>
        </td>
        <td class="tg-competenties" colspan="2" rowspan="2"><?php echo $formattedArray['PC20'][1]["CompetentiesRegel1"] . ' ' . $formattedArray['PC20'][1]["CompetentiesRegel2"]?>
        </td>
        <td class="tg-competenties" colspan="2" rowspan="2"><?php echo $formattedArray['PC21'][1]["CompetentiesRegel1"] . ' ' . $formattedArray['PC21'][1]["CompetentiesRegel2"]?>
        </td>
        <td class="tg-competenties" colspan="2" rowspan="2"><?php echo $formattedArray['PC28'][1]["CompetentiesRegel1"] . ' ' . $formattedArray['PC28'][1]["CompetentiesRegel2"]?>
        </td>
    </tr>
    <tr>
        <td class="tg-spacing"></td>
        <td class="tg-spacing"></td>
        <td class="tg-spacing"></td>
        <td class="tg-spacing"></td>
    </tr>
    <?php
 for ($i = 2; $i < 8; $i++) { ?>
    <tr>
        <td class="tg-foto" rowspan="3"><?php if ($formattedArray['19'][$i]) {
     echo '<img src="'.getProfilePictureSource($formattedArray['19'][$i]["RelatieNr"]).'" style="display:block;width:100%;height:100%;"/>';
 } ?>
        </td>
        <td class="tg-naam" colspan="2"><?php if ($formattedArray['19'][$i]) {
     echo $formattedArray['19'][$i]["VolledigeNaam"];
 } ?>
        </td>
        <td class="tg-foto" rowspan="3"><?php if ($formattedArray['20'][$i]) {
     echo '<img src="'.getProfilePictureSource($formattedArray['20'][$i]["RelatieNr"]).'" style="display:block;width:100%;height:100%;"/>';
 } ?>
        </td>
        <td class="tg-naam" colspan="2"><?php if ($formattedArray['20'][$i]) {
     echo $formattedArray['20'][$i]["VolledigeNaam"];
 } ?>
        </td>
        <td class="tg-foto" rowspan="3"><?php if ($formattedArray['21'][$i]) {
     echo '<img src="'.getProfilePictureSource($formattedArray['21'][$i]["RelatieNr"]).'" style="display:block;width:100%;height:100%;"/>';
 } ?>
        </td>
        <td class="tg-naam" colspan="2"><?php if ($formattedArray['21'][$i]) {
     echo $formattedArray['21'][$i]["VolledigeNaam"];
 } ?>
        </td>
        <td class="tg-foto" rowspan="3"><?php if ($formattedArray['28'][$i]) {
     echo '<img src="'.getProfilePictureSource($formattedArray['28'][$i]["RelatieNr"]).'" style="display:block;width:100%;height:100%;"/>';
 } ?>
        </td>
        <td class="tg-naam" colspan="2"><?php if ($formattedArray['28'][$i]) {
     echo $formattedArray['28'][$i]["VolledigeNaam"];
 } ?>
        </td>
    </tr>
    <tr>
        <td class="tg-leeftijd"><?php echo $formattedArray['19'][$i]["Leeftijd"]; ?>
            jaar</td>
        <td class="tg-ervaring"><?php echo $formattedArray['19'][$i]["WekenErvaring"]; ?>
            <?php if (1 > $formattedArray['19'][$i]["WekenErvaring"]) {
     echo 'Geen';
 } elseif ($formattedArray['19'][$i]["WekenErvaring"] == 1) {
     echo 'week';
 } else {
     echo 'weken';
 }?>
        </td>
        <td class="tg-leeftijd"><?php echo $formattedArray['20'][$i]["Leeftijd"]; ?>
            jaar</td>
        <td class="tg-ervaring"><?php echo $formattedArray['20'][$i]["WekenErvaring"]; ?>
            <?php if (1 > $formattedArray['20'][$i]["WekenErvaring"]) {
     echo 'Geen';
 } elseif ($formattedArray['20'][$i]["WekenErvaring"] == 1) {
     echo 'week';
 } else {
     echo 'weken';
 }?>
        </td>
        <td class="tg-leeftijd"><?php echo $formattedArray['21'][$i]["Leeftijd"]; ?>
            jaar</td>
        <td class="tg-ervaring"><?php echo $formattedArray['21'][$i]["WekenErvaring"]; ?>
            <?php if (1 > $formattedArray['21'][$i]["WekenErvaring"]) {
     echo 'Geen';
 } elseif ($formattedArray['21'][$i]["WekenErvaring"] == 1) {
     echo 'week';
 } else {
     echo 'weken';
 }?>
        </td>
        <td class="tg-leeftijd"><?php echo $formattedArray['28'][$i]["Leeftijd"]; ?>
            jaar</td>
        <td class="tg-ervaring"><?php echo $formattedArray['28'][$i]["WekenErvaring"]; ?>
            <?php if (1 > $formattedArray['28'][$i]["WekenErvaring"]) {
     echo 'Geen';
 } elseif ($formattedArray['28'][$i]["WekenErvaring"] == 1) {
     echo 'week';
 } else {
     echo 'weken';
 }?>
        </td>
    </tr>
    <tr>
        <td class="tg-spacing"></td>
        <td class="tg-spacing"></td>
        <td class="tg-spacing"></td>
        <td class="tg-spacing"></td>
    </tr>
    <?php } ?>
</table>
<?php
    }

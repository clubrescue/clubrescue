<?php include $_SERVER['DOCUMENT_ROOT'] . '/clubredders/header.php'; ?>
<main>
    <div class="container">
        <?php if( current_user_can('author') ||  current_user_can('editor') || current_user_can('administrator') || current_user_can('contributor')) { ?>
        <form id="bw" action="" method="post">
            <label for="bewaker">Kies een bewaker om aan te passen:</label>
            <select id="bewaker" class="form-control browser-default" name="bewaker">
                <?php
			foreach ($dropdownLedenResult as $key => $value) { ?>
                <option <?php if($selectedBewaker === $value["Relatienr"]) { echo 'selected="selected"'; }?>
                    value="<?php echo $value["Relatienr"]; ?>"><?php echo $value["VolledigeNaam"]; ?></option>
                <?php } ?>
            </select>
            <button class="btn waves-effect waves-light cr-button" type="submit" name="search">Open de geselecteerde bewaker<i
                    class="material-icons right">send</i></button>
        </form>
        <?php } ?>
		<?php if(isset($updateResult)) { echo "Het lid met sportlinkid $selectedBewaker is succesvol geupdate!"; } ?>
        <?php if($selectedBewaker){ ?>
        <form id="rl" action="" method="post">
            <div class="row">
                <ul class="tabs">
                    <li class="tab"><a class="active" href="#membersdata">Persoonsgegevens</a></li>
                    <li class="tab"><a href="#addressdata">Adresgegevens</a></li>
                    <li class="tab"><a href="#communicationdata">Communicatie gegevens</a></li>
                    <li class="tab"><a href="#paymentdata">Betaalgegevens</a></li>
                    <li class="tab"><a href="#lifeguardingdata">Strandbewakingsgegevens</a></li>
                    <li class="tab"><a href="#securitydata">Functie (rechten)</a></li>
                </ul>
                <div id="membersdata" class="col s12">
                    <div class="col s2 push-s10"><img src="<?php echo getProfilePictureSource($selectedBewaker); ?>" style="float:left;position:absolute;right:0;top:0;max-height:230px;" />
                    </div>
                    <div class="col s10 pull-s2">
                        <table class="striped">
                            <tr>
                                <th>Relatienr</th>
                                <td><input readonly id="Relatienr" name="Relatienr" type="text"
                                        value="<?php echo $ledenGegevensResult["Relatienr"]; ?>" /></td>
                                <th>Soort</th>
                                <td><input name="Soort" type="text"
                                        value="<?php echo $ledenGegevensResult["Soort"]; ?>"></td>
                            </tr>
                            <tr>
                                <th>Achternaam</th>
                                <td><input name="Achternaam" type="text"
                                        value="<?php echo $ledenGegevensResult["Achternaam"]; ?>"></td>
                                <th>Tussenvoegsels</th>
                                <td><input name="Tussenvoegsels" type="text"
                                        value="<?php echo $ledenGegevensResult["Tussenvoegsels"]; ?>"></td>
                            </tr>
                            <tr>
                                <th>Roepnaam</th>
                                <td><input name="Roepnaam" type="text"
                                        value="<?php echo $ledenGegevensResult["Roepnaam"]; ?>"></td>
                                <th>Voorletters</th>
                                <td><input name="Voorletters" type="text"
                                        value="<?php echo $ledenGegevensResult["Voorletters"]; ?>"></td>
                            </tr>
                            <tr>
                                <th>Volledige naam</th>
                                <td><?php echo $ledenGegevensResult["VolledigeNaam"]; ?></td>
                                <th>Geslacht</th>
                                <td><select class="form-control" name="Geslacht">
                                        <option
                                            <?php if($ledenGegevensResult["Geslacht"] === 'M') { echo 'selected="selected"'; }?>
                                            value="M">M</option>
                                        <option
                                            <?php if($ledenGegevensResult["Geslacht"] === 'V') { echo 'selected="selected"'; }?>
                                            value="V">V</option>
                                    </select></td>
                            </tr>
                            <tr>
                                <th>Geboortedatum</th>
                                <td><input name="GeboorteDatum" type="date"
                                        value="<?php echo $ledenGegevensResult["GeboorteDatum"]; ?>"></td>
                                <th>Geboorteplaats</th>
                                <td><input name="Geboorteplaats" type="text"
                                        value="<?php echo ucfirst(strtolower($ledenGegevensResult["Geboorteplaats"])); ?>">
                                </td>
                            </tr>
                            <tr>
                                <th>Lid van de TRB sinds</th>
                                <td><input name="LidSinds" type="date"
                                        value="<?php echo $ledenGegevensResult["LidSinds"]; ?>"></td>
                                <th>Lidstatus</th>
                                <td><select class="form-control" name="Lidstatus">
                                        <?php foreach ($lidStatusResult as $key => $value) { ?>
                                        <option
                                            <?php if($ledenGegevensResult["Lidstatus"] === $value["Lidstatus"]) { echo 'selected="selected"'; }?>
                                            value="<?php echo $value["Lidstatus"]; ?>">
                                            <?php echo $value["Lidstatus"]; ?></option>
                                        <?php } ?>
                                    </select></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div id="addressdata" class="col s12">
                    <div class="col s12">
                        <table class="striped">
                            <tr>
                                <th>Postcode</th>
                                <td><input name="Postcode" type="text"
                                        value="<?php echo $ledenGegevensResult["Postcode"]; ?>"></td>
                                <th>Huisnr</th>
                                <td><input name="Huisnr" type="text"
                                        value="<?php echo $ledenGegevensResult["Huisnr"]; ?>"></td>
                                <th>Woonplaats</th>
                                <td><input name="Woonplaats" type="text"
                                        value="<?php echo ucfirst(strtolower($ledenGegevensResult["Woonplaats"])); ?>">
                                </td>
                            </tr>
                            <tr>
                                <th>Straat</th>
                                <td><input name="Straat" type="text"
                                        value="<?php echo $ledenGegevensResult["Straat"]; ?>"></td>
                                <th>HuisnrToev</th>
                                <td><input name="HuisnrToev" type="text"
                                        value="<?php echo $ledenGegevensResult["HuisnrToev"]; ?>"></td>
                                <th>Land</th>
                                <td><select class="form-control" name="Land">
                                        <?php foreach (getCountries() as $key => $value) { ?>
                                        <option
                                            <?php if($ledenGegevensResult["Land"] === $value) { echo 'selected="selected"'; }?>
                                            value="<?php echo $value; ?>"><?php echo $value; ?></option>
                                        <?php } ?>
                                    </select></td>
                            </tr>
                            <tr>
                                <th>Volledig Adres</th>
                                <td><?php echo $ledenGegevensResult["VolledigAdres"]; ?></td>
                                <th></th>
                                <td></td>
                                <th></th>
                                <td></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div id="communicationdata" class="col s12">
                    <div class="col s12">
                        <table class="striped">
                            <tr>
                                <th>Telefoon</th>
                                <td><input name="Telefoon" type="text"
                                        value="<?php echo $ledenGegevensResult["Telefoon"]; ?>"></td>
                                <th>Mobiel</th>
                                <td><input name="Mobiel" type="text"
                                        value="<?php echo $ledenGegevensResult["Mobiel"]; ?>"></td>
                                <th>E-mail adres</th>
                                <td><input name="Email" type="text"
                                        value="<?php echo $ledenGegevensResult["Email"]; ?>"></td>
                            </tr>
                            <tr>
                                <th>Nieuwsbrief</th>
                                <td><select class="form-control" name="Opt-ins">
                                        <option
                                            <?php if($ledenGegevensResult["Opt-ins"] === '1') { echo 'selected="selected"'; }?>
                                            value="1">Ja</option>
                                        <option
                                            <?php if($ledenGegevensResult["Opt-ins"] === '0') { echo 'selected="selected"'; }?>
                                            value="0">Nee</option>
                                    </select></td>
                                <th></th>
                                <td></td>
                                <th></th>
                                <td></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div id="paymentdata" class="col s12">
                    <div class="col s12">
                        <table class="striped">
                            <tr>
                                <th>Bankrek. type</th>
                                <td><input name="BankrekType" type="text"
                                        value="<?php echo $ledenGegevensResult["BankrekType"]; ?>"></td>
                                <th></th>
                                <td></td>
                            </tr>
                            <tr>
                                <th>Bankrek. nr.</th>
                                <td><input name="BankrekNr" type="text"
                                        value="<?php echo $ledenGegevensResult["BankrekNr"]; ?>"></td>
                                <th></th>
                                <td></td>
                            </tr>
                            <tr>
                                <th>BIC</th>
                                <td><input name="BIC" type="text" value="<?php echo $ledenGegevensResult["BIC"]; ?>">
                                </td>
                                <th>Contributie saldo</th>
                                <td><?php echo $ledenGegevensResult["TransactieBedrag"]; ?></td>
                            </tr>
                            <tr>
                                <th>Machtigingskenmerk</th>
                                <td><?php echo $ledenGegevensResult["Machtigingskenmerk"]; ?></td>
                                <th>Ondertekend op</th>
                                <td><?php echo $ledenGegevensResult["MachtigingOndertekend"]; ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div id="lifeguardingdata" class="col s12">
                    <div class="col s12">
                        <table class="striped">
                            <tr>
                                <th>Nationaliteit</th>
                                <td><select class="form-control" name="Nationaliteit">
                                        <?php foreach (getNationalities() as $key => $value) { ?>
                                        <option
                                            <?php if($ledenGegevensResult["Nationaliteit"] === $value) { echo 'selected="selected"'; }?>
                                            value="<?php echo $value; ?>"><?php echo $value; ?></option>
                                        <?php } ?>
                                    </select></td>
                                <th>Dieet</th>
                                <td><input name="Dieet" type="text"
                                        value="<?php echo $ledenGegevensResult["Dieet"]; ?>"></td>
                            </tr>
                            <tr>
                                <th>Noodcontact(en)</th>
                                <td><input name="Noodcontact" type="text"
                                        value="<?php echo $ledenGegevensResult["Noodcontact"]; ?>"></td>
                                <th>Geboorteland</th>
                                <td><select class="form-control" name="Geboorteland">
                                        <?php foreach (getCountries() as $key => $value) { ?>
                                        <option
                                            <?php if($ledenGegevensResult["Land"] === $value) { echo 'selected="selected"'; }?>
                                            value="<?php echo $value; ?>"><?php echo $value; ?></option>
                                        <?php } ?>
                                    </select></td>
                            </tr>
                            <th>Legitimatie</th>
                            <td><select class="form-control" name="Legitimatietype">
                                    <option
                                        <?php if($ledenGegevensResult["Legitimatietype"] === 'NI') { echo 'selected="selected"'; }?>value="NI">
                                        Nederlands Indentiteitskaart</option>
                                    <option
                                        <?php if($ledenGegevensResult["Legitimatietype"] === 'PN') { echo 'selected="selected"'; }?>value="PN">
                                        Nederlands Paspoort</option>
                                    <option
                                        <?php if($ledenGegevensResult["Legitimatietype"] === 'RB') { echo 'selected="selected"'; }?>value="RB">
                                        Rijbewijs</option>
                                </select></td>
                            <th></th>
                            <td></td>
                            </tr>
                            </tr>
                            <th>Legitimatie nr.</th>
                            <td><input name="Legitimatienr" type="text"
                                    value="<?php echo $ledenGegevensResult["Legitimatienr"]; ?>"></td>
                            <th></th>
                            <td></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div id="securitydata" class="col s12">
                    <div class="col s12">
                        <table class="striped">
                            <tr>
                                <th>Functie</th>
                                <td><select class="form-control" name="Verenigingsfunctie">
                                        <option
                                            <?php if($ledenGegevensResult["Verenigingsfunctie"] === '') { echo 'selected="selected"'; }?>value="">
                                        </option>
                                        <option
                                            <?php if($ledenGegevensResult["Verenigingsfunctie"] === 'Bestuur-AB') { echo 'selected="selected"'; }?>value="Bestuur-AB">
                                            Bestuur-AB</option>
                                        <option
                                            <?php if($ledenGegevensResult["Verenigingsfunctie"] === 'Bestuur-OC') { echo 'selected="selected"'; }?>value="Bestuur-OC">
                                            Bestuur-OC</option>
                                        <option
                                            <?php if($ledenGegevensResult["Verenigingsfunctie"] === 'Bestuur-PM') { echo 'selected="selected"'; }?>value="Bestuur-PM">
                                            Bestuur-PM</option>
                                        <option
                                            <?php if($ledenGegevensResult["Verenigingsfunctie"] === 'Bestuur-SE') { echo 'selected="selected"'; }?>value="Bestuur-SE">
                                            Bestuur-SE</option>
                                        <option
                                            <?php if($ledenGegevensResult["Verenigingsfunctie"] === 'Bestuur-VZ') { echo 'selected="selected"'; }?>value="Bestuur-VZ">
                                            Bestuur-VZ</option>
                                        <option
                                            <?php if($ledenGegevensResult["Verenigingsfunctie"] === 'Commissie-Opleidingen') { echo 'selected="selected"'; }?>value="Commissie-Opleidingen">
                                            Commissie-Opleidingen</option>
                                        <option
                                            <?php if($ledenGegevensResult["Verenigingsfunctie"] === 'Commissie-Strandzaken') { echo 'selected="selected"'; }?>value="Commissie-Strandzaken">
                                            Commissie-Strandzaken</option>
                                        <option
                                            <?php if($ledenGegevensResult["Verenigingsfunctie"] === 'Functionaris-RVR') { echo 'selected="selected"'; }?>value="Functionaris-RVR">
                                            Functionaris-RVR</option>
                                        <option
                                            <?php if($ledenGegevensResult["Verenigingsfunctie"] === 'Functionaris-VCP') { echo 'selected="selected"'; }?>value="Functionaris-VCP">
                                            Functionaris-VCP</option>
                                        <option
                                            <?php if($ledenGegevensResult["Verenigingsfunctie"] === 'Functionaris-Webmaster') { echo 'selected="selected"'; }?>value="Functionaris-Webmaster">
                                            Functionaris-Webmaster</option>
                                        <option
                                            <?php if($ledenGegevensResult["Verenigingsfunctie"] === 'Kascommissie') { echo 'selected="selected"'; }?>value="Kascommissie">
                                            Kascommissie</option>
                                        <option
                                            <?php if($ledenGegevensResult["Verenigingsfunctie"] === 'Ledenraad') { echo 'selected="selected"'; }?>value="Ledenraad">
                                            Ledenraad</option>
                                        <option
                                            <?php if($ledenGegevensResult["Verenigingsfunctie"] === 'Werkgroep-Scenarios') { echo 'selected="selected"'; }?>value="Werkgroep-Scenarios">
                                            Werkgroep-Scenarios</option>
                                    </select></td>
                            </tr>
                        </table>
                    </div>
                </div>
				<button class="btn waves-effect waves-light cr-button" type="submit">Update het lid<i class="material-icons right">send</i></button>
        </form>
    </div>
    <?php } ?>
    </div>
</main>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/clubredders/footer.php'; ?>
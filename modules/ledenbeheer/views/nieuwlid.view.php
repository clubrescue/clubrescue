<?php include $_SERVER['DOCUMENT_ROOT'] . '/clubredders/header.php'; ?>
<main>
    <div class="container">
        <?php if( current_user_can('author') ||  current_user_can('editor') || current_user_can('administrator') || current_user_can('contributor')) { ?>
		<?php echo $message;if($showCreateTabs) { ?>
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
                    <div class="col s12">
                        <table class="striped">
                            <tr>
                                <th>Relatienr</th>
                                <td><input id="Relatienr" name="Relatienr" type="text" /></td>
                                <th>Soort</th>
                                <td><input name="Soort" type="text" value="<?php if(isset($newLid["Soort"])) { echo $newLid["Soort"]; } else { echo 'Bondslid'; }?>"></td>
                            </tr>
                            <tr>
                                <th>Achternaam</th>
                                <td><input name="Achternaam" type="text" 
                                        value="<?php if(isset($newLid["Achternaam"])) { echo $newLid["Achternaam"]; }?>"></td>
                                <th>Tussenvoegsels</th>
                                <td><input name="Tussenvoegsels" type="text"
                                        value="<?php if(isset($newLid["Tussenvoegsels"])) { echo $newLid["Tussenvoegsels"]; }?>"></td>
                            </tr>
                            <tr>
                                <th>Roepnaam</th>
                                <td><input name="Roepnaam" type="text"
                                        value="<?php if(isset($newLid["Roepnaam"])) { echo $newLid["Roepnaam"]; }?>"></td>
                                <th>Voorletters</th>
                                <td><input name="Voorletters" type="text"
                                        value="<?php if(isset($newLid["Voorletters"])) { echo $newLid["Voorletters"]; }?>"></td>
                            </tr>
                            <tr>
                                <th></th>
                                <td></td>
                                <th>Geslacht</th>
                                <td><select class="form-control" name="Geslacht">
                                        <option
                                            <?php if(isset($newLid["Geslacht"]) && $newLid["Geslacht"] === 'M') { echo 'selected="selected"'; }?>
                                            value="M">M</option>
                                        <option
                                            <?php if(isset($newLid["Geslacht"]) && $newLid["Geslacht"] === 'V') { echo 'selected="selected"'; }?>
                                            value="V">V</option>
                                    </select></td>
                            </tr>
                            <tr>
                                <th>Geboortedatum</th>
                                <td><input name="GeboorteDatum" type="date" 
                                        value="<?php if(isset($newLid["GeboorteDatum"])) { echo $newLid["GeboorteDatum"]; }?>"></td>
                                <th>Geboorteplaats</th>
                                <td><input name="Geboorteplaats" type="text"
                                        value="<?php if(isset($newLid["Geboorteplaats"])) { echo ucfirst(strtolower($newLid["Geboorteplaats"])); }?>">
                                </td>
                            </tr>
                            <tr>
                                <th>Lid van de TRB sinds</th>
                                <td><input name="LidSinds" type="date"
                                        value="<?php if(isset($newLid["LidSinds"])) { echo $newLid["LidSinds"]; } else { echo date('Y-m-d'); }?>"></td>
                                <th>Lidstatus</th>
                                <td><select class="form-control" name="Lidstatus">
                                        <?php foreach ($lidStatusResult as $key => $value) { ?>
                                        <option <?php if(isset($newLid["Lidstatus"]) && $newLid["Lidstatus"] === $value["Lidstatus"]) { echo 'selected="selected"'; }?> value="<?php echo $value["Lidstatus"]; ?>">
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
                                        value="<?php if(isset($newLid["Postcode"])) { echo $newLid["Postcode"]; }?>"></td>
                                <th>Huisnr</th>
                                <td><input name="Huisnr" type="number"
                                        value="<?php if(isset($newLid["Huisnr"])) { echo $newLid["Huisnr"]; }?>"></td>
                                <th>Woonplaats</th>
                                <td><input name="Woonplaats" type="text"
                                        value="<?php if(isset($newLid["Woonplaats"])) { echo ucfirst(strtolower($newLid["Woonplaats"])); }?>">
                                </td>
                            </tr>
                            <tr>
                                <th>Straat</th>
                                <td><input name="Straat" type="text"
                                        value="<?php if(isset($newLid["Straat"])) { echo $newLid["Straat"]; }?>"></td>
                                <th>HuisnrToev</th>
                                <td><input name="HuisnrToev" type="text"
                                        value="<?php if(isset($newLid["HuisnrToev"])) { echo $newLid["HuisnrToev"]; }?>"></td>
                                <th>Land</th>
                                <td><select class="form-control" name="Land">
                                        <?php foreach (getCountries() as $key => $value) { ?>
                                        <option
                                            <?php if(isset($newLid["Land"]) && $newLid["Land"] === $value) { echo 'selected="selected"'; } elseif($value = 'Nederland'){echo 'selected="selected"';}?>
                                            value="<?php echo $value; ?>"><?php echo $value; ?></option>
                                        <?php } ?>
                                    </select></td>
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
                                        value="<?php if(isset($newLid["Telefoon"])) { echo $newLid["Telefoon"]; }?>"></td>
                                <th>Mobiel</th>
                                <td><input name="Mobiel" type="text"
                                        value="<?php if(isset($newLid["Mobiel"])) { echo $newLid["Mobiel"]; }?>"></td>
                                <th>E-mail adres</th>
                                <td><input name="Email" type="email"
                                        value="<?php if(isset($newLid["Email"])) { echo $newLid["Email"]; }?>"></td>
                            </tr>
                            <tr>
                                <th>Nieuwsbrief</th>
                                <td><select class="form-control" name="Opt-ins">
                                        <option
                                            <?php if(isset($newLid["Opt-ins"]) && $newLid["Opt-ins"] === 'Ja') { echo 'selected="selected"'; }?>
                                            value="1">Ja</option>
                                        <option
                                            <?php if(isset($newLid["Opt-ins"]) && $newLid["Opt-ins"] === 'Nee') { echo 'selected="selected"'; }?>
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
                                        value="<?php if(isset($newLid["BankrekType"])) { echo $newLid["BankrekType"]; }?>"></td>
                                <th>Bankrek. nr.</th>
                                <td><input name="BankrekNr" type="text"
                                        value="<?php if(isset($newLid["BankrekNr"])) { echo $newLid["BankrekNr"]; }?>"></td>
                            </tr>
                            <tr>
                                <th>BIC</th>
                                <td><input name="BIC" type="text" value="<?php if(isset($newLid["BIC"])) { echo $newLid["BIC"]; }?>">
                                </td>
                                <th></th>
                                <td></td>
                            </tr>
                            <tr>
                                <th>Machtigingskenmerk</th>
                                <td><input name="Machtigingskenmerk" type="text" value="<?php if(isset($newLid["Machtigingskenmerk"])) { echo $newLid["Machtigingskenmerk"]; }?>"></td>
                                <th>Ondertekend op</th>
                                <td><input name="MachtigingOndertekend" type="date" value="<?php if(isset($newLid["MachtigingOndertekend"])) { echo $newLid["MachtigingOndertekend"]; } else { echo '1970-01-01'; }?>"></td>
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
                                            <?php if(isset($newLid["Nationaliteit"]) && $newLid["Nationaliteit"] === $value) { echo 'selected="selected"'; } else if($value === 'Nederlandse') {echo 'selected="selected"'; }?>
                                            value="<?php echo $value; ?>"><?php echo $value; ?></option>
                                        <?php } ?>
                                    </select></td>
                                <th>Dieet</th>
                                <td><input name="Dieet" type="text"
                                        value="<?php if(isset($newLid["Dieet"])) { echo $newLid["Dieet"]; }?>"></td>
                            </tr>
                            <tr>
                                <th>Noodcontact(en)</th>
                                <td><input name="Noodcontact" type="text"
                                        value="<?php if(isset($newLid["Noodcontact"])) { echo $newLid["Noodcontact"]; }?>"></td>
                                <th>Geboorteland</th>
                                <td><select class="form-control" name="Geboorteland">
                                        <?php foreach (getCountries() as $key => $value) { ?>
                                        <option
                                            <?php if(isset($newLid["Land"]) && $newLid["Land"] === $value) { echo 'selected="selected"'; } else if($value === 'Nederland') { echo 'selected="selected"'; }?>
                                            value="<?php echo $value; ?>"><?php echo $value; ?></option>
                                        <?php } ?>
                                    </select></td>
                            </tr>
                            <th>Legitimatie</th>
                            <td><select class="form-control" name="Legitimatietype">
                                    <option
                                        <?php if(isset($newLid["Legitimatietype"]) && $newLid["Legitimatietype"] === 'NI') { echo 'selected="selected"'; }?>value="NI">
                                        Nederlands Indentiteitskaart</option>
                                    <option
                                        <?php if(isset($newLid["Legitimatietype"]) && $newLid["Legitimatietype"] === 'PN') { echo 'selected="selected"'; }?>value="PN">
                                        Nederlands Paspoort</option>
                                    <option
                                        <?php if(isset($newLid["Legitimatietype"]) && $newLid["Legitimatietype"] === 'RB') { echo 'selected="selected"'; }?>value="RB">
                                        Rijbewijs</option>
                                </select></td>
                            <th></th>
                            <td></td>
                            </tr>
                            </tr>
                            <th>Legitimatie nr.</th>
                            <td><input name="Legitimatienr" type="text"
                                    value="<?php if(isset($newLid["Legitimatienr"])) { echo $newLid["Legitimatienr"]; }?>"></td>
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
                                        <option value=""></option>
                                        <option
                                            <?php if(isset($newLid["Verenigingsfunctie"]) && $newLid["Verenigingsfunctie"] === 'Bestuur-AB') { echo 'selected="selected"'; }?>value="Bestuur-AB">
                                            Bestuur-AB</option>
                                        <option
                                            <?php if(isset($newLid["Verenigingsfunctie"]) && $newLid["Verenigingsfunctie"] === 'Bestuur-OC') { echo 'selected="selected"'; }?>value="Bestuur-OC">
                                            Bestuur-OC</option>
                                        <option
                                            <?php if(isset($newLid["Verenigingsfunctie"]) && $newLid["Verenigingsfunctie"] === 'Bestuur-PM') { echo 'selected="selected"'; }?>value="Bestuur-PM">
                                            Bestuur-PM</option>
                                        <option
                                            <?php if(isset($newLid["Verenigingsfunctie"]) && $newLid["Verenigingsfunctie"] === 'Bestuur-SE') { echo 'selected="selected"'; }?>value="Bestuur-SE">
                                            Bestuur-SE</option>
                                        <option
                                            <?php if(isset($newLid["Verenigingsfunctie"]) && $newLid["Verenigingsfunctie"] === 'Bestuur-VZ') { echo 'selected="selected"'; }?>value="Bestuur-VZ">
                                            Bestuur-VZ</option>
                                        <option
                                            <?php if(isset($newLid["Verenigingsfunctie"]) && $newLid["Verenigingsfunctie"] === 'Commissie-Opleidingen') { echo 'selected="selected"'; }?>value="Commissie-Opleidingen">
                                            Commissie-Opleidingen</option>
                                        <option
                                            <?php if(isset($newLid["Verenigingsfunctie"]) && $newLid["Verenigingsfunctie"] === 'Commissie-Strandzaken') { echo 'selected="selected"'; }?>value="Commissie-Strandzaken">
                                            Commissie-Strandzaken</option>
                                        <option
                                            <?php if(isset($newLid["Verenigingsfunctie"]) && $newLid["Verenigingsfunctie"] === 'Functionaris-RVR') { echo 'selected="selected"'; }?>value="Functionaris-RVR">
                                            Functionaris-RVR</option>
                                        <option
                                            <?php if(isset($newLid["Verenigingsfunctie"]) && $newLid["Verenigingsfunctie"] === 'Functionaris-VCP') { echo 'selected="selected"'; }?>value="Functionaris-VCP">
                                            Functionaris-VCP</option>
                                        <option
                                            <?php if(isset($newLid["Verenigingsfunctie"]) && $newLid["Verenigingsfunctie"] === 'Functionaris-Webmaster') { echo 'selected="selected"'; }?>value="Functionaris-Webmaster">
                                            Functionaris-Webmaster</option>
                                        <option
                                            <?php if(isset($newLid["Verenigingsfunctie"]) && $newLid["Verenigingsfunctie"] === 'Kascommissie') { echo 'selected="selected"'; }?>value="Kascommissie">
                                            Kascommissie</option>
                                        <option
                                            <?php if(isset($newLid["Verenigingsfunctie"]) && $newLid["Verenigingsfunctie"] === 'Ledenraad') { echo 'selected="selected"'; }?>value="Ledenraad">
                                            Ledenraad</option>
                                        <option
                                            <?php if(isset($newLid["Verenigingsfunctie"]) && $newLid["Verenigingsfunctie"] === 'Werkgroep-Scenarios') { echo 'selected="selected"'; }?>value="Werkgroep-Scenarios">
                                            Werkgroep-Scenarios</option>
                                    </select></td>
                            </tr>
                        </table>
                    </div>
                </div>
				<button class="btn waves-effect waves-light cr-button" type="submit">Lid aanmaken!<i class="material-icons right">send</i></button>
        </form>
    </div>
    <?php }} ?>
    </div>
</main>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/clubredders/footer.php'; ?>
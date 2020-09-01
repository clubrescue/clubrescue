<?php
	/*** PREVENT THE PAGE FROM BEING CACHED BY THE WEB BROWSER ***/
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Wed, 9 Apr 1986 21:00:00 GMT");
	require_once("../../wp-authenticate.php");
	/*** REQUIRE USER AUTHENTICATION ***/
	login();
	/*** RETRIEVE LOGGED IN USER INFORMATION ***/
	$user = wp_get_current_user();
	
	// Include database class
	include '../../util/utility.class.php';
	include '../../util/database.class.php';
	include '../../util/msgraph/msgraph.class.php';
	
	// Declare Namespace for MS Graph Model
    use Microsoft\Graph\Graph;
	use Microsoft\Graph\Model;
	use GuzzleHttp\Client;
	use GuzzleHttp\Psr7;
	
?>
<?php include __DIR__ . '/../../header.php'; ?>
<main>
<div class="container">
		<div class="section">
			<!---// BEGIN stuff here for all contributors, authors, editors or admins--->
			<?php if(current_user_can('editor') || current_user_can('administrator') ) {  ?>
				<?php

				$tableQuery = 'SELECT `option_id`, `option_name`, `option_value`, `autoload` FROM `u70472p67165_wp282`.`wpwx_options` WHERE `option_name` = \'aadsso_settings\' '; //`option_value`, 
				
				$database = new Database("WP");
				$database->query($tableQuery);	
				$tableResult = $database->resultset();

    //$option_value = get_option( 'aadsso_settings' );	//Loading data over the WP database connection results in an empty array. Deprecated by the line below;
	$option_value = unserialize($tableResult[0]["option_value"]); //Loading data over the CR database using the database name in the SELECT FROM works. Use PHP unserialize to render the option_value as an array.
		echo 'WordPress Single Sign-on with Azure Active Directory' . '<br>';
		echo 'Current WordPress configuration for single sign-on with Azure Active Directory.' . '<br>';
		echo 'General' . '<br>';
		echo' <table id=aadsso_settings>';
	//		echo '<tr>';
	//			echo '<td>option_id</td>';
	//			echo '<td>'.$tableResult[0]["option_id"]. '</td>';
	//			//echo '<td>option_id is a field from the WP_Options table in addition to the option_value field.</td>';
	//		echo '</tr>';
	//		echo '<tr>';
	//			echo '<td>option_name</td>';
	//			echo '<td>'.$tableResult[0]["option_name"]. '</td>';
	//			//echo '<td>option_name is a field from the WP_Options table in addition to the option_value field.</td>';
	//		echo '</tr>';
			echo '<tr>';
				echo '<td>Display name</td>';
				echo '<td>'.$option_value["org_display_name"]. '</td>';
				//echo '<td>Display Name will be shown on the WordPress login screen.</td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>Domain hint</td>';
				echo '<td>'.$option_value["org_domain_hint"]. '</td>';
				//echo '<td>Provides a hint to Azure AD about the domain or tenant they will be logging in to. If the domain is federated, the user will be automatically redirected to federation endpoint.</td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>Client ID</td>';
				echo '<td>'.$option_value["client_id"]. '</td>';
				//echo '<td>The client ID of the Azure AD application representing this blog.</td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>Client secret</td>';
				echo '<td>client_secret</td>';
				//echo '<td>A secret key for the Azure AD application representing this blog.</td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>Redirect URL</td>';
				echo '<td>'.$option_value["redirect_uri"]. '</td>';
				//echo '<td>The URL where the user is redirected to after authenticating with Azure AD. This URL must be registered in Azure AD as a valid redirect URL, and it must be a page that invokes the "authenticate" filter. If you don\'t know what to set, leave the default value (which is this blog\'s login page).</td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>Logout redirect URL</td>';
				echo '<td>'.$option_value["logout_redirect_uri"]. '</td>';
				//echo '<td>The URL where the user is redirected to after signing out of Azure AD. This URL must be registered in Azure AD as a valid redirect URL. (This does not affect logging out of the blog, it is only used when logging out of Azure AD.)</td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>Enable full logout</td>';
				//echo '<td>'.$option_value["enable_full_logout"]. '</td>';
					if ($option_value["enable_full_logout"] <> 1 ) {
						echo '<td>false</td>';
					}elseif ($option_value["enable_full_logout"] = 1 ){
						echo '<td>true</td>';
					}
				//echo '<td>Do a full logout of Azure AD when logging out of WordPress.</td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>Field to match to UPN</td>';
					if ($option_value["field_to_match_to_upn"] === "login") {
						echo '<td>Login Name</td>';
					}elseif ($option_value["field_to_match_to_upn"] === "email"){
						echo '<td>Email Address</td>';
					}
				//echo '<td>This specifies the WordPress user field which will be used to match to the Azure AD user\'s UserPrincipalName.</td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>Match on alias of the UPN</td>';
				//echo '<td>'.$option_value["match_on_upn_alias"]. '</td>';
					if ($option_value["match_on_upn_alias"] <> 1 ) {
						echo '<td>false</td>';
					}elseif ($option_value["match_on_upn_alias"] = 1 ){
						echo '<td>true</td>';
					}
				//echo '<td>Match WordPress users based on the alias of their Azure AD UserPrincipalName. For example, Azure AD username bob@example.com will match WordPress user bob.</td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>Enable auto-provisioning</td>';
				//echo '<td>'.$option_value["enable_auto_provisioning"]. '</td>';
					if ($option_value["enable_auto_provisioning"] <> 1 ) {
						echo '<td>false</td>';
					}elseif ($option_value["enable_auto_provisioning"] = 1 ){
						echo '<td>true</td>';
					}
				//echo '<td>Automatically create WordPress users, if needed, for authenticated Azure AD users.</td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>Enable auto-forward to Azure AD</td>';
				//echo '<td>'.$option_value["enable_auto_forward_to_aad"]. '</td>';
					if ($option_value["enable_auto_forward_to_aad"] <> 1 ) {
						echo '<td>false</td>';
					}elseif ($option_value["enable_auto_forward_to_aad"] = 1 ){
						echo '<td>true</td>';
					}
				//echo '<td>Automatically forward users to the Azure AD to sign in, skipping the WordPress login screen.</td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>Enable Azure AD group to WP role association</td>';
				//echo '<td>'.$option_value["enable_aad_group_to_wp_role"]. '</td>';
					if ($option_value["enable_aad_group_to_wp_role"] <> 1 ) {
						echo '<td>false</td>';
					}elseif ($option_value["enable_aad_group_to_wp_role"] = 1 ){
						echo '<td>true</td>';
					}
				//echo '<td>Automatically assign WordPress user roles based on Azure AD group membership.</td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>Default WordPress role if not in Azure AD group</td>';
				//echo '<td>'.ucfirst($option_value["default_wp_role"]). '</td>';
					if ($option_value["default_wp_role"] === "" ) {
						echo '<td>(None, deny access)</td>';
					}else {
						echo '<td>'.ucfirst($option_value["default_wp_role"]). '</td>';
					}
				//echo '<td>This is the default role that users will be assigned to if matching Azure AD group to WordPress roles is enabled, but the signed in user isn\'t a member of any of the configured Azure AD groups.</td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>WordPress role to Azure AD group map</td>';
				echo '<td>';
					echo '<table id=aadsso_settings_option_value_role_map>';
						echo '<tr>';
							echo '<td>WordPress Role</td>';
							echo '<td>Azure AD Group displayName</td>';
							echo '<td>Azure AD Group Object ID</td>';
						echo '</tr>';
						//Load MSGraphAPI to translate group ObjectID to group displayName.
						$msgraph = new MSGraphAPI();
						foreach ( $option_value["role_map"] as $rmkey => $rmvalue ) {
							$encode = $msgraph->getGroupById($rmvalue);
							$decode = json_encode($encode);
							$rmvalueID= json_decode($decode);
							echo '<tr><td>'.$rmkey.'</td><td>'.$rmvalueID->displayName.'</td><td>'.$rmvalue.'</td></tr>';
						}
					echo '</table>';
				echo '</td>';
				//echo '<td>Map WordPress roles to Azure Active Directory groups.</td>';
			echo '</tr>';
		echo '</table>';
		echo 'Advanced' . '<br>';
		echo' <table id=aadsso_settings_advanced>';
			echo '<tr>';
				echo '<td>OpenID Connect configuration endpoint</td>';
				echo '<td>'.$option_value["openid_configuration_endpoint"]. '</td>';
				//echo '<td>The OpenID Connect configuration endpoint to use. To support Microsoft Accounts and external users (users invited in from other Azure AD directories, known sometimes as "B2B users") you must use: https://login.microsoftonline.com/{tenant-id}/.well-known/openid-configuration, where {tenant-id} is the tenant ID or a verified domain name of your directory.</td>';
			echo '</tr>';
	//		echo '<tr>';
	//			echo '<td>autoload</td>';
	//			echo '<td>'.$tableResult[0]["autoload"]. '</td>';
	//			//echo '<td>autoload is a field from the WP_Options table in addition to the option_value field.</td>';
	//		echo '</tr>';
		echo '</table>';

				?>
			<!-- END stuff here for all contributors, authors, editors or admins -->
			<?php } ?>
		</div>
	</div>
</main>
<?php include '../../footer.php'; ?>
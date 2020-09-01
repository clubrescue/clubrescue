<?php include $_SERVER['DOCUMENT_ROOT'] . '/clubredders/header.php'; ?>
<main>
	<div class="container">
		<?php if (current_user_can('author') ||  current_user_can('editor') || current_user_can('administrator') || current_user_can('contributor')) {
		echo $message;if($showDropdown) { ?>
		<form id="bw" action="" method="post">
			<label for="bewaker">Kies een bewaker om aan te passen:</label>
			<select id="bewaker" class="form-control browser-default" name="Relatienr">
				<?php
					foreach ($dropdownLedenResult as $key => $value) { ?>
				<option <?php if ($selectedBewaker === $value["Relatienr"]) {
						echo 'selected="selected"';
					}?>
					value="<?php echo $value["Relatienr"]; ?>"><?php echo $value["VolledigeNaam"]; ?>
				</option>
				<?php } ?>
			</select>
		</form>
		<button class="btn waves-effect waves-light cr-button" onclick="show_confirm()">Delete de geselecteerde
			bewaker<i class="material-icons right">send</i></button>
		<?php }} ?>
</main>
<script>
	function show_confirm() {
		// build the confirm box
		var c = confirm("Weet je zeker dat je dit lid wilt verwijderen uit ClubRedders en Office365?");
		if (c) {
			document.getElementById("bw").submit();
		}
	}
</script>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/clubredders/footer.php';

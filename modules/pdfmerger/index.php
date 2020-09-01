<?php
$path = "declarations";

if ($handle = opendir($path)) {
  while (false !== ($file = readdir($handle))) {
      if ('.' === $file) continue;
      if ('..' === $file) continue;
      unlink($path . "/" . $file);
      }
  closedir($handle);
}
?>
<?php include __DIR__ . '/../../header.php'; ?>
<main>
	<div class="container">
		<div class="section">
			<form action="upload_file.php" method="post" enctype="multipart/form-data">
			  <input name="upload[]" type="file" multiple="multiple" />
			  <br />
			  <input type="submit" value="Upload" />
			  <br />
			</form>
		</div>
	</div>
</main>
<?php include '../../footer.php'; ?>

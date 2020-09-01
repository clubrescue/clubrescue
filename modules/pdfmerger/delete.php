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
echo "Deleted all files!";
?>
<?php include __DIR__ . '/../../header.php'; ?>
<main>
	<div class="container">
		<div class="section">
			</br>
			<input type="button" value="Back / New upload" onclick="location='index.php'" />
		</div>
	</div>
</main>
<?php include '../../footer.php'; ?>
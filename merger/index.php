<?php
$path = "Declaraties";

if ($handle = opendir($path)) {
  while (false !== ($file = readdir($handle))) {
      if ('.' === $file) continue;
      if ('..' === $file) continue;
      unlink($path . "/" . $file);
      }
  closedir($handle);
}
?>
<form action="upload_file.php" method="post" enctype="multipart/form-data">

  <input name="upload[]" type="file" multiple="multiple" />
  <br />
  <input type="submit" value="Upload" />
  <br />
</form>

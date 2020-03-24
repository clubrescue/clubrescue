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
echo "Deleted all files!";
?>
</br>
<input type="button" value="Back / New upload" onclick="location='index.php'" />

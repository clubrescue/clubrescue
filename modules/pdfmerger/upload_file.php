<?php

$targetfolder = "declarations/";
$total = count($_FILES['upload']['name']);
$file_type=$_FILES['upload']['type'][0];

for($i=0; $i<$total; $i++){
  if ($file_type=="application/pdf") {
    if(move_uploaded_file($_FILES['upload']['tmp_name'][$i], $targetfolder . "{$_FILES['upload']['name'][$i]}")) {
      echo "<pre>The file ". basename( $_FILES['upload']['name'][$i]). " is uploaded.</pre>";
    }
    else {
      echo "Problem uploading file";
    }

  }
  else {
    echo "You may only upload PDFs <br>";
  }
}
$files = array_diff( scandir($targetfolder), array('.', '..'));
//<pre>print_r($files);</pre>"
?>
<?php include __DIR__ . '/../../header.php'; ?>
<main>
	<div class="container">
		<div class="section">
			<input type="button" value="Done? Merge!" onclick="location='merge.php'" />
			<br />
			<input type="button" value="Delete all" onclick="location='delete.php'" />
		</div>
	</div>
</main>
<?php include '../../footer.php'; ?>
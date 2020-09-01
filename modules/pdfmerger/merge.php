<?php
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfReader;

require_once('fpdf/fpdf.php');
require_once('fpdi2/src/autoload.php');

function is_dir_empty($dir) {
  if (!is_readable($dir)) return NULL;
  return (count(scandir($dir)) == 2);
}

$path = "declarations/";

if ($handle = opendir($path)) {
  $pdf = new Fpdi();
  while (false !== ($file = readdir($handle))) {
      if ('.' === $file) continue;
      if ('..' === $file) continue;
      if(is_dir_empty($path)) {
          echo "No files uploaded";
      }
      else {
        $pageCount = $pdf->setSourceFile($path . $file);
        for ($i = 0; $i < $pageCount; $i++) {
          $tpl = $pdf->importPage($i+1, '/MediaBox');
          $pdf->addPage();
          $pdf->useTemplate($tpl);
        }
        $merged = 1;
      }
    }
    $pdf->Output('F', $path . "merged.pdf");
  }
  closedir($handle);
if ($merged = 1){
  echo "Merged!";
}
?>
<?php include __DIR__ . '/../../header.php'; ?>
<main>
	<div class="container">
		<div class="section">
			<br />
			<input type="button" value="Download" onclick="location='rename.php'" />
			<br />
		</div>
	</div>
</main>
<?php include '../../footer.php'; ?>
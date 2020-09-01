<?php include __DIR__ . '/../../header.php'; ?>
<main>
	<div class="container">
		<div class="section">
			<pre>Please select a filename and press Download.</pre>
			<form name="form" action="download.php" method="get">
			  <input type="text" name="varname" id="varname">
			  <input type="submit" value="Download">
			</form>
		</div>
	</div>
</main>
<?php include '../../footer.php'; ?>
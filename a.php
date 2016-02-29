<!DOCTYPE html>
<html lang="en">

	<head>
		<meta charset="utf-8">
		<title>File search</title>
	</head>

	<body>
		<h1>File search</h1>
		
		<?php
		$db = new mysqli("127.0.0.1", "file_search", "s34rch1n", "file_search");
		?>
		
		<form method="post" enctype="multipart/form-data">
			Search <input type="file" name="haystack">
			for <input type="text" name="needle">
			<button type="submit">Search!</button>
		</form>
		
		<?php
		if ($_SERVER["REQUEST_METHOD"] === "POST") {			
			if ($_FILES["haystack"]["type"] !== "text/plain") {
				echo "<strong>The file you uploaded is not a text file.</strong>";
			} else if ($_FILES["haystack"]["size"] > 50000) {
				echo "<strong>The file you uploaded is too large.</strong>";
			} else if ($_POST["needle"] === "") {
				echo "<strong>You must specify a term to search for.</strong>";
			} else {
				echo "<h3>Search results</h3>";
				
				$results = preg_split("/\r?\n/", `grep {$_POST["needle"]} {$_FILES["haystack"]["tmp_name"]}`);
				echo "<p>" . count($results) . " search result" . (count($results) === 1 ? "" : "s") . " for <strong>" . htmlspecialchars($_POST["needle"], ENT_QUOTES) . "</strong>:</p>";
				echo "<ul>";
				foreach ($results as &$r) {
					echo "<p>" . htmlspecialchars($r, ENT_QUOTES) . "</p>";
				}
				echo "</ul>";
				
				if ($db && $query = $db->prepare("insert into history (??)")) {
					if ($query->bind_param("si", $_POST["needle"], count($results))) {
						$query->execute();
					}
					$query->close();
					mysqli_close($db);
				}
			}
		}
		?>
		
	</body>
</html>
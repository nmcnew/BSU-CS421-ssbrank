<?php

        session_start();

?>
<!DOCTYPE html>
<html>
<head>
	<title>SSBRank</title>
	<link href="/rsc/landing.css" rel="stylesheet">
</head>
<body>
	<div class="content">
	<?php
		if(isset($_GET['error']))
		{
			echo '<div class="error">Error: <br/>' . $_GET['error'] . '</div>';

		}
	?>
	<form action="enter_site.php" method="POST">
		<h2>Basic Information:</h2>
		<input class="text-input" type="text" name="email" required="TRUE" placeholder="Email or Username" />
		<input class="text-input" type="password" name="password" required="TRUE" placeholder="Password" />
		<input class="landing-submit" type="submit" value="Submit">	
		</form>
	</div>
</body>
</html>
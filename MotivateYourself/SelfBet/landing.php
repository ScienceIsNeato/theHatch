<!DOCTYPE html>
<html lang="en">
<head>

<?php
session_start();
$name = $_SESSION['usersName'];
$error = $_SESSION['sqlError'];
?>
<meta charset="utf-8">
<title>HTML5 Landing</title>
<body>


Thanks! '<?php echo $name ?>'
'<?php echo $error ?>'
</body>
</html>
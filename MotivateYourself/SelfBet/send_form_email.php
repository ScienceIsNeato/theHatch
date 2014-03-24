
<?php

require_once "Mail.php";
include('Mail/mime.php');
include('access.conf');

session_start();

// Constructing the email
        
$name = strip_tags($_POST['name']);
$email = strip_tags($_POST['email']);
$message = strip_tags($_POST['message']);
$endDate = strip_tags($_POST['endDate']);
$amount = strip_tags($_POST['amount']);
$useReminders = strip_tags($_POST['useReminders']);
$remindersInterval = strip_tags($_POST['reminderInterval']);


if(!$useReminders)
{
	$remindersInterval = NULL;
}

else
{
	// Need to calculate the number of remaining reminder
	$endDateTemp = strtotime($endDate); // put endDate in nice time string
	$currentDate = date('Y-m-d H:i:s'); // put current time in nice time string
	$currentDate = strtotime($currentDate);
	$daysBetween = floor(abs($endDateTemp - $currentDate) / 86400); // figure out how many days left until end of bet

	// Check to see if reminder period is less than days remaining
	if($daysBetween > $remindersInterval)
	{
		// Figure out how many reminders remain
		$remindersRemaining = floor($daysBetween/$remindersInterval);
		// Figure out next reminder date
		$nextReminder = date('Y/m/d', strtotime("+$remindersInterval days"));
		$remindersRemaining--;
	}
	else
	{
		$remindersRemaining = 0;
		$nextReminder = null;
		
	}

}




$_SESSION['usersName'] = $name;
$_SESSION['sqlError'] = false;
$insertError  = 'will';


// Create connection
$con=mysql_connect($dbURL,$dbUser,$dbPass);

// Check connection
if (!$con)
  {
  $_SESSION['sqlError'] = "Failed to connect to MySQL: " . mysql_error();
  echo "Failed to connect to MySQL: " . mysql_error();
  }
  else
  {
  //$insertError = mysql_query("INSERT INTO  `slim720_wme`.`selfBet` (  `userKey` ,  `emailaddress` ,  `amount` ,  `endDate` ,  `username` ) VALUES ( 4,  'imanuggo@gmail.com', 80.00,  '2008-11-11 13:23:44',  'Jimmy' )", $con);
  echo "end date is ";
  echo $endDate;
  $insertError = mysql_query("INSERT INTO  `slim720_wme`.`selfBet` (
`emailaddress` ,
`username` ,
`theBet` ,
`amount` ,
`sendReminders` ,
`remindersInterval` ,
`remindersRemaining` ,
`nextReminder` ,
`endDate`
)
VALUES('$email','$name','$message','$amount','$useReminders','$remindersInterval','$remindersRemaining','$nextReminder','$endDate'
)", $con);

//$ result = mysql_query($con,"SELECT * FROM 'selfBet' WHERE 1");
	if (1) 
	{
	$_SESSION['sqlError'] = $insertError;
	}
	else
	{
	$_SESSION['sqlError'] = $insertError;
	}
  
  
  }



// Your name and email address
$sender = 'The Cheese <selfbet@williammartinengineering.com>'; 
// The Recipients name and email address
$recipient = ''.$name.' <'.$email.'>';                          
$subject = "Making Headway";                                   

$html = '<html><body><p>Whats up '.$name.'? Have you ever asked yourself '
        . 'and others... \''.$message.'\'?
<img src="http://williammartinengineering.com/SelfBet/smiley.gif" 
alt="Smiley face" width="42" height="42">
Well then, I have good news for you. A new website has come about that lets you
do only the things that Alex trebec thought you could do. Not only can you do those things, 
like having Miely Cyrus invent a speed jazz submarine sandwich club and invent her own
sandwich called the McCyrus Deluxe, but you can also send emails! Hey look at
all the non-stinky emails you can create! Once youre done with that, then visit (<a href
="http://williammartinengineering.com/SelfBet/index.php"> a place</a>). 
But then, low and behold, there was a monster on the prowl. It said that I needed at least
800 bytes in order for this email not to be treated as that gorgeos hawain meat. I know I 
misspelled hawaiin, but I dont care. The point here is that I have enough text to get through
the hawain meat filters. On second thought, whatever this is solving, its not going to be a solution
becuase theres no way I can have this much text in a message. At any rate, I think that this is probaby
enough random gibberish for now. Be sure to check your hawaiin meat filter in future releases to
avoid this sort of hogwashery. 
</body></html>';  // HTML version of the email

$text = 'Whats up dude? Have you ever asked yourself and others... ?
Well then, I have good news for you. A new website has come about that lets you
do only the things that Alex trebec thought you could do. Not only can you do those things, 
like having Miely Cyrus invent a speed jazz submarine sandwich club and invent her own
sandwich called the McCyrus Deluxe, but you can also send emails! Hey look at
all the non-stinky emails you can create! Once you\'re done with that, then visit (a place. 
But then, low and behold, there was a monster on the prowl. It said that I needed at least
800 bytes in order for this email not to be treated as that gorgeos hawain meat. I know I 
misspelled hawaiin, but I dont care. The point here is that I have enough text to get through
the hawain meat filters. On second thought, whatever this is solving, its not going to be a solution
becuase theres no way I can have this much text in a message. At any rate, I think that this is probaby
enough random gibberish for now. Be sure to check your hawaiin meat filter in future releases to
avoid this sort of hogwashery.'; 

$crlf = "\n";
$headers2 = array(
    'From'          => $sender,
    'Return-Path'   => $sender,
    'Subject'       => $subject
    );

// Creating the Mime message
$mime = new Mail_mime($crlf);

// Setting the body of the email
$mime->setTXTBody($text);
$mime->setHTMLBody($html);

$body = $mime->get();
$headers2 = $mime->headers($headers2);

// Sending the email
$mail =& Mail::factory('mail');
$mail->send($recipient, $headers2, $body);

 ?>
 
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
'<?php 
echo "Error status is";
echo $insertError;
 ?>'
</body>
</html>
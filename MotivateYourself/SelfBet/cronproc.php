
<?php

require_once "Mail.php";
include('Mail/mime.php');
include('access.conf');

//session_start();
$errorString = '';
// Constructing the email
$name = 'will';
$email = 'quarkswithforks@gmail.com';

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
	$errorString .= "Failed to connect to MySQL: " . mysql_error();
}
else
{
	// First, go through database and find users that need reminders sent
	$query = mysql_query("SELECT emailaddress, username, theBet, amount, endDate, userKey, remindersRemaining, remindersInterval, nextReminder
	FROM  `slim720_wme`.`selfBet` 
	WHERE endDate > NOW( ) AND sendReminders IS TRUE AND remindersRemaining > 0 AND nextReminder > NOW()", $con);
	
	// Make sure the querry was successful
	if (!$query) 
	{
		echo 'Could not run query: ' . mysql_error();
		exit;
	}

	// Get the number of users to parse
	$numReminders = mysql_num_rows($query);
	if ( 0==$numReminders ) 
	{
    	echo 'no records';
	}
	else
	{
		// Go through each row one by one. Extract the email address, username, bet and end date
		for ($counter = 0; $counter < $numReminders; $counter++)
		{ 
			$row = mysql_fetch_row($query); //Retriev first row, with multiple rows use mysql_fetch_assoc
			$remindTmpKey = $row[5];
			$remindTmpEmail = $row[0];
			$remindTmpUserName = $row[1];
			$remindTmpTheBet = $row[2];
			$remindTmpAmount = $row[3];
			$remindTmpEndDate = $row[4];
			$remindTmpRemindsRem = $row[6];
			$remindTmpInterval = $row[7];
			
			$emailStr = 'Hey'.$remindTmpUserName.', remember your bet for $'. $remindTmpAmount.' that you\'d '.
			$remindTmpTheBet.' by '. $remindTmpEndDate.'? You\'ll get '.$remindTmpRemindsRem.' more reminders.';
				
			echo "$emailStr<br />";
			echo "\n";
		
			// Generate and send the email
			
			// Compute next reminder date (problem here. Don't use now. Use last reminder date instead)
			$nextReminderDate = date('Y/m/d', strtotime("+$remindTmpInterval days"));
			
			// Should probably re-calculate number of reminders remaining as well
			
			// Update the database with the either the next reminder date
			$reminderMinus1 = $remindTmpRemindsRem - 1;
			
			$updSuccess = mysql_query("UPDATE `slim720_wme`.`selfBet` 
			SET remindersRemaining=$reminderMinus1, nextReminder=$nextReminderDate
			WHERE userKey=$remindTmpKey", $con);
 		}
	}



	//$retval = $row['1']; //Retriev first field

	//$retval = trim($retval); 

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
	$subject = "CRON";                                   

	$html = $row[0];  // HTML version of the email

	$text = $row[0]; 

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
	/* temporarily commented out while working on cron job 
	$mail =& Mail::factory('mail');
	$mail->send($recipient, $headers2, $body);
	*/
 ?>
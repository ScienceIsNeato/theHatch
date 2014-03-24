
<?php

require_once "Mail.php";
include('Mail/mime.php');
include('access.conf');


//session_start();
$errorString = '';
// Constructing the email
$name = 'will';
$email = 'quarkswithforks@gmail.com';
// Your name and email address
$sender = 'The Cheese <selfbet@williammartinengineering.com>'; 

$_SESSION['usersName'] = $name;
$_SESSION['sqlError'] = false;
$insertError  = 'will';

$numReminders = 0; // initialize as no emails sent
$userKeyList = '';


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
	$query = mysql_query("SELECT emailaddress, username, theBet, amount, 
	endDate, userKey, remindersRemaining, remindersInterval, nextReminder
	FROM  `slim720_wme`.`selfBet` 
	WHERE 
		(sendReminders IS TRUE AND
		remindersRemaining > 0 AND
		nextReminder < NOW())
		OR
		(endDate < NOW() AND 
		finalNoticeSent IS NULL)"
		, $con);
	
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
    	//echo 'no records';
	}
	else
	{
		//echo 'have records';
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
						
			$now = date('Y-m-d H:i:s'); 
            $nowStr = strtotime($now); 
            $endStr = strtotime($remindTmpEndDate); 
            if ($nowStr >= $endStr)
            {
            	echo $remindTmpUserName; 
            	// Send final email
                $subject = 'Your bet is complete, '.$name.'! How\'d you do?'; 
                 
                $userKeyList = $userKeyList .'<br/>Final for key: '. $remindTmpKey .' name: '.$remindTmpUserName.
                ' Amount: '.$remindTmpAmount.' endDate: '.$remindTmpEndDate;   
                
                $html = "
				<!DOCTYPE html>
				<html>
				<body>
				<a href = 'http://williammartinengineering.com/SelfBet/yes.php'> I did! </a>
				<button type=\"button\" onclick=\"window.open('Congrats!', 'http://williammartinengineering.com/SelfBet/yes.php');\" >Yes!</button>
				<br/>
				<button type=\"button\" onclick=\"window.open('Boo.     ', 'http://williammartinengineering.com/SelfBet/no.php');\" >No:(</button>
				<a href = 'http://williammartinengineering.com/SelfBet/no.php'> I didn't:( </a>
				</body>
				</html>";
				
				$text = "Select yes or no.";   
				// COMMENTED OUT FOR EASE OF TESTING
				$updSuccess = mysql_query("UPDATE `slim720_wme`.`selfBet` 
				SET finalNoticeSent=1, remindersRemaining = 0
				WHERE userKey=$remindTmpKey", $con);
				if (!$updSuccess)
				{
				echo 'Could not run query: ' . mysql_error();
				}

	
            } 
            else 
            {
            	// Send a reminder email
				$emailStr = 'Hey'.$remindTmpUserName.', remember your bet for $'. $remindTmpAmount.' that you\'d '.
				$remindTmpTheBet.' by '. $remindTmpEndDate.'? You\'ll get '.$remindTmpRemindsRem.' more reminders.<br />';
					
				//echo "$emailStr<br />";
				//echo "\n";
			
				$userKeyList = $userKeyList .'<br/>Reminder for key: '. $remindTmpKey .' name: '.$remindTmpUserName.
                ' Amount: '.$remindTmpAmount.' endDate: '.$remindTmpEndDate; 
				
				// Generate and send the email
				
				// Compute next reminder date (problem here. Don't use now. Use last reminder date instead)
				$nextReminderDate = date('Y/m/d', strtotime("+$remindTmpInterval days"));
				
				// Should probably re-calculate number of reminders remaining as well
				
				// Update the database with the either the next reminder date
				$reminderMinus1 = $remindTmpRemindsRem - 1;
				
				// COMMENTED OUT FOR EASE OF TESTING
				$updSuccess = mysql_query("UPDATE `slim720_wme`.`selfBet` 
				SET remindersRemaining=$reminderMinus1, nextReminder=$nextReminderDate
				WHERE userKey=$remindTmpKey", $con);
				
				// Craft the email
				$subject = $name.', here\'s a reminder about your SelfBet due in X days!';
				
				
				$html = "Consider yourself reminded";  // HTML version of the email

				$text = "Consider yourself reminded";  // HTML version of the email

			}
			
			// All users will receive either a reminder email or a final email
			$recipient = ''.$name.' <'.$email.'>';


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
			//temporarily commented out while working on cron job 
			$mail =& Mail::factory('mail');
			$mail->send($recipient, $headers2, $body);                                                           
 		}
	}


}



if ($numReminders > 0)
{
	// Let the admin know that emails have been sent and to who
	
	// The Recipients name and email address
	$admin = "Slick Pits";
	$adminEmail = "wmartin8@utk.edu";
	$recipient = ''.$admin.' <'.$adminEmail.'>';                          
	$subject = "CRON PROC Details";                                   

	$html = $userKeyList;  // HTML version of the email

	$text = $userKeyList; 

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
	//temporarily commented out while working on cron job 
	$mail =& Mail::factory('mail');
	$mail->send($recipient, $headers2, $body);
}
	
 ?>
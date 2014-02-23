<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>SelfBet - Bend yourself to your will!</title>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
<script src="js/jquery.validate.js"></script>
<script src="js/jquery.placeholder.js"></script>
<script src="js/jquery.form.js"></script>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
</head>

<body>

<div id="top-banner">	
<IMG SRC="http://williammartinengineering.com/SelfBet/images/butterfly.jpg" ALT="top banner">
</div>

<div id="contact-form">	
<form id="contact" method="post" action="send_form_email.php" >
<fieldset>	
<label for="message">My Bet:&#09</label>
<textarea name="message" placeholder="I will quit smoking"></textarea> by
<br>
<label for="date-picker">Bet End Date</label>
<input type="date" id="datetimepicker" name="endDate"/> or I'll give  $

<input pattern="\d?\d\.\d\d" maxlength=5 size=5 onchange="check(this)" name="amount" placeholder="20.00">
<script>
function check(elem) {
  if(!elem.value.match(/^\d?\d\.\d\d$/)) {
    alert('Error in data – use the format dd.dd (d = digit)');
  }
}
</script>
to the Disgusting Hog Beast<br>
<br>
Pledged by:
<label for="name">Name</label>
<input type="text" name="name" placeholder="Your First Name" title="Enter your first name" class="required">
<br>
<label for="email">E-mail</label>
<input type="email" name="email" placeholder="Ex: youremail@gmail.com" title="Enter your e-mail address" class="required email">
<br>
<input type="checkbox" name="useReminders" value="1">Send me reminders every 
<select name="reminderInterval">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3" selected>3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="7">7</option>
                <option value="14">14</option>
                <option value="31">31</option>
                <option value="365">365</option>
        </select>
day(s) until the end of my bet. <br>

<input type="submit" name="submit" class="button" id="submit" value="Pledge your bet!" />

</fieldset>
</form>

</div><!-- /end #contact-form -->

<script src="js/modernizr-min.js"></script>
<script>
if (!Modernizr.input.placeholder){
      $('input[placeholder], textarea[placeholder]').placeholder();
}
</script>
</body>
<!-- this should go after your </body> -->
<link rel="stylesheet" type="text/css" href="jquery.datetimepicker.css" >
<script src="./jquery.js"></script>
<script src="./jquery.datetimepicker.js"></script>
<script>
$('#datetimepicker').datetimepicker({
showTimePicker: false,
	value:"<?php $date = date('Y/m/d', strtotime('+14 days')); echo $date; ?>",
	minDate: +0,
	timepicker: false,
	format:'Y/m/d'
});
</script>



</html>
<!DOCTYPE html>
<html>
<head>
  <title>The Mouse and Key
  </title>
</head>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

    $DATA1 = array(
                    "ThisThat1" => "This, that, and the other.",
                    "ThisThat2" => "This, that, and the other.",
                    "FooBarBaz1" => "foo bar baz",
                    "FooBarBaz2" => "Foo Bar Baz",
                    "pass1" => "password",
                    "pass2" => "1Password!"
                  );
    $DATA2 = array (
                    "CDM1" => "cat dog mouse cow fox bear fish",
                    "CDM2" => "Cat Dog Mouse Cow Fox Bear Fish",
                    "CDM3" => "CAT Dog Mouse COW Fox BEAR Fish",
                    "Diff1" => "pizzas suburban assuming obstinance",
                    "Diff2" => "diddle duddle fiddle fuddle diddly fiddly"
                  );
    $DATA3 = array(
                    "Fox1" => "The quick brown fox jumped over the lazy dogs back.",
                    "Fox2" => "The quick Brown fox jumped over the LAZY dog's back!"
                  );

function GUID()
{
    if (function_exists('com_create_guid') === true)
    {
        return trim(com_create_guid(), '{}');
    }
    return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
}

  if ($_SERVER['REQUEST_METHOD'] === 'GET') {
?>

<body onload=>
  <script type="text/javascript" src="/js/mousenkeycollect.js"></script>

<H2>This exercise is to collect typing patterns and mouse movement data.</H2>
<p>The data is anonymous. No personal data is being collected that would allow for the indetification of a specific person.
<P>Fill out the form as many times as you like and see how fast you can get thru it with no errors.
<P>The timer starts once you answer the fist question
<P>Thank you for participating
<hr/>
<form name="myform" id="myform1" action="/index.php" method="POST">
<P> Is this your first time filling out this form?
 <b>Yes</b>  <input id="check1" type="checkbox" name "check1" onclick="startTime()"/>
 <b>No</B> <input id="check1" type="checkbox" name "check1" onclick="startTime()"/>
<br>

<H3>Type the following phrases into the text boxes.</H3>
<?php
foreach ( $DATA1 as $id => $string) {
?>
  <H4> <?php echo $string ?> <BR>
    <input id="<?php echo $id ?>" type="text" size = "50" name="<?php echo $id ?>" />
  </H4>
<?php  }  ?>

<P> Check this box
  <input id="check1" type="checkbox" name "check1"/>
  <BR>
<?php
foreach ( $DATA2 as $id => $string) {
?>
  <H4> <?php echo $string ?> <BR>
    <input id="<?php echo $id ?>" type="text" size = "50" name="<?php echo $id ?>" />
  </H4>
<?php  }  ?>


<P>  How is the weather outside?
<select name="Weather" id="Weather">
  <option value="No"> </option>
  <option value="Sunny">Sunny</option>
  <option value="Rainy">Rainy</option>
  <option value="Cloudy">Cloudy</option>
  <option value="Snowy">Snowy</option>
</select>
<BR>


<?php
  foreach ( $DATA3 as $id => $string) {
?>
  <H4> <?php echo $string ?> <BR>
    <input id="<?php echo $id ?>" type="text" size = "50" name="<?php echo $id ?>" />
  </H4>
<?php  }  ?>

<P>  What is your favorite color?
<select name="color" id="color">
  <option value="No"> </option>
  <option value="Yellow">Yellow</option>
  <option value="Green">Green</option>
  <option value="Red">Red</option>
  <option value="Blue">blue</option>
  <option value="Black">blue</option>
  <option value="While">blue</option>
</select>

<P>Do you think this is silly? <B>Yes:</B> <input id="lottery" type="checkbox" name "lottery"/>
 <b>No: </B> <input id="lottery" type="checkbox" name "lottery"/><br><br>
  <input id="submit" name="Submit" type="submit" value="Submit" onclick="dumpDATA()"/>

  <P>
  <P>
  <P>
    <textarea hidden="true" cols="100" rows="50" id="Data1" name="Data1" >
</form>


<?php

} // IF GET

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $filename = GUID() .".json";

  $data = json_decode( $_POST['Data1'], true );

  $runtime = ($data['Meta']['stopTime'] - $data['Meta']['startTime'])/1000;
  $TOTAL = 0;
  $FAIL = 0;

#echo var_dump( $data);
?>


<H2>Thank you</H2>

<H3> Time to submit <?php echo $runtime ?> seconds</H3>

  <TABLE border = "1" cellpadding="3" cellspacing="0" width="50%">

    <TR>
      <TH>Form ID</TH>
      <TH>Form String</TH>
      <TH>Your String</TH>
      <TH>Score</TH>
    </TR>
<?php
  foreach ( $DATA1 as $id => $string ) {
    $TOTAL++;
      $userstring = $_POST[$id];
      $data['POST'][$id]['user'] = $userstring;
      $data['POST'][$id]['form'] = $string;

?>
      <TR>
        <TD><?php echo $id ?></TD>
        <TD><?php echo $string ?></TD>
        <TD><?php echo $userstring ?></TD>
        <TD><?php if (strcmp($string, $userstring) == 0) {
          echo "Pass";
        } else { echo "Error"; $FAIL++;}

         ?></TD>
      </TR>
      <?php
    }
  foreach ( $DATA2 as $id => $string ) {
    $TOTAL++;
      $userstring = $_POST[$id];
      $data['POST'][$id]['user'] = $userstring;
      $data['POST'][$id]['form'] = $string;

?>
<TR>
  <TD><?php echo $id ?></TD>
  <TD><?php echo $string ?></TD>
  <TD><?php echo $userstring ?></TD>
  <TD><?php if (strcmp($string, $userstring) == 0) {
    echo "Pass";
  } else { echo "Error"; $FAIL++; }

   ?></TD>
</TR>
<?php
}
  foreach ( $DATA3 as $id => $string ) {
    $TOTAL++;
      $userstring = $_POST[$id];
      $data['POST'][$id]['user'] = $userstring;
      $data['POST'][$id]['form'] = $string;

?>
      <TR>
        <TD><?php echo $id ?></TD>
        <TD><?php echo $string ?></TD>
        <TD><?php echo $userstring ?></TD>
        <TD><?php if (strcmp($string, $userstring) == 0) {
          echo "Pass";
        } else { echo "Error"; $FAIL++;}

         ?></TD>
      </TR>





<?php }

$SCORE = ceil(($FAIL / $TOTAL)*100);


?>
</table>


<H3> Your Score is <?php echo $SCORE; ?>%</H3>

<P>Below is the data collected from this exercise.
<P>Your reference filename is <B><?php echo $filename;?></b>
<P>Refer to this filename if you wish your data deleted
<BR>
  <BR>
    <BR>

<?php

  file_put_contents("../data/$filename", json_encode($data,JSON_PRETTY_PRINT ));

?>
<textarea cols="100" rows="50" id="Data1" name="Data1" ><?php

  echo json_encode($data,JSON_PRETTY_PRINT );



?></textarea><?php
}


 ?>
</body>
</HTML>

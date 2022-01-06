<?php
/*
 *  CONFIGURE EVERYTHING HERE
 */

// an email address that will be in the From field of the email.
$from = 'Kontaktformular Stefan Pahl Immobilien <kontakt@stefan-pahl-immobilien.de>';

// an email address that will receive the email with the output of the form
//$sendTo = 'Stefan Pahl Immobilien <info@stefan-pahl-immobilien.de>';
$sendTo = 'Stefan Pahl Immobilien <stefanpahl@t-online.de>';

// subject of the email
$subject = 'Kontaktaufnahme www.stefan-pahl-immobilien.de';

// form field names and their translations.
// array variable name => Text to appear in the email
$fields = array('name' => 'Name', 'phone' => 'Telefon', 'email' => 'Email', 'message' => 'Nachricht'); 

// message that will be displayed when everything is OK :)
$okMessage = 'Vielen Dank für Ihre Anfrage, wir setzen uns schnellstmöglich mit Ihnen in Verbindung.';

// If something goes wrong, we will display this message.
$errorMessage = 'Fehler bei der Übermittlung. Bitte später noch einmal versuchen oder direkt telefonisch kontaktieren. Vielen Dank';

/*
 *  LET'S DO THE SENDING
 */

// if you are not debugging and don't need error reporting, turn this off by error_reporting(0);
//error_reporting(E_ALL & ~E_NOTICE);
error_reporting(0);

try
{

    if(count($_POST) == 0) throw new \Exception('Formular ist leer');
            
    $emailText = "Kontaktformular www.stefan-pahl-immobilien.de\n=============================\n";

    foreach ($_POST as $key => $value) {
        // If the field exists in the $fields array, include it in the email 
        if (isset($fields[$key])) {
            $emailText .= "$fields[$key]: $value\n";
        }
    }

    // All the neccessary headers for the email.
    $headers = array('Content-Type: text/plain; charset="UTF-8";',
        'From: ' . $from,
        'Reply-To: ' . $from,
        'Return-Path: ' . $from,
    );
    
    // Send email
    if(isset($_POST['url']) && $_POST['url'] == ''){
        if(preg_match('/http|www/i',$_POST['message'])) {
            $errorMessage = "Fehler bei der Übermittlung.<br/>Bitte verwenden Sie keine URL / Webseite in ihrer Nachricht.";
            throw new Exception('We do not allow a url in the comment.<br/>');
          } else {
            mail($sendTo, $subject, $emailText, implode("\n", $headers));
          }
    }

    $responseArray = array('type' => 'success', 'message' => $okMessage);
}
catch (\Exception $e)
{
    $responseArray = array('type' => 'danger', 'message' => $errorMessage);
}


// if requested by AJAX request return JSON response
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    $encoded = json_encode($responseArray);

    header('Content-Type: application/json');

    echo $encoded;
}
// else just display the message
else {
    echo $responseArray['message'];
}

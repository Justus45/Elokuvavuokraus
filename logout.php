<?php
// Aloitetaan sessio jotta se voidaan tyhjentää
session_start();
// tyhjennetään kaikki sessiomuuttujat
$_SESSION = [];
// Tuhotaan sessio kokonaa
session_destroy();

//Ohjataan käyttäjä takaisin kirjautumissivulle
header("Location: kirjautuminen.php");
exit;
?>
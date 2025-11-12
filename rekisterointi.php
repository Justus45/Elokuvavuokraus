<?php
include "db.php";

if (isset($_POST['rekisteroi'])) {

     $Nimi = trim($_POST['Nimi']);
     $Osoite = trim($_POST['Osoite']);
     $LiittymisPVM = ($_POST['LiittymisPVM']);
     $Syntymavuosi = ($_POST['Syntymavuosi']);
      $tunnus = trim($_POST['kayttajatunnus']);
       $salasana =($_POST['salasana']);

   //virhelista
   $virheet=[];

   //Tarkistaa että pakolliset kentät eivät ole tyhjiä
   if (empty($Nimi) || empty($Osoite) || empty($tunnus) || empty($salasana)) {
    $virheet[] = "Kaikki kentät ovat pakollisia.";
   }

   // Tarkistetaan, että syntymävuosi on nelinumeroinen ja järkevä
   if (!preg_match("/^\d{4}$/", $Syntymavuosi) || $Syntymavuosi < 1900 || $Syntymavuosi > date("Y")) {
    $virheet[]= "Syntymävuosi ei ole kelvollinen";
   }

   // Tarkistaa että liittymispäivämäärä on kelvollinen
   if (!strtotime($LiittymisPVM)) {
    $virheet[] = "Liittymispäivämäärä ei ole kelvollinen.";
   }
//Tarkistetaan että löytyykö käyttäjätunnus jo tietokannasta
$stmt_check = $conn->prepare("SELECT JasenID FROM Jasen WHERE Kayttajatunnus = ?");
$stmt_check->bind_param("s", $tunnus);
$stmt_check->execute();
$stmt_check->store_result();

if ($stmt_check->num_rows > 0) {
    $virheet[] = "Käyttäjätunnus on jo käytössä. Valitse toinen.";
}
// jos kaikki hyvin, uusi jäsen tietokantaan.
if(empty($virheet)) {
    //salataan salasana turvallisesti
    $hash = password_hash($salasana, PASSWORD_DEFAULT);

    //valmistaa SQL-lauseen ja sitoo arvot
    $stmt = $conn->prepare("INSERT INTO Jasen (Nimi, Osoite, LiittymisPVM, Syntymavuosi, Kayttajatunnus, SalasanaHash) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiss", $Nimi, $Osoite, $LiittymisPVM, $Syntymavuosi, $tunnus, $hash);

    //suoritetaan lisäys
    $stmt->execute();

    // ilmoitetaan onnistuneesta rekisteröinnistä
    echo "<div class='message success'>✅ Rekisteröinti onnistui!</div>";
} else {
    // Näytetään kaikki virheilmoitukset
    foreach ($virheet as $virhe) {
        echo "<div class='message error'>❌ $virhe</div>";
    }
}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekisteröinti</title>
    <link rel="stylesheet" href="tyyli1.css">
</head>
<body>
<h2> Rekisteröidy jäseneksi </h2>
<form method="POST">
    <label>Nimi</label>
    <input type="text" name="Nimi" required>

    <label>Osoite</label>
    <input type="text" name="Osoite" required>

    <label>Liittymispäivämäärä</label>
    <input type="date" name="LiittymisPVM" required>

    <label>Syntymävuosi</label>
    <input type="number" name="Syntymavuosi" required>

    <label>Käyttäjätunnus</label>
    <input type="text" name="kayttajatunnus" required>

    <label>Salasana</label>
    <input type="password" name="salasana" required>

    <input type="submit" name="rekisteroi" value="Rekisteröidy">

    <p> <a href="kirjautuminen.php">Siirry kirjautumaan &raquo; </a> </p>

</form>







    
</body>
</html>
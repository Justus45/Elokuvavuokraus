<?php
// Aloitetaan sessio, jotta voidaan käyttää kirjautumistietoja
session_start();

// Otetaan tietokantayhteys mukaan
include 'db.php';

// Tarkistetaan onko käyttäjä kirjautunut sisään
if (!isset($_SESSION['JasenID'])) {
    // Jos ei ole ohjataan kirjautumissivulle
    header("Location: kirjautuminen.php");
    exit;
}

//haetaan kirjautuneen käyttäjän ID
$JasenID = $_SESSION['JasenID'];

// Jos lomakkeella on lähetetty uusi vuokraus
if (isset($_POST['Vuokra'])) {
    $ElokuvaID = $_POST['ElokuvaID'];
    $VuokrausPVM = $_POST['VuokrausPVM'];
    $PalautusPVM = $_POST['PalautusPVM'];

    // valmistellaan ja suoritetaan vuokrauksen lisäys tietokantaan
    $stmt = $conn->prepare("INSERT INTO Vuokraus (JasenID, ElokuvaID, VuokrausPVM, PalautusPVM) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $JasenID, $ElokuvaID, $VuokrausPVM, $PalautusPVM);
    $stmt->execute();
}

// Jos käyttäjä haluaa poistaa vuokrauksen (GET-parametri 'poista')
if (isset($_GET['poista'])) {
    $ElokuvaID = $_GET['poista'];
    // Poistetaan vuokraus kirjautuneen käyttäjän tiedoista
    $conn->query("DELETE FROM Vuokraus WHERE JasenID=$JasenID AND ElokuvaID=$ElokuvaID");
}
?>
<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Omat vuokraukset</title>
    <link rel="stylesheet" href="tyyli1.css">
</head>
<body>
    <h2>Omat vuokraukset</h2>
    <a href="logout.php">↩️ Kirjaudu ulos</a><br><br>

    <table>
        <tr><th>Elokuva</th><th>Vuokrauspäivä</th><th>Palautuspäivä</th><th>Toiminnot</th></tr>
        <?php
        $result = $conn->query("SELECT V.ElokuvaID, E.Nimi, V.VuokrausPVM, V.PalautusPVM
        FROM Vuokraus V JOIN Elokuva E ON V.ElokuvaID = E.ElokuvaID WHERE V.JasenID = $JasenID");
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
            <td>{$row['Nimi']}</td>
            <td>{$row['VuokrausPVM']}</td>
            <td>{$row['PalautusPVM']}</td>
            <td> <a href='omavuokraus.php?poista={$row['ElokuvaID']}'>Poista</a></td>
            </tr>";
        }
        ?>
        </table>
        <h3>Vuokraa uusi elokuva</h3>
        <form method="post" class="form-container">
            <label>Elokuva:</label>
            <select name="ElokuvaID">
                <?php
                $Elokuvat = $conn->query("SELECT ElokuvaID, Nimi FROM Elokuva");
                while ($e = $Elokuvat->fetch_assoc()) {
                    echo "<option value='{$e['ElokuvaID']}'>{$e['Nimi']}</option>";
                } 
                ?>
                </select>
                
                <label>Vuokrauspäivä:</label>
                <input type="date" name="VuokrausPVM" required>
                <label>Palautuspäivä:</label>
                <input type="date" name="PalautusPVM">

                <input type="submit" name="Vuokra" value="Vuokraa elokuva">

            </form>
</body>
</html>
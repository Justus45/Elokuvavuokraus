<?php
session_start();
include 'db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tunnus = $_POST['kayttajatunnus'];
    $salasana = $_POST['salasana'];

    //haetaan jäsen tietokannasta
    $stmt = $conn->prepare("SELECT JasenID, SalasanaHash FROM Jasen WHERE Kayttajatunnus = ?");
    $stmt->bind_param("s", $tunnus);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($JasenID, $hash);
        $stmt->fetch();

        if (password_verify($salasana, $hash)) {
            $_SESSION['JasenID'] = $JasenID;
            header("Location: omavuokraus.php");
            exit;
        }else {
            $error = "❌ Väärä salasana.";
        }
    } else {
        $error = "❌ Käyttäjätunnusta ei löytynyt.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>kirjautuminen</title>
    <link rel="stylesheet" href="tyyli1.css">
</head>
<body>
    <div class="page-container">
    <h2>Kirjaudu sisään</h2>
    <?php if ($error): ?>
        <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" class="form-container">
            <label>Käyttäjätunnus:</label>
            <input type="text" name="kayttajatunnus" required>

            <label>Salasana</label>
            <input type="password" name="salasana" required>

            <input type="submit" value="Kirjaudu">
    </form>
    </div>
</body>
</html>
<?php

session_start();
include("kantayhteys.php");

$sivu = $_POST['lomaketunnistin'];

$kayttaja_tunnus  = mysqli_real_escape_string($dbconnect, $_POST['kayttaja_tunnus']);
$kayttaja_salasana = mysqli_real_escape_string($dbconnect, $_POST['kayttaja_salasana']);

    if($sivu == 0) {
        $kayttaja_sahkoposti = mysqli_real_escape_string($dbconnect, $_POST['kayttaja_sahkoposti']);
        $varmistus = $_POST['varmistus'];

    if(empty($kayttaja_tunnus) || empty($kayttaja_salasana) || empty($kayttaja_sahkoposti) || ($varmistus !== "kuusi")) {
        die("Jätit tietoja täyttämättä. Ole hyvä ja <a href='rekisterointi.html'>täytä lomake uudestaan</a>.");
    }

    $query = mysqli_query($dbconnect, "SELECT *  FROM kayttajat WHERE `kayttaja_tunnus` = '$kayttaja_tunnus'");

    if(mysqli_num_rows($query) !== 0) {
        echo "Tunnus on jo käytössä! <a href='rekisterointi.html'>Kokeile uudestaan</a>.";
    } else {
        //Suojaus:
        //(1) $kayttaja_salasana = hash('sha512', $kayttaja_salasana);
        //(2) 
        $kayttaja_salasana = password_hash($kayttaja_salasana, PASSWORD_BCRYPT, ['cost' => 8,]);
        //(3) $kayttaja_salasana = md5($kayttaja_salasana);
        mysqli_query($dbconnect,"INSERT INTO `kayttajat` (`kayttaja_id`, `kayttaja_taso`, `kayttaja_tunnus`, `kayttaja_salasana`, `kayttaja_sahkoposti`) VALUES (NULL,'user','$kayttaja_tunnus', '$kayttaja_salasana', '$kayttaja_sahkoposti' ) ");
        echo "Rekisteröinti onnistui! <a href='kirjautuminen.html'>Kirjaudu sisälle</a> palveluun."; 
    }

mysqli_close($dbconnect);
} 


if($sivu == 1) {
        //Suojaus:
        //(1) $kayttaja_salasana = hash('sha512', $kayttaja_salasana);
        //(2) $kayttaja_salasana = password_hash($kayttaja_salasana, PASSWORD_BCRYPT, ['cost' => 8,]);*/
        //(3) $kayttaja_salasana = md5($kayttaja_salasana);

    $query = mysqli_query($dbconnect, "SELECT * FROM kayttajat WHERE kayttaja_tunnus ='$kayttaja_tunnus'");
    $tiedot = mysqli_fetch_array($query) or die(mysqli_error());
    /*if(mysqli_num_rows($query) == 0) {*/
    if(password_verify($kayttaja_salasana, $tiedot['kayttaja_salasana'])) {
    echo "Kirjautuminen onnistui! <br> <a href='index.php'>Siirry palveluun</a>.";
    
    $_SESSION["kayttaja_id"] = $tiedot['kayttaja_id'];
    $_SESSION["kayttaja_taso"] = $tiedot['kayttaja_taso'];
    $_SESSION["kayttaja_tunnus"] = $tiedot['kayttaja_tunnus'];
    $_SESSION["kayttaja_salasana"] = $tiedot['kayttaja_salasana'];
    $_SESSION["kayttaja_sahkoposti"] = $tiedot['kayttaja_sahkoposti'];
    $_SESSION['LOGGEDIN'] = 1;
    }else{
    echo "Kirjautuminen ei onnistunut. Joko kirjoitit tiedot väärin tai et ole <a href='rekisterointi.html'>rekisteröitynyt</a> palvelun käyttäjäksi. Kokeile <a href='kirjautuminen.html'>uudestaan</a>.";
    }
    
    }



if($sivu == 2) {
    $kayttaja_uusisalasana=$_POST['kayttaja_uusisalasana'];

        //Suojaus:
        //(1) $kayttaja_salasana = hash('sha512', $kayttaja_salasana);
    $kayttaja_salasana = password_hash($kayttaja_salasana, PASSWORD_BCRYPT, ['cost' => 8,]);
        //(3) $kayttaja_salasana = md5($kayttaja_salasana);

    function vaihdaSahkoposti() {
    $kayttaja_uusisahkoposti=$_POST['kayttaja_uusisahkoposti'];
    global $dbconnect, $kayttaja_tunnus;
    
    if(!empty($kayttaja_uusisahkoposti)) {
    $query = mysqli_query($dbconnect, "UPDATE kayttajat SET kayttaja_sahkoposti='$kayttaja_uusisahkoposti' WHERE kayttaja_tunnus = '$kayttaja_tunnus'"); //tässä on korjattavaa
    
    $_SESSION["kayttaja_sahkoposti"] = $kayttaja_uusisahkoposti;
    } 
    else {
    echo "Jätit kentän tyhjäksi. Kokeile <a href='tiedot.php'>uudestaan</a>.";
    }
    }
    
    $query = mysqli_query($dbconnect, "SELECT * FROM kayttajat WHERE kayttaja_tunnus = '$kayttaja_tunnus'");
    
    $tiedot = mysqli_fetch_array($query) or die(mysqli_error($dbconnect));
    
    if (empty($kayttaja_salasana)) {
    vaihdaSahkoposti();
    
    echo "Tietojen muutos onnistui. <br> <a href='index.php'>Palaa etusivulle</a>.";
    } 
    else {
    /*if ($tiedot['kayttaja_salasana'] !== $kayttaja_salasana || empty($kayttaja_uusisalasana)) {*/
    if (!(password_verify($kayttaja_salasana, $tiedot['kayttaja_salasana'])) || empty($kayttaja_uusisalasana)) {    
    echo "Syötit väärän salasanan tai jätit tietoja täyttämättä. Kokeile <a href='tiedot.php'>uudestaan</a>.";
    } 
    else {
    $query= mysqli_query($dbconnect, "UPDATE kayttajat SET kayttaja_salasana='$kayttaja_uusisalasana' WHERE kayttaja_tunnus = '$kayttaja_tunnus'");
    vaihdaSahkoposti();
    
    echo "Tietojen muutos onnistui. <br> <a href='index.php'>Palaa etusivulle</a>.";
    }
    
    }
    }
            

?>

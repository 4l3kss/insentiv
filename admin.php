<?php
require 'config.php';

if(!empty($_SESSION["id"])){
	$id = $_SESSION["id"];
	$result = mysqli_query($conn, "SELECT * FROM tb_user WHERE id = $id");
	$row1 = mysqli_fetch_assoc($result);
}
else{
	header("Location: login.php");
}

$query = "SELECT * FROM tb_user WHERE id = $id";
$result = $conn->query($query);

if ($result) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
			$gotKid = $row["kid"];
        }
    }
}

if($gotKid != "adminsecret") {
    header("Location: index.php");
}

$duplicate = mysqli_query($conn, "SELECT * FROM preise");
if (mysqli_num_rows($duplicate) > 0) {
    if (isset($_POST["optin_push"])) {
        $optin = $_POST["optin"] ?? 0;

        $query = "UPDATE preise SET optin = '$optin';";
        if ($conn->query($query) === TRUE) {
        }
    }
    if (isset($_POST["gas"])) {
        $xsell_gas = $_POST["xsell"] ?? 0;
        $pw_gas = $_POST["pw"] ?? 0;
        $umzug_gas = $_POST["umzug"] ?? 0;

        $query = "UPDATE preise SET gas_pw = '$pw_gas', gas_xsell = '$xsell_gas', gas_umzug = '$umzug_gas';";
        if ($conn->query($query) === TRUE) {
        }
    }
    if (isset($_POST["strom"])) {
        $optin = $_POST["optin"] ?? 0;
        $xsell_strom = $_POST["xsell"] ?? 0;
        $pw_strom = $_POST["pw"] ?? 0;
        $umzug_strom = $_POST["umzug"] ?? 0;

        $query = "UPDATE preise SET strom_pw = '$pw_strom', strom_xsell = '$xsell_strom', strom_umzug = '$umzug_strom';";
        if ($conn->query($query) === TRUE) {
        }
    }
} else {
    $optin = 1;
    $xsell_strom = 11.50;
    $pw_strom = 6.50;
    $umzug_strom = 4.75;
    $xsell_gas = 11.50;
    $pw_gas = 6.50;
    $umzug_gas = 4.75;
    $datum = date("Y-m-d");

    $query = "INSERT INTO preise (optin, strom_pw, strom_xsell, strom_umzug, gas_pw, gas_xsell, gas_umzug) VALUES ('$optin', '$pw_strom', '$xsell_strom', '$umzug_strom', '$pw_gas', '$xsell_gas', '$umzug_gas')";
    if ($conn->query($query) === TRUE) {
    }
}

?>

<!DOCTYPE html>
	<head>
		<link rel="stylesheet" href="admin_style.css">
		<meta charset="uts-8">
		<title>Admin Übersicht</title>
		<body>
		<div class="background-image">
			<div class="header">
                <h1>Preisanpassung</h1> 
                <div class="header-icons">
                <a class="logout" href="logout.php"><img class="lo_size" src="./images/logout.png" alt="Logout"></a>
            </div>
            </div>
            <div class="center_preis">
                <form method="post">
                    <div>
                        <b>Optin</b>
                        <input type="number" name="optin" id="optin" min="0" max="1000" step="0.01" required value= ""> €<br>
                        <button class="button-29" role="button" type="optin_push" name="optin_push">Optin Preis setzen</button>
                    </div>
                </form>
                <form method="post">
                    <div>
                        <b>XSell</b>
                        <input type="number" name="xsell" id="xsell" min="0" max="1000" step="0.01" required value= ""> €<br>
                    </div>
                    <div>
                        <b>Produktwechsel</b>
                        <input type="number" name="pw" id="pw" min="0" max="1000" step="0.01" required value= ""> €<br>
                    </div>
                    <div>
                        <b>Umzug</b>
                        <input type="number" name="umzug" id="umzug" min="0" max="1000" step="0.01" required value= ""> €<br>
                    </div>
                    <div>
                    <button class="button-28" role="button" type="gas" name="gas">Preise für Gas setzen</button>
                    <button class="button-28" role="button" type="strom" name="strom">Preise für Strom setzen</button>
                    </div>
                </form>
            </div>
        </div>
            <div>
                <div class="cr">
                    <p>Hergestellt von Aleks mit ❤️ für die E.ON Mitarbeiter</p>
                </div>
                <div class = "version">
                    <p>V. 1.0.2</p>
                </div>
            </div>
        </body>
    </head>

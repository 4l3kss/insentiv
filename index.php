<?php
$gotOptin = 0;
$gotXsell_strom = 0;
$gotPW_strom = 0;
$gotUmzug_strom = 0;
$gotXsell_gas = 0;
$gotPW_gas = 0;
$gotUmzug_gas = 0;
$gotGeld = 0;
$monat = date("m");
$jahr = date("Y");
$datum = date("Y-m-d");


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

//downloaded?
$query = "SELECT * FROM button_clicks WHERE user_id = $id";
$result = $conn->query($query);

if ($result) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
			$clicked_monat = $row["click_date"] ?? 0;
            $clicked_jahr = $row["jahr"] ??0;
        }
    } else {
        $clicked_monat = 0;
        $clicked_jahr = 0;
    }
}

$checkQuery = "SELECT * FROM monatlicher_status WHERE kid = '$gotKid'";
$checkResult = $conn->query($checkQuery);
if ($checkResult->num_rows === 0) {
    $imageSrc = 'konto.png';
    if (isset($_POST["clicked"])) {
        header("Location: konto.php");
    }
} else {
    if ($clicked_monat == date("m") && $clicked_jahr == date("Y")) {
        $imageSrc = 'konto.png';
        if (isset($_POST["clicked"])) {
            header("Location: konto.php");
        }
    } else {
        $imageSrc = 'konto_msg.png';
        if (isset($_POST["clicked"])) {
            header("Location: konto.php");
        }
    }
}

//get data
$query = "SELECT * FROM insentiv WHERE id_ins = $id";
$result = $conn->query($query);

if ($result) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
			$gotOptin = $row["optin"] ?? 0;
			$gotXsell_strom = $row["xsell_strom"] ?? 0;
			$gotUmzug_strom = $row["umzug_strom"] ?? 0;
			$gotPW_strom = $row["produktwechsel_strom"] ?? 0;
            $gotXsell_gas = $row["xsell_gas"] ?? 0;
			$gotUmzug_gas = $row["umzug_gas"] ?? 0;
			$gotPW_gas = $row["produktwechsel_gas"] ?? 0;
            $gotGeld = $row["geld"] ?? 0;
        }
    }
}

//get preise
$query = "SELECT * FROM preise";
$result = $conn->query($query);

if ($result) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $optin_preise = $row["optin"] ?? 0;
            $xsell_strom_preise = $row["strom_xsell"] ?? 0;
            $pw_strom_preise = $row["strom_pw"] ?? 0;
            $umzug_strom_preise = $row["strom_umzug"] ?? 0;
            $xsell_gas_preise = $row["gas_xsell"] ?? 0;
            $pw_gas_preise = $row["gas_pw"] ?? 0;
            $umzug_gas_preise = $row["gas_umzug"] ?? 0;
            $datum_preise = $row["datum"] ?? 0;
        }
    }
}


// push data insentiv
$sparte = null;
if (isset($_COOKIE['sparte'])) {
    $phpVar = $_COOKIE['sparte'];
} else {
    $phpVar = null;
}
switch($phpVar) {
    case "gas":
        $sparte = "gas";
        break;
    case "strom":
        $sparte = "strom";
        break;      
}

if (isset($_POST["submit"])) {
    $id_ins = $id;
    $datum = date("Y-m-d");

    $duplicate = mysqli_query($conn, "SELECT * FROM insentiv WHERE id_ins = '$id'");
    if (mysqli_num_rows($duplicate) > 0) {
        $optin = $gotOptin + $_POST["optin"] ?? 0;

        if($phpVar == "strom") {
            $xsell = $gotXsell_strom + $_POST["xsell"] ?? 0;
            $umzug = $gotUmzug_strom + $_POST["umzug"] ?? 0;
            $pw = $gotPW_strom + $_POST["pw"] ?? 0;
            $query = "UPDATE insentiv SET optin = $optin, produktwechsel_strom = $pw, xsell_strom = $xsell, umzug_strom = $umzug, datum = '$datum' WHERE id_ins = $id";
            if ($conn->query($query) === TRUE) {
                header("Location: index.php");
            }
        } else {
            $xsell = $gotXsell_gas + $_POST["xsell"] ?? 0;
            $umzug = $gotUmzug_gas + $_POST["umzug"] ?? 0;
            $pw = $gotPW_gas + $_POST["pw"] ?? 0;
            $query = "UPDATE insentiv SET optin = $optin, produktwechsel_gas = $pw, xsell_gas = $xsell, umzug_gas = $umzug, datum = '$datum' WHERE id_ins = $id";
            if ($conn->query($query) === TRUE) {
                header("Location: index.php");
            }
        }
    } else {
        $optin = $_POST["optin"] ?? 0;
    	$xsell = $_POST["xsell"] ?? 0;
    	$umzug = $_POST["umzug"] ?? 0;
    	$pw = $_POST["pw"] ?? 0;


        if($phpVar == "strom") {
            $query = "INSERT INTO insentiv (id_ins, optin, produktwechsel_strom, xsell_strom, umzug_strom, datum) VALUES ('$id_ins', '$optin', '$pw', '$xsell', '$umzug', '$datum')";
            if ($conn->query($query) === TRUE) {
                header("Location: index.php");
            }
        } else {
            $query = "INSERT INTO insentiv (id_ins, optin, produktwechsel_gas, xsell_gas, umzug_gas, datum) VALUES ('$id_ins', '$optin', '$pw', '$xsell', '$umzug', '$datum')";
            if ($conn->query($query) === TRUE) {
                header("Location: index.php");
            }
        }
    }

    $akt = $_POST["akt"];
    $optin = $_POST["optin"];
    $xsell = $_POST["xsell"];
    $umzug = $_POST["umzug"];
    $pw = $_POST["pw"];

    $query = "INSERT INTO verlauf (id_ins, sparte, kid, akt, optin, produktwechsel, xsell, umzug, datum) VALUES ('$id_ins', '$phpVar', '$gotKid', '$akt', '$optin', '$pw', '$xsell', '$umzug', '$datum')";
    if ($conn->query($query) === TRUE) {
        header("Location: index.php");
    }
}
//total
    $duplicate = mysqli_query($conn, "SELECT * FROM insentiv WHERE id_ins = '$id'");
	if ($duplicate === false) {
		die("Error: " . mysqli_error($conn));
	}

	if (mysqli_num_rows($duplicate) > 0) {
		$totalOptin = $gotOptin * $optin_preise;
		$totalXsell_strom = $gotXsell_strom * $xsell_strom_preise;
        $totalPW_strom = $gotPW_strom * $pw_strom_preise;
		$totalUmzug_strom = $gotUmzug_strom * $umzug_strom_preise;
        $totalXsell_gas = $gotXsell_gas * $xsell_gas_preise;
        $totalPW_gas = $gotPW_gas * $pw_gas_preise;
		$totalUmzug_gas = $gotUmzug_gas * $umzug_gas_preise;

        $total = $totalOptin + $totalXsell_strom + $totalPW_strom + $totalUmzug_strom + $totalXsell_gas + $totalPW_gas + $totalUmzug_gas;
        $query = "UPDATE insentiv SET geld = '$total' WHERE id_ins = $id";
        if ($conn->query($query) === TRUE) {
        }


		$total = "Bis jetzt hast du " . number_format($total, 2) . "€ gemacht!";
        if($total == "Bis jetzt hast du 0.00€ gemacht!"){
            $total = "Keine Info";
        }
	} else {
		$total = "Keine Info";
	}

    if(date("d") == 01) {
        $currentMonth = date('n') - 1;
    
        if ($currentMonth == 0) {
            $currentMonth = 12;
            $jahr = $jahr - 1;
        }
        $checkQuery = "SELECT * FROM monatlicher_status WHERE kid = '$gotKid' AND monat = '$currentMonth' AND jahr = '$jahr'";
        $checkResult = $conn->query($checkQuery);
        if ($checkResult->num_rows === 0) {
            $geld = number_format($gotGeld, 2) . "€";
            $insertQuery = "INSERT INTO monatlicher_status (kid, monat, jahr, geld) VALUES ('$gotKid', '$currentMonth', '$jahr', '$geld')";
            if ($conn->query($insertQuery) === TRUE) {
                $resetQuery = "UPDATE insentiv SET optin = 0, produktwechsel_strom = 0, xsell_strom = 0, umzug_strom = 0, produktwechsel_gas = 0, xsell_gas = 0, umzug_gas = 0, datum = '$datum', geld = 0 WHERE id_ins = $id";
                if ($conn->query($resetQuery) === TRUE) {
                    header("Location: {$_SERVER['PHP_SELF']}");
                    exit();
                }
            }
        }
    }
?>


<!DOCTYPE html>
	<head>
		<link rel="stylesheet" href="index_style.css">
		<meta charset="uts-8">
		<title><?php echo $row1["name"];?>' Insentiv</title>
		<body>
		<div class="background-image">
			<div class="header">
                <form method="post">
                    <input type="hidden" name="clicked" value="true">
                    <button type="submit" class="konto"><img class="kt_size" src="./images/<?php echo $imageSrc; ?>"></button>
                </form>
			<h1>Willkommen <?php echo $row1["name"];?>!</h1>
			<a class="logout" href="logout.php"><img class="lo_size" src="./images/logout.png"></a>
		</div>

		<div id="auswahl" class= "center">
		<form method="post">
    <div>
        <div>
            <a href="index.php">
                <img href="index.php" class="close_size" src="images/x.png" alt="Button Edit">
            </a>
            <label for="akt">Aktivität:</label>
        </div>
        <input type="text" name="akt" id="akt" required value=""> <br>
    </div>

    <div>
    <label for="optin">Optin:</label>
    <div>
        <label for="optin0" class="button-27 optin">
            <input type="radio" id="optin0" name="optin" value="0" style="display: none;">
            0
        </label>

        <label for="optin1" class="button-27 optin">
            <input type="radio" id="optin1" name="optin" value="1" style="display: none;">
            1
        </label>

		<label for="optin2" class="button-27 optin">
            <input type="radio" id="optin2" name="optin" value="2" style="display: none;">
            2
        </label>

        <label for="optin3" class="button-27 optin">
            <input type="radio" id="optin3" name="optin" value="3" style="display: none;">
            3
        </label>

        <label for="optin4" class="button-27 optin">
            <input type="radio" id="optin4" name="optin" value="4" style="display: none;">
            4
        </label>
        </div>
    </div>

    <div>
        <label for="xsell">Cross Sell:</label>
        <div>
        <label for="xsell0" class="button-27 xsell">
            <input type="radio" id="xsell0" name="xsell" value="0" style="display: none;">
            0
        </label>

        <label for="xsell1" class="button-27 xsell">
            <input type="radio" id="xsell1" name="xsell" value="1" style="display: none;">
            1
        </label>

		<label for="xsell2" class="button-27 xsell">
            <input type="radio" id="xsell2" name="xsell" value="2" style="display: none;">
            2
        </label>

        <label for="xsell3" class="button-27 xsell">
            <input type="radio" id="xsell3" name="xsell" value="3" style="display: none;">
            3
        </label>

        <label for="xsell4" class="button-27 xsell">
            <input type="radio" id="xsell4" name="xsell" value="4" style="display: none;">
            4
        </label>
        </div>
    </div>

    <div>
        <label for="umzug">Umzug:</label>
        <div>
        <label for="umzug0" class="button-27 umzug">
            <input type="radio" id="umzug0" name="umzug" value="0" style="display: none;">
            0
        </label>

        <label for="umzug1" class="button-27 umzug">
            <input type="radio" id="umzug1" name="umzug" value="1" style="display: none;">
            1
        </label>

		<label for="umzug2" class="button-27 umzug">
            <input type="radio" id="umzug2" name="umzug" value="2" style="display: none;">
            2
        </label>

        <label for="umzug3" class="button-27 umzug">
            <input type="radio" id="umzug3" name="umzug" value="3" style="display: none;">
            3
        </label>

        <label for="umzug4" class="button-27 umzug">
            <input type="radio" id="umzug4" name="umzug" value="4" style="display: none;">
            4
        </label>
        </div>
    </div>

    <div>
        <label for="pw">Produktwechsel:</label>
        <div>
		<label for="pw0" class="button-27 pw">
            <input type="radio" id="pw0" name="pw" value="0" style="display: none;">
            0
        </label>

        <label for="pw1" class="button-27 pw">
            <input type="radio" id="pw1" name="pw" value="1" style="display: none;">
            1
        </label>

		<label for="pw2" class="button-27 pw">
            <input type="radio" id="pw2" name="pw" value="2" style="display: none;">
            2
        </label>

        <label for="pw3" class="button-27 pw">
            <input type="radio" id="pw3" name="pw" value="3" style="display: none;">
            3
        </label>

        <label for="pw4" class="button-27 pw">
            <input type="radio" id="pw4" name="pw" value="4" style="display: none;">
            4
        </label>
        </div>
    </div>
    <button class="button-28" role="button" type="submit" name="submit">Mitrechnen</button>
</form>
</div>
<div id="gos" class= "center_aus">
    <div>
        <p>Wähle eine Sparte aus.</p>
    </div>
    <button class="button-30" onclick="changeValue('strom')">Strom</button>
    <button class="button-30" onclick="changeValue('gas')">Gas</button>
</div>
    <div>

		</div>
		<div class="footer">
			<b><?php echo $total;?></b>
			<a href="index.php"><img class="rl_size" src="./images/relaod.png"></a>
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
</html>

<script  type="text/javascript">
    const buttonGroups = document.querySelectorAll('.button-27');
    let currentButtons = {};

    function changeColor(event) {
        const groupName = event.target.classList[1];
        if (currentButtons[groupName]) {
            currentButtons[groupName].style.backgroundColor = '';
        }

        event.target.style.backgroundColor = '#940000';
        currentButtons[groupName] = event.target;
    }

    buttonGroups.forEach(button => {
        button.addEventListener('click', changeColor);
    });
</script>

<script>
    var div = document.getElementById('auswahl');
    var div_sp = document.getElementById('gos');
    div.style.display = 'none';
    setSparte("sparte", "keine");

    function setSparte(name, value) {
        document.cookie = name + "=" + value + ";path=/";
    }

    function getSparte(name) {
        const decodedCookies = decodeURIComponent(document.cookie);
        const cookiesArray = decodedCookies.split(';');
        for (let i = 0; i < cookiesArray.length; i++) {
            let cookie = cookiesArray[i];
            while (cookie.charAt(0) === ' ') {
                cookie = cookie.substring(1);
            }
            if (cookie.indexOf(name) === 0) {
                return cookie.substring(name.length + 1, cookie.length);
            }
        }
        return "";
    }

    let sparte = getSparte("sparte") || 0;

    function changeValue(neueSparte) {
        sparte = neueSparte;
        setSparte("sparte", sparte);
        div.style.display = 'block';
        div_sp.style.display = 'none';
    }
    function deleteCookie(name) {
        document.cookie = name + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    }
</script>
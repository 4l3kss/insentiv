<?php
$data = array();
$monat = date("m");
$jahr = date("Y");
$downloaded = false;
$error = "";
require 'config.php';

require_once('tcpdf/tcpdf.php');

if(!empty($_SESSION["id"])){
	$id = $_SESSION["id"];
	$result = mysqli_query($conn, "SELECT * FROM tb_user WHERE id = $id");
	$row1 = mysqli_fetch_assoc($result);
}
else{
	header("Location: login.php");
}

//get data
$query = "SELECT * FROM tb_user WHERE id = $id";
$result = $conn->query($query);

if ($result) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
			$gotKid = $row["kid"];
        }
    }
}

$query = "SELECT * FROM verlauf WHERE id_ins = $id";
$result = $conn->query($query);

if ($result) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
			$gotId = $row["id_ins"] ?? 0;
            $gotSparte = $row["sparte"] ?? 0;
            $gotenKid = $row["kid"] ?? 0;
            $gotAkt = $row["akt"] ?? 0;
			$gotOptin = $row["optin"] ?? 0;
            $gotPW = $row["produktwechsel"] ?? 0;
			$gotXsell = $row["xsell"] ?? 0;
			$gotUmzug = $row["umzug"] ?? 0;
			$gotDatum = $row["datum"] ?? 0;

            $data[] = array(
                'akt' => $gotAkt,
                'sparte' => $gotSparte,
                'optin' => $gotOptin,
                'produktwechsel' => $gotPW,
                'xsell' => $gotXsell,
                'umzug' => $gotUmzug,
                'datum' => $gotDatum
            );
        }
    }
}

//download?
if (isset($_POST["downloaded"])) {
    $query = "INSERT INTO button_clicks (user_id, click_date, jahr) VALUES ('$id', '$monat', '$jahr')";
    if ($conn->query($query) === TRUE) {
    }
}

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
    $imageDisplay = 'none';
} else {
    if ($clicked_monat == date("m") && $clicked_jahr == date("Y")) {
        $imageDisplay = 'none';
    } else {
        $imageDisplay = 'block';
    }
}



//Download

$query = "SELECT * FROM insentiv WHERE id_ins = $id";
$result = $conn->query($query);

if ($result) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $gotId = $row["id_ins"] ?? 0;
            $gotOptin = $row["optin"] ?? 0;
            $gotPW_strom = $row["produktwechsel_strom"] ?? 0;
            $gotXsell_strom = $row["xsell_strom"] ?? 0;
            $gotUmzug_strom = $row["umzug_strom"] ?? 0;
            $gotPW_gas = $row["produktwechsel_gas"] ?? 0;
            $gotXsell_gas = $row["xsell_gas"] ?? 0;
            $gotUmzug_gas = $row["umzug_gas"] ?? 0;
            $gotDatum = $row["datum"] ?? 0;
        }
    }
}

$selectedMonth = $_GET['monat'] ?? date('n');
$currentMonth = $selectedMonth;
if($currentMonth == 12) {
    $jahr = $jahr - 1;
}

if(isset($_POST['downloaded'])) {
    $checkQuery = "SELECT * FROM monatlicher_status WHERE kid = '$gotKid' AND monat = '$currentMonth' AND jahr = '$jahr'";
    $checkResult = $conn->query($checkQuery);
    if ($checkResult->num_rows === 0) {
        $error = "Nicht vorhanden!";
    } else {
        $downloaded = downloadPDFTableFromMySQL($conn, $currentMonth, $gotKid);
    }
}

function downloadPDFTableFromMySQL($conn, $currentMonth, $gotKid) {
    $query = "SELECT kid, monat, jahr, geld FROM monatlicher_status WHERE monat = $currentMonth AND kid = '$gotKid'";
    
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $pdf = new TCPDF();
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 14);

        $colWidths = array(60, 60, 60, 60);

        while ($row = $result->fetch_assoc()) {
            $rowData = array(
                array('Kid' => $row['kid']),
                array('Monat' => $row['monat']),
                array('Jahr' => $row['jahr']),
                array('Geld' => $row['geld'])
            );

            $pdf->SetLineWidth(0.2);

            foreach ($rowData as $index => $row) {
                foreach ($row as $key => $value) {
                    if ($key === 'Kid') {
                        $pdf->SetFillColor(255, 99, 71);
                    } else {
                        $pdf->SetFillColor(255, 255, 255);
                    }
                    $pdf->Cell($colWidths[$index], 15, $key, 1, 0, 'C', 1);
                    $pdf->Cell($colWidths[$index], 15, $value, 1, 0, 'C', 1);
                    $pdf->Ln();
                }
            }

            $pdf->Ln(15);
        }
        $pdfJahr = date('Y');

        if($currentMonth == 12) {
            $pdfJahr = date('Y') - 1;
        }

        $fileName = $currentMonth . "." . $pdfJahr . '.pdf';
        if ($pdf->Output($fileName, 'D')) {
            return true;
        }
    }
}

//edit data
if (isset($_COOKIE['akt'])) {
    $akt_cookie = $_COOKIE['akt'];
} else {
    $akt_cookie = null;
}

if (isset($_POST["done"])) {
    $optin_edit = $_POST["optin"] ?? 0;
    $xsell_edit = $_POST["xsell"] ?? 0;
    $pw_edit = $_POST["produktwechsel"] ?? 0;
    $umzug_edit = $_POST["umzug"] ?? 0;

    $query = "SELECT * FROM verlauf WHERE id_ins = $id AND akt = $akt_cookie";
    $result = $conn->query($query);

    if ($result) {
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $sparte_edit = $row["sparte"] ?? 0;
                $optin_got = $row["optin"] ?? 0;
                $pw_got = $row["produktwechsel"] ?? 0;
                $xsell_got = $row["xsell"] ?? 0;
                $umzug_got = $row["umzug"] ?? 0;
            }
        }
    }
    
    $total_optin = $optin_got - $optin_edit;
    $ins_optin = $gotOptin - $total_optin;

    if($sparte_edit == "strom") {

        $total_pw = $pw_got - $pw_edit;
        $ins_pw = $gotPW_strom - $total_pw;
    
        $total_xsell = $xsell_got - $xsell_edit;
        $ins_xsell = $gotXsell_strom - $total_xsell;
    
        $total_umzug = $umzug_got - $umzug_edit;
        $ins_umzug = $gotUmzug_strom - $total_umzug;

        $query = "UPDATE insentiv SET optin = $ins_optin, produktwechsel_strom = $ins_pw, xsell_strom = $ins_xsell, umzug_strom = $ins_umzug, produktwechsel_gas = $gotPW_gas, xsell_gas = $gotXsell_gas, umzug_gas = $gotUmzug_gas WHERE id_ins = $id";
        if ($conn->query($query) === TRUE) {
        }
    } else if($sparte_edit == "gas") {
        
        $total_pw = $pw_got - $pw_edit;
        $ins_pw = $gotPW_gas - $total_pw;
    
        $total_xsell = $xsell_got - $xsell_edit;
        $ins_xsell = $gotXsell_gas - $total_xsell;
    
        $total_umzug = $umzug_got - $umzug_edit;
        $ins_umzug = $gotUmzug_gas - $total_umzug;

        $query = "UPDATE insentiv SET optin = $ins_optin, produktwechsel_strom = $gotPW_strom, xsell_strom = $gotXsell_strom, umzug_strom = $gotUmzug_strom, produktwechsel_gas = $ins_pw, xsell_gas = $ins_xsell, umzug_gas = $ins_umzug WHERE id_ins = $id";
        if ($conn->query($query) === TRUE) {
        }
    }


    $query = "UPDATE verlauf SET optin = $optin_edit, produktwechsel = $pw_edit, xsell = $xsell_edit, umzug = $umzug_edit WHERE id_ins = $id AND akt = $akt_cookie";
    if ($conn->query($query) === TRUE) {
        header("Location: konto.php");
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="konto_style.css">
    <meta charset="utf-8">
    <title>Konto</title>
</head>
<body>
    <div class="background-image">
        <div class="header">
            <div class="header-icons">
                <a class="home" href="index.php"><img class="home_size" src="./images/home.jpg" alt="Home"></a>
                <a class="logout" href="logout.php"><img class="lo_size" src="./images/logout.png" alt="Logout"></a>
            </div>
        </div>
        <div class="box">
            <div class="footer">
                <p>Boni für den Monat: </p>
                <form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <select class="monat" name="monat" id="monat" onchange="this.form.submit()">
                        <?php
                            $currentMonth = date('n') - 1;
        
                            for ($i = 1; $i <= 12; $i++) {
                                $selected = ($i == ($_GET['monat'] ?? $currentMonth)) ? 'selected' : '';
                                printf('<option value="%d" %s>%02d</option>', $i, $selected, $i);
                            }
                        ?>
                    </select>
                </form>
                <?php
                    if (!isset($_GET['monat'])) {
                        $defaultMonth = date('n') - 1;

                        if($defaultMonth == 0) {
                            $defaultMonth = 12;
                        }

                        header("Location: konto.php?monat=$defaultMonth");
                        exit();
                    }
                ?>
            </div>
            <form method="post">
                <div class="button-container">
                    <input type="hidden" name="downloaded" value="true">
                    <button type="submit" class="button-30">Herunterladen</button>
                    <img class="top-right-image" src="./images/notification.png" style="display: <?php echo $imageDisplay; ?>" />
                </div>
            </form>
            <p class="error"><?php echo $error;?></p>
        </div>

        <div class="center">
            <?php
            if (empty($data)) {
                echo "<p>Keinen vorhandenen Verlauf.</p>";
            } else {
                echo "<p>Dein Verlauf</p>";
                $reversedData = array_reverse($data);
                foreach ($reversedData as $index => $row) {
                    $divNormalId = 'normal_' . $index;
                    $divEditId = 'edit_' . $index;
                    ?>
                    <div class="table">
                        <div id="<?php echo $divNormalId; ?>">
                            <b>Aktivität: <?php echo $row['akt']; ?> | </b>
                            <b>Sparte: <?php echo ucfirst($row['sparte']); ?> | </b>
                            <b>Optin: <?php echo $row['optin']; ?> | </b>
                            <b>Produktwechsel: <?php echo $row['produktwechsel']; ?> | </b>
                            <b>XSell: <?php echo $row['xsell']; ?> | </b>
                            <b>Umzug: <?php echo $row['umzug']; ?> | </b>
                            <b>Datum: <?php echo $row['datum']; ?></b>
                            <button class="edit_btn" type="button" onclick="changeValue('<?php echo $row['akt']; ?>'); toggleEditNormal('<?php echo $divNormalId; ?>', '<?php echo $divEditId; ?>')">
                                <img class="edit_size" src="images/edit.png" alt="Button Edit">
                            </button>

                        </div>
                        <div id="<?php echo $divEditId; ?>" style="display: none;">
                            <form method="post">
                                <b>Aktivität: <?php echo $row['akt']; ?> | </b>
                                <b>Sparte: <?php echo ucfirst($row['sparte']); ?> | </b>
                                <b>Optin:</b> <input type="number" name="optin" id="optin" min="-4" max="4" required value= "<?php echo $row['optin']; ?>"> |
                                <b>Produktwechsel:</b> <input type="number" name="produktwechsel" id="produktwechsel" min="-4" max="4" required value= "<?php echo $row['produktwechsel']; ?>"> |
                                <b>XSell:</b> <input type="number" name="xsell" id="xsell" min="-4" max="4" required value= "<?php echo $row['xsell']; ?>"> |
                                <b>Umzug:</b> <input type="number" name="umzug" id="umzug" min="-4" max="4" required value= "<?php echo $row['umzug']; ?>"> |
                                <button class="edit_btn" type="submit" name="done">
                                    <img class="done_size" src="images/done.png" alt="Button Done">
                                </button>
                            </form>
                        </div>
                    </div>
                <?php }
            }
            ?>
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
</html>

<script>
    function toggleEditNormal(normalId, editId) {
        var normalDiv = document.getElementById(normalId);
        var editDiv = document.getElementById(editId);

        if (normalDiv.style.display === 'none') {
            normalDiv.style.display = 'block';
            editDiv.style.display = 'none';
        } else {
            normalDiv.style.display = 'none';
            editDiv.style.display = 'block';
        }
    }

    function setAkt(name, value) {
        document.cookie = name + "=" + value + ";path=/";
    }

    function getAkt(name) {
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

    let akt = getAkt("akt") || 0;

    function changeValue(neueAkt) {
        akt = neueAkt;
        setAkt("akt", akt);
    }
    function deleteCookie(name) {
        document.cookie = name + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    }
</script>


<?php
$error = "";

require 'config.php';
if(!empty($_SESSION["id"])){
    header("Location: index.php");
}
if(isset($_POST["submit"])){
    $kid = $_POST["kid"];
    $password = $_POST["password"];
    $result = mysqli_query($conn, "SELECT * FROM tb_user WHERE kid = '$kid'");
    $row = mysqli_fetch_assoc($result);
    if(mysqli_num_rows($result) > 0){

        if($password == $row["password"]){
            $_SESSION["login"] = true;
            $_SESSION["id"] = $row["id"];
            if($kid == "adminsecret") {
                header("Location: admin.php");
            } else {
                header("Location: index.php");
            }
        }
        else {
            $error = "Passwort stimmt nicht überein.";
        }
    }
    else {
        $error = "Keine Registrierung mit diesem KID gefunden.";   
    }
}
?>

<!DOCTYPE html>
	<head>
    <link rel="stylesheet" href="log_reg_style.css">
		<meta charset="uts-8">
		<title>Einloggen</title>
		<body>
        <div class="background-image">
			<div class="header">
			<h1>Einloggen</h1>
		</div>
        <div class= "center">
            <form class="" action="" method="post" autocomplete="off">
                <div>
                    <label for="kid"> KID : </label>
                    <input type="text" name="kid" id="kid" required value= ""> <br>
                </div>
                <div>
                <label for="password"> Passwort : </label>
                <input type="password" name="password" id="password" required value= ""> <br>
                </div>

                <button class="button-29" type= "submit" name="submit">Einloggen</button>
            </form>
            <a href="registration.php"><button class="button-30">Neu Anmelden</button></a>
            <br>
            <p class="error"><?php echo $error;?></p>
        </div>
            <div>
                <div class="cr_log">
                    <p>Hergestellt von Aleks mit ❤️ für die E.ON Mitarbeiter</p>
                </div>
                <div class = "version">
                    <p>V. 1.0.2</p>
                </div>
            </div>
		</body>
	</head>
</html>
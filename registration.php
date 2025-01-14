<?php
$error = "";

require 'config.php';
if(!empty($_SESSION["id"])){
    header("Location: index.php");
}
if(isset($_POST["submit"])){
    $name = $_POST["name"];
    $kid = $_POST["kid"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    $duplicate = mysqli_query($conn, "SELECT * FROM tb_user WHERE kid = '$kid'");
    if (mysqli_num_rows($duplicate) > 0){
        $error = "Sie sind bereits registriert.";
    }
    else{
        if($password == $confirm_password){
            $query = "INSERT INTO tb_user VALUES('', '$name', '$kid', '$password')";
            mysqli_query($conn, $query);
            header("Location: login.php");
        }
        else{
            $error = "Die Passwörter stimmen nicht überein.";
        }
    }
} 
?>

<!DOCTYPE html>
	<head>
    <link rel="stylesheet" href="log_reg_style.css">
		<meta charset="uts-8">
		<title>Neu Anmelden</title>
		<body>
        <div class="background-image">
			<div class="header">
			<h1>Neu Anmelden</h1>
		</div>
        <div class= "center">
            <form class="" action="" method="post" autocomplete="off">
                <label for="name"> Name : </label>
                <input type="text" name="name" id="name" required value= ""> <br>
                <label for="kid"> KID : </label>
                <input type="text" name="kid" id="kid" required value= ""> <br>
                <label for="password"> Passwort : </label>
                <input type="password" name="password" id="password" required value= ""> <br>
                <label for="confirm_password">Bestätige das Passwort : </label>
                <input type="password" name="confirm_password" id="confirm_password" required value= ""> <br>
                <button class="button-29" type= "submit" name="submit">Neu Anmelden</button>
            </form>
            <br>
            <a href="login.php"><button class="button-30">Einloggen</button></a>
            <p class="error"><?php echo $error;?></p>
        </div>
        <div>
                <div class="cr_reg">
                    <p>Hergestellt von Aleks mit ❤️ für die E.ON Mitarbeiter</p>
                </div>
                <div class = "version">
                    <p>V. 1.0.2</p>
                </div>
            </div>
		</body>
	</head>
</html>
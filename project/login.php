<?php 
include "DB_connection.php";
include "data/setting.php";
$setting = getSetting($conn);

if ($setting == 0) {
	header("Location: login.php");
	exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Welcome to <?=$setting['school_name']?></title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="css/style.css">
	<link rel="icon" href="logo.png">
</head>
<body class="body-login">
    <div>
    	<form class="login" 
    	      method="post"
    	      action="req/login.php">

            <div class="text-center mb-3">
                <img src="img/logo.png" alt="Logo" width="100">
            </div>

    		<h3>Login</h3>
    		<?php if (isset($_GET['error'])) { ?>
    		<div class="alert alert-danger" role="alert">
			  <?=$_GET['error']?>
			</div>
			<?php } ?>
		  <div class="mb-3">
		    <label class="form-label">Логин</label>
		    <input type="text" 
		           class="form-control"
		           name="uname">
		  </div>
		  
		  <div class="mb-3">
		    <label class="form-label">Пароль</label>
		    <input type="password" 
		           class="form-control"
		           name="pass">
		  </div>

		  <div class="mb-3">
		    <label class="form-label">Войти как</label>
		    <select class="form-control"
		            name="role">
		    	<option value="1">Admin</option>
		    	<option value="2">Teacher</option>
		    	<option value="3">Student</option>
		    	<option value="4">Registrar Office</option>
		    </select>
		  </div>

		  <button type="submit" class="btn btn-primary">Login</button>
		</form>
        <div class="text-center text-light">
        	Copyright &copy; <?=$setting['current_year']?> <?=$setting['school_name']?>. All rights reserved.
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>	
</body>
</html>

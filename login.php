<?php

require('connection.php');

// Login page

if (check_login()) {
	header('Location: /');
	exit;
}

function getRealUserIp(){
    switch(true){
      case (!empty($_SERVER['HTTP_X_REAL_IP'])) : return $_SERVER['HTTP_X_REAL_IP'];
      case (!empty($_SERVER['HTTP_CLIENT_IP'])) : return $_SERVER['HTTP_CLIENT_IP'];
      case (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) : return $_SERVER['HTTP_X_FORWARDED_FOR'];
      default : return $_SERVER['REMOTE_ADDR'];
    }
 }

$error = '';

if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['token'])) {
	$username = htmlspecialchars($_POST['username']);
	$password = htmlspecialchars($_POST['password']);
	if ($_POST['token'] == $_SESSION['token']){
		// Prepare statement
		$result = $db->query("SELECT * FROM users WHERE username = '$username'");

		if ($result->num_rows === 1){
			$row = $result->fetch_assoc();
			if(password_verify($password, $row["password"])){

				$_SESSION['id'] = $row['id'];
				$_SESSION['username'] = $row['username'];
				$_SESSION['password'] = $row['password'];

				if (isset($_POST['remember'])) {
					$remember['id'] = $row['id'];
					$remember['password'] = $row['password'];

					$remember = openssl_encrypt(json_encode($remember), 'AES-128-ECB' ,"beebeyttropbgwtfmec");

					setcookie('remember', $remember, time()+7*24*60*60, '/', $_SERVER['HTTP_HOST'], true, true);
				}

				header('Location: /');
				exit;
			} else {
				header('Location: /login.php?error=1337');
			}
		} else {
			header('Location: /login.php?error=999');
		}
	} else {
		header('Location: /login.php?error=csrf');
	}
}

if (isset($_GET['error']) && $_GET['error'] == 1337) {
	$error = '<div class="flex p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800" role="alert">
	<svg aria-hidden="true" class="flex-shrink-0 inline w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
	<span class="sr-only">Info</span>
	<div>
	  <span class="font-medium">Failed login!</span> Change the password and try submitting again.
	</div>
  </div>';
} else if ((isset($_GET['error']) && $_GET['error'] == 999)) {
	$error = '<div class="flex p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800" role="alert">
	<svg aria-hidden="true" class="flex-shrink-0 inline w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
	<span class="sr-only">Info</span>
	<div>
	  <span class="font-medium">Failed login!</span> The username doesn\'t exist.
	</div>
  </div>';
} else if ((isset($_GET['error']) && $_GET['error'] == "csrf")) {
	$error = '<div class="flex p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800" role="alert">
	<svg aria-hidden="true" class="flex-shrink-0 inline w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
	<span class="sr-only">Info</span>
	<div>
	  <span class="font-medium">Failed login!</span> Wrong CSRF. Please retry
	</div>
  </div>';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Login - Pteranodon</title>
	<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="antialiased bg-slate-200">
	<?php echo $error ?>
	<div class="max-w-lg mx-auto my-10 bg-white p-8 rounded-xl shadow shadow-slate-300">
		<h1 class="text-4xl font-medium">Login</h1>
		<p class="text-slate-500">Hi, please login to continue</p>

		<div class="my-5">
			<button class="w-full text-center py-3 my-3 border flex space-x-2 items-center justify-center border-slate-200 rounded-lg text-slate-700 hover:border-slate-400 hover:text-slate-900 hover:shadow transition duration-150">
				<img src="https://www.svgrepo.com/show/355037/google.svg" class="w-6 h-6" alt="Google logo"> <span>Login with Google</span>
			</button>
		</div>
		<form action="login.php" method="post" class="my-10">
			<div class="flex flex-col space-y-5">
				<label for="username">
					<p class="font-medium text-slate-700 pb-2">Username</p>
					<input id="username" name="username" type="username" class="w-full py-3 border border-slate-200 rounded-lg px-3 focus:outline-none focus:border-slate-500 hover:shadow" placeholder="Enter username">
				</label>
				<label for="password">
                    <p class="font-medium text-slate-700 pb-2">Password</p>
                    <input id="password" name="password" type="password" class="w-full py-3 border border-slate-200 rounded-lg px-3 focus:outline-none focus:border-slate-500 hover:shadow" placeholder="Enter your password">
                </label>
                <div class="flex flex-row justify-between">
                    <div>
                        <label for="remember" class="">
                            <input type="checkbox" name="remember" id="remember" class="w-4 h-4 border-slate-200 focus:bg-indigo-600">
                            Remember me
                        </label>
                    </div>
                    <div>
                        <a href="reset-password.php" class="font-medium text-indigo-600">Forgot Password?</a>
                    </div>
                </div>
				<input type="hidden" name="token" value="<?=$_SESSION['token'] ?>"/>
                <button id="loginbtn" class="w-full py-3 font-medium text-white bg-indigo-600 hover:bg-indigo-500 rounded-lg border-indigo-500 hover:shadow inline-flex space-x-2 items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                      </svg>
                      <span>Login</span>
                </button>
                <p class="text-center">Not registered yet? <a href="signup.php" class="text-indigo-600 font-medium inline-flex space-x-1 items-center"><span>Register now </span><span><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                  </svg></span></a></p>
			</div>
		</form>
	</div>			
</body>
</html>
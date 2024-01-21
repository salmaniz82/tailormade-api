<!DOCTYPE html>
<html>
<head>
    <title><?php if( isset($data['title']) ) { echo $data['title']; } else {echo 'no title';} ?></title>
	<link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400&display=swap" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="/pages/dashboard/react-dashboard/src/App.css" />
    
</head>
<body class="bg-offwhite">


<header class="max-w-2xl mx-auto mt-100 bg-black">
    
</header>

<section class="max-w-2xl bg-white mx-auto" id="login-section">

	<div class="d-flex justify-center">
    	<a href="#"><img src="https://www.tailormadelondon.com/cdn/shop/files/Logo-Black-1-1_4.svg" class="img-w-res"></a>
    </div>



		<div class="p-20">
		
		<?php if(isset($_SESSION['flashMsg'])) {?>

		<div class="message <?=$_SESSION['fClass']?>" id="flash-message"><?=$_SESSION['flashMsg']?></div>

			<?php }unset($_SESSION['flashMsg']); unset($_SESSION['fClass']);?>
			
		<form method="post" name="login" action="/login" id="login-form">
			<div class="d-flex flex-column align-center">

			
				<div class="dfx metaauto-fields">

					<label for="email">Email:</label>
					<input type="email" name="email" class="mb-10 w-100 d-block p-10" id="email">		
					<div>&nbsp;</div>	
					
				</div>
				
			
				<div class="dfx metaauto-fields">
					<label>Password:</label>
					<input type="password" name="password" class="mb-10 w-100 d-block p-10">
					<div>&nbsp;</div>
				</div>
				

			

			<div class="flashButtonWrapper"><div class="text_btn_lg" id="triggerLogin">LOGIN</div></div>	

			
			</div>
			
		</form>			
		</div>



		</section>

    

<!--
<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
  <script>window.jQuery || document.write('<script src="js/vendor/jquery-3.3.1.min.js"><\/script>')</script>
  <script src="/assets/js/mesh-main.js"></script>
-->


<script type="text/javascript">

	console.log("hello what is going on");

	let triggerLogin = document.querySelector("#triggerLogin");

	let loginForm = document.querySelector("#login-form"); 

	triggerLogin.addEventListener('click', e => {

		e.preventDefault();

		loginForm.submit();

	});
	
</script>



</body>
</html>
</body>
</html>
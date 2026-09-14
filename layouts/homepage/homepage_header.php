<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo SCHOOL_NAME; ?> - Official Website</title>
<meta name="description" content="<?php echo htmlspecialchars($SCHOOL_TAGLINE); ?>">
<link rel="icon" href="<?php echo WEB_ROOT . SCHOOL_LOGO; ?>">
<link rel="stylesheet" href="<?php echo WEB_ROOT; ?>plugins/fontawesome-free/css/all.min.css">
<link rel="stylesheet" href="<?php echo WEB_ROOT; ?>dist/css/homepage.css">
<?php /* Ang .hero background ay dynamic (depende kung may na-upload na
         hero-banner.jpg), kaya dito na lang sa isang maliit na inline
         <style> - lahat ng ibang styles ay nasa dist/css/homepage.css na. */ ?>
<style>
	.hero{
		background:<?php echo $heroImg ? "url('".$heroImg."') center/cover no-repeat" : "linear-gradient(135deg, var(--maroon-dark), var(--maroon) 60%, var(--maroon-light))"; ?>;
	}
</style>
</head>
<body>

<!-- ============================ NAVBAR ============================ -->
<header class="navbar">
	<div class="container">
		<a href="#top" class="navbar-brand">
			<img src="<?php echo WEB_ROOT . SCHOOL_LOGO; ?>" alt="<?php echo htmlspecialchars(SCHOOL_NAME); ?> seal">
			<span><?php echo htmlspecialchars(SCHOOL_NAME); ?></span>
		</a>
		<nav class="navbar-links" id="navLinks">
			<a href="#top">Home</a>
			<a href="#programs">Programs</a>
			<a href="#about">About</a>
			<a href="#history">History</a>
			<a href="#news">News</a>
			<a href="#admissions">Admissions</a>
			<a href="#contact">Contact</a>
			<a href="<?php echo WEB_ROOT; ?>login.php" class="btn btn-maroon">Login</a>
		</nav>
		<button class="navtoggle" id="navToggle" aria-label="Menu"><i class="fa fa-bars"></i></button>
	</div>
</header>


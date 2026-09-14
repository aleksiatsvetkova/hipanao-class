<!-- ============================ FOOTER ============================ -->
<footer class="footer">
	<div class="container">
		<div>
			<h4><?php echo htmlspecialchars(SCHOOL_NAME); ?></h4>
			<p><?php echo htmlspecialchars($SCHOOL_TAGLINE); ?></p>
			<div class="footer-social">
				<a href="<?php echo htmlspecialchars($SOCIAL_FACEBOOK); ?>" target="_blank" rel="noopener"><i class="fab fa-facebook-f"></i></a>
				<a href="<?php echo $SOCIAL_EMAIL_LINK; ?>"><i class="fa fa-envelope"></i></a>
			</div>
		</div>
		<div>
			<h4>Quick Links</h4>
			<ul>
				<li><a href="#programs">Programs</a></li>
				<li><a href="#about">About</a></li>
				<li><a href="#history">History</a></li>
				<li><a href="#news">News</a></li>
				<li><a href="#admissions">Admissions</a></li>
			</ul>
		</div>
		<div>
			<h4>Portal</h4>
			<ul>
				<li><a href="<?php echo WEB_ROOT; ?>login.php">Student / Staff Login</a></li>
				<li><a href="#contact">Contact Us</a></li>
			</ul>
		</div>
	</div>
	<div class="footer-bottom">
    Copyright &copy; 2026 <a href="http://localhost/hipanao-class/index.php#">HIPANAO</a>. All rights reserved.
</div>
</footer>

<script>
	document.getElementById('navToggle').addEventListener('click', function(){
		document.getElementById('navLinks').classList.toggle('open');
	});

	// Bahagyang mas malakas na shadow sa navbar pag naka-scroll na pababa.
	var hpNavbar = document.querySelector('.navbar');
	window.addEventListener('scroll', function(){
		if (window.scrollY > 10) { hpNavbar.classList.add('scrolled'); }
		else { hpNavbar.classList.remove('scrolled'); }
	});

	// ---- Announcement "See More" - FULL SCREEN view (larawan sa taas +
	// buong content), may Close button sa taas at sa pinakababa - pareho
	// silang babalik sa dating kinaroroonan sa homepage (hindi sa top),
	// kaya isinasave muna ang scroll position bago buksan ang fullscreen. ----
	var hpAnnScrollY = 0;
	function hpOpenAnnouncement(el){
		hpAnnScrollY = window.scrollY;

		var img = document.getElementById('hpAnnImg');
		var vid = document.getElementById('hpAnnVideo');
		var videoSrc = el.getAttribute('data-video');

		if (videoSrc) {
			// May video ang announcement na ito - video player ang ipinapakita
			// sa taas (may controls), tinatago na lang ang larawan.
			vid.src = videoSrc;
			vid.style.display = 'block';
			img.style.display = 'none';
		} else {
			vid.pause();
			vid.removeAttribute('src');
			vid.load();
			vid.style.display = 'none';
			img.style.display = '';
			img.src = el.getAttribute('data-img');
			img.alt = el.getAttribute('data-title');
		}

		document.getElementById('hpAnnTitle').innerText = el.getAttribute('data-title');
		document.getElementById('hpAnnDate').innerText = el.getAttribute('data-date');
		document.getElementById('hpAnnContent').innerText = el.getAttribute('data-content');
		document.getElementById('hpAnnFullscreen').classList.add('open');
		document.body.classList.add('hp-noscroll');
	}
	function hpCloseAnnouncement(){
		var vid = document.getElementById('hpAnnVideo');
		vid.pause();
		document.getElementById('hpAnnFullscreen').classList.remove('open');
		document.body.classList.remove('hp-noscroll');
		// Babalik sa eksaktong kinaroroonan niya bago binuksan ang
		// announcement (hindi na sa pinaka-taas ng homepage).
		window.scrollTo({top: hpAnnScrollY, behavior: 'auto'});
	}

	// ---- Gallery lightbox: i-tap ang photo para lumaki, may close (X) ----
	// May null-check dahil hindi lumalabas ang gallery section (at itong
	// modal) kapag walang laman ang images/gallery/ folder.
	function hpOpenGallery(src){
		var modal = document.getElementById('hpGalleryModal');
		if (!modal) { return; }
		document.getElementById('hpGalleryImg').src = src;
		modal.classList.add('open');
	}
	function hpCloseGallery(){
		var modal = document.getElementById('hpGalleryModal');
		if (!modal) { return; }
		modal.classList.remove('open');
	}
	var hpGalleryModal = document.getElementById('hpGalleryModal');
	if (hpGalleryModal) {
		hpGalleryModal.addEventListener('click', function(e){
			if (e.target === this) { hpCloseGallery(); }
		});
	}

	// Esc key - isara ang kahit anong bukas na overlay/modal.
	document.addEventListener('keydown', function(e){
		if (e.key === 'Escape') {
			hpCloseAnnouncement();
			hpCloseGallery();
			hpCloseAbout();
			hpCloseHistory();
		}
	});

	// ---- About "See More" - FULL SCREEN view. Ang larawan ay makikita
	// pa rin sa taas ng fullscreen view, tapos buong description, at
	// Close button sa pinakababa na babalik sa eksaktong kinaroroonan
	// niya bago binuksan (hindi sa top ng homepage). ----
	var hpAboutScrollY = 0;
	function hpOpenAbout(){
		hpAboutScrollY = window.scrollY;
		document.getElementById('hpAboutFullscreen').classList.add('open');
		document.body.classList.add('hp-noscroll');
	}
	function hpCloseAbout(){
		document.getElementById('hpAboutFullscreen').classList.remove('open');
		document.body.classList.remove('hp-noscroll');
		window.scrollTo({top: hpAboutScrollY, behavior: 'auto'});
	}

	// ---- History "See More" - hiwalay na FULL SCREEN view din, gaya
	// ng About. Larawan sa taas, tapos buong History + Mission/Vision,
	// at Close button sa pinakababa na babalik sa eksaktong kinaroroonan
	// niya bago binuksan (hindi sa top ng homepage). ----
	var hpHistoryScrollY = 0;
	function hpOpenHistory(){
		hpHistoryScrollY = window.scrollY;
		document.getElementById('hpHistoryFullscreen').classList.add('open');
		document.body.classList.add('hp-noscroll');
	}
	function hpCloseHistory(){
		document.getElementById('hpHistoryFullscreen').classList.remove('open');
		document.body.classList.remove('hp-noscroll');
		window.scrollTo({top: hpHistoryScrollY, behavior: 'auto'});
	}

	// ---- Fade-up on scroll para sa mga section (subtle lang, professional) ----
	var hpRevealTargets = document.querySelectorAll('.reveal, .reveal-stagger');
	if ('IntersectionObserver' in window) {
		var hpObserver = new IntersectionObserver(function(entries){
			entries.forEach(function(entry){
				if (entry.isIntersecting) {
					entry.target.classList.add('visible');
					hpObserver.unobserve(entry.target);
				}
			});
		}, {threshold:0.15});
		hpRevealTargets.forEach(function(t){ hpObserver.observe(t); });
	} else {
		// Kung walang IntersectionObserver support, ipakita na lang lahat.
		hpRevealTargets.forEach(function(t){ t.classList.add('visible'); });
	}

	// Auto-close mobile menu once a link is tapped
	document.querySelectorAll('#navLinks a').forEach(function(link){
		link.addEventListener('click', function(){
			document.getElementById('navLinks').classList.remove('open');
		});
	});
</script>
</body>
</html>

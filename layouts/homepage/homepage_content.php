<!-- ============================ HERO ============================ -->
<section class="hero" id="top">
	<div class="hero-content">
		<img src="<?php echo WEB_ROOT . SCHOOL_LOGO; ?>" class="hero-seal" alt="<?php echo htmlspecialchars(SCHOOL_NAME); ?> seal">
		<h1><?php echo htmlspecialchars(SCHOOL_NAME); ?></h1>
		<p><?php echo htmlspecialchars($SCHOOL_TAGLINE); ?></p>
		<div class="hero-actions">
			<a href="<?php echo WEB_ROOT; ?>enroll.php" class="btn btn-maroon">Apply / Enroll Now</a>
			<a href="#about" class="btn btn-outline">Learn More</a>
		</div>
	</div>
</section>

<!-- ============================ PROGRAMS ============================ -->
<section class="programs reveal" id="programs">
	<div class="container">
		<h2 class="section-title">Programs Offered</h2>
		<p class="section-sub">Choose the program that fits your passion and career goals.</p>
		<div class="program-grid reveal-stagger">
		<?php if (count($courses) > 0): foreach ($courses as $c):
			$icon = isset($courseIcons[$c->COURSE_CODE]) ? $courseIcons[$c->COURSE_CODE] : 'fa-book';
		?>
			<div class="program-card">
				<div class="program-icon"><i class="fa <?php echo $icon; ?>"></i></div>
				<span class="code"><?php echo htmlspecialchars($c->COURSE_CODE); ?></span>
				<h3><?php echo htmlspecialchars($c->COURSE_NAME); ?></h3>
				<p><?php echo htmlspecialchars($c->COURSE_DESC != '' ? $c->COURSE_DESC : 'A program built to prepare you for a real-world career.'); ?></p>
				<a href="#admissions">Learn more <i class="fa fa-arrow-right"></i></a>
			</div>
		<?php endforeach; else: ?>
			<div class="ann-empty">No active programs found yet. Add courses in the Admin panel to show them here.</div>
		<?php endif; ?>
		</div>
	</div>
</section>

<!-- ============================ ABOUT (SYSTEM) ============================ -->
<section class="about reveal" id="about">
	<div class="container">
		<?php if ($aboutImg): ?>
			<img src="<?php echo $aboutImg; ?>" alt="About <?php echo htmlspecialchars(SCHOOL_NAME); ?> System">
		<?php else: ?>
			<img src="<?php echo WEB_ROOT . SCHOOL_LOGO; ?>" alt="<?php echo htmlspecialchars(SCHOOL_NAME); ?> seal" style="max-width:280px;margin:0 auto;">
		<?php endif; ?>
		<div class="about-text" id="aboutText">
			<h2>About</h2>

			<!-- Ang "About" ay tungkol sa SYSTEM/website mismo (ano ito,
			     para saan ito) - hiwalay na ito sa History section sa
			     ibaba. Sariling larawan ito - ilagay sa
			     images/about-system/about-system-photo.jpg kapag meron
			     na (fallback muna sa seal habang wala pa). -->
			<p class="about-excerpt"><?php echo htmlspecialchars(hp_excerpt($SCHOOL_ABOUT_SYSTEM, 160)); ?></p>

			<button type="button" class="about-readmore" onclick="hpOpenAbout()">
				See More <i class="fa fa-chevron-down"></i>
			</button>
		</div>
	</div>
</section>

<!-- About - FULL SCREEN na view (buong screen), lumalabas pag tinap ang
     "See More" sa itaas. Larawan pa rin sa taas, tapos buong description,
     at Close button sa pinakababa na babalik sa homepage. -->
<div class="hp-fullscreen" id="hpAboutFullscreen">
	<button class="hp-fullscreen-close-top" onclick="hpCloseAbout()" aria-label="Close">&times;</button>
	<div class="hp-fullscreen-inner">
		<?php if ($aboutImg): ?>
			<img src="<?php echo $aboutImg; ?>" class="hp-fullscreen-img" alt="About <?php echo htmlspecialchars(SCHOOL_NAME); ?> System">
		<?php else: ?>
			<img src="<?php echo WEB_ROOT . SCHOOL_LOGO; ?>" class="hp-fullscreen-img" style="max-width:220px;object-fit:contain;margin:0 auto 28px;" alt="<?php echo htmlspecialchars(SCHOOL_NAME); ?> seal">
		<?php endif; ?>

		<div class="about-text">
			<h2>About</h2>
			<?php foreach (explode("\n\n", $SCHOOL_ABOUT_SYSTEM) as $aboutPara): ?>
				<p><?php echo htmlspecialchars($aboutPara); ?></p>
			<?php endforeach; ?>
		</div>

		<div class="hp-fullscreen-close-wrap">
			<button type="button" class="hp-fullscreen-close-bottom" onclick="hpCloseAbout()">
				<i class="fa fa-chevron-up"></i> Close
			</button>
		</div>
	</div>
</div>

<!-- ============================ HISTORY / MISSION-VISION ============================ -->
<section class="about reveal" id="history">
	<div class="container">
		<?php if ($historyImg): ?>
			<img src="<?php echo $historyImg; ?>" alt="History of <?php echo htmlspecialchars(SCHOOL_NAME); ?>">
		<?php else: ?>
			<img src="<?php echo WEB_ROOT . SCHOOL_LOGO; ?>" alt="<?php echo htmlspecialchars(SCHOOL_NAME); ?> seal" style="max-width:280px;margin:0 auto;">
		<?php endif; ?>
		<div class="about-text" id="historyText">
			<h2>History of <?php echo htmlspecialchars(SCHOOL_NAME); ?></h2>

			<!-- Hiwalay na section ito sa About - sariling larawan din
			     ito (images/about/about-photo.jpg). Pag tinap ang
			     "See More", buong screen na ang lalabas (larawan sa
			     taas + buong History + Mission/Vision). -->
			<p class="about-excerpt"><?php echo htmlspecialchars(hp_excerpt($SCHOOL_HISTORY, 160)); ?></p>

			<button type="button" class="about-readmore" onclick="hpOpenHistory()">
				See More <i class="fa fa-chevron-down"></i>
			</button>
		</div>
	</div>
</section>

<!-- History - FULL SCREEN na view (buong screen), lumalabas pag tinap ang
     "See More" sa itaas. Larawan pa rin sa taas, tapos buong History,
     Mission, Vision, at Close button sa pinakababa na babalik sa homepage. -->
<div class="hp-fullscreen" id="hpHistoryFullscreen">
	<button class="hp-fullscreen-close-top" onclick="hpCloseHistory()" aria-label="Close">&times;</button>
	<div class="hp-fullscreen-inner">
		<?php if ($historyImg): ?>
			<img src="<?php echo $historyImg; ?>" class="hp-fullscreen-img" alt="History of <?php echo htmlspecialchars(SCHOOL_NAME); ?>">
		<?php else: ?>
			<img src="<?php echo WEB_ROOT . SCHOOL_LOGO; ?>" class="hp-fullscreen-img" style="max-width:220px;object-fit:contain;margin:0 auto 28px;" alt="<?php echo htmlspecialchars(SCHOOL_NAME); ?> seal">
		<?php endif; ?>

		<div class="about-text">
			<h2>History of <?php echo htmlspecialchars(SCHOOL_NAME); ?></h2>
			<p><?php echo htmlspecialchars($SCHOOL_HISTORY); ?></p>

			<h4><i class="fa fa-bullseye"></i> Mission</h4>
			<p><?php echo htmlspecialchars($SCHOOL_MISSION); ?></p>
			<h4><i class="fa fa-eye"></i> Vision</h4>
			<p><?php echo htmlspecialchars($SCHOOL_VISION); ?></p>
		</div>

		<div class="hp-fullscreen-close-wrap">
			<button type="button" class="hp-fullscreen-close-bottom" onclick="hpCloseHistory()">
				<i class="fa fa-chevron-up"></i> Close
			</button>
		</div>
	</div>
</div>

<!-- ============================ ANNOUNCEMENTS ============================ -->
<section class="announcements reveal" id="news">
	<div class="container">
		<h2 class="section-title">News &amp; Announcements</h2>
		<p class="section-sub">Stay updated with the latest news from <?php echo htmlspecialchars(SCHOOL_NAME); ?>.</p>
		<div class="ann-list reveal-stagger">
		<?php if (count($announcements) > 0): foreach ($announcements as $a):
			$imgUrl = (isset($a->PICTURE) && $a->PICTURE != '') ? WEB_ROOT.'module/announcement/'.$a->PICTURE : WEB_ROOT.'no.png';
			$vidUrl = (isset($a->VIDEO) && $a->VIDEO != '') ? WEB_ROOT.'module/announcement/'.$a->VIDEO : '';
			$dateNice = date("F d, Y", strtotime($a->DATE_POSTED));
		?>
			<div class="ann-row">
				<div class="ann-row-img">
					<img src="<?php echo $imgUrl; ?>" alt="<?php echo htmlspecialchars($a->TITLE); ?>" onerror="this.src='<?php echo WEB_ROOT; ?>no.png'">
					<?php if ($vidUrl != ''): ?>
					<i class="fa fa-play-circle ann-video-badge" title="Has video"></i>
					<?php endif; ?>
				</div>
				<div class="ann-row-body">
					<div class="ann-date"><?php echo $dateNice; ?></div>
					<h3><?php echo htmlspecialchars($a->TITLE); ?></h3>
					<p><?php echo htmlspecialchars(hp_excerpt($a->CONTENT)); ?></p>
					<span class="readmore"
						data-img="<?php echo htmlspecialchars($imgUrl, ENT_QUOTES); ?>"
						data-video="<?php echo htmlspecialchars($vidUrl, ENT_QUOTES); ?>"
						data-title="<?php echo htmlspecialchars($a->TITLE, ENT_QUOTES); ?>"
						data-date="<?php echo htmlspecialchars($dateNice, ENT_QUOTES); ?>"
						data-content="<?php echo htmlspecialchars($a->CONTENT, ENT_QUOTES); ?>"
						onclick="hpOpenAnnouncement(this)">See More <i class="fa fa-angle-right"></i></span>
				</div>
			</div>
		<?php endforeach; else: ?>
			<div class="ann-empty">No announcements posted yet.</div>
		<?php endif; ?>
		</div>
	</div>
</section>

<!-- Announcement - FULL SCREEN na view (buong screen), lumalabas pag
     tinap ang "See More" sa alinmang announcement card. Larawan pa rin
     sa taas, tapos title/date/buong content, at Close button sa
     pinakababa na babalik sa homepage. -->
<div class="hp-fullscreen" id="hpAnnFullscreen">
	<button class="hp-fullscreen-close-top" onclick="hpCloseAnnouncement()" aria-label="Close">&times;</button>
	<div class="hp-fullscreen-inner">
		<img src="" id="hpAnnImg" class="hp-fullscreen-img" alt="">
		<video id="hpAnnVideo" class="hp-fullscreen-img" controls style="display:none;background:#000;"></video>
		<div class="ann-date" id="hpAnnDate"></div>
		<h2 id="hpAnnTitle"></h2>
		<p id="hpAnnContent"></p>

		<div class="hp-fullscreen-close-wrap">
			<button type="button" class="hp-fullscreen-close-bottom" onclick="hpCloseAnnouncement()">
				<i class="fa fa-chevron-up"></i> Close
			</button>
		</div>
	</div>
</div>

<?php if (count($galleryImgs) > 0): ?>
<!-- ============================ GALLERY ============================ -->
<!-- HIPANAO SOLUTIONS - Ayaw na ng auto-fit na grid (kung ilan kasya sa
     row); 3 columns na palagi (3 sa taas, 3 sa baba para sa 6 photos),
     bumababa lang sa 2/1 column sa maliliit na screen (dist/css/homepage.css).
     Bawat photo ay pwede i-tap para lumaki sa isang lightbox, may close (X). -->
<section class="gallery reveal" id="gallery">
	<div class="container">
		<h2 class="section-title">Gallery</h2>
		<p class="section-sub">A glimpse of life at <?php echo htmlspecialchars(SCHOOL_NAME); ?>.</p>
		<div class="gallery-grid reveal-stagger">
		<?php foreach ($galleryImgs as $gi => $g): ?>
			<div class="gallery-item" onclick="hpOpenGallery('<?php echo htmlspecialchars($g, ENT_QUOTES); ?>')">
				<img src="<?php echo $g; ?>" alt="Campus photo <?php echo ($gi + 1); ?>">
				<div class="gallery-overlay"><i class="fa fa-search-plus"></i></div>
			</div>
		<?php endforeach; ?>
		</div>
	</div>
</section>

<!-- Gallery lightbox modal - iisa lang, ang src na lang ang pinapalitan. -->
<div class="hp-modal" id="hpGalleryModal">
	<div class="hp-modal-box hp-modal-img-box">
		<button class="hp-modal-close" onclick="hpCloseGallery()">&times;</button>
		<img src="" id="hpGalleryImg" alt="Campus photo">
	</div>
</div>
<?php endif; ?>

<!-- ============================ CONTACT ============================ -->
<section class="contact reveal" id="admissions">
	<div class="container">
		<div class="contact-info">
			<h2 id="contact">Get in Touch</h2>
			<p class="section-sub" style="text-align:left;margin:0 0 20px;">Have questions about admissions or programs? Reach out to us.</p>
			<div class="contact-item">
				<i class="fa fa-map-marker-alt"></i>
				<div><strong>Address</strong><br><span><?php echo htmlspecialchars(SCHOOL_ADDRESS); ?></span></div>
			</div>
			<div class="contact-item">
				<i class="fa fa-phone"></i>
				<div><strong>Phone</strong><br><span><?php echo SCHOOL_CONTACT != '' ? htmlspecialchars(SCHOOL_CONTACT) : '(TODO: add contact number)'; ?></span></div>
			</div>
			<div class="contact-item">
				<i class="fa fa-envelope"></i>
				<div><strong>Email</strong><br><a href="<?php echo $SOCIAL_EMAIL_LINK; ?>"><?php echo htmlspecialchars($SCHOOL_EMAIL); ?></a></div>
			</div>
			<a href="<?php echo WEB_ROOT; ?>enroll.php" class="btn btn-maroon" style="margin-top:10px;">Apply / Enroll Now</a>
		</div>
		<div class="map-wrap">
			<iframe src="https://www.google.com/maps?q=<?php echo urlencode(SCHOOL_ADDRESS); ?>&output=embed" loading="lazy" allowfullscreen></iframe>
		</div>
	</div>
</section>


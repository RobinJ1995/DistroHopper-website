<?php
$lastCommit = json_decode (file_get_contents ('cron/lastCommit.json'));
?>
<!DOCTYPE html>
<html data-theme="gnome">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>DistroHopper</title>
	<script type="text/javascript">
		// Applied before the stylesheets paint so the persisted theme doesn't flash.
		// The attribute on <html> above is the no-JS fallback and the default.
		try
		{
			var theme = localStorage.getItem ('distrohopper.theme');
			if (theme == 'unity' || theme == 'gnome')
			{
				document.documentElement.setAttribute ('data-theme', theme);
			}
		}
		catch (e) {} // Private browsing, storage disabled, ...
	</script>
	<link type="text/css" rel="stylesheet" href="css/base.css" />
	<link type="text/css" rel="stylesheet" href="css/theme.unity.css" />
	<link type="text/css" rel="stylesheet" href="css/theme.gnome.css" />
	<link type="text/css" rel="stylesheet" href="wallpaper.php" />
	<script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript" src="js/script.js"></script>
</head>
<body>
<div id="desktop">
	<div class="shell panel">
		<span class="panel-clock"></span>
	</div>
	<div class="shell launcher">
		<div class="launchericon bfb">
			<img src="img/unity.launcher.bfb.png" alt="Dash Home" />
		</div>
		<div class="launchericon" id="github">
			<a href="https://github.com/RobinJ1995/DistroHopper">
				<img alt="GitHub" src="img/unity.launcher.github.png">
			</a>
		</div>
		<div class="launchericon" id="googleplay">
			<a href="https://play.google.com/store/apps/details?id=be.robinj.distrohopper">
				<img alt="Google Play Store" src="img/unity.launcher.googleplay.png">
			</a>
		</div>
		<div class="launchericon" id="themes">
			<img src="img/launcher.preferences.png" alt="Change theme" title="Change theme" />
		</div>
		<div class="launchericon trash">
			<a href="https://tinyurl.com/2fcpre6" target="_blank">
				<img src="img/unity.launcher.trash.empty.png" alt="Rubbish Bin" />
			</a>
		</div>
	</div>
	<div class="shell dash">
		<div class="dash ribbon">
			<button type="button" class="ribbonicon" id="ribbonhome">
				<img src="img/unity.dash.ribbon.home.png" alt="" />
				<span class="ribbonlabel">Home</span>
			</button>
			<button type="button" class="ribbonicon" id="ribbonscreenshots">
				<img src="img/unity.dash.ribbon.photo.png" alt="" />
				<span class="ribbonlabel">Screenshots</span>
			</button>
		</div>

		<div class="dash page" id="home">
			<section id="intro">
				<h1>DistroHopper</h1>
				<p><em>The Linux desktop on your Android device!</em></p>
				<p>Are you a Linux/open-source enthusiast? Whether you are or not, if it seems cool to be able to have the Linux desktop on your Android device, then this app is what you're looking for. Currently there is a choice between Ubuntu's Unity desktop, elementary OS' Pantheon desktop, and Gnome. Missing your desktop of choice? Get in touch and if there's enough interest I might just add it 😉</p>
				<p>Features include a couple of different themes, a search feature which allows you to search from a multitude of different search sources (both local and remote), and customisation options.</p>
				<p>If you have any suggestions or feedback, feel free to get in touch. The project is open-source with the source code publicly available at <a href="https://github.com/RobinJ1995/DistroHopper">https://github.com/RobinJ1995/DistroHopper</a>. If you're less technically-inclined but would still like to contribute, you can join the project's translation team over at <a href="https://www.transifex.com/distrohopper/">https://www.transifex.com/distrohopper/</a>.</p>

				<h2>Disclaimers</h2>
				<p>Ubuntu is a registered trademark of Canonical Ltd.<br />elementary is a registered trademark of elementary LLC.<br />Gnome is a registered trademark of the Gnome Foundation.<p>

				<h2>Download</h2>
				<p>You can download the latest version of DistroHopper either from the Google Play Store, or you can download the APK file from GitHub. Note that if you download and install DistroHopper via the provided APK file, you are responsible for manually keeping DistroHopper up-to-date on your device and will not automatically receive updates for it.</p>
				<p class="download">
					<a href="https://play.google.com/store/apps/details?id=be.robinj.distrohopper">Get it from the Google Play Store</a>
				</p>
				<p class="download">
					<a href="https://github.com/RobinJ1995/DistroHopper/releases">Download APK from GitHub</a>
				</p>

				<p id="homeScreenshot">
					<a href="#dash#screenshots" onClick="goToPage ('ribbonscreenshots');"><img src="img/screenshots/ubuntu.png" alt="[Screenshot]" class="screenshot" /></a>
					<a href="#dash#screenshots" onClick="goToPage ('ribbonscreenshots');"><img src="img/screenshots/gnome.png" alt="[Screenshot]" class="screenshot" /></a>
				</p>
			</section>
			<section id="side">
				<h2><a href="<?php echo htmlentities ($lastCommit->url); ?>" title="<?php echo $lastCommit->sha; ?>">Last commit</a></h2>
				<p>
					Message: <em><?php echo $lastCommit->commit->message; ?></em><br />
					Branch: <em><?php echo $lastCommit->branch->name; ?></em><br />
					Date: <em><?php echo $lastCommit->commit->committer->date; ?></em><br />
					Changes: <em><?php echo $lastCommit->stats->total; ?></em>
				</p>

				<h2>Note</h2>
				<p>This project was formerly known as Ubuntu Launcher, but was renamed to DistroHopper to prevent copyright/trademark issues from arising by using the Ubuntu name.</p>
			</section>
		</div>
		<div class="dash page" id="screenshots">
			<section class="imagegallery">
				<img src="img/screenshots/ubuntu.png" alt="[Ubuntu]" class="screenshot thumbnail" />
				<img src="img/screenshots/gnome.png" alt="[Gnome]" class="screenshot thumbnail" />
				<img src="img/screenshots/elementary.png" alt="[elementary]" class="screenshot thumbnail" />
				<img src="img/screenshots/search.png" alt="[Search]" class="screenshot thumbnail" />
			</section>
		</div>
	</div>

	<div id="themepicker" hidden>
		<div class="themepicker-dialog" role="dialog" aria-modal="true" aria-label="Theme">
			<h2>Theme</h2>
			<div class="themecards">
				<button type="button" class="themecard" role="radio" aria-checked="false" data-theme="unity" style="--card-colour: #E95420;">
					<img src="img/unity.launcher.bfb.png" alt="" />
					<span>
						<span class="themecard-name">Unity</span>
						<span class="themecard-description">Ubuntu's Unity desktop</span>
					</span>
				</button>
				<button type="button" class="themecard" role="radio" aria-checked="false" data-theme="gnome" style="--card-colour: #4A86CF;">
					<img src="img/theme.gnome.card.png" alt="" />
					<span>
						<span class="themecard-name">Gnome</span>
						<span class="themecard-description">Gnome Shell</span>
					</span>
				</button>
			</div>
		</div>
	</div>
</div>
</body>
</html>

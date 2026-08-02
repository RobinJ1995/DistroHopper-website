var dashOpened = false;

// Both themes use @drawable/launcher_preferences for the launcher's settings
// button, so only the BFB is per-theme artwork.
var THEMES =
{
	'unity': { bfb: 'img/unity.launcher.bfb.png' },
	'gnome': { bfb: 'img/gnome.launcher.bfb.png' }
};
var DEFAULT_THEME = 'gnome';
var THEME_STORAGE_KEY = 'distrohopper.theme';

$(document).ready
(
	function ()
	{
		initTheme ();
		initClock ();
		initSearch ();

		toggleDash ();

		$(document).keypress
		(
			function (key)
			{
				if ($(key.target).is ('input, textarea'))
				{
					return;
				}

				if (key.which == 0) // Why are all the Fx keys and the super key 0? >.<
				{
					toggleDash ();
				}
			}
		);

		$(document).keydown
		(
			function (key)
			{
				if (key.which == 27) // Escape
				{
					closeThemePicker ();
				}
			}
		);

		$('div.launchericon.bfb').click
		(
			function ()
			{
				toggleDash ();
			}
		);

		$('.ribbonicon').click
		(
			function ()
			{
				goToPage ($(this).attr ('id'));
			}
		);

		$('div.dash.page#screenshots img.screenshot.thumbnail').click
		(
			function ()
			{
				$('div.dash.page#screenshots img.screenshot.thumbnail').not(this).removeClass ('zoomed');
				$(this).toggleClass ('zoomed');
			}
		);

		$('div.launchericon#themes').click
		(
			function ()
			{
				openThemePicker ();
			}
		);

		$('button.themecard').click
		(
			function ()
			{
				applyTheme ($(this).attr ('data-theme'));
				closeThemePicker ();
			}
		);

		$('div#themepicker').click
		(
			function (event)
			{
				if (event.target === this) // Only the backdrop, not the dialog
				{
					closeThemePicker ();
				}
			}
		);
	}
);

function goToPage (name)
{
	name = name.substr (6);
	if ($('div.dash.page#' + name).length != 0) // Check if it exists
	{
		$('.ribbonicon').removeClass ('active');
		$('.ribbonicon#ribbon' + name).addClass ('active');

		$('div.dash.page').fadeOut (0);
		$('div.dash.page#' + name).delay (300).fadeIn (400);
	}
	else
	{
		throw ("Page \"" + name + "\" doesn't exist");
	}

	document.location.hash = 'dash#' + name;
}

function toggleDash ()
{
	$('div.shell.dash').fadeToggle (400);
	$('div.shell.launcher').toggleClass ('dashOpened');
	$('div.shell.launcher div.launchericon').toggleClass ('dashOpened');
	$('div.shell.panel').toggleClass ('dashOpened');
	dashOpened = (! dashOpened);

	var pageActive = false;
	$('.ribbonicon').each
	(
		function (i, object)
		{
			if ($(object).hasClass ('active'))
			{
				pageActive = true;
			}
		}
	)
	if (dashOpened)
	{
		document.location.hash = 'dash';
		if (! pageActive)
		{
			goToPage ('ribbonhome');
		}
	}
	else
	{
		document.location.hash = 'desktop';
	}
}

/* Themes ------------------------------------------------------------------ */
/* Mirrors ThemeCards.kt in the app: picking a card applies and persists the
   theme immediately. */

function storedTheme ()
{
	try
	{
		var theme = localStorage.getItem (THEME_STORAGE_KEY);

		return THEMES.hasOwnProperty (theme) ? theme : DEFAULT_THEME;
	}
	catch (e) // Private browsing, storage disabled, ...
	{
		return DEFAULT_THEME;
	}
}

function initTheme ()
{
	applyTheme (storedTheme ());
}

function applyTheme (name)
{
	if (! THEMES.hasOwnProperty (name))
	{
		name = DEFAULT_THEME;
	}

	document.documentElement.setAttribute ('data-theme', name);

	// The BFB is per-theme artwork rather than styling, so it's swapped here
	// instead of in CSS.
	$('div.launchericon.bfb img').attr ('src', THEMES[name].bfb);

	$('button.themecard').each
	(
		function ()
		{
			$(this).attr ('aria-checked', $(this).attr ('data-theme') == name ? 'true' : 'false');
		}
	);

	try
	{
		localStorage.setItem (THEME_STORAGE_KEY, name);
	}
	catch (e) {}
}

function openThemePicker ()
{
	$('div#themepicker').removeAttr ('hidden');
}

function closeThemePicker ()
{
	$('div#themepicker').attr ('hidden', 'hidden');
}

/* Panel clock — Gnome shows the date and time centred in the top bar. --------- */

function initClock ()
{
	updateClock ();
	setInterval (updateClock, 10000);
}

function updateClock ()
{
	var now = new Date ();
	var date = now.toLocaleDateString (undefined, { month: 'short', day: 'numeric' });
	var time = ('0' + now.getHours ()).slice (-2) + ':' + ('0' + now.getMinutes ()).slice (-2);

	$('span.panel-clock').text (date + '  ' + time);
}

/* Search — highlights matches on the page that's currently open in the dash. -- */

function initSearch ()
{
	$('input#dashsearch').on
	(
		'input',
		function ()
		{
			search ($(this).val ());
		}
	);
}

function search (query)
{
	var page = $('div.dash.page:visible').get (0) || $('div.dash.page#home').get (0);
	if (! page)
	{
		return;
	}

	$('div.dash.page').each
	(
		function ()
		{
			clearHighlights (this);
		}
	);

	query = $.trim (query);
	if (query.length < 2)
	{
		return;
	}

	if (highlight (page, query))
	{
		var first = $(page).find ('mark.searchhit').get (0);
		if (first && first.scrollIntoView)
		{
			first.scrollIntoView ({ block: 'center' });
		}
	}
}

function clearHighlights (root)
{
	$(root).find ('mark.searchhit').each
	(
		function ()
		{
			var parent = this.parentNode;

			while (this.firstChild)
			{
				parent.insertBefore (this.firstChild, this);
			}

			parent.removeChild (this);
			parent.normalize (); // Stitch the split text nodes back together
		}
	);
}

function highlight (root, query)
{
	var needle = query.toLowerCase ();
	var walker = document.createTreeWalker (root, NodeFilter.SHOW_TEXT, null, false);
	var nodes = [];
	var node;
	var found = false;

	while ((node = walker.nextNode ()))
	{
		nodes.push (node);
	}

	for (var i = 0; i < nodes.length; i++)
	{
		var text = nodes[i].nodeValue;
		var lower = text.toLowerCase ();
		var index = lower.indexOf (needle);

		if (index < 0)
		{
			continue;
		}

		var fragment = document.createDocumentFragment ();
		var position = 0;

		while (index >= 0)
		{
			fragment.appendChild (document.createTextNode (text.slice (position, index)));

			var mark = document.createElement ('mark');
			mark.className = 'searchhit';
			mark.appendChild (document.createTextNode (text.substr (index, needle.length)));
			fragment.appendChild (mark);

			position = index + needle.length;
			index = lower.indexOf (needle, position);
			found = true;
		}

		fragment.appendChild (document.createTextNode (text.slice (position)));
		nodes[i].parentNode.replaceChild (fragment, nodes[i]);
	}

	return found;
}

<?php
header ('Content-type: text/css');

$dir = scandir ('img/wallpapers');
$wallpapers = array ();
for ($i = 0; $i < count ($dir); $i++)
{
	if (($dir[$i] != '.') && ($dir[$i] != '..'))
	{
		array_push ($wallpapers, 'img/wallpapers/' . $dir[$i]);
	}
}
$wallpaper = $wallpapers[array_rand ($wallpapers)];

/**
 * The Unity theme is "chameleonic": the launcher, the dash and the app tiles all
 * take their colour from the wallpaper. The app gets this from
 * WallpaperManager's primaryColor (Wallpaper.getAverageColour()), which is the
 * most populous cluster of a quantised palette rather than a plain mean — a mean
 * turns most photographs into the same muddy grey. This does the same thing:
 * downscale, bucket into a coarse colour cube, and pick the busiest bucket,
 * weighted towards the colourful ones.
 *
 * Falls back to Wallpaper.COLOUR_UBUNTU_ORANGE (#B43C12) when GD is missing or
 * the image can't be read, exactly as the app does when it can't read the
 * wallpaper's colours.
 */
function dominantColour ($path)
{
	$fallback = array (180, 60, 18); // #B43C12, "Ubuntu orange"

	if (! function_exists ('imagescale'))
	{
		return $fallback;
	}

	$extension = strtolower (pathinfo ($path, PATHINFO_EXTENSION));
	if (($extension == 'jpg') || ($extension == 'jpeg'))
	{
		$image = @imagecreatefromjpeg ($path);
	}
	else if ($extension == 'png')
	{
		$image = @imagecreatefrompng ($path);
	}
	else
	{
		return $fallback;
	}

	if ($image === false)
	{
		return $fallback;
	}

	$small = @imagescale ($image, 48, 48, IMG_BILINEAR_FIXED);
	imagedestroy ($image);

	if ($small === false)
	{
		return $fallback;
	}

	$buckets = array ();
	for ($y = 0; $y < 48; $y++)
	{
		for ($x = 0; $x < 48; $x++)
		{
			$rgb = imagecolorat ($small, $x, $y);
			$r = ($rgb >> 16) & 0xFF;
			$g = ($rgb >> 8) & 0xFF;
			$b = $rgb & 0xFF;

			// Near-black and near-white carry no hue; Palette discards them too.
			$max = max ($r, $g, $b);
			$min = min ($r, $g, $b);
			if (($max < 24) || ($min > 240))
			{
				continue;
			}

			$key = (($r >> 4) << 8) | (($g >> 4) << 4) | ($b >> 4);
			if (! isset ($buckets[$key]))
			{
				$buckets[$key] = array (0, 0, 0, 0); // count, r, g, b
			}

			$buckets[$key][0]++;
			$buckets[$key][1] += $r;
			$buckets[$key][2] += $g;
			$buckets[$key][3] += $b;
		}
	}
	imagedestroy ($small);

	if (count ($buckets) == 0)
	{
		return $fallback;
	}

	$best = null;
	$bestScore = -1;
	foreach ($buckets as $bucket)
	{
		$r = $bucket[1] / $bucket[0];
		$g = $bucket[2] / $bucket[0];
		$b = $bucket[3] / $bucket[0];

		$max = max ($r, $g, $b);
		$saturation = ($max > 0) ? (($max - min ($r, $g, $b)) / $max) : 0;

		$score = $bucket[0] * (1 + $saturation);
		if ($score > $bestScore)
		{
			$bestScore = $score;
			$best = array ((int) round ($r), (int) round ($g), (int) round ($b));
		}
	}

	return $best;
}

$colour = dominantColour ($wallpaper);
?>

:root
{
	--chameleon: <?php echo implode (', ', $colour); ?>;
}

div#desktop
{
	background: black url('<?php echo $wallpaper; ?>');
	background-repeat: no-repeat;
	background-position: center;
	background-size: cover;
}

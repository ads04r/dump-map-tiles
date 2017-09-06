#!/usr/bin/php -q
<?php

// Modify the following values as you wish.

$minlat = 50.8972;
$maxlat = 50.9679;
$minlon = -1.4838;
$maxlon = -1.3611;
$url = "http://127.0.0.1:9090/osm/[Z]/[X]/[Y].png";
$path = "/home/ash/tools/dump-map-tiles/dump";

for($zoom = 1; $zoom <= 20; $zoom++)
{
	error_log("[INFO] Beginning zoom level " . $zoom);

	if(!(file_exists($path . "/" . $zoom))) { mkdir($path . "/" . $zoom); }

	$minx = floor((($minlon + 180) / 360) * pow(2, $zoom));
	$miny = floor((1 - log(tan(deg2rad($minlat)) + 1 / cos(deg2rad($minlat))) / pi()) /2 * pow(2, $zoom));
	$maxx = floor((($maxlon + 180) / 360) * pow(2, $zoom));
	$maxy = floor((1 - log(tan(deg2rad($maxlat)) + 1 / cos(deg2rad($maxlat))) / pi()) /2 * pow(2, $zoom));

	if($maxx < $minx)
	{
		$tmp = $maxx;
		$maxx = $minx;
		$minx = $tmp;
	}
	if($maxy < $miny)
	{
		$tmp = $maxy;
		$maxy = $miny;
		$miny = $tmp;
	}

	for($x = $minx; $x <= $maxx; $x++)
	{
		if(!(file_exists($path . "/" . $zoom . "/" . $x))) { mkdir($path . "/" . $zoom . "/" . $x); }

		for($y = $miny; $y <= $maxy; $y++)
		{
				$tile_url = $url;
				$tile_url = str_replace("[X]", $x, $tile_url);
				$tile_url = str_replace("[Y]", $y, $tile_url);
				$tile_url = str_replace("[Z]", $zoom, $tile_url);
				$tile_path = $path . "/" . $zoom . "/" . $x . "/" . $y . ".png";

				if(file_exists($tile_path)) { continue; } // Skip if the tile's already downloaded

				$png = @file_get_contents($tile_url);
				if(strlen($png) == 0)
				{
					error_log("[FAIL] " . $tile_url); // If file is empty, don't store but warn
					continue;
				}
				$fp = fopen($tile_path, "w");
				fwrite($fp, $png);
				fclose($fp);
		}
	}
}

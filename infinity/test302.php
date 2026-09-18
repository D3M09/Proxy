<?php
$ch = curl_init('http://lottogamez.gamer.free/m/app.73f3eb85.js');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIE, '__test=a659ca047c396b85433fabfb94a9edfc');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_HEADER, true);
$r = curl_exec($ch);
echo $r;
curl_close($ch);

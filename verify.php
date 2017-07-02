<?php
$access_token = 'LiqqHhZZ3Fejw0AYMisCCzy44E6hop5sHAkzaUYpbDdFUrg7rIQchP+Vbv1dgHI9IuY3OEJKx9AhMwq8NEOLUvI1/AR1bWg9C1/Qf9jzKISV5818paDcbZwlF7UkaGUa5G9oWUbTbmQ/RUWBkRgCqwdB04t89/1O/w1cDnyilFU=';

$url = 'https://api.line.me/v1/oauth/verify';

$headers = array('Authorization: Bearer ' . $access_token);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
$result = curl_exec($ch);
curl_close($ch);

echo $result;
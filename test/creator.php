<?php
require __DIR__ . '/../vendor/autoload.php';
use \crichain\Creator;

$keyPair = Creator::keyPair();
var_dump($keyPair);

$sign = Creator::sign($keyPair['privateKey'], 'dddddsds');
var_dump($sign);
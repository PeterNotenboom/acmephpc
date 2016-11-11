<?php

$autoload = require __DIR__.'/../vendor/autoload.php';

$config = require __DIR__.'/../src/config.php';

// Get client
$client = new \Octopuce\Acme\Client($config);

// Make your calls !

// Works but needed only if no default account in config
// $client->newAccount('test107@nodeshot.com');

// Works but needed only if no default account in config
// $client->loadAccount('test107@nodeshot.com');

// Works
$client->newOwnership('nodeshot.com');

// Works
// $client->getChallengeData('nodeshot.com');
$client->getChallengeData('nodeshot.com', 'dns'); /// Can override challenge type for each call

// Works
$client->challengeOwnership('nodeshot.com', 'dns');

// Works
//$client->signCertificate('nodeshot.com');

// Works
//$client->getCertificate('nodeshot.com');

// Works
// $client->revokeCertificate('nodeshot.com');

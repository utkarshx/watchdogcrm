<?php
require_once '../../src/apiClient.php';
require_once '../../src/contrib/apiWebfontsService.php';

$client = new apiClient();
$client->setApplicationName("Google WebFonts PHP Starter Application");

// Visit https://code.google.com/apis/console?api=webfonts
// to generate your developer key.
// $client->setDeveloperKey('insert_your_developer_key');
$service = new apiWebfontsService($client);
$fonts = $service->webfonts->listWebfonts();
print "<h1>Fonts</h1><pre>" . print_r($fonts, true) . "</pre>";

<?php

// Autoload
use Ed2kLinksGrabber\Forum\Service\ForumAuth;
use Ed2kLinksGrabber\Forum\Service\ForumReader;
use MlDonkeySender\MlDonkeySender\Data\MlDonkeyServerData;
use MlDonkeySender\MlDonkeySender\Service\MlDonkeyServerDownload;

require __DIR__ . '/vendor/autoload.php';

// Ignore list
$ignoreList = array(
    'REGOLAMENTO DI SEZIONE',
);

// Command line
$cmd = new Commando\Command();

// Define a flag "-q" a.k.a. "--mldonkey-host"
$cmd->option('q')
    ->require()
    ->aka('mldonkey-host')
    ->describedAs('Mldonkey host');

// Define a flag "-w" a.k.a. "--mldonkey-port"
$cmd->option('w')
    ->default(4080)
    ->aka('mldonkey-port')
    ->describedAs('Mldonkey port');

// Define a flag "-e" a.k.a. "--mldonkey-user"
$cmd->option('e')
    ->require()
    ->aka('mldonkey-username')
    ->describedAs('Mldonkey username');

// Define a flag "-r" a.k.a. "--mldonkey-password"
$cmd->option('r')
    ->require()
    ->aka('mldonkey-password')
    ->describedAs('Mldonkey password');

// Define a flag "-t" a.k.a. "--forum-username"
$cmd->option('t')
    ->require()
    ->aka('forum-username')
    ->describedAs('Forum username');

// Define a flag "-y" a.k.a. "--forum-password"
$cmd->option('y')
    ->require()
    ->aka('forum-password')
    ->describedAs('Forum password');

// Define a flag "-u" a.k.a. "--forum-page"
$cmd->option('u')
    ->require()
    ->aka('forum-page')
    ->describedAs('Forum page');

// Server MLDonkey
$mldonkeyServerData = new MlDonkeyServerData();
$mldonkeyServerData->ip = (string)$cmd['mldonkey-host'];
$mldonkeyServerData->port = (int)$cmd['mldonkey-port'];
$mldonkeyServerData->username = (string)$cmd['mldonkey-username'];
$mldonkeyServerData->password  = (string)$cmd['mldonkey-password'];

// Detect input service
$parsedUrl = parse_url($cmd['forum-page']);
$service = 'http://'.($parsedUrl['host']);

// Auth
$forumAuth = new ForumAuth();
$forumData = $forumAuth->authenticate($service, (string)$cmd['forum-username'], (string)$cmd['forum-password']);
if(!$forumData) {
    die('Login failed'.PHP_EOL);
}

// Read links
$forumReader = new ForumReader($forumData);
if(parse_url($cmd['forum-page'])['path'] == '/viewforum.php') {
    // Topic
    $topics = $forumReader->getTopicsFromPage((string)$cmd['forum-page'], 2, $ignoreList);

    foreach($topics as $i => $topic) {
        echo ($i + 1).'. '.$topic[0].PHP_EOL;
    }

    echo PHP_EOL;
    $number = readline('Enter your number: ');
    $number--;
    $pageToDownload = (string)$topics[$number][1];
}
else {
    $pageToDownload = (string)$cmd['forum-page'];
}

// Get ed2kLinks
$ed2kLinks = $forumReader->getLinksFromPage($pageToDownload);
if($ed2kLinks === null) {
    die('Parse failed'.PHP_EOL);
}

// Download
$mldonkeyServer = new MlDonkeyServerDownload($mldonkeyServerData);
$mldonkeyServer->download($ed2kLinks);

// Log
print('Download ok');
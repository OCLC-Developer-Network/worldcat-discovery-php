<?php
namespace WorldCat\Discovery;

use Guzzle\Http\StaticClient;
use Symfony\Component\Yaml\Yaml;
use OCLC\Auth\WSKey;
use OCLC\Auth\AccessToken;
use WorldCat\Discovery\Bib;

require __DIR__ . '/../vendor/autoload.php';

\VCR\VCR::turnOn();
\VCR\VCR::configure()->setCassettePath('mocks');
\VCR\VCR::insertCassette('accessToken');

$mockFolder = __DIR__ . "/mocks/";

if (isset($argv[2])){
    $mockFolder .= $argv[2] . '/';
}

// load the YAML for mocks
$mockBuilder = Yaml::parse(__DIR__ . '/mockBuilder.yml');

    // load the YAML for config
    $config = Yaml::parse(__DIR__ . '/config.yml');
    if (isset($argv[2])){
        $environment = $argv[2];
        AccessToken::$authorizationServer = $config[$environment]['authorizationServiceUrl'];
        Bib::$serviceUrl = $config[$environment]['discoveryUrl'];
        Database::$serviceUrl = $config[$environment]['discoveryUrl'];
    } else {
        $environment = 'prod';
    }


// Go get an accessToken
$options =  array(
    'services' => array('WorldCatDiscoveryAPI')
);
$wskey = new WSKey($config[$environment]['wskey'], $config[$environment]['secret'], $options);

$retrievedToken = $wskey->getAccessTokenWithClientCredentials($config['institution'], $config['institution']);
\VCR\VCR::eject();
if ($argv[1] == 'all'  || $argv[1] == 'bibFind'){    
    //BibFind mocks
    foreach ($mockBuilder['bibFind'] as $mock => $mockValues) {
            // delete files
            if (file_exists($mockFolder . $mock)){
                unlink($mockFolder . $mock);
            }
            \VCR\VCR::insertCassette($mock);
            printf("Mock created for '%s'.\n", $mock);
            if (isset($mockValues['accessToken'])){
                $accessToken = new AccessToken('client_credentials', array('accessTokenString' => $mockValues['accessToken'], 'expiresAt' => '2018-08-30 18:25:29Z'));
            } else {
                $accessToken = $retrievedToken;
            }
            $bib = Bib::find($mockValues['oclcNumber'], $accessToken);
            \VCR\VCR::eject();
    }
}

if ($argv[1] == 'all'  || $argv[1] == 'bibSearch'){
    //BibSearch mocks
    foreach ($mockBuilder['bibSearch'] as $mock => $mockValues) {
        // delete files
        if (file_exists($mockFolder . $mock)){
            unlink($mockFolder . $mock);
        }
        \VCR\VCR::insertCassette($mock);
        printf("Mock created for '%s'.\n", $mock);
        $options = array();
        if (isset($mockValues['facets'])){
            $options['facets'] = $mockValues['facets'];
        }
        if (isset($mockValues['startNum'])) {
            $options['startNum'] = $mockValues['startNum'];
        }
        
        if (isset($mockValues['itemsPerPage'])) {
            $options['itemsPerPage'] = $mockValues['itemsPerPage'];
        }
        
        $bib = Bib::search($mockValues['query'], $retrievedToken, $options);
        \VCR\VCR::eject();
    }
}

if ($argv[1] == 'all'  || $argv[1] == 'database'){
    //database mocks
    foreach ($mockBuilder['databaseFind'] as $mock => $mockValues) {
        // delete files
        if (file_exists($mockFolder . $mock)){
            unlink($mockFolder . $mock);
        }
        \VCR\VCR::insertCassette($mock);
        printf("Mock created for '%s'.\n", $mock);
        $database = Database::find($mockValues['id'], $retrievedToken);
        \VCR\VCR::eject();
    }
    
    //database list mock
    $mock = 'databaseListSuccess';
    // delete files
    if (file_exists($mockFolder . $mock)){
        unlink($mockFolder . $mock);
    }
    \VCR\VCR::insertCassette($mock);
    printf("Mock created for '%s'.\n", $mock);
    \VCR\VCR::insertCassette($mockBuilder['databaseSearch']);
    $database = Database::getList($retrievedToken);
    \VCR\VCR::eject();
}

// delete the accessToken file
unlink($mockFolder . 'accessToken');

// loop through the files in the mocks directory and update the Authorization header if necessary
foreach(glob($mockFolder ."*") as $file) {
    $mockFile = Yaml::parse($file);
    if ($mockFile[0]['request']['headers']['Authorization'] != 'Bearer tk_12345'){
        file_put_contents($file, str_replace($mockFile[0]['request']['headers']['Authorization'], "Bearer tk_12345", file_get_contents($file)));
    }
} 
?>
<?php
namespace WorldCat\Discovery;

use Guzzle\Http\StaticClient;
use Symfony\Component\Yaml\Yaml;
use OCLC\Auth\WSKey;
use OCLC\Auth\AccessToken;
use WorldCat\Discovery\Bib;
use WorldCat\Discovery\Offer;

require __DIR__ . '/../vendor/autoload.php';

if (!class_exists('Guzzle')) {
    \Guzzle\Http\StaticClient::mount();
}

\VCR\VCR::turnOn();
if (isset($argv[2])){
    $cassettePath = 'mocks/' . $argv[2];
} else {
    $cassettePath = 'mocks';
}
\VCR\VCR::configure()->setCassettePath($cassettePath);
\VCR\VCR::insertCassette('accessToken'); 

$mockFolder = __DIR__ . "/mocks/";

// load the YAML for mocks
$mockBuilder = Yaml::parse(__DIR__ . '/mockBuilder.yml');

    // load the YAML for config
    $config = Yaml::parse(__DIR__ . '/config.yml');
    if (isset($argv[2])){
        $mockFolder .= $argv[2] . '/';
        $environment = $argv[2];
        AccessToken::$authorizationServer = $config[$environment]['authorizationServiceUrl'];
        WSKey::$testServer = TRUE;
        Bib::$serviceUrl = $config[$environment]['discoveryUrl'];
        Bib::$testServer = TRUE;
        Database::$serviceUrl = $config[$environment]['discoveryUrl'];
        Database::$testServer = TRUE;
        Offer::$serviceUrl = $config[$environment]['discoveryUrl'];
        Offer::$testServer = TRUE;
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
            file_put_contents($mockFolder . $mock, str_replace("Bearer " . $accessToken->getValue(), "Bearer tk_12345", file_get_contents($mockFolder . $mock)));
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
        if (isset($mockValues['facetFields'])){
            $options['facetFields'] = $mockValues['facetFields'];
        }
        if (isset($mockValues['startNum'])) {
            $options['startNum'] = $mockValues['startNum'];
        }
        
        if (isset($mockValues['itemsPerPage'])) {
            $options['itemsPerPage'] = $mockValues['itemsPerPage'];
        }
        
        if (isset($mockValues['dbIds'])) {
            $options['dbIds'] = $mockValues['dbIds'];
        }
        
        $bib = Bib::search($mockValues['query'], $retrievedToken, $options);
        \VCR\VCR::eject();
        file_put_contents($mockFolder . $mock, str_replace("Bearer " . $accessToken->getValue(), "Bearer tk_12345", file_get_contents($mockFolder . $mock)));
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
        file_put_contents($mockFolder . $mock, str_replace("Bearer " . $accessToken->getValue(), "Bearer tk_12345", file_get_contents($mockFolder . $mock)));
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
    file_put_contents($mockFolder . $mock, str_replace("Bearer " . $accessToken->getValue(), "Bearer tk_12345", file_get_contents($mockFolder . $mock)));
}

if ($argv[1] == 'all'  || $argv[1] == 'offers'){
    //offer mocks
    foreach ($mockBuilder['offers'] as $mock => $mockValues) {
        // delete files
        if (file_exists($mockFolder . $mock)){
            unlink($mockFolder . $mock);
        }
        \VCR\VCR::insertCassette($mock);
        printf("Mock created for '%s'.\n", $mock);
        
        if (isset($mockValues['options'])){
            $options = $mockValues['options'];
        } else {
            $options = array();
        }
        
        if (isset($mockValues['accessToken'])){
            $accessToken = new AccessToken('client_credentials', array('accessTokenString' => $mockValues['accessToken'], 'expiresAt' => '2018-08-30 18:25:29Z'));
        } else {
            $accessToken = $retrievedToken;
        }
        
        $bib = Offer::findByOclcNumber($mockValues['id'], $accessToken, $options);
        \VCR\VCR::eject();
        file_put_contents($mockFolder . $mock, str_replace("Bearer " . $accessToken->getValue(), "Bearer tk_12345", file_get_contents($mockFolder . $mock)));
    }
}

// delete the accessToken file
unlink($mockFolder . 'accessToken'); 
?>
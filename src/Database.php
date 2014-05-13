<?php
namespace WorldCat\Discovery;

use \EasyRdf_Graph;
use \EasyRdf_Resource;
use \EasyRdf_Format;
use \EasyRdf_Namespace;
use \EasyRdf_TypeMapper;

/**
 * A class that represents a Bibliographic Resource in WorldCat
 *
 */
class Database
{
    public static $serviceUrl = 'https://beta.worldcat.org/discovery';
    private $database;
    
    public static function find($id, $accessToken, $options = null)
    {
        
        if (!is_int($id)){
            Throw new \BadMethodCallException('You must pass a valid ID');
        } elseif (!is_a($accessToken, '\OCLC\Auth\AccessToken')) {
            Throw new \BadMethodCallException('You must pass a valid OCLC/Auth/AccessToken object');
        }
        
        static::requestSetup();
        
        $guzzleOptions = array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $accessToken->getValue()
                //'Accept' => 'application/rdf+xml'
            )
        );
        
        if (isset($options['mockFile'])){
            $guzzleOptions['plugins'] = array(
                new \Guzzle\Plugin\Mock\MockPlugin(array($options['mockFile']))
            );
        }
        
        $databaseURI = static::$serviceUrl . '/database/data/' . $id;
        
        try {
            $response = \Guzzle::get($databaseURI, $guzzleOptions);
            return $response;
        } catch (\Guzzle\Http\Exception\BadResponseException $error) {
            return $error;
        }
    }
    
    public static function getList($accessToken, $options = null)
    {
        if (!is_a($accessToken, '\OCLC\Auth\AccessToken')) {
            Throw new \BadMethodCallException('You must pass a valid OCLC/Auth/AccessToken object');
        }
        
        static::requestSetup();
                
        $guzzleOptions = array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $accessToken->getValue()
                //'Accept' => 'application/rdf+xml'
            )
        );
        
        if (isset($options['mockFile'])){
            $guzzleOptions['plugins'] = array(
            	new \Guzzle\Plugin\Mock\MockPlugin(array($options['mockFile']))
            );
        }
        
        $databaseListURI = static::$serviceUrl . '/database/list';
        
        try {
            $listResponse = \Guzzle::get($databaseListURI, $guzzleOptions);
            return $listResponse;
        } catch (\Guzzle\Http\Exception\BadResponseException $error) {
            return $error;
        }
    }
    
    private static function requestSetup()
    {       
        if (!class_exists('Guzzle')) {
            \Guzzle\Http\StaticClient::mount();
        }
    }
    
}
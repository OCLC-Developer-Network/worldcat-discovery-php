<?php
// Copyright 2014 OCLC
//
// Licensed under the Apache License, Version 2.0 (the "License");
// you may not use this file except in compliance with the License.
// You may obtain a copy of the License at
//
// http://www.apache.org/licenses/LICENSE-2.0
//
// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.

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
class Database extends EasyRdf_Resource
{
    public static $serviceUrl = 'https://beta.worldcat.org/discovery';
    public static $testServer = FALSE;
    public static $userAgent = 'WorldCat Discovery API PHP Client';
    
    private $database;
    
    function getId()
    {
        return $this->getUri();
    }
    
    /**
     * Get Name
     *
     * @return EasyRDF_Literal
     */
    function getName()
    {
        $name = $this->get('schema:name');
        return $name;
    }
    
    /**
     * Get Open Access
     *
     * @return EasyRDF_Literal
     */
    function getOpenAccess()
    {
        $openAccess = $this->get('discovery:openAccess');
        return $openAccess;
    }
    
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
                'Authorization' => 'Bearer ' . $accessToken->getValue(),
                'Accept' => 'application/rdf+xml',
                'User-Agent' => static::$userAgent
            )
        );
        
        if (static::$testServer){
            $guzzleOptions['verify'] = false;
        }
        
        $databaseURI = static::$serviceUrl . '/database/data/' . $id;
        
        try {
            $response = \Guzzle::get($databaseURI, $guzzleOptions);
            $graph = new EasyRdf_Graph();
            $graph->parse($response->getBody(true));
            //$database = $graph->resource($databaseURI);
            $database = $graph->allOfType('dcmi:Dataset');
            return $database[0];
        } catch (\Guzzle\Http\Exception\BadResponseException $error) {
            return Error::parseError($error);
        }
    }
    
    public static function getList($accessToken)
    {
        if (!is_a($accessToken, '\OCLC\Auth\AccessToken')) {
            Throw new \BadMethodCallException('You must pass a valid OCLC/Auth/AccessToken object');
        }
        
        static::requestSetup();
                
        $guzzleOptions = array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $accessToken->getValue(),
                'Accept' => 'application/rdf+xml'
            )
        );
        
        if (static::$testServer){
            $guzzleOptions['verify'] = false;
        }
        
        $databaseListURI = static::$serviceUrl . '/database/list';
        
        try {
            $listResponse = \Guzzle::get($databaseListURI, $guzzleOptions);
            $graph = new EasyRdf_Graph();
            $graph->parse($listResponse->getBody(true));
            $list = $graph->allOfType('dcmi:Dataset');
            return $list;
        } catch (\Guzzle\Http\Exception\BadResponseException $error) {
            return Error::parseError($error);
        }
    }
    
    private static function requestSetup()
    {   
        EasyRdf_Namespace::set('schema', 'http://schema.org/');
        EasyRdf_Namespace::set('discovery', 'http://worldcat.org/vocab/discovery/');
        EasyRdf_Namespace::set('response', 'http://worldcat.org/xmlschemas/response/');
        EasyRdf_Namespace::set('dcmi', 'http://purl.org/dc/dcmitype/');
        
        EasyRdf_TypeMapper::set('dcmi:Dataset', 'WorldCat\Discovery\Database');
        EasyRdf_TypeMapper::set('response:ClientRequestError', 'WorldCat\Discovery\Error');
        
        if (!class_exists('Guzzle')) {
            \Guzzle\Http\StaticClient::mount();
        }
    }
    
}
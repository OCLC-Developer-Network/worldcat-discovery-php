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
        
        if (isset($options['mockFilePath'])){
            $guzzleOptions['plugins'] = array(
                new \Guzzle\Plugin\Mock\MockPlugin(array($options['mockFilePath']))
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
        
        if (isset($options['mockFilePath'])){
            $guzzleOptions['plugins'] = array(
            	new \Guzzle\Plugin\Mock\MockPlugin(array($options['mockFilePath']))
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
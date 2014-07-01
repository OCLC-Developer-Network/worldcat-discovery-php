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

use Guzzle\Http\StaticClient;
use \EasyRdf_Graph;
use \EasyRdf_Resource;
use \EasyRdf_Format;
use \EasyRdf_Namespace;
use \EasyRdf_TypeMapper;

/**
* A class that represents a MADS Authority
* 
* Used for compound authorities like http://id.loc.gov/authorities/subjects/sh2008124372.rdf
*
*/
class Authority extends EasyRdf_Resource
{
    public static function findAuthority($url){
        if (!class_exists('Guzzle')) {
            \Guzzle\Http\StaticClient::mount();
        }
        $guzzleOptions = array(
            'headers' => array(
                'Accept' => 'application/rdf+xml'
            )
        );
        try {
            $response = \Guzzle::get($url, $guzzleOptions);
            EasyRdf_Namespace::set('madsrdf', 'http://www.loc.gov/mads/rdf/v1#');
            EasyRdf_TypeMapper::set('madsrdf:Topic', 'WorldCat\Discovery\AuthorityTopical');
            EasyRdf_TypeMapper::set('madsrdf:Geographic', 'WorldCat\Discovery\AuthorityGeographic');
            EasyRdf_TypeMapper::set('madsrdf:Authority', 'WorldCat\Discovery\Authority');
            $authorityGraph = new EasyRdf_Graph();
            $authorityGraph->parse($response->getBody('true'));
            $authority = $authorityGraph->resource($url);
            return $authority;
        } catch (\Guzzle\Http\Exception\BadResponseException $error) {
            return $error;
        }
    }
    
    public function load($format = null)
    {
        $formats = EasyRdf_Format::getNames();
        foreach ($formats as $format){
            if ($format != 'rdfxml'){
                EasyRdf_Format::unregister($format);
            }
        }
        parent::load($format = null);
    }
    
    function label($lang = null) {
        if (parent::label($lang)){
            return parent::label($lang);
        } else {
            return $this->getLiteral('madsrdf:authoritativeLabel', $lang)->getValue();
        }
    }
    
    function getComponentList()
    {
        return $this->getResource('madsrdf:componentList');
    }
    
    function getTopics()
    {
        $topics = $this->graph->allOfType('madsrdf:Topic');
        return $topics;
    }
    
    function getGeographics()
    {
        $geographics = $this->graph->allOfType('madsrdf:Geographic');
        return $geographics;
    }
    
        
    function getGenres()
    {
        $genres = $this->graph->allOfType('madsrdf:GenreForm');
        return $genres;
    }
}
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

use \EasyRdf_Resource;
use \EasyRdf_Format;
use \EasyRdf_Graph;

/**
 * A class that represents a Person in Schema.org
 *
 */
class Person extends Thing
{   
    function __construct($uri, $graph = null){
        parent::__construct($uri, $graph);
        if (strpos($this->getURI(), 'viaf')){
            // build a new graph from VIAF
            $formats = EasyRdf_Format::getNames();
            foreach ($formats as $format){
                if ($format != 'rdfxml'){
                    EasyRdf_Format::unregister($format);
                }
            }
            $viafGraph = new EasyRdf_Graph();
            $viafGraph->load($this->getURI(), 'rdfxml');
            $viafResource = $viafGraph->resource($this->getUri());
            
            // loop through and add VIAF properties to this resource
            foreach ($viafResource->properties() as $property){
                foreach ($viafResource->all($property) as $value){
                    $this->add($property, $value);
                }
            }
        }
    }
    
    function getBirthDate(){
        return $this->getLiteral('rdaGr2:dateOfBirth');
    }
    
    function getDeathDate(){
        return $this->getLiteral('rdaGr2:dateOfDeath');
    }
    
    function getSameAsProperties(){
        return $this->all('owl:sameAs');
    }
    
    function getDbpediaUri(){
        $sameAsProperties = self::getSameAsProperties();
        $dbpediaPerson = array_filter($sameAsProperties, function($sameAs)
        {
            return(strpos($sameAs->getURI(), 'dbpedia'));
        }); 
        $dbpediaPerson = array_shift($dbpediaPerson);
        return $dbpediaPerson->getURI();
    }
    
}

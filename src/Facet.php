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

/**
 * A class that represents a Facet Resource in WorldCat
 *
 */
class Facet extends EasyRdf_Resource
{
   /**
    * Get facetIndex
    * return string
    */

    function getFacetIndex(){
        return $this->get('searcho:facetIndex');
    }
    
    /**
     * Get an array of FacetItems (WorldCat/Discovery/FacetItem)
     *
     * @return array
     */
    function getFacetValues(){
        $facetValueList = $this->allResources('searcho:facetValue');
        $sortedFacetValueList  = array();
        foreach ($facetValueList as $facetValue){
            $sortedFacetValueList[$facetValue->getCount()->getValue()] = $facetValue;
        }
        krsort($sortedFacetValueList);
        return $sortedFacetValueList;
    }
}
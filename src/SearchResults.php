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
 * A class that represents a Bibliographic Resource in WorldCat
 *
 */
class SearchResults extends EasyRdf_Resource
{ 
    /**
     * Get Start Index
     *
     * @return integer
     */
    function getStartIndex()
    {
        $startIndex = $this->get('discovery:startIndex');
        return $startIndex->getValue();
    }
    
    /**
     * Get Items Per page
     *
     * @return integer
     */
    function getItemsPerPage()
    {
        $itemsPerPage = $this->get('discovery:itemsPerPage');
        return $itemsPerPage->getValue();
    }
    
    /**
     * Get Total Results
     *
     * @return integer
     */
    function getTotalResults()
    {
        $totalResults = $this->get('discovery:totalResults');
        return $totalResults->getValue();
    }
    
    /**
     * Get an array of search results (EasyRDF_Resource objects)
     * 
     * @return array
     */
    function getSearchResults(){
        $searchResults = $this->graph->allOfType('http://www.w3.org/2006/gen/ont#InformationResource');
        $sortedSearchResults = array();
        foreach ($searchResults as $result){
            $sortedSearchResults[(int)$result->getCreativeWork()->getDisplayPosition()] = $result->getCreativeWork();
        }
        ksort($sortedSearchResults);
        return $sortedSearchResults;
    }
    
    function getOffers(){
        
        $offers = $this->graph->allOfType('schema:Offer');
        $sortedOffers = array();
        foreach ($offers as $offer){
            $sortedOffers[(int)$offer->getDisplayPosition()] = $offer;
        }
        ksort($sortedOffers);
        return $sortedOffers;
    }
    
    /**
     * Get an array of Facets (WorldCat/Discovery/Facet)
     * 
     * @return array
     */
     function getFacets(){
         $facetList = $this->graph->allOfType('discovery:FacetItem');
         return $facetList;
     } 
}
<?php
namespace OCLC\WorldCatDiscovery;

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
     * @return string
     */
    function getStartIndex()
    {
        $startIndex = $this->get('searcho:startIndex');
        return $startIndex;
    }
    
    /**
     * Get Items Per page
     *
     * @return integer
     */
    function getItemsPerPage()
    {
        $itemsPerPage = $this->get('searcho:itemsPerPage');
        return $itemsPerPage->getValue();
    }
    
    /**
     * Get Total Results
     *
     * @return integer
     */
    function getTotalResults()
    {
        $totalResults = $this->get('searcho:totalResults');
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
            $sortedSearchResults[(int)$result->getDisplayPosition()] = $result;
        }
        ksort($sortedSearchResults);
        return $sortedSearchResults;
    }
}
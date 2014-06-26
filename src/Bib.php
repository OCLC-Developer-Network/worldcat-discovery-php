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
class Bib extends EasyRdf_Resource
{
    public static $serviceUrl = 'https://beta.worldcat.org/discovery';
    private $bib; 
   
    public function __construct($uri, $graph = null){
        parent::__construct($uri, $graph);
        $this->metadata = $this->getResource('schema:about');
    }
    
    function getId()
    {
        return $this->metadata->getUri();
    }
    
    function getDisplayPosition()
    {
        return $this->metadata->get('gr:displayPosition')->getValue();
    }
    /**
     * Get Name
     *
     * @return EasyRDF_Literal
     */
    function getName()
    {
        $name = $this->metadata->get('schema:name');
        return $name;
    }
    
    /**
     * @return EasyRDF_Literal
     */
    function getOCLCNumber()
    {
        $oclcNumber = $this->metadata->get('library:oclcnum');
        return $oclcNumber;
    }
    
    /**
     * 
     * @return WorldCat\Discovery\Person or WorldCat\Discovery\Organization
     */
    function getAuthor(){
        $author = $this->metadata->getResource('schema:author');
        return $author;
    }
    
    /**
     * 
     * @return array
     */
    
    function getContributors(){
        $contributors = $this->metadata->allResources('schema:contributor');
        return $contributors;
    }
    
    /**
     * 
     * @return array
     */
    function getDescriptions()
    {
        $description = $this->metadata->all('schema:description');
        return $description;
    }
    
    /**
     * @return EasyRDF_Literal
     */
    function getLanguage()
    {
        $language = $this->metadata->get('schema:inLanguage');
        return $language;
    }
    
    /**
     * @return EasyRDF_Literal
     */
    function getDatePublished()
    {
        $datePublished = $this->metadata->get('schema:datePublished');
        return $datePublished;
    }
    
    /**
     * @return EasyRDF_Literal
     */
    function getCopyrightYear()
    {
        $copyrightYear = $this->metadata->get('schema:copyrightYear');
        return $copyrightYear;
    }
    
    /**
     * @return \WorldCat\Discovery\Organization
     */
    function getPublisher()
    {
        $publisher = $this->metadata->getResource('schema:publisher');
        return $publisher;   
    }
    
    /**
     * @return EasyRDF_Literal
     */
    function getBookEdition(){
        $bookEdition = $this->metadata->get('schema:bookEdition');
        return $bookEdition;
    }
    
    /**
     * @return EasyRDF_Literal
     */
    function getNumberOfPages(){
        $numberOfPages = $this->metadata->get('schema:numberOfPages');
        return $numberOfPages;
    }
    
    /**
     * @return array
     */
    function getGenres(){
        $genres = $this->metadata->all('schema:genre');
        return $genres;
    }
    
    /**
     * @return string
     */
    function getType(){
        return $this->metadata->type();
    }
    
    /**
     * @return EasyRDF_Resource
     */
    function getWork(){
        return $this->metadata->getResource('schema:exampleOfWork');
    }
    
    /**
     * @return array
     */
    function getManifestations(){
        return $this->metadata->allResources('schema:workExample');
    }
    
    /**
     * @return array
     */
    function getAbout() {
        $about = $this->metadata->allResources('schema:about');
        return $about; 
    }
    
    /**
     * @return array
     */
    function getPlacesOfPublication(){
        $placesOfPublication = $this->metadata->all('library:placeOfPublication');
        return $placesOfPublication;
    }
    
    /**
     * @return array
     */
    function getReviews(){
        $reviews = $this->metadata->all('schema:review');
        return $reviews;
    }
    
    /**
     * return array
     */
    function getAwards()
    {
        $awards =  $this->metadata->all('schema:awards');
        return $awards;
    }
    
    /**
     * @param $id string
     * @param $accessToken OCLC/Auth/AccessToken
     * @package $options array
     * @return WorldCat\Discovery\Bib or \Guzzle\Http\Exception\BadResponseException
     */
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
                'Accept' => 'application/rdf+xml'
            )
        );
        
        $bibURI = Bib::$serviceUrl . '/bib/data/' . $id;
        
        try {
            $response = \Guzzle::get($bibURI, $guzzleOptions);
            $graph = new EasyRdf_Graph();
            $graph->parse($response->getBody(true));
            $bib = $graph->resource('http://www.worldcat.org/title/-/oclc/' . $id);
            return $bib;
        } catch (\Guzzle\Http\Exception\BadResponseException $error) {
            return $error;
        }
    }
    
    /**
     * @param $query string
     * @param $accessToken OCLC/Auth/AccessToken
     * @param $options array All the optional parameters are valid
     * - heldBy comma seperated list which is a limiter to restrict search results to items held by a given institution(s)
     * - notHeldBy comma seperated list which is imiter to restrict search results to items that are not held by a given institution(s).
     * - heldByGroup
     * - heldInCountry
     * - inLanguage
     * - materialType
     * - datePublished
     * - inCatalogLanguage
     * - catalogSource
     * - itemType
     * - itemSubType
     * - peerReview
     * - useFRBRGrouping
     * - facetFields an array of facets to be returned.
     * - startNum integer offset from the beginning of the search result set. defaults to 0
     * - itemsPerPage integer representing the number of items to return in the result set. defaults to 10
     * @return WorldCat\Discovery\SearchResults or \Guzzle\Http\Exception\BadResponseException
     */
    
    public static function search($query, $accessToken, $options = null)
    {
        if (!is_string($query)){
            Throw new \BadMethodCallException('You must pass a valid query');
        } elseif (!is_a($accessToken, '\OCLC\Auth\AccessToken')) {
            Throw new \BadMethodCallException('You must pass a valid OCLC/Auth/AccessToken object');
        }
        
        static::requestSetup();
                
        $guzzleOptions = array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $accessToken->getValue(),
                'Accept' => 'application/rdf+xml'
            )
        );
        
        $bibSearchURI = Bib::$serviceUrl . '/bib/search?' . static::buildParameters($query, $options);
        
        try {
            $searchResponse = \Guzzle::get($bibSearchURI, $guzzleOptions);
            $graph = new EasyRdf_Graph();
            $graph->parse($searchResponse->getBody(true));
            $search = $graph->allOfType('discovery:SearchResults');
            $search = $search[0];
            return $search;
        } catch (\Guzzle\Http\Exception\BadResponseException $error) {
            return $error;
        }
    }
    
    private static function requestSetup()
    {
        EasyRdf_Namespace::set('schema', 'http://schema.org/');
        EasyRdf_Namespace::set('discovery', 'http://worldcat.org/vocab/discovery/');
        EasyRdf_Namespace::set('library', 'http://purl.org/library/');
        EasyRdf_Namespace::set('gr', 'http://purl.org/goodrelations/v1#');
        EasyRdf_Namespace::set('owl', 'http://www.w3.org/2002/07/owl#');
        EasyRdf_Namespace::set('foaf', 'http://xmlns.com/foaf/0.1/');
        EasyRdf_Namespace::set('rdaGr2', 'http://rdvocab.info/ElementsGr2/');
        EasyRdf_TypeMapper::set('http://www.w3.org/2006/gen/ont#InformationResource', 'WorldCat\Discovery\Bib');
        EasyRdf_TypeMapper::set('schema:Country', 'WorldCat\Discovery\Country');
        EasyRdf_TypeMapper::set('schema:Event', 'WorldCat\Discovery\Event');
        EasyRdf_TypeMapper::set('schema:Intangible', 'WorldCat\Discovery\Intangible');
        EasyRdf_TypeMapper::set('schema:Organization', 'WorldCat\Discovery\Organization');
        EasyRdf_TypeMapper::set('schema:Person', 'WorldCat\Discovery\Person');
        EasyRdf_TypeMapper::set('schema:Place', 'WorldCat\Discovery\Place');
        EasyRdf_TypeMapper::set('schema:ProductModel', 'WorldCat\Discovery\ProductModel');
        
        EasyRdf_TypeMapper::set('foaf:Agent', 'WorldCat\Discovery\Organization');
        EasyRdf_TypeMapper::set('discovery:SearchResults', 'WorldCat\Discovery\SearchResults');
        EasyRdf_TypeMapper::set('discovery:FacetItem', 'WorldCat\Discovery\Facet');
        EasyRdf_TypeMapper::set('discovery:FacetItemValue', 'WorldCat\Discovery\FacetValue');
        
        if (!class_exists('Guzzle')) {
            \Guzzle\Http\StaticClient::mount();
        }
    }
    
private static function buildParameters($query, $options = null)
    {
        $parameters = array('q' => $query);
        
        $facetFieldsString = '';
        if (isset($options['facetFields'])){
        
            foreach ($options['facetFields'] as $facetName => $numberOfFacets){
                $facetFieldsString .= '&facetFields=' . $facetName . ':' . $numberOfFacets;
            }
        
        }
        unset($options['facetFields']);
        
        $facetQueriesString = '';
        if (isset($options['facetQueries'])){
        
            foreach ($options['facetQueries'] as $facetName => $numberOfFacets){
                $facetQueriesString .= '&facetQueries=' . $facetName . ':' . $numberOfFacets;
            }
        
        }
        unset($options['facetQueries']);

        if (!empty($options)){
            foreach ($options as $option => $optionValue){
                $parameters[$option] = $optionValue;
            }
        }
        
        $queryString =  http_build_query($parameters) . $facetFieldsString . $facetQueriesString;     
        
        return $queryString;         
    }
    
}
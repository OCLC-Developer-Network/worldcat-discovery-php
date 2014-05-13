<?php
namespace WorldCat\Discovery;

use Guzzle\Http\StaticClient;
use OCLC\Auth\WSKey;
use OCLC\Auth\AccessToken;
use OCLC\User;
use WorldCat\Discovery\Bib;

class SearchResultsTest extends \PHPUnit_Framework_TestCase
{

    function setUp()
    {
        $options = array(
            'authenticatingInstitutionId' => 128807,
            'contextInstitutionId' => 128807,
            'scope' => array('WorldCatDiscoveryAPI')
        );
        $this->mockAccessToken = $this->getMock('OCLC\Auth\AccessToken', array('getValue'), array('client_credentials', $options));
        $this->mockAccessToken->expects($this->any())
                    ->method('getValue')
                    ->will($this->returnValue('tk_12345'));
    }
    
    /** can parse set of Bibs from a Search Result with Facets*/
    
    function testSearchFacets(){
        $query = 'cats';
        $facets = array('author' => 10, 'inLanguage' => 10);
        $mock = __DIR__ . '/mocks/bibSearchFacets.txt';
        $search = Bib::Search($query, $this->mockAccessToken, array('mockFilePath' => $mock, 'facets' => $facets));
        
        $this->assertInstanceOf('WorldCat\Discovery\SearchResults', $search);
        $this->assertEquals('0', $search->getStartIndex());
        $this->assertEquals('10', $search->getItemsPerPage());
        $this->assertInternalType('integer', $search->getTotalResults());
        $this->assertEquals('10', count($search->getSearchResults()));
        $results = $search->getSearchResults();
        $i = 0;
        foreach ($search->getSearchResults() as $searchResult){
            $this->assertInstanceOf('WorldCat\Discovery\Bib', $searchResult);
            $i++;
            $this->assertEquals($i, $searchResult->getDisplayPosition());
        }
        $facetList = $search->getFacetList();
        $this->assertNotEmpty($facetList);
        return $facetList;
    }
    
    /**
     * 
     * @depends testSearchFacets
     */
    function testFacetList($facetList){
        $previousCount = 0;
        foreach ($facetList as $facet){
            $this->assertInstanceOf('WorldCat\Discovery\Facet', $facet);
            $this->assertNotEmpty($facet->getName());
            $this->assertNotEmpty($facet->getCount());
            $this->assertGreaterThanOrEqual($previousCount, $facet->getCount());
            $previousCount = $facet->getCount();
        }
    }
}
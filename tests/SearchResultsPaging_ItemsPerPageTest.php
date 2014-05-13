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
    
    /** can parse set of Bibs from a Search Result where the start index is not 0*/
    
    function testSearchStartIndex10(){
        $query = 'cats';
        $mock = __DIR__ . '/mocks/bibSearchStartNum10.txt';
        $search = Bib::Search($query, $this->mockAccessToken, array('mockFilePath' => $mock, 'startNum' => 10));
        
        $this->assertInstanceOf('WorldCat\Discovery\SearchResults', $search);
        $this->assertEquals('10', $search->getStartIndex());
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
    }
    
    /** can parse set of Bibs from a Search Result where itemsPerPage is 5*/
    
    function testSearchItemsPerPage5(){
        $query = 'cats';
        $mock = __DIR__ . '/mocks/bibSearchItemsPerPage5.txt';
        $search = Bib::Search($query, $this->mockAccessToken, array('mockFilePath' => $mock, 'itemsPerPage' => 5));
    
        $this->assertInstanceOf('WorldCat\Discovery\SearchResults', $search);
        $this->assertEquals('0', $search->getStartIndex());
        $this->assertEquals('5', $search->getItemsPerPage());
        $this->assertInternalType('integer', $search->getTotalResults());
        $this->assertEquals('5', count($search->getSearchResults()));
        $results = $search->getSearchResults();
        $i = 0;
        foreach ($search->getSearchResults() as $searchResult){
            $this->assertInstanceOf('WorldCat\Discovery\Bib', $searchResult);
            $i++;
            $this->assertEquals($i, $searchResult->getDisplayPosition());
        }
    }
}
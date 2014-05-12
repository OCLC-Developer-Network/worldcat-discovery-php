<?php
use Guzzle\Http\StaticClient;
use OCLC\Auth\WSKey;
use OCLC\Auth\AccessToken;
use OCLC\User;
use OCLC\WorldCatDiscovery\Bib;

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

    /**
     * 
     */
    function testSearchByOCLCNumber(){
        $query = 'no:7977212';
        $mock = __DIR__ . '/mocks/bibSearchByOclcNumberSuccess.txt';
        $search = Bib::Search($query, $this->mockAccessToken, array('mockFilePath' => $mock));
        $this->assertInstanceOf('OCLC\WorldCatDiscovery\SearchResults', $search);
        $i = 0;
        foreach ($search->getSearchResults() as $searchResult){
            $this->assertInstanceOf('OCLC\WorldCatDiscovery\Bib', $searchResult);
            $i++;
            $this->assertEquals($i, $searchResult->getDisplayPosition());
        }
    }
    
    /** can parse set of Bibs from a Search Result */
    
    function testSearchByKeyword(){
        $query = 'kw:cats';
        $mock = __DIR__ . '/mocks/bibSearchSuccess.txt';
        $search = Bib::Search($query, $this->mockAccessToken, array('mockFilePath' => $mock));
        
        $this->assertInstanceOf('OCLC\WorldCatDiscovery\SearchResults', $search);
        $this->assertEquals('0', $search->getStartIndex());
        $this->assertEquals('10', $search->getItemsPerPage());
        $this->assertInternalType('integer', $search->getTotalResults());
        $this->assertEquals('10', count($search->getSearchResults()));
        $results = $search->getSearchResults();
        $i = 0;
        foreach ($search->getSearchResults() as $searchResult){
            $this->assertInstanceOf('OCLC\WorldCatDiscovery\Bib', $searchResult);
            $i++;
            $this->assertEquals($i, $searchResult->getDisplayPosition());
        }
    }

    /**
     * @expectedException BadMethodCallException
     * @expectedExceptionMessage You must pass a valid query
     */
    function testQueryNotString()
    {
        $bib = Bib::search(1, $this->mockAccessToken);
    }
    
    /**
     * @expectedException BadMethodCallException
     * @expectedExceptionMessage You must pass a valid OCLC/Auth/AccessToken object
     */
    function testAccessTokenNotAccessTokenObject()
    {
        $this->bib = Bib::search('kw:cats', 'NotAnAccessToken');
    }
}
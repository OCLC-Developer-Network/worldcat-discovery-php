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

    /**
     * @vcr bibSearchByOclcNumber
     */
    function testSearchByOCLCNumber(){
        $query = 'no:7977212';
        $search = Bib::Search($query, $this->mockAccessToken);
        $this->assertInstanceOf('WorldCat\Discovery\BibSearchResults', $search);
        $i = $search->getStartIndex();
        foreach ($search->getSearchResults() as $searchResult){
            $this->assertFalse(get_class($searchResult) == 'EasyRdf_Resource');
            $i++;
            $this->assertEquals($i, $searchResult->getDisplayPosition());
        }
    }
    
    /** 
     * @vcr bibSearchSuccessKeyword
     * can parse set of Bibs from a Search Result */
    
    function testSearchByKeyword(){
        $query = 'cats';
        $search = Bib::Search($query, $this->mockAccessToken);
        
        $this->assertInstanceOf('WorldCat\Discovery\BibSearchResults', $search);
        $this->assertEquals('0', $search->getStartIndex());
        $this->assertEquals('10', $search->getItemsPerPage());
        $this->assertInternalType('integer', $search->getTotalResults());
        $this->assertEquals('10', count($search->getSearchResults()));
        $results = $search->getSearchResults();
        $i = $search->getStartIndex();
        foreach ($search->getSearchResults() as $searchResult){
            $this->assertFalse(get_class($searchResult) == 'EasyRdf_Resource');
            $i++;
            $this->assertEquals($i, $searchResult->getDisplayPosition());
        }
    }
    
    /**
     * @vcr bibSearchMultiField
     * can parse set of Bibs from a Search Result */
    
    function testSearchByMultipleFields(){
    	$query = 'name:hunger+games+AND+creator:collins';
    	$search = Bib::Search($query, $this->mockAccessToken);
    
    	$this->assertInstanceOf('WorldCat\Discovery\BibSearchResults', $search);
    	$this->assertEquals('0', $search->getStartIndex());
    	$this->assertEquals('10', $search->getItemsPerPage());
    	$this->assertInternalType('integer', $search->getTotalResults());
    	$this->assertEquals('10', count($search->getSearchResults()));
    	$results = $search->getSearchResults();
    	$i = $search->getStartIndex();
    	foreach ($search->getSearchResults() as $searchResult){
    		$this->assertFalse(get_class($searchResult) == 'EasyRdf_Resource');
    		$i++;
    		$this->assertEquals($i, $searchResult->getDisplayPosition());
    	}
    }
    
    /**
     * @vcr bibSearchTypeMapIssue
     * can parse set of Bibs from a Search Result */
    
    function testSearchTypeMapIssues(){
    	$query = 'world aids day';
    	$search = Bib::Search($query, $this->mockAccessToken);
    
    	$this->assertInstanceOf('WorldCat\Discovery\BibSearchResults', $search);
    	$this->assertEquals('0', $search->getStartIndex());
    	$this->assertEquals('10', $search->getItemsPerPage());
    	$this->assertInternalType('integer', $search->getTotalResults());
    	$this->assertEquals('10', count($search->getSearchResults()));
    	$results = $search->getSearchResults();
    	$i = $search->getStartIndex();
    	foreach ($search->getSearchResults() as $searchResult){
    		$this->assertFalse(get_class($searchResult) == 'EasyRdf_Resource');
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
        $this->bib = Bib::search('cats', 'NotAnAccessToken');
    }
    
}
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
use WorldCat\Discovery\Offer;

class OfferTest extends \PHPUnit_Framework_TestCase
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
     * Find Offers by OCLC NUmber
     */
    function testFindOfferByOclcNumber(){
        \VCR\VCR::insertCassette('offerSuccess');
        $options = array('heldBy' => 'GZM,GZN,GZO');
        $offerSet = Offer::findByOclcNumber(30780581, $this->mockAccessToken, $options);
        \VCR\VCR::eject();
        $this->assertInstanceOf('WorldCat\Discovery\OfferSet', $offerSet);
        //$this->assertEquals('0', $offerSet->getStartIndex()); // broken as of 6/30/2014
        $this->assertEquals('10', $offerSet->getItemsPerPage());
        $this->assertInternalType('integer', $offerSet->getTotalResults());
        //$this->assertEquals('2', count($offerSet->getOffers())); // need new mock
        
        $offers = $offerSet->getOffers();
        return $offers;
    }

    /**
     * can parse Offers
     * @depends testFindOfferByOclcNumber
     */
    function testParseOffers($offers)
    {
        foreach ($offers as $offer){
            $this->assertInstanceOf('WorldCat\Discovery\SomeProducts', $offer->getItemOffered());
            $this->assertNotNull($offer->getPrice());
            $this->assertInstanceOf('WorldCat\Discovery\Library', $offer->getSeller());
            $this->assertFalse(get_class($offer->getItemOffered()->getCreativeWork()) == 'EasyRdf_Resource');
            $this->assertInstanceOf('WorldCat\Discovery\Collection', $offer->getItemOffered()->getCollection());
        
        }
        return $offers[1]->getItemOffered()->getCollection();
    }
    
    /**
     * @depends testParseOffers
     */
    function testParseCollection($collection){
        $this->assertNotEmpty($collection->getOclcSymbol());
        $this->assertInstanceOf('WorldCat\Discovery\Library', $collection->getManagedBy());
        
        return $collection->getManagedBy();
    }
    
    /**
     * @depends testParseCollection
     */
    function testParseLibrary($library){
        $this->assertNotEmpty($library->getName());
    }
    
    /**
     * Find Offers by OCLC Number heldByGroup
     */
    function testFindOfferByOclcNumberHeldByGroup(){
        \VCR\VCR::insertCassette('offerHeldByGroupSuccess');
        $options = array('heldByGroup' => 'OHLL');
        $offerSet = Offer::findByOclcNumber(30780581, $this->mockAccessToken, $options);
        \VCR\VCR::eject();
        $this->assertInstanceOf('WorldCat\Discovery\OfferSet', $offerSet);
        //$this->assertEquals('0', $offerSet->getStartIndex()); // broken as of 6/30/2014
        $this->assertEquals('10', $offerSet->getItemsPerPage());
        $this->assertInternalType('integer', $offerSet->getTotalResults());
        $this->assertEquals('10', count($offerSet->getOffers()));
    }
    
    /**
     * Find Offers by OCLC Number HeldInCountry
     */
    function testFindOfferByOclcNumberHeldInCountry(){
        \VCR\VCR::insertCassette('offerHeldInCountrySuccess');
        $options = array('heldInCountry' => 'CA');
        $offerSet = Offer::findByOclcNumber(30780581, $this->mockAccessToken, $options);
        \VCR\VCR::eject();
        $this->assertInstanceOf('WorldCat\Discovery\OfferSet', $offerSet);
        //$this->assertEquals('0', $offerSet->getStartIndex()); // broken as of 6/30/2014 
        $this->assertEquals('10', $offerSet->getItemsPerPage());
        $this->assertInternalType('integer', $offerSet->getTotalResults());
        $this->assertEquals('10', count($offerSet->getOffers()));
    }
    
    /**
     * Find Offers by OCLC Number useFRBRGrouping
     */
    function testFindOfferByOclcNumberUseFRBRGrouping(){
        \VCR\VCR::insertCassette('offerUseFRBRGroupingSuccess');
        $options = array('useFRBRGrouping' => 'false');
        $offerSet = Offer::findByOclcNumber(30780581, $this->mockAccessToken, $options);
        \VCR\VCR::eject();
        $this->assertInstanceOf('WorldCat\Discovery\OfferSet', $offerSet);
        //$this->assertEquals('0', $offerSet->getStartIndex()); // broken as of 6/30/2014
        $this->assertEquals('10', $offerSet->getItemsPerPage());
        $this->assertInternalType('integer', $offerSet->getTotalResults());
        $this->assertEquals('10', count($offerSet->getOffers()));
    }

    
    /**
     * Find Offers by OCLC Number itemStartPage
     */
    function testFindOfferByOclcNumberItemStartPage(){
        \VCR\VCR::insertCassette('offerStartPageSuccess');
        $options = array('heldInCountry' => 'CA', 'startNum' => '10');
        $offerSet = Offer::findByOclcNumber(30780581, $this->mockAccessToken, $options);
        \VCR\VCR::eject();
        $this->assertInstanceOf('WorldCat\Discovery\OfferSet', $offerSet);
        //$this->assertEquals('10', $offerSet->getStartIndex()); //need new mock
        $this->assertEquals('10', $offerSet->getItemsPerPage());
        $this->assertInternalType('integer', $offerSet->getTotalResults());
        $this->assertEquals('10', count($offerSet->getOffers()));
    }
    
    /**
     * Find Offers by OCLC Number itemsPerPage
     */
    function testFindOfferByOclcNumberItemsPerPage(){
        \VCR\VCR::insertCassette('offerItemsPerPageSuccess');
        $options = array('heldInCountry' => 'CA', 'itemsPerPage' => '2');
        $offerSet = Offer::findByOclcNumber(30780581, $this->mockAccessToken, $options);
        \VCR\VCR::eject();
        $this->assertInstanceOf('WorldCat\Discovery\OfferSet', $offerSet);
        $this->assertEquals('0', $offerSet->getStartIndex());
        $this->assertEquals('2', $offerSet->getItemsPerPage());
        $this->assertInternalType('integer', $offerSet->getTotalResults());
        $this->assertEquals('2', count($offerSet->getOffers()));
    }
    
    /**
     * Find Offers by OCLC Number & geographic coordinates
     */
    function testFindOfferByOclcNumberGeoCoordinates(){
        //need new mock
        \VCR\VCR::insertCassette('offerGeoCoordinatesSuccess');
        $options = array(
            'heldBy' => 'GZM,GZN,GZO',
            'lat' => '45.032916',
            'lon' => '-84.668979',
            'unit' => 'M',
            'distance' => '60'
        );
        $offerSet = Offer::findByOclcNumber(30780581, $this->mockAccessToken, $options);
        \VCR\VCR::eject();
        $this->assertInstanceOf('WorldCat\Discovery\OfferSet', $offerSet);
        $this->assertEquals('0', $offerSet->getStartIndex());
        $this->assertEquals('10', $offerSet->getItemsPerPage());
        $this->assertInternalType('integer', $offerSet->getTotalResults());
        $this->assertEquals('2', count($offerSet->getOffers()));
    
        $offers = $offerSet->getOffers();
        return $offers;
    }

    /**
     * @expectedException BadMethodCallException
     * @expectedExceptionMessage You must pass a valid ID
     */
    function testIDNotInteger()
    {
        $options = array('heldBy' => 'GZM,GZN,GZO');
        $offerSet = Offer::findByOclcNumber('string', 'NotAnAccessToken', $options);
    }

    /**
     * @expectedException BadMethodCallException
     * @expectedExceptionMessage You must pass a valid OCLC/Auth/AccessToken object
     */
    function testAccessTokenNotAccessTokenObject()
    {
        $options = array('heldBy' => 'GZM,GZN,GZO');
        $offerSet = Offer::findByOclcNumber(30780581, 'NotAnAccessToken', $options);
    }

    /** Invalid Access Token **/
    function testFailureInvalidAccessToken()
    {
        \VCR\VCR::insertCassette('offerFailureInvalidAccessToken');
        $options = array('heldBy' => 'GZM,GZN,GZO');
        $offerSet = Offer::findByOclcNumber(30780581, $this->mockAccessToken, $options);
        \VCR\VCR::eject();
        $this->assertInstanceOf('\Guzzle\Http\Exception\BadResponseException', $offerSet);
    }

    /** Expired Access Token **/
    function testFailureExpiredAccessToken()
    {
        \VCR\VCR::insertCassette('offerFailureExpiredAccessToken');
        $options = array('heldBy' => 'GZM,GZN,GZO');
        $offerSet = Offer::findByOclcNumber(30780581, $this->mockAccessToken, $options);
        \VCR\VCR::eject();
        $this->assertInstanceOf('\Guzzle\Http\Exception\BadResponseException', $offerSet);
    }
}

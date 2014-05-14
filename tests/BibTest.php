<?php
namespace WorldCat\Discovery;

use Guzzle\Http\StaticClient;
use OCLC\Auth\WSKey;
use OCLC\Auth\AccessToken;
use WorldCat\Discovery\Bib;

class BibTest extends \PHPUnit_Framework_TestCase
{

    function setUp()
    {   global $config;
        
        $this->config = $config;
        
        $options = array(
            'authenticatingInstitutionId' => 128807,
            'contextInstitutionId' => 128807,
            'scope' => array('WorldCatDiscoveryAPI')
        );
        $this->mockAccessToken = $this->getMock('OCLC\Auth\AccessToken', array('getValue'), array('client_credentials', $options));
        $this->mockAccessToken->expects($this->any())
                    ->method('getValue')
                    ->will($this->returnValue('tk_12345'));

        $mock = __DIR__ . '/mocks/bibSuccess.txt';
        $this->bib = Bib::find(7977212, $this->mockAccessToken, array('mockFilePath' => $mock));
         
    }

    /**
     *
     */
    function testGetBib(){
        $this->assertInstanceOf('WorldCat\Discovery\Bib', $this->bib);
    }

    /**
     * can parse Single Bibs Literal values
     * @depends testGetBib
     */
    function testParseLiterals()
    {
        $this->assertNotEmpty($this->bib->getId());
        $this->assertNotEmpty($this->bib->getName());
        $this->assertNotEmpty($this->bib->getOCLCNumber());
        $this->assertNotEmpty($this->bib->getDescriptions());
        $this->assertNotEmpty($this->bib->getLanguage());
        $this->assertNotEmpty($this->bib->getDatePublished());
        $this->assertNotEmpty($this->bib->getCopyrightYear());
        $this->assertNotEmpty($this->bib->getBookEdition());
        $this->assertNotEmpty($this->bib->getNumberOfPages());
        $this->assertNotEmpty($this->bib->getGenres());
    }

    /**
     * can parse Single Bibs Resources
     * @depends testGetBib
     */
    function testParseResources(){
        $this->assertThat($this->bib->getAuthor(), $this->logicalOr(
            $this->isInstanceOf('WorldCat\Discovery\Person'),
            $this->isInstanceOf('WorldCat\Discovery\Organization')
        ));

        foreach ($this->bib->getContributors() as $contributor){
            $this->assertThat($contributor, $this->logicalOr(
                $this->isInstanceOf('WorldCat\Discovery\Person'),
                $this->isInstanceOf('WorldCat\Discovery\Organization')
            ));
        }

        $this->assertInstanceOf('WorldCat\Discovery\Organization', $this->bib->getPublisher());

        $this->assertInstanceOf('EasyRdf_Resource', $this->bib->getWork());

        foreach ($this->bib->getManifestations() as $manifestation){
            $this->assertInstanceOf('WorldCat\Discovery\ProductModel', $manifestation);
        }

        foreach ($this->bib->getAbout() as $about){
            $this->assertInstanceOf('WorldCat\Discovery\Intangible', $about);
        }

        foreach ($this->bib->getPlacesOfPublication() as $place){
            $this->assertThat($place, $this->logicalOr(
                $this->isInstanceOf('WorldCat\Discovery\Place'),
                $this->isInstanceOf('WorldCat\Discovery\Country')
            ));
        }
        
        foreach ($this->bib->getReviews() as $review){
            $this->assertInstanceOf('WorldCat\Discovery\Review', $review);
        }
        
    }
    
    /** Test for awards in the Bib - 41266045 **/

    function testParseBibWithAward()
    {
        $bib = Bib::find(41266045, $this->mockAccessToken, array('mockFilePath' => __DIR__ . '/mocks/bibWithAwards.txt'));
        $this->assertNotEmpty($bib->getAwards());
    }
    
    /**
     * @expectedException BadMethodCallException
     * @expectedExceptionMessage You must pass a valid ID
     */
    function testIDNotInteger()
    {
        $bib = Bib::find('string', $this->mockAccessToken);
    }
    
    /**
     * @expectedException BadMethodCallException
     * @expectedExceptionMessage You must pass a valid OCLC/Auth/AccessToken object
     */
    function testAccessTokenNotAccessTokenObject()
    {
        $this->bib = Bib::find(1, 'NotAnAccessToken');
    }
    
    /** Invalid Access Token **/
    function testFailureInvalidAccessToken()
    {
        $bib = Bib::find(41266045, $this->mockAccessToken, array('mockFilePath' => __DIR__ . '/mocks/bibFailureInvalidAccessToken.txt'));
        $this->assertInstanceOf('WorldCat\Guzzle\Http\Exception\BadResponseException', $bib);
    }
    
    /** Expired Access Token **/
    function testFailureExpiredAccessToken()
    {
        $bib = Bib::find(41266045, $this->mockAccessToken, array('mockFilePath' => __DIR__ . '/mocks/bibFailureExpiredAccessToken.txt'));
        $this->assertInstanceOf('\Guzzle\Http\Exception\BadResponseException', $bib);
    }
}

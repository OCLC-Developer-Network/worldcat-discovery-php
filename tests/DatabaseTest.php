<?php
namespace WorldCat\Discovery;

use Guzzle\Http\StaticClient;
use OCLC\Auth\WSKey;
use OCLC\Auth\AccessToken;
use WorldCat\Discovery\Bib;

class DatabaseTest extends \PHPUnit_Framework_TestCase
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
    }

    /**
     *Get a single database resource
     */
    function testGetDatabase(){
        $mock = __DIR__ . '/mocks/databaseSuccess.txt';
        $database = Database::find(638, $this->mockAccessToken, array('mockFilePath' => $mock));
        $this->assertTrue($database->isSuccessful());
    }
    
    /**
     * List database resources
     */
    function testlistDatabases(){
        $mock = __DIR__ . '/mocks/databaseListSuccess.txt';
        $databaseList = Database::getList($this->mockAccessToken, array('mockFilePath' => $mock));
        $this->assertTrue($databaseList->isSuccessful());
    }
    
    /**
     * @expectedException BadMethodCallException
     * @expectedExceptionMessage You must pass a valid ID
     */
    function testIDNotInteger()
    {
        $database = Database::find('string', $this->mockAccessToken);
    }
    
    /**
     * @expectedException BadMethodCallException
     * @expectedExceptionMessage You must pass a valid OCLC/Auth/AccessToken object
     */
    function tesccessTokenNotAccessTokenObject()
    {
        $database = Database::find(638, 'NotAnAccessToken');
    }
    
    /**
     * @expectedException BadMethodCallException
     * @expectedExceptionMessage You must pass a valid OCLC/Auth/AccessToken object
     */
    function testListDatabasesAccessTokenNotAccessTokenObject()
    {
        $databaseList = Database::getList('NotAnAccessToken');
    }
}

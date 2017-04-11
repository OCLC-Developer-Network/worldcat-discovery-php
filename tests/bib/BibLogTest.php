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

use OCLC\Auth\WSKey;
use OCLC\Auth\AccessToken;
use WorldCat\Discovery\Bib;

use Monolog\Logger;
use Monolog\Handler\TestHandler;

class BibLogTest extends \PHPUnit_Framework_TestCase
{

    function setUp()
    {     
        $options = array(
            'authenticatingInstitutionId' => 128807,
            'contextInstitutionId' => 128807,
            'scope' => array('WorldCatDiscoveryAPI')
        );
        $this->mockAccessToken = $this->getMockBuilder(AccessToken::class)
        ->setConstructorArgs(array('client_credentials', $options))
        ->getMock();
        
        $this->mockAccessToken->expects($this->any())
        ->method('getValue')
        ->will($this->returnValue('tk_12345'));
    }

    /**
     *@vcr bibSuccess
     */
    function testLoggerSuccess(){
        $logger = new Logger('testLogger');
        $handler = new TestHandler;
        $logger->pushHandler($handler);
        $options = array(
            'logger' => $logger
        );
        $bib = Bib::find(7977212, $this->mockAccessToken, $options);
        
        $records = $handler->getRecords();
        $this->assertContains('/discovery/bib/data/7977212', $records[0]['message']);
        
    }
    
    /**
     *@vcr bibSuccess
     */
    function testLoggerSuccessSpecificFormat(){
    	$logger = new Logger('testLogger');
    	$handler = new TestHandler;
    	$logger->pushHandler($handler);
    	$options = array(
    			'logger' => $logger,
    			'log_format' => 'Request - {method} - {uri} - {code}'
    	);
    	$bib = Bib::find(7977212, $this->mockAccessToken, $options);
    	
    	$records = $handler->getRecords();
    	$this->assertContains('Request - GET - https://beta.worldcat.org/discovery/bib/data/7977212 - 200', $records[0]['message']);
    	
    }
    
    /**
     * @expectedException BadMethodCallException
     * @expectedExceptionMessage The logger must be an object that uses a valid Psr\Log\LoggerInterface interface
     */
    function testLoggerNotValid()
    {
        $options = array(
            'logger' => 'lala'
        );
        $bib = Bib::find('string', $this->mockAccessToken, $options);
    }
}

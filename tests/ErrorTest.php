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
use WorldCat\Discovery\Bib;
use WorldCat\Discovery\Error;

class ErrorTest extends \PHPUnit_Framework_TestCase
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
     * Invalid Access Token
     */
    function testErrorInvalidAccessToken(){
        \VCR\VCR::insertCassette('bibFailureInvalidAccessToken');
        $error = Bib::find(7977212, $this->mockAccessToken);
        \VCR\VCR::eject();
        $this->assertInstanceOf('WorldCat\Discovery\Error', $error);
        $this->assertNotEmpty($error->getErrorType());
        $this->assertNotEmpty($error->getErrorCode());
        $this->assertNotEmpty($error->getErrorMessage());
    }
    
    /**
     * Database not enabled
     */
    function testErrorDatabaseNotEnabled(){
        \VCR\VCR::insertCassette('bibFailureDatabaseNoEnabled');
        $error = Bib::find(7977212, $this->mockAccessToken,  array('dbIds' => 2663));
        \VCR\VCR::eject();
        $this->assertInstanceOf('WorldCat\Discovery\Error', $error);
        $this->assertNotEmpty($error->getErrorType());
        $this->assertNotEmpty($error->getErrorCode());
        $this->assertNotEmpty($error->getErrorMessage());
    }
}

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

class BibTest extends \PHPUnit_Framework_TestCase
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
    function testGetBib(){
        $bib = Bib::find(7977212, $this->mockAccessToken);
        $this->assertInstanceOf('WorldCat\Discovery\Book', $bib);
        return $bib;
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
        $bib = Bib::find(1, 'NotAnAccessToken');
    }
    
    /**
     * @expectedException BadMethodCallException
     * @expectedExceptionMessage Options must be a valid array
     */
    function testOptionsNotAnArray()
    {
        $bib = Bib::find(1, $this->mockAccessToken, 'lala');
    }
    
    /**
     * @expectedException BadMethodCallException
     * @expectedExceptionMessage Options must be a valid array
     */
    function testOptionsEmptyArray()
    {
        $bib = Bib::find(1, $this->mockAccessToken, array());
    }
}

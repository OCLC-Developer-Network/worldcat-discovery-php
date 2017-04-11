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

class ThesisTest extends \PHPUnit_Framework_TestCase
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
     * @vcr thesisSuccess
     */
    function testGetBib(){
        $bib = Bib::find(56394544, $this->mockAccessToken);
        $this->assertInstanceOf('WorldCat\Discovery\Book', $bib);
        return $bib;
    }

    /**
     * can parse Single Bibs Literal values
     * @depends testGetBib
     */
    function testParseLiterals($bib)
    {
        $this->assertNotEmpty($bib->getId());
        $this->assertNotEmpty($bib->getName());
        $this->assertNotEmpty($bib->getOCLCNumber());
        $this->assertNotEmpty($bib->getLanguage());
        $this->assertNotEmpty($bib->getDatePublished());
    }

    /**
     * can parse Single Bibs Resources
     * @depends testGetBib
     */
    function testParseResources($bib){
        $this->assertThat($bib->getAuthor(), $this->logicalOr(
            $this->isInstanceOf('WorldCat\Discovery\Person'),
            $this->isInstanceOf('WorldCat\Discovery\Organization')
        ));

        foreach ($bib->getContributors() as $contributor){
            $this->assertThat($contributor, $this->logicalOr(
                $this->isInstanceOf('WorldCat\Discovery\Person'),
                $this->isInstanceOf('WorldCat\Discovery\Organization')
            ));
        }

        $this->assertInstanceOf('EasyRdf_Resource', $bib->getWork());

        foreach ($bib->getAbout() as $about){
            $this->assertInstanceOf('WorldCat\Discovery\Intangible', $about);
        }
        
    }
}

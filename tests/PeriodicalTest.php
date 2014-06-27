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

class PeriodicalTest extends \PHPUnit_Framework_TestCase
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
    function testGetBib(){
        \VCR\VCR::insertCassette('PeriodicalSuccess');
        $bib = Bib::find(2243594, $this->mockAccessToken);
        \VCR\VCR::eject();
        $this->assertInstanceOf('WorldCat\Discovery\Periodical', $bib);
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
        $this->assertNotEmpty($bib->getIssn());
    }

    /**
     * can parse Single Bibs Resources
     * @depends testGetBib
     */
    function testParseResources($bib){
        
        $this->assertInstanceOf('WorldCat\Discovery\Organization', $bib->getPublisher());

        $this->assertInstanceOf('EasyRdf_Resource', $bib->getWork());

        foreach ($bib->getAbout() as $about){
            $this->assertInstanceOf('WorldCat\Discovery\Intangible', $about);
        }

        foreach ($bib->getPlacesOfPublication() as $place){
            $this->assertThat($place, $this->logicalOr(
                $this->isInstanceOf('WorldCat\Discovery\Place'),
                $this->isInstanceOf('WorldCat\Discovery\Country')
            ));
        }
        
        $this->assertInstanceOf('EasyRdf_Resource', $bib->getIsLike());
        
    }
}

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

/**
* A class that represents a Geographic MADS Authority
* 
* http://id.loc.gov/authorities/names/n82068148.rdf
*/
class AuthorityGeographic extends Authority
{
    
    function getVariants()
    {
        $variants = $this->get('madsrdf:hasVariant');
        if (empty($variants)){
            $this->load();
            $variants = $this->get('madsrdf:hasVariant');
        }
        return $variants;
    }
}
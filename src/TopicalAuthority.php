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
* A class that represents a MADS Topical Authority
* http://id.loc.gov/authorities/subjects/sh85149296
*
*/
class AuthorityTopical extends Authority
{
    
    function getBroaderAuthorities()
    {
        $broaderAuthorities = $this->get('madsrdf:hasBroaderAuthority');
        if (empty($broaderAuthorities)){
            $this->load();
            $broaderAuthorities = $this->get('madsrdf:hasBroaderAuthority');
        }
        return $broaderAuthorities;
    }
    
    function getNarrowerAuthorities()
    {
        $narrowerAuthorities = $this->get('madsrdf:hasNarrowerAuthority');
        if (empty($narrowerAuthorities)){
            $this->load();
            $narrowerAuthorities = $this->get('madsrdf:hasNarrowerAuthority');
        }
        return $narrowerAuthorities;
    }
    
    function getReciprocalAuthorities()
    {
        $reciprocalAuthorities = $this->get('madsrdf:hasReciprocalAuthority');
        if (empty($reciprocalAuthorities)){
            $this->load();
            $reciprocalAuthorities = $this->get('madsrdf:hasReciprocalAuthority');
        }
        return $reciprocalAuthorities;
    }
    
    function getCloseExternalAuthorities()
    {
        $closeExternalAuthorities = $this->get('madsrdf:hasCloseExternalAuthority');
        if (empty($closeExternalAuthorities)){
            $this->load();
            $closeExternalAuthorities = $this->get('madsrdf:hasCloseExternalAuthority');
        }
        return $closeExternalAuthorities;
    }
    
}
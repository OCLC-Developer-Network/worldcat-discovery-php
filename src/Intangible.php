<?php
namespace OCLC\WorldCatDiscovery;

use \EasyRdf_Resource;
use \EasyRdf_Format;

/**
 * A class that represents an Intangible in Schema.org
 *
 */
class Intangible extends Thing
{
    /**
     * Get Name
     *
     * @return string
     */
    function getName()
    {
        if (strstr($this->getUri(), 'dewey.info')) {
            $name = $this->getUri();
        } elseif ($this->get('schema:name')){
            $name = $this->get('schema:name');
        } else {
            $this->load();
            $name = $this->get('schema:name');
        }
        return $name;
    }
    
    function getSameAs(){
        $sameAs = $this->getResource('schema:sameAs');
        return $sameAs;
    }
}

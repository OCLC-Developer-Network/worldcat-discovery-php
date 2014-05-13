<?php
namespace WorldCat\Discovery;

use \EasyRdf_Resource;
use \EasyRdf_Format;
use \EasyRdf_Namespace;

/**
 * A class that represents an Intangible in Schema.org
 *
 */
class Country extends EasyRdf_Resource
{
    
    /**
     * Get Name
     *
     * @return string
     */
    function getName()
    {   
        EasyRdf_Namespace::set('madsrdf', 'http://www.loc.gov/mads/rdf/v1#');
        $this->load();
        return $this->get('madsrdf:authoritativeLabel')->getValue();
    }
}
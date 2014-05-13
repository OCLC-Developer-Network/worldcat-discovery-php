<?php
namespace WorldCat\Discovery;

use \EasyRdf_Resource;
use \EasyRdf_Format;

/**
 * A class that represents an Intangible in Schema.org
 *
 */
class Thing extends EasyRdf_Resource
{
    
    /**
     * Get Name
     *
     * @return string
     */
    function getName()
    {
        $name = $this->get('schema:name');
        return $name;
    }
}

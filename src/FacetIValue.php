<?php
namespace WorldCat\Discovery;

use \EasyRdf_Resource;
use \EasyRdf_Format;

/**
 * A class that represents a Facet Value in WorldCat
 *
 */
class FacetValue extends EasyRdf_Resource
{
    /**
     * Get name of facet value
     * return string
     */

    function getName(){
        return $this->get('schema:Name')->getValue();
    }

    /**
     * Get count of facet value
     *
     * @return string
     */
    function getCount(){
        return $this->get('searcho:count')->getValue();
    }
}
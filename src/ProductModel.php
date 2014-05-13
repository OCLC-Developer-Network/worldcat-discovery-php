<?php
namespace WorldCat\Discovery;

use \EasyRdf_Resource;
use \EasyRdf_Format;

/**
 * A class that represents a Product Model in WorldCat
 *
 */
class ProductModel extends EasyRdf_Resource
{
    
    /**
     * Get ISBN
     *
     * @return string
     */
    function getISBN()
    {
        return $this->get('schema:isbn');
    }
    
    /**
     * Get Book Format
     * @return string
     */
    function getBookFormat(){
        return $this->get('schema:bookFormat');
    }
    
    /**
     * Get Description
     */
    
    function getDescription(){
        return $this->get('schema:description');
    }
    
}

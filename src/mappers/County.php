<?php
namespace Mapper;

use 
    Spot\Mapper,
    Symfony\Component\HttpFoundation\Request
    ;

class County extends Mapper
{
    use \Traits\GenericMapper;

    function getForSelect() {

        $results = $this->all()->order(['name' => 'ASC'])->toArray();

        $ret = [];
        foreach( $results as $r ) {
            $ret[$r['id']] = $r['name'];
        }

        return $ret;

    }
}

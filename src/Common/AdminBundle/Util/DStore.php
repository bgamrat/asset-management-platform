<?php

namespace Common\AdminBundle\Util;

use Symfony\Component\HttpFoundation\Request;

class DStore
{
    const ASC = 'asc';
    const DESC = 'desc';

    static $DIRMAP = ['-' => 'desc', '+' => 'asc'];
    
    const OP = 'op';
    const FIELD = 'field';
    const VALUE = 'value';  
    const LIKE = 'LIKE';
    const GT = '>';

    public function gridParams( Request $request, $default = null )
    {
        $offset = null;
        $limit = null;
        if( $request->headers->has( 'X-Range' ) )
        {
            // TODO: Add validation
            $range = $request->headers->get( 'X-Range' );
            $values = explode( '-', explode( '=', $range )[1] );
            $offset = $values[0];
            if ($values[0] === $values[1]) {
                $limit = 1;
            } else {
                $limit = $values[1] - $offset;
            }
        }
        $queryString = urldecode($request->getQueryString());
        $field = $default;
        $direction = self::ASC;
        if( strpos( $queryString, 'sort' ) !== false )
        {
            preg_match( '/sort\(([-+])(\w+)\)/', $queryString, $matches );
            if( count( $matches ) >= 3 )
            {
                $direction = (array_key_exists( $matches[1], self::$DIRMAP ) !== false) ? self::$DIRMAP[$matches[1]] : self::ASC;
                $field = $matches[2];
            }
        }
        $query = $request->query;
        $filter = null;
        if( $query->has( 'match' ) )
        {
            preg_match( '#match=/(\w+)/#i', $query->get( 'match' ), $matches );
            $filter = [self::OP => 'LIKE', self::VALUE => '%'.$matches[1].'%'];
        }
        if( strpos($queryString, '=gt=') !== false )
        {
            preg_match( '#(\w+)=gt=(\w+)#i', $queryString, $matches );
            $filter = [self::OP => self::GT, self::FIELD => $matches[1], self::VALUE => $matches[2]];
        }

        return [ 'filter' => $filter, 'offset' => $offset, 'limit' => $limit, 'sort-field' => $field, 'sort-direction' => $direction];
    }

}

<?php

namespace AppBundle\Util;

use Symfony\Component\HttpFoundation\Request;

class DStore
{

    const ASC = 'asc';
    const DESC = 'desc';

    static $DIRMAP = ['-' => 'desc', '+' => 'asc'];

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
            $limit = $values[1] - $offset;
        }
        $queryString = $request->getQueryString();
        $field = $default;
        $direction = self::ASC;
        if( strpos( $queryString, 'sort' ) !== false )
        {
            $sortValue = preg_match( '/sort\(([-+])(\w+)\)/', $queryString, $matches );
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
            $filter = preg_match( '#match=/(\w+)/#i', $query->get( 'match' ), $matches );
            $filter = '%'.$matches[1].'%';
        }

        return [ 'filter' => $filter, 'offset' => $offset, 'limit' => $limit, 'sort-field' => $field, 'sort-direction' => $direction];
    }

}

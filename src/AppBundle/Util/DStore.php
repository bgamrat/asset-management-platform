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
        if( $request->headers->has( 'X-Range' ) )
        {
            // TODO: Add validation
            $range = $request->headers->get( 'X-Range' );
            $values = explode( '-', explode( '=', $range )[1] );
            $offset = $values[0];
            $limit = $values[1] - $offset;
        }
        $field = $default;
        $direction = self::ASC;
        $sort = urldecode( $request->getQueryString() );
        if( strpos( $sort, 'sort' ) !== false )
        {
            $sortValue = preg_match( '/\(([-+])(\w+)\)/', $sort, $matches );
            if( count( $matches ) >= 3 )
            {
                $direction = (array_key_exists( $matches[1], self::$DIRMAP ) !== false) ? self::$DIRMAP[$matches[1]] : self::ASC;
                $field = $matches[2];
            }
        }

        return [ 'offset' => $offset, 'limit' => $limit, 'sort-field' => $field, 'sort-direction' => $direction];
    }

}

<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.07.05.
 * Time: 10:42
 */

namespace ShopMoves\UnasMigrationBundle\Utils;


class CsvToArray
{
    public function convert($fileUrl)
    {
        $handle = @fopen( $fileUrl, "r");
        if ( !$handle ) {
            throw new \Exception( "Couldn't open $fileUrl!" );
        }

        $result = [];

        // read the first line
        $first = ( fgets( $handle, 4096 ) );
        // get the keys
        $first = trim($first, "\xef\xbb\xbf");
        $keys = str_getcsv( $first, ";", "\"" );

        // read until the end of file
        while ( ($buffer = fgets( $handle, 4096 )) !== false ) {

            // read the next entry
            $array = str_getcsv ( $buffer, ";", "\""  );
            if ( empty( $array ) ) continue;

            $row = [];
            $i=0;

            // replace numeric indexes with keys for each entry
            foreach ( $keys as $key ) {
                $row[ $key ] = $array[ $i ];
                $i++;
            }

            // add relational array to final result
            $result[] = $row;
        }

        fclose( $handle );
        return $result;
    }
}
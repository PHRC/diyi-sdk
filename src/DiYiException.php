<?PHP

declare( strict_types = 1 );

namespace DiYi;


use Exception;

class DiYiException extends Exception
{
    public function __construct( $message = "", $code = 0, $previous = null )
    {
        parent::__construct( $message, $code, $previous );
        var_dump( "code:" . $code );
        var_dump( "message:" . $message );
    }
}

<?PHP

declare( strict_types = 1 );

namespace DiYi;


use Hanson\Foundation\AbstractAPI;

class Api extends AbstractAPI
{
    //商户号
    protected string $merchant_no = '';
    //apikey
    protected string $secret_key = '';
    //节点地址
    protected string $gateway_address = '';
    //回调地址
    protected string $call_url = '';


    public function __construct( string $merchant_no, string $secret_key, string $gateway_address, string $call_url )
    {
        $this->merchant_no = $merchant_no;
        $this->secret_key = $secret_key;
        $this->gateway_address = $gateway_address;
        $this->call_url = $call_url;
    }


    /**
     * @param string $method
     * @param array $body
     * @return result
     * @throws DiYiException
     */
    public function request( string $method, array $body )
    {
        $time = time();
        $nonce = rand( 100000, 999999 );
        if( $method == '/mch/support-coins' ) {
            $body = json_encode( $body );
        } else {
            $body = '[' . json_encode( $body ) . ']';
        }

        $sign = $this->signature( $body, $time, $nonce );
        $params = [
            'timestamp' => $time,
            'nonce' => $nonce,
            'sign' => $sign,
            'body' => $body,
        ];
        $http = $this->getHttp();
        $response = $http->json( $this->gateway_address . $method, $params );
        $result = json_decode( strval( $response->getBody() ), true );
        $this->checkErrorAndThrow( $result );
        return $result;
    }

    public function timestamp(): float
    {
        return round( microtime( true ) * 1000 );
    }

    public function sign( array $array ): string
    {
        unset( $array[ 'sign' ] );
        ksort( $array );
        $arr = [];
        $i = 0;
        foreach( $array as $key => $value ) {
            if( !empty( $value ) ) {
                $arr[ $i++ ] = $key . '=' . $value;
            }
        }
        //error_log( '[' . date( 'Y-m-d H:i:s' ) . '] $arr: ' .
        //var_export( $arr, true ) . "\n", 3,
        //__DIR__ . '/error_log.log' );

        return hash_hmac( 'sha256', implode( '&', $arr ), $this->secret_key );
    }

    public function nonce(): string
    {
        $str = '';
        for( $i = 0; $i < 64; $i++ ) {
            $str .= chr( rand( 33, 126 ) );
        }
        return $str;
    }

    /**
     * @param $result
     * @throws DiYiException
     */
    private function checkErrorAndThrow( $result )
    {
        if( !$result || $result[ 'code' ] != 200 ) {
            throw new DiYiException( $result[ 'message' ], $result[ 'code' ] );
        }
    }
}

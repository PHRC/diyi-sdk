<?PHP

declare( strict_types = 1 );

namespace DiYi;


use Hanson\Foundation\AbstractAPI;

class Api extends AbstractAPI
{
    //商户号
    protected string $merchant_no = '';
    //apikey
    protected string $api_key = '';
    //节点地址
    protected string $gateway_address = '';
    //回调地址
    protected string $call_url = '';


    public function __construct( string $merchant_no, string $api_key, string $gateway_address, string $call_url )
    {
        $this->merchant_no = $merchant_no;
        $this->api_key = $api_key;
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

    public function signature( $body, $time, $nonce )
    {
        return md5( $body . $this->api_key . $nonce . $time );
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

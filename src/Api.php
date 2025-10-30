<?PHP

declare( strict_types = 1 );

namespace DiYi;


use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Psr7\Request;
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


    public function __construct(
        string $merchant_no,
        string $secret_key,
        string $gateway_address,
        string $call_url
    ) {
        $this->merchant_no = $merchant_no;
        $this->secret_key = $secret_key;
        $this->gateway_address = $gateway_address;
        $this->call_url = $call_url;
    }


    /**
     * @param string $method
     * @param array $body
     * @return mixed
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

    /**
     * @param string $api
     * @param array $body
     * @return array
     * @throws DiYiException
     */
    public function request_wrapper( string $api, array $body ): array
    {
        $client = new GuzzleHttpClient();
        $options = [
            'form_params' => array_merge( $body, [
                'mch_no' => $this->merchant_no,
                'timestamp' => $this->timestamp(),
                'nonce' => $this->nonce(),
            ] ),
        ];
        //error_log( '[' . date( 'Y-m-d H:i:s' ) . '] $options: ' .
        //var_export( $options, true ) . "\n", 3,
        //__DIR__ . '/error_log.log' );
        //error_log( '[' . date( 'Y-m-d H:i:s' ) . '] $this->gateway_address: ' .
        //var_export( $this->gateway_address, true ) . "\n", 3,
        //__DIR__ . '/error_log.log' );
        $options[ 'form_params' ][ 'sign' ] = $this->sign( $options[ 'form_params' ] );
        $request = new Request( 'POST', $this->gateway_address . $api );
        $response = $client->sendAsync( $request, $options )->wait();
        // 读取流并解析为数组
        $body = $response->getBody()->getContents();
        $data = json_decode( $body, true );

        if( json_last_error() !== JSON_ERROR_NONE ) {
            throw new DiYiException( 'JSON解析失败：' . json_last_error_msg() );
        }

        return $data; // 直接返回数组，外部可直接使用
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

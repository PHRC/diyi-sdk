<?PHP

declare( strict_types = 1 );

namespace DiYi;


use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Psr7\Request;

class Client extends Api
{
    /**
     * @throws DiYiException
     */
    public function generate_address( string $chain )
    {
        $client = new GuzzleHttpClient();
        $options = [
            'form_params' => [
                'chain' => $chain,
                'mch_no' => $this->merchant_no,
                'timestamp' => $this->timestamp(),
                'nonce' => $this->nonce(),
                'sign' => '',
            ],
        ];
        //error_log( '[' . date( 'Y-m-d H:i:s' ) . '] $options: ' .
        //var_export( $options, true ) . "\n", 3,
        //__DIR__ . '/error_log.log' );
        //error_log( '[' . date( 'Y-m-d H:i:s' ) . '] $this->gateway_address: ' .
        //var_export( $this->gateway_address, true ) . "\n", 3,
        //__DIR__ . '/error_log.log' );
        $options[ 'form_params' ][ 'sign' ] = $this->sign( $options[ 'form_params' ] );
        $request = new Request( 'POST', $this->gateway_address . '/api/generate_address' );
        $response = $client->sendAsync( $request, $options )->wait();
        // 读取流并解析为数组
        $body = $response->getBody()->getContents();
        $data = json_decode( $body, true );

        if( json_last_error() !== JSON_ERROR_NONE ) {
            throw new DiYiException( 'JSON解析失败：' . json_last_error_msg() );
        }

        return $data; // 直接返回数组，外部可直接使用
    }

    public function verify_address( string $chain )
    {
        $client = new GuzzleHttpClient();
        $options = [
            'form_params' => [
                'chain' => 'TRX',
                'mch_no' => '',
                'timestamp' => '',
                'nonce' => '',
                'sign' => '',
            ],
        ];
        $request = new Request( 'POST', '/api/verify_address' );
        $res = $client->sendAsync( $request, $options )->wait();
        return $res->getBody();
    }

    public function withdrawal(
        string $chain,
        string $token,
        string $to,
        string $amount,
        string $mch_order_no,
        string $memo
    ) {
        $client = new GuzzleHttpClient();
        $options = [
            'form_params' => [
                'chain' => 'TRX',
                'mch_no' => '',
                'timestamp' => '',
                'nonce' => '',
                'sign' => '',
            ],
        ];
        $request = new Request( 'POST', '/api/withdrawal' );
        $res = $client->sendAsync( $request, $options )->wait();
        return $res->getBody();
    }

    public function coins()
    {
        $client = new GuzzleHttpClient();
        $options = [
            'form_params' => [
                'chain' => 'TRX',
                'mch_no' => '',
                'timestamp' => '',
                'nonce' => '',
                'sign' => '',
            ],
        ];
        $request = new Request( 'POST', '/api/coins' );
        $res = $client->sendAsync( $request, $options )->wait();
        return $res->getBody();
    }
}

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
        return $this->request_wrapper( '/api/generate_address', [
            'chain' => $chain,
        ] );
    }

    /**
     * @param string $chain
     * @return mixed
     * @throws DiYiException
     */
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

    /**
     * @param string $chain
     * @param string $token
     * @param string $to
     * @param string $amount
     * @param string $mch_order_no
     * @param string $memo
     * @return array
     * @throws DiYiException
     */
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
                'chain' => $chain,
                'token' => $token,
                'to' => $to,
                'amount' => $amount,
                'mch_order_no' => $mch_order_no,
                'memo' => $memo,
            ],
        ];
        $request = new Request( 'POST', '/api/withdrawal' );
        $res = $client->sendAsync( $request, $options )->wait();
        return $res->getBody();
    }

    /**
     * @return mixed
     * @throws DiYiException
     */
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

    public function sign_check( array $params )
    {
        return $params[ 'sign' ] === $this->sign( $params );
    }
}

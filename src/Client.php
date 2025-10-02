<?PHP

declare( strict_types = 1 );

namespace DiYi;


use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Psr7\Request;

class Client extends Api
{
    public function generate_address( string $chain )
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
        $request = new Request( 'POST', '/api/generate_address' );
        $res = $client->sendAsync( $request, $options )->wait();
        return $res->getBody();
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

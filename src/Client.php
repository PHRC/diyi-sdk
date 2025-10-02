<?PHP

declare( strict_types = 1 );

namespace DiYi;


use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Psr7\Request;

class Client extends Api
{
    public function generate_address()
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
}

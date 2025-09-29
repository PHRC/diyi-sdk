<?PHP

declare( strict_types = 1 );

namespace DiYi;


class Coin
{
    public const ETH = 'ETH';
    public const BSC = 'BSC';
    public const TRX = 'TRX';
    public const POL = 'POL';

    public static $relation = [
        self::ETH => [ 'ETH' ],
        self::BSC => [ 'BSC' ],
        self::TRX => [ 'TRX' ],
        self::POL => [ 'POL' ],
        'USDT' => [ 'ETH', 'BSC', 'TRX' ],
    ];
}

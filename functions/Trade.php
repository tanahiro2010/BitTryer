<?php
class Trade
{
    private $BitAPI = null;
    private $DB_PATH = "";
    private $user_id = "";

    function __construct(BitAPI $BitAPI, string $DB_PATH, string $user_id)
    {
        $this->BitAPI = $BitAPI;
        $this->DB_PATH = $DB_PATH;
        $this->user_id = $user_id;
    }

    private function load()
    {
        return json_decode(file_get_contents($this->DB_PATH), true);
    }

    private function save($database)
    {
        return file_put_contents($this->DB_PATH, json_encode($database, JSON_PRETTY_PRINT));
    }

    /**
     * @param int $Yen
     * @return float|int
     */

    public function getMaxBit(int $Yen) // 最大何bitcoin買収できるか取得
    {
        /*
         * 計算方法
         * 1ビットコインがx円だから
         * 1 : 90000で
         * 買収可能bitcoin : $Yen
         */

        $OneBitPrice = $this->BitAPI->getYenPrice();
        $canPrice = $OneBitPrice / $Yen;
        return $canPrice;
    }

    /**
     * @param float $Bit
     * @return int
     */

    public function getMaxPrice(float $Bit)
    {
        /*
         * 計算方法
         * 1ビットコインがx円だから
         * 1 : 90000で
         * $Bit : 売却可能額
         */

        $OneBitPrice = $this->BitAPI->getYenPrice();
        return (int)$OneBitPrice * $Bit;
    }

    public function buy_bitcoin(float $yen)        // ビットコイン買収
    {

    }

    public function sell_bitcoin(float $bitcoin)   // ビットコイン
    {

    }
}
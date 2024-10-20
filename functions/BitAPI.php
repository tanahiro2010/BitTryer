<?php
class BitAPI
{
    private $ApiUrl = 'https://coincheck.com/api/ticker';

    private $Accounts = null;
    function __construct(Accounts $Accounts=null)
    {
        $this->Accounts = $Accounts;
        return;
    }

    private function api_get(string $url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        // JSONを配列としてデコード
        $data = json_decode($response, true);

        // レスポンスがnullやfalseの場合のエラーハンドリング
        if (!$data) {
            throw new Exception('APIリクエストに失敗しました。');
        }

        return $data;
    }

    private function getBitCoinApiResponse()
    {
        try {
            return $this->api_get($this->ApiUrl);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @return int
     */

    public function getYenPrice()
    {
        try {
            $response = $this->getBitCoinApiResponse();
        } catch (Exception $e) {
            return null;
        }

        return $response['last'];
    }

    /**
     * @param int $Yen
     * @return float
     */

    public function calculate_max_bitcoin(int $Yen) // 最大何ビットコイン買収できるか取得
    {
        /**
         * 1 : getYenPrice()
         * x : $Yen
         */

        // ビットコイン価格（デフォルトは1BTC = 10,000,000円）
        $btc_price = $this->getYenPrice();

        // 最大購入可能なビットコインの量を計算
        $max_btc = $Yen / $btc_price;

        // 結果を小数点以下8桁まで表示（ビットコインの最小単位は0.00000001BTC）
        return round($max_btc, 8);
    }

    public function sell_bitcoin(float $bitcoin)
    {

    }
}

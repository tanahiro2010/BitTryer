<?php
class BitAPI
{
    private $ApiUrl = 'https://api.coingecko.com/api/v3/';
    private $BitApiEndPoint = 'simple/price?ids=bitcoin&vs_currencies=usd';
    private $ConvertApiEndPoint = 'exchange_rates';

    function __construct()
    {
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
            return $this->api_get($this->ApiUrl . $this->BitApiEndPoint);
        } catch (Exception $e) {
            return null;
        }
    }

    private function convertDollarToYen($amountInDollars)
    {
        try {
            $data = $this->api_get($this->ApiUrl . $this->ConvertApiEndPoint);

            if (!isset($data['rates']['jpy'])) {
                throw new Exception('為替レート(JPY)の取得に失敗しました。');
            }

            // レートを取得して変換
            $usdToJpyRate = $data['rates']['jpy']['value'];
            return $amountInDollars * $usdToJpyRate;

        } catch (Exception $e) {
            return null;
        }
    }

    private function convertYenToDollar($amountInYen)
    {
        try {
            $data = $this->api_get($this->ApiUrl . $this->ConvertApiEndPoint);

            if (!isset($data['rates']['usd'])) {
                throw new Exception('為替レート(USD)の取得に失敗しました。');
            }

            // 円からドルへの変換
            $jpyToUsdRate = 1 / $data['rates']['jpy']['value'];
            return $amountInYen * $jpyToUsdRate;

        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @return float|int|null
     */
    public function getDollarPrice()
    {
        try {
            $response = $this->getBitCoinApiResponse();
            if ($response == null) {
                throw new Exception('Error');
            }

            if (!isset($response['bitcoin']['usd'])) {
                throw new Exception('Bitcoinの価格データがありません。');
            }

            $DollarPrice = $response['bitcoin']['usd'];
        } catch (Exception $e) {
            return null;
        }

        return $DollarPrice;
    }

    /**
     * @return float|int|null
     */

    public function getYenPrice()
    {
        try {
            $response = $this->getBitCoinApiResponse();

            if ($response == null) {
                throw new Exception('Error');
            }

            if (!isset($response['bitcoin']['usd'])) {
                throw new Exception('Bitcoinの価格データがありません。');
            }

            $DollarPrice = $response['bitcoin']['usd'];

        } catch (Exception $e) {
            return null;
        }

        // ドルから円への変換
        return $this->convertDollarToYen($DollarPrice);
    }
}

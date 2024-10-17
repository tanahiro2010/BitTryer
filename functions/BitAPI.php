<?php
class BitAPI
{
    private $ApiUrl = 'https://coincheck.com/api/ticker';

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
}

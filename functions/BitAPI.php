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

    private function api_get(string $url): null | array
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

    private function getBitCoinApiResponse(): null | array
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

    public function getYenPrice(): int
    {
        try {
            $response = $this->getBitCoinApiResponse();
        } catch (Exception $e) {
            return null;
        }

        return $response['last'];
    }

    /**
     * @param float $bit
     * @return int
     */
    public function bitToJpy(float $bit): int
    {
        /*
         * getYenPrice() : 1
         * return        : $bit
         */
        return (int)$this->getYenPrice() * $bit;
    }

    public function jpyToBit(int $jpy_amount): float
    {
        /*
         * getYenPrice() : 1
         * $bit          : return
         */

        $bit_jpy_amount = $this->getYenPrice();
        return (float)$bit_jpy_amount * $jpy_amount;
    }

    /**
     * @param int $Yen
     * @return float
     */

    public function calculate_max_bitcoin(int $Yen): float // 最大何ビットコイン買収できるか取得
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

    /**
     * @param string $user_id
     * @param float $bitcoin
     * @param boolean $isBot
     * @return boolean
     */
    public function sell_bitcoin(string $user_id, float $bitcoin, bool $isBot = false): bool
    {
        $user_data = $this->Accounts->in_account($user_id);
        $jpyPrice = $this->bitToJpy($bitcoin);

        $hasbit = $user_data['total_bitcoin'];

        if ($hasbit >= $bitcoin && $bitcoin > 0) { // 正常
            $user_data['total_bitcoin'] -= $bitcoin;
            $user_data['total_yen'] += $jpyPrice;

            $this->Accounts->save_user($user_id, $user_data);

            $this->Accounts->appendFromArrayCenter($user_id, 'trade_history', array(
                "type" => "sell",
                "bitcoin" => $bitcoin,
                "jpy_amount" => $jpyPrice,
                "time" => date('Y-m-d H:i:s'),
                "is_bot" => $isBot
            ));
            return true;
        }

        return false;
    }

    /**
     * @param string $user_id
     * @param int $jpy_amount
     * @param bool $isBot
     * @return bool
     */

    public function buy_bitcoin(string $user_id, int $jpy_amount, bool $isBot = false): bool
    {
        $user_data = $this->Accounts->in_account($user_id);
        $bitPrice = $this->calculate_max_bitcoin($jpy_amount);
        echo $bitPrice . '<br>';

        $hasJpy = $user_data['total_yen'];

        if ($hasJpy >= $bitPrice && $jpy_amount > 0) {
            $user_data['total_bitcoin'] += $bitPrice;
            $user_data['total_yen'] -= $jpy_amount;
            $user_data['last_jpy'] = $this->getYenPrice();

            $this->Accounts->save_user($user_id, $user_data);

            $this->Accounts->appendFromArrayCenter($user_id, 'trade_history', array(
                "type" => "buy",
                "bitcoin" => $bitPrice,
                "jpy_amount" => $jpy_amount,
                "time" => date('Y-m-d H:i:s'),
                "is_bot" => $isBot
            ));
            return true;
        }

        return false;
    }

    /**
     * @param string $user_id
     * @param string $target_id
     * @param int $jpy_payment
     * @return bool
     */

    public function send_jpy(string $user_id, string $target_id, int $jpy_payment, bool $isBot = false): bool
    {
        $user_data = $this->Accounts->in_account($user_id);
        $target_data = $this->Accounts->in_account($target_id);

        if ($user_data && $target_data && 0 < $jpy_payment) {
            $user_data['total_yen'] -= $jpy_payment;
            $target_data['total_yen'] += $jpy_payment;

            $this->Accounts->save_user($user_id, $user_data);
            $this->Accounts->save_user($target_id, $target_data);

            $this->Accounts->appendFromArrayCenter($user_id, 'trade_history', array(
                "type" => "send",
                "target_id" => $target_id,
                "bitcoin" => null,
                "jpy_amount" => $jpy_payment,
                "time" => date('Y-m-d H:i:s'),
                "is_bot" => $isBot
            ));

            $this->Accounts->appendFromArrayCenter($target_id, 'trade_history', array(
                "type" => "catch",
                "from_id" => $user_id,
                "bitcoin" => null,
                "jpy_amount" => $jpy_payment,
                "time" => date('Y-m-d H:i:s'),
                "is_bot" => $isBot
            ));

            return true;
        }

        return false;
    }
}

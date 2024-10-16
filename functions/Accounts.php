<?php
class Accounts
{
    private string $DB_PATH;
    private string $AppName;

    /**
     * @param string $DB_PATH
     * @param string $SESSION_NAME
     */

    function __construct(string $DB_PATH, string $SESSION_NAME)
    {
        session_start();
        $this->DB_PATH = $DB_PATH;
        $this->AppName = $SESSION_NAME;
    }

    private function load()
    {
        return json_decode(file_get_contents($this->DB_PATH), true);
    }

    private function save(mixed $database)
    {
        return file_put_contents($this->DB_PATH, json_encode($database, JSON_PRETTY_PRINT));
    }

    /**
     * @param string $id
     * @param string $name
     * @param string $mail
     * @param string $password
     * @return array|false
     */

    public function register(string $id, string  $name, string $mail, string $password)
    {
        $database = $this->load();

        if (isset($database['user'][$id])) { // ユーザーが存在するなら
            return false;
        } else {                             // ユーザーが存在しないなら
            $user_data = array(
                'id' => $id,
                'name' => $name,
                'mail' => $mail,
                'password' => password_hash(md5($password), PASSWORD_DEFAULT),

                'total_yen' => 100000,
                'total_bitcoin' => 0,

                'trade_history' => array()
            );

            $database['user'][$id] = $user_data;

            $this->save($user_data);
            $_SESSION[$this->AppName]['user'] = $user_data;

            return $user_data;
        }
    }

    /**
     * @param string $id
     * @param string $password
     * @return false|mixed
     */

    public function login(string $id, string $password)
    {
        $database = $this->load();

        if (isset($database['user'][$id])) {
            if (password_verify(md5($password), $database['user'][$id]['password'])) {
                $_SESSION[$this->AppName]['user'] = $database['user'][$id];
                return $database['user'][$id];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * @return mixed
     */

    public function isLogin()
    {
        $database = $this->load();
        $user_id = $_SESSION[$this->AppName]['user']['id'] ?? '';

        if (isset($database['user'][$user_id])) {
            return $database['user'][$user_id];
        }
        return null;
    }

    /**
     * @return void
     */
    public function logout()
    {
        session_destroy();
    }

    /**
     * @param string $user_id
     * @return false|mixed
     */
    public function in_account(string $user_id)
    {
        $database = $this->load();
        return $database['user'][$user_id] ?? false;
    }

    public function forget_password_create_token(string $user_id)
    {
        $database = $this->load();

        $user_data = $this->in_account($user_id);
        if ($user_data) {
            $token = bin2hex(random_bytes(32));
            $database['token'][$token] = $user_id;
            $user_mail = $user_data['mail'];

            $content = "BitTryerをご利用していただきありがとうございます。\n";

            mb_internal_encoding('UTF-8');
            mb_language('ja');

            mb_send_mail($user_mail, "BitTryerパスワードリセット", $mail_content);
        }
    }
}
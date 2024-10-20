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

            $this->save($database);
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
            if ($database['user'][$user_id]['password'] == $_SESSION[$this->AppName]['user']['password']) {
                return $database['user'][$user_id];
            }
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

    /**
     * @param string $user_id
     * @return false|string
     * @throws \Random\RandomException
     */

    public function forget_password_create_token(string $user_id)
    {
        $database = $this->load();

        $user_data = $this->in_account($user_id);
        if ($user_data) {
            $token = bin2hex(random_bytes(32));
            $_SESSION[$this->AppName]['token'] = $token;
            $database['forget_token'][$token] = $user_id;
            $this->save($database);

            $user_mail = $user_data['mail'];

            $content = "平素よりBitTryerをご利用していただきありがとうございます。\n\nパスワード変更する場合は以下のURLをタップしてください\n\nhttps://bitcoin.tanahiro2010.com/login/forget_password?token=$token\n\n身に覚えのないメールの場合、無視してください";

            mb_internal_encoding('UTF-8');
            mb_language('ja');

            mb_send_mail($user_mail, "BitTryerパスワードリセット", $content);

            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string $token
     * @param string $password
     * @return false|mixed
     */

    public function forget_password_change_password(string $token, string $password)
    {
        $database = $this->load();

        if ($this->in_forget_password_token($token)) {
            $user_id = $database['forget_token'][$token];

            $database['user'][$user_id]['password'] = password_hash(md5($password), PASSWORD_DEFAULT);
            $this->save($database);

            $_SESSION[$this->AppName]['token'] = null;

            return $database['user'][$user_id];
        } else {
            return false;
        }
    }

    /**
     * @return false|mixed
     */
    public function forget_password_auth()
    {
        return $_SESSION[$this->AppName]['token'] ?? false;
    }

    /**
     * @param string $token
     * @return bool
     */
    public function in_forget_password_token(string $token)
    {
        $database = $this->load();
        return isset($database['forget_token'][$token]);
    }
}
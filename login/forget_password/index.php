<?php
require_once '../../functions/Accounts.php';
require_once '../../functions/functions.php';
require_once '../../functions/Trade.php';
require_once '../../functions/header.php';
require_once '../../functions/BitAPI.php';

$Accounts = new Accounts('../../database/database.json', 'bitcoin');
$BitAPI = new BitAPI();

$user_data = $Accounts->isLogin();
$error = false;
$error_message = "";

if ($user_data) {
    header('Location: /');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $Accounts->forget_password_auth();
    if ($token) { // パスワードを忘れていた場合
        if (isset($_POST['password'])) {
            $result = $Accounts->forget_password_change_password($token, $_POST['password']);

            if ($result) { // 変更を実施できた場合
                header('Location: /login');
            } else {
                header('Location: ./?error=token');
            }
        }
    } else {      // まだ忘れていなかった場合
        if (isset($_POST['id'])) {
            $id = $_POST['id'];
            $token = $Accounts->forget_password_create_token($id);

            if ($token) {
                echo '<script>alert("登録時に入力されたメールアドレスにリセットメールを送信しました");location.href="/";</script>';
            } else {
                header('Location: ./?error=notfound');
            }

        } else {
            header('Location: ./?error=required');
        }

        exit();
    }
    exit();
} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
    echo_header($user_data, $BitAPI);

    if (isset($_GET['error'])) {
        switch ($_GET['error']) {
            case 'token':
                $error = true;
                $error_message = "Invalid token";
                break;

            case 'notfound':
                $error = true;
                $error_message = "User not found";
                break;

            case 'required':
                $error = true;
                $error_message = "Required field";
                break;
        }
    }
} else {
    header("HTTP/1.0 405 Method Not Allowed");
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    ?>

    <section class="flex">
        <div class="w-1/3"></div>

        <section class="w-1/3 rounded">
            <?php if ($error): // エラーダイアログ ?>
                <div class="bg-red-500 rounded">
                    <div class="text-2xl ml-1.5">Error</div>
                    <div class="ml-1.5"><?php echo $error_message; ?></div>
                </div>
            <?php endif;
    if (isset($_GET['token'])) {
        $token = $_GET['token'];
        if ($Accounts->in_forget_password_token($token)):
        ?>
            <!-- ログインフォーム -->
            <div class="items-center text-center <?php echo $error ? 'mt-2' : ''; ?>">
                <form action="./" method="post" class="bg-gradient-to-r from-gray-900 to-blue-900 rounded shadow-md">
                    <div class="text-2xl hover:text-black font-mono">変更後のパスワードを入力</div><br>
                    <input type="text" class="rounded py-2 px-4 text-black" name="password" placeholder="password" required><br>

                    <button type="submit" class="bg-gradient-to-r from-blue-900 to-gray-900 font-semibold text-white py-2 px-4 mt-3 mu-6 rounded shadow-md">パスワードリセットメールを送信</button><br><br>

                    <a href="/login" class="text-blue-500 underline hover:text-blue-100">ログインはこちら</a><br>

                </form>
            </div>

        <?php
        endif;
    } else {
        ?>
            <!-- ログインフォーム -->
            <div class="items-center text-center <?php echo $error ? 'mt-2' : ''; ?>">
                <form action="./" method="post" class="bg-gradient-to-r from-gray-900 to-blue-900 rounded shadow-md">
                    <div class="text-2xl hover:text-black font-mono">パスワードをリセット</div><br>
                    <input type="text" class="rounded py-2 px-4 text-black" name="id" placeholder="id" required><br>

                    <button type="submit" class="bg-gradient-to-r from-blue-900 to-gray-900 font-semibold text-white py-2 px-4 mt-3 mu-6 rounded shadow-md">パスワードリセットメールを送信</button><br><br>

                    <a href="/login" class="text-blue-500 underline hover:text-blue-100">ログインはこちら</a><br>

                </form>
            </div>
        </section>


        <section class="w-1/3"></section>

        <?php
    }

    ?>
    </section>
    <section class="w-1/3"></section>
    </section>
    <?php
}
?>
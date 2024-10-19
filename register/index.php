<?php
require_once '../functions/Accounts.php';
require_once '../functions/functions.php';
require_once '../functions/Trade.php';
require_once '../functions/header.php';
require_once '../functions/BitAPI.php';

$Accounts = new Accounts('../database/database.json', 'bitcoin');
$BitAPI = new BitAPI();

$user_data = $Accounts->isLogin();
$error = false;
$error_message = "";

if ($user_data) {
    header('Location: /');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['id']) && isset($_POST['name']) && isset($_POST['mail']) && isset($_POST['password'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $mail = $_POST['mail'];
        $password = $_POST['password'];

        $result = $Accounts->register($id, $name, $mail, $password);

        if ($result) {
            header('Location: /');
        } else {
            header('Location: ./?error=include');
        }
    } else {
        header('Location: ./?error=required');
    }
    exit();
} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
    echo_header($user_data, $BitAPI);
} else {
    header("HTTP/1.0 405 Method Not Allowed");
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['error'])) {
        $error = true;
        switch ($_GET['error']) {
            case 'include':
                $error_message = "The id used";
                break;

            case 'required':
                $error_message = "Please input all parameter.";
                break;
        }
    }
    ?>

    <section class="flex">
        <dix class="w-1/3"></dix>

        <section class="w-1/3 rounded">
            <?php if ($error): // エラーダイアログ ?>
                <div class="bg-red-500 rounded">
                    <div class="text-2xl ml-1.5">Error</div>
                    <div class="ml-1.5"><?php echo $error_message; ?></div>
                </div>
            <?php endif; ?>

            <!-- ログインフォーム -->
            <div class="items-center text-center <?php echo $error ? 'mt-2' : ''; ?>">
                <form action="./" method="post" class="bg-gradient-to-r from-gray-900 to-blue-900 rounded shadow-md">
                    <div class="text-2xl hover:text-black font-mono">register</div><br>
                    <input type="text" class="rounded py-2 px-4 text-black" name="id" placeholder="id" required><br>
                    <input type="text" class="rounded py-2 px-4 text-black mt-2" name="name" placeholder="name"><br>
                    <input type="email" class="rounded py-2 px-4 text-black mt-2" name="mail" placeholder="mail"><br>
                    <input type="password" class="rounded py-2 px-4 text-black mt-2" name="password" placeholder="password" required><br>

                    <button type="submit" class="bg-gradient-to-r from-blue-900 to-gray-900 font-semibold text-white py-2 px-4 mt-3 mu-6 rounded shadow-md">
                        register
                    </button><br><br>

                    <a href="/login" class="text-blue-500 underline hover:text-blue-100">ログインはこちら</a>
                </form>
            </div>
        </section>


        <section class="w-1/3"></section>
    </section>
    <?php
}
?>
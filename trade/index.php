<?php
require_once '../functions/Accounts.php';
require_once '../functions/header.php';
require_once '../functions/BitAPI.php';
require_once '../functions/Trade.php';
require_once '../functions/functions.php';

$Accounts = new Accounts('../database/database.json', 'bitcoin');
$BitAPI = new BitAPI();

$user_data = $Accounts->isLogin();

if (!$user_data) {
    header('Location: /login');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['type'])) {
        switch ($_POST['type']) {
            case 'bitcoin':    // ビットコインを購入
                if (isset($_POST['bitcoin'])) {

                }
                break;

            case 'jpy_change': // ビットコインをポイントに換金

                break;
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
    echo_header($user_data, $BitAPI);
}

if ($_SERVER['REQUEST_METHOD'] == 'GET'):
?>

<div class="flex">
    <div class="w-1/4"></div>
    <section class="w-2/4">
        <div class="text-3xl text-center">取引フォーム</div>
        <div class="text-2xl">貴方の資産</div>
        <div class="text-lg mt-1">
            売買ポイント: <?php echo $user_data['total_yen']; ?>円<br>
            BitCoin: <?php echo $user_data['total_bitcoin']; ?> Coin<br>
        </div>

        <form action="./" method="post" class="text-center bg-gradient-to-r from-gray-900 to-blue-900 rounded shadow-md">
            <input type="hidden" name="type" value="bitcoin">
            <div class="h-3"></div>
            <div class="text-2xl">BitCoin売却</div>
            <div class="h-3"></div>
            <input type="number" name="bitcoin" placeholder="最低0.00000001" class="rounded py-2 px-4 text-black" max="" min=""><br>

            <button type="submit" class="bg-gradient-to-r from-blue-900 to-gray-900 font-semibold text-white py-2 px-4 mt-3 mu-6 rounded shadow-m">買収</button>
            <div class="h-5"></div>
        </form>
    </section>
    <div class="w-1/4"></div>
</div>

<?php endif; ?>
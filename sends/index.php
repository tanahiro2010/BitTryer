<?php
require_once '../functions/Accounts.php';
require_once '../functions/header.php';
require_once '../functions/BitAPI.php';
require_once '../functions/Trade.php';
require_once '../functions/functions.php';

$Accounts = new Accounts('../database/database.json', 'bitcoin');
$BitAPI = new BitAPI($Accounts);

$result = 0; // 0 はまだ取引をしていない. 1 は取引成功. 2 は取引失敗.
$user_data = $Accounts->isLogin();

if (!$user_data) {
    header('Location: /login');
}

echo_header($user_data, $BitAPI);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['id']) && $_POST['payment']) {
        $target_id = $_POST['id'];
        $jpy_payment = (int)$_POST['payment'];

        $result = $BitAPI->send_jpy($user_data['id'], $target_id, $jpy_payment) ? 1 : 2;

        header('Location: ./?result=' . $result);
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $result = $_GET['result'] ?? 0;
} else {
    header('HTTP/1.0 405 Method Not Allowed');
}

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

        <?php if ($result == 1): ?>

            <div class="bg-blue-300 w-full rounded">
                <div class="text-black-600 text-center font-bold">送信が成功しました</div>
            </div>

        <?php elseif ($result == 2): ?>

            <div class="bg-red-300 w-full rounded">
                <div class="text-black-600 text-center font-bold">送信が失敗しました</div>
            </div>

        <?php endif; ?>

        <form action="./" method="post" class="text-center bg-gradient-to-r from-gray-900 to-blue-900 rounded shadow-md">
            <input type="hidden" name="type" value="jpy">
            <div class="h-3"></div>
            <div class="text-2xl">送金</div>
            <div class="h-3"></div>
            <input type="text" name="id" placeholder="送金先" class="rounded py-2 px-4 text-black"><br>

            <input type="text" name="payment" placeholder="最大: <?php echo $user_data['total_yen']; ?>" class="mt-3 rounded py-2 px-4 text-black" max="<?php echo $user_data['total_yen']; ?>" min="0"><br>

            <button type="submit" class="bg-gradient-to-r from-blue-900 to-gray-900 font-semibold text-white py-2 px-4 mt-3 mu-6 rounded shadow-m">送信</button>
            <div class="h-5"></div>
        </form>
    </section>
    <div class="w-1/4"></div>
</div>

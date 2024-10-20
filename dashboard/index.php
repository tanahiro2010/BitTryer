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

echo_header($user_data, $BitAPI);
?>

<div class="flex">
    <div class="w-1/4"></div>
    <section class="w-2/4">
        <div class="text-3xl text-center"><?php echo $user_data['name']; ?>さん、ようこそ！</div>

        <div class="text-2xl">資産売買</div>
        <div class="text-lg mt-1">
            売買ポイント: <?php echo $user_data['total_yen']; ?>円<br>
            BitCoin: <?php echo $user_data['total_bitcoin']; ?> Coin<br>
        </div>

        <div>
            <a href="/trade"
               class="justify-between bg-gradient-to-r from-blue-900 to-gray-900 font-semibold text-white py-2 px-5 w-full mt-3 mu-6 rounded shadow-md flex cursor-pointer hover:bg-white">
                <div class="text-left">取引</div><div class="text-right">ここをタップ</div>
            </a>
            <a href="/sends"
               class="justify-between bg-gradient-to-r from-blue-900 to-gray-900 font-semibold text-white py-2 px-5 w-full mt-3 mu-6 rounded shadow-md flex cursor-pointer hover:bg-white">
                <div class="text-left">送金</div><div class="text-right">ここをタップ</div>
            </a>
        </div>

        <div class="text-2xl">Accounts</div>
        <div>
            <a href="/change_password"
               class="justify-between bg-gradient-to-r from-blue-900 to-gray-900 font-semibold text-white py-2 px-5 w-full mt-3 mu-6 rounded shadow-md flex cursor-pointer hover:bg-white">
                <div class="text-left">パスワード変更</div><div class="text-right">ここをタップ</div>
            </a>
            <a href="/logout"
               class="justify-between bg-gradient-to-r from-red-900 to-gray-900 font-semibold text-white py-2 px-5 w-full mt-3 mu-6 rounded shadow-md flex cursor-pointer hover:bg-white">
                <div class="text-left">ログアウト</div><div class="text-right">ここをタップ</div>
            </a>
        </div>
    </section>
    <div class="w-1/4"></div>
</div>

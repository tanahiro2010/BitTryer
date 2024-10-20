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
        <div class="text-3xl"><?php echo $user_data['name']; ?>さん、ようこそ！</div>

        <div class="text-2xl">資産売買</div>

        <div>
            <a href="/trade"
               class="justify-between bg-gradient-to-r from-blue-900 to-gray-900 font-semibold text-white py-2 px-5 w-full mt-3 mu-6 rounded shadow-md flex cursor-pointer hover:bg-white">
                <div class="text-left">取引</div><div class="text-right">ここをタップ</div>
            </a>
        </div>

        <div class="text-2xl">Accounts</div>
    </section>
    <div class="w-1/4"></div>
</div>

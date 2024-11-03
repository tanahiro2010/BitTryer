<?php
require_once '../functions/Accounts.php';
require_once '../functions/functions.php';
require_once '../functions/BitAPI.php';
require_once '../functions/header.php';

$Accounts = new Accounts('../database/database.json', 'bitcoin');
$BitAPI = new BitAPI($Accounts);

$user_data = $Accounts->isLogin();

if (!$user_data) {
    header('Location: /login');
    exit();
}

echo_header($user_data, $BitAPI);
?>

<div class="flex">
    <div class="w-1/4"></div>
    <section class="w-1/2"> <!-- Main -->
        <div class="text-center text-3xl">取引履歴</div>

        <div>
            <?php foreach ($user_data['trade_history'] as $history): ?>
                <div
                        class="justify-between bg-gradient-to-r from-blue-900 to-gray-900 font-semibold text-white py-2 px-5 w-full mt-3 mu-6 rounded shadow-md flex cursor-pointer hover:bg-white">
                    <div class="w-full">
                        <?php if ($history['type'] == 'sell') {
                            echo '【売却】 売却Bit額: ' . $history['bitcoin']   . ' 受取額: ' . $history['jpy_amount'] . '円';
                        } elseif ($history['type'] == 'buy') {
                            echo '【買取】 買取Bit額: ' . $history['bitcoin']   . ' 使用額: ' . $history['jpy_amount'] . '円';
                        } elseif ($history['type'] == 'send') {
                            echo '【送信】 送信先: @'   . $history['target_id'] . ' 送信額: ' . $history['jpy_amount'] . '円';
                        } elseif ($history['type'] == 'catch') {
                            echo '【受取】 送信元: @'   . $history['from_id']   . ' 受取額: ' . $history['jpy_amount'] . '円';
                        } else {
                            echo '【Error】履歴が破損しています';
                        }
                        ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <div class="w-1/4">
    </div>
</div>

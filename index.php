<?php
require_once './functions/BitAPI.php';
require_once './functions/header.php';
require_once './functions/functions.php';
require_once './functions/Trade.php';
require_once './functions/Accounts.php';

$BitAPI = new BitAPI();
$Accounts = new Accounts('./database/database.json', 'bitcoin');
$Trade = new Trade($BitAPI, './database/database.json', '');

$user_data = $Accounts->isLogin();

echo_header($user_data, $BitAPI);

// 価格を取得して表示
$bitcoinPrice = $BitAPI->getYenPrice();
echo $Trade->getMaxPrice($bitcoinPrice)
?>

</main>
</body>
</html>

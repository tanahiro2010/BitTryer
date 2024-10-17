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

$bitcoinPrice = echo_header($user_data, $BitAPI);

// 価格を取得して表字
$canBuyPrice = $Trade->getMaxBit($bitcoinPrice); // 1って出ないとおかしい
?>

<canvas id="myChart" class="items-center bg-white" width="400" height="200"></canvas>

<script>
    let chart = document.getElementById('myChart');
    let ctx = chart.getContext('2d');
    let myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['January', 'February', 'March', 'April', 'May', 'Hello'],
            datasets: [{
                label: 'Sales',
                data: [65, 59, 80, 81, 56],
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

</main>
</body>
</html>

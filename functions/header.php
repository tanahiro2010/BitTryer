<?php
function echo_header($user_data, $BitAPI)
{
    // 価格を取得して表示
    $bitcoinPrice = $BitAPI->getYenPrice();
    ?>
    <html>
    <head>
        <title>BitTryer</title>
        <meta charset="UTF-8">

        <!-- metaタグゾーン -->
        <meta name="description" content="BitCoin取引をお金を使わず体験しましょう！！">
        <meta name="copyright" content="Copyright &copy; 2024 tanahiro2010. All rights reserved." />

        <meta property="og:title" content="BitTryer" />
        <meta property="og:site_name" content="bitcoin.tanahiro2010.com">
        <meta property="og:locale" content="ja_JP" />

        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" content="BitTryer" />
        <meta name="twitter:description" content="BitCoin取引をお金を使わず体験しましょう！！">
        <meta name="twitter:image:src" content />
        <meta name="twitter:site" content="bitcoin.tanahiro2010.com" />
        <meta name="twitter:creator" content="@tanahiro2010" />
        <meta name="keywords" content="tanahiro2010,bitcoin,ビットコイン,暗号資産,取引体験,ゲーム,bit,try,bittryer">

        <!-- Tailwind CSS CDN -->
        <script src="https://cdn.tailwindcss.com"></script>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    </head>

    <dody>
        <section>
            <header class="bg-gradient-to-r from-blue-900 to-gray-900 space-y-4 p-4 sm:px-8 sm:py-6 lg:p-4 xl:px-8 xl:py-6 shadow-md">
                <div class="flex items-center justify-between">
                    <h2 class="font-semibold text-blue-200 text-3xl cursor-pointer">BitTryer</h2>
                    <div class="text-white">
                        <?php if ($bitcoinPrice): ?>
                            現在、1ビットコインは<?php echo $bitcoinPrice ?>円で買収可能です
                        <?php else: ?>
                            あなたもビットコイン売買を学習しよう！！
                        <?php endif; ?>
                    </div>
                    <a href="/<?php echo $user_data == null ? "login" : "dashboard"; ?>"
                       class="hover:bg-blue-400 group flex items-center rounded-md bg-blue-500 text-white text-sm font-medium pl-3 pr-3 py-2 shadow-sm text-8xl">
                        <?php echo $user_data == null ? "Login" : $user_data['name']; ?>
                    </a>
                </div>
            </header>
        </section>

    <main class="bg-gradient-to-r from-blue-800 via-purple-600 to-gray-500 text-white p-8 shadow-lg">
        <!-- コンテンツをここに追加 -->

    <?php
    return $bitcoinPrice;
}
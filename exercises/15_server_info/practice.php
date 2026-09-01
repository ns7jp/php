<?php

// =====================================================================
// 演習15: サーバー情報取得スクリプト
// =====================================================================
// このファイルは「未完成のひな形」です。
// "// TODO:" と書かれている部分を自分で実装してください。
// まだ実装していない部分は「まだ実装されていません」等のわかりやすい表示になるので、
// このままの状態でも php コマンドでエラーなく実行できます。
//
// このスクリプトが完成すると、今このPHPを動かしている環境について、
//   1. PHPのバージョン
//   2. OSの情報
//   3. ディスクの空き容量・総容量・使用率
//   4. ロードアベレージ(直近の負荷状況)
//   5. メモリ使用量
// をまとめて調べ、見やすい表形式でコンソールに表示できるようになります。
// =====================================================================


// ---------------------------------------------------------------------
// 0. formatBytes() 関数(演習05で作った関数をそのまま使います)
// ---------------------------------------------------------------------
// バイト数(int)を "1.5 KB" のような読みやすい文字列に変換する関数です。
// この関数はすでに完成した状態で用意してあります(演習05を参照)。
function formatBytes(int $bytes, int $precision = 2): string
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];

    if ($bytes <= 0) {
        return '0 B';
    }

    $power = (int) floor(log($bytes, 1024));
    $power = min($power, count($units) - 1);

    $value = round($bytes / (1024 ** $power), $precision);

    return $value . ' ' . $units[$power];
}


// ---------------------------------------------------------------------
// 1. PHPのバージョンを取得する
// ---------------------------------------------------------------------
// TODO: getPhpVersionText() を実装してください。
// 要件:
//   ・引数なしで使える phpversion() 関数を使い、
//     今このスクリプトを動かしているPHPのバージョン(string)を返す
function getPhpVersionText(): string
{
    // この行は仮の実装です。上のヒントを参考に書き換えてください。
    return '(まだ実装されていません)';
}


// ---------------------------------------------------------------------
// 2. OSの情報を取得する
// ---------------------------------------------------------------------
// TODO: getOsInfo() を実装してください。
// 要件:
//   ・キー 'os' に、組み込み定数 PHP_OS の値(例: "Linux")を入れる
//     (PHP_OS は「定数」なので () を付けずにそのまま使います)
//   ・キー 'detail' に、php_uname() 関数の戻り値(詳しいOS情報の文字列)を入れる
//   ・['os' => ..., 'detail' => ...] という連想配列を返す
function getOsInfo(): array
{
    // この行は仮の実装です。上のヒントを参考に書き換えてください。
    return [
        'os' => '(まだ実装されていません)',
        'detail' => '(まだ実装されていません)',
    ];
}


// ---------------------------------------------------------------------
// 3. ディスクの空き容量・総容量・使用率を取得する
// ---------------------------------------------------------------------
// TODO: getDiskUsageRows() を実装してください。
// 要件:
//   ・$path = '.'(カレントディレクトリ)を対象にする
//   ・disk_free_space($path) で空き容量(バイト、float)を取得する
//   ・disk_total_space($path) で総容量(バイト、float)を取得する
//   ・どちらかが false を返した場合、または総容量が0以下の場合は、
//     「この環境では取得できません」という内容の行を返す(ゼロ除算を避けるため)
//   ・使用中の容量は「総容量 - 空き容量」で計算する
//   ・使用率(%)は「使用中 ÷ 総容量 × 100」を round() で小数点1桁に丸めて計算する
//   ・formatBytes() を使って、空き容量・総容量・使用中の容量を読みやすい文字列にする
//   ・戻り値は [ [ラベル, 値], [ラベル, 値], ... ] という形の配列にする
//
// ヒント:
//   $free = disk_free_space($path);
//   $total = disk_total_space($path);
//   if ($free === false || $total === false || $total <= 0) { ... }
//   $used = $total - $free;
//   $usedPercent = round(($used / $total) * 100, 1);
//   formatBytes((int) $free) のように、float を int にキャストしてから渡します。
function getDiskUsageRows(): array
{
    // この行は仮の実装です。上のヒントを参考に書き換えてください。
    return [
        ['ディスク容量(空き/総)', '(まだ実装されていません)'],
        ['ディスク使用率', '(まだ実装されていません)'],
    ];
}


// ---------------------------------------------------------------------
// 4. ロードアベレージ(直近の負荷状況)を取得する
// ---------------------------------------------------------------------
// TODO: getLoadAverageText() を実装してください。
// 要件:
//   ・sys_getloadavg() は Windows環境では使えない(関数自体が存在しない)ため、
//     呼び出す前に function_exists('sys_getloadavg') で使えるかどうかを確認する
//   ・使えない場合は「この環境では取得できません」という内容の文字列を返す
//   ・使える場合は sys_getloadavg() を呼び出し、
//     戻り値の配列 [1分平均, 5分平均, 15分平均] を
//     sprintf() などを使って読みやすい1つの文字列にまとめて返す
//
// ヒント:
//   if (!function_exists('sys_getloadavg')) { return '...'; }
//   $load = sys_getloadavg(); // [0]=1分平均, [1]=5分平均, [2]=15分平均
//   sprintf('%.2f, %.2f, %.2f (1分, 5分, 15分の平均)', $load[0], $load[1], $load[2])
function getLoadAverageText(): string
{
    // この行は仮の実装です。上のヒントを参考に書き換えてください。
    return '(まだ実装されていません)';
}


// ---------------------------------------------------------------------
// 5. メモリ使用量を取得する
// ---------------------------------------------------------------------
// TODO: getMemoryUsageText() を実装してください。
// 要件:
//   ・memory_get_usage(true) を使い、PHPが実際にOSから確保している
//     メモリ量(バイト、int)を取得する
//   ・formatBytes() を使って読みやすい文字列に変換して返す
//
// ヒント:
//   $bytes = memory_get_usage(true);
//   return formatBytes($bytes) . ' ...';
function getMemoryUsageText(): string
{
    // この行は仮の実装です。上のヒントを参考に書き換えてください。
    return '(まだ実装されていません)';
}


// ---------------------------------------------------------------------
// 6.【おまけ・オプション】shell_exec() を使った安全なコマンド実行の例
// ---------------------------------------------------------------------
// ★★★ 重要:セキュリティ上の注意 ★★★
// shell_exec() に「外部からの入力(フォームの値・URLのクエリパラメータ・
// コマンドライン引数・ユーザーが入力した文字列など)」を検証せずに
// そのまま組み込んで実行すると、「コマンドインジェクション」という
// 重大な脆弱性につながります。
//
//     $host = $_GET['host'];
//     shell_exec("ping -c 1 " . $host); // このような書き方は絶対にしないでください
//
// 詳しくは explanation.md や answer.php のコメントを参照してください。
//
// TODO: getUptimeSafely() を実装してください。
// 要件:
//   ・function_exists('shell_exec') で使えるかどうかを確認する(使えなければ null を返す)
//   ・引数を一切持たない、固定の文字列 'uptime' だけを shell_exec() に渡す
//     (外部からの入力は絶対に混ぜないこと)
//   ・実行結果が null または false の場合は null を返す
//   ・成功した場合は、trim() で末尾の改行などを取り除いて返す
function getUptimeSafely(): ?string
{
    // この行は仮の実装です。上のヒントを参考に書き換えてください。
    return null;
}


// =====================================================================
// メイン処理: 各関数を呼び出して結果を集め、表形式で表示する
// =====================================================================
// ここから下は、すでに完成しています(書き換える必要はありません)。
// 上のTODOをすべて実装すると、answer.php と同じような結果が表示されます。

$rows = [];

$rows[] = ['PHPバージョン', getPhpVersionText()];

$osInfo = getOsInfo();
$rows[] = ['OS種別 (PHP_OS)', $osInfo['os']];
$rows[] = ['OS詳細 (php_uname())', $osInfo['detail']];

foreach (getDiskUsageRows() as $diskRow) {
    $rows[] = $diskRow;
}

$rows[] = ['ロードアベレージ', getLoadAverageText()];
$rows[] = ['メモリ使用量 (real)', getMemoryUsageText()];

// ラベル(項目名)の表示幅を揃えるため、mb_strwidth() で最大幅を求めます。
// (全角文字を2、半角文字を1として数えてくれるので、日本語と英数字が
//  混ざっていてもきれいに揃えられます)
$labelWidth = 0;
foreach ($rows as [$label, $value]) {
    $labelWidth = max($labelWidth, mb_strwidth($label));
}
$labelWidth += 2;

$separator = str_repeat('=', 68);

echo $separator . PHP_EOL;
echo '  サーバー情報レポート  (取得時刻: ' . date('Y-m-d H:i:s') . ')' . PHP_EOL;
echo $separator . PHP_EOL;

foreach ($rows as [$label, $value]) {
    $pad = $labelWidth - mb_strwidth($label);
    echo $label . str_repeat(' ', $pad) . ': ' . $value . PHP_EOL;
}

echo $separator . PHP_EOL;
echo PHP_EOL;

// 【おまけ】--with-uptime オプションを付けたときだけ、shell_exec() のデモを実行します。
echo str_repeat('-', 68) . PHP_EOL;
echo '【おまけ】shell_exec() を使った安全なコマンド実行の例' . PHP_EOL;
echo str_repeat('-', 68) . PHP_EOL;

$wantsUptimeDemo = in_array('--with-uptime', $argv, true);

if ($wantsUptimeDemo) {
    $uptime = getUptimeSafely();

    if ($uptime !== null) {
        echo 'shell_exec(\'uptime\') の実行結果:' . PHP_EOL;
        echo '  ' . $uptime . PHP_EOL;
    } else {
        echo 'この環境では uptime コマンドを実行できませんでした(まだ未実装かもしれません)。' . PHP_EOL;
    }
} else {
    echo '実行時に --with-uptime オプションを付けると、' . PHP_EOL;
    echo 'shell_exec(\'uptime\') を使った安全な実行例を確認できます。' . PHP_EOL;
    echo '例: php practice.php --with-uptime' . PHP_EOL;
}

echo PHP_EOL;
echo '※ shell_exec() に外部からの入力をそのまま渡すのは大変危険です' . PHP_EOL;
echo '  (コマンドインジェクションの原因になります)。詳しくは explanation.md を参照してください。' . PHP_EOL;

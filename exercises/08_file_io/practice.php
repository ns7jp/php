<?php

// =====================================================================
// 演習08: ファイル読み書き
// =====================================================================
// このファイルは「未完成のひな形」です。
// "// TODO:" と書かれている部分を自分で実装してください。
// まだ実装していない部分は「まだ実装されていません」と表示されるように
// なっているので、このままの状態でも php コマンドでエラーなく
// 実行することができます。
// =====================================================================


// ---------------------------------------------------------------------
// 0. パスを準備する
// ---------------------------------------------------------------------
// __DIR__ は「このPHPファイル自身が置かれているディレクトリ」を表す
// 特別な定数です。相対パスではなく __DIR__ を使ってパスを組み立てると、
// どのディレクトリから php コマンドを実行してもファイルの場所が
// ぶれずに済みます。
$configPath = __DIR__ . '/config.txt';
$logPath = __DIR__ . '/app.log';


// ---------------------------------------------------------------------
// 1. loadConfig(): 設定ファイルを読み込んで連想配列に変換する関数
// ---------------------------------------------------------------------
/**
 * key=value 形式の設定ファイルを読み込み、連想配列に変換する。
 *
 * 例: "host=localhost" という行は ["host" => "localhost"] に変換される。
 *
 * @param string $path 設定ファイルのパス
 * @return array 読み込んだ設定(キー => 値)
 */
function loadConfig(string $path): array
{
    // TODO: file_exists($path) を使って、ファイルが存在するかどうかを
    //       確認してください。存在しない場合は空の配列 [] を
    //       return するようにしましょう。
    // if (!file_exists($path)) {
    //     return [];
    // }

    // TODO: file() を使ってファイルを1行ずつの配列として読み込んで
    //       ください。FILE_IGNORE_NEW_LINES を指定すると、各行末の
    //       改行コードを自動的に取り除いてくれます。
    // $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    $config = [];

    // TODO: foreach で1行ずつ取り出し、explode("=", $line, 2) で
    //       キーと値に分割してください。分割した結果を
    //       $config[trim($key)] = trim($value); のような形で
    //       $config に追加していきましょう。
    // foreach ($lines as $line) {
    //     $parts = explode('=', $line, 2);
    //     if (count($parts) < 2) {
    //         continue;
    //     }
    //     [$key, $value] = $parts;
    //     $config[trim($key)] = trim($value);
    // }

    return $config;
}


// ---------------------------------------------------------------------
// 2. writeLog(): メッセージをログファイルに追記する関数
// ---------------------------------------------------------------------
/**
 * 実行日時付きのメッセージを app.log に追記する簡易ロガー。
 *
 * @param string $message ログに残したいメッセージ
 */
function writeLog(string $message): void
{
    // TODO: __DIR__ を使って app.log のパスを組み立ててください。
    // $logPath = __DIR__ . '/app.log';

    // TODO: date('Y-m-d H:i:s') を使って現在時刻の文字列を作り、
    //       $message と組み合わせて1行分の文字列を作ってください。
    // $line = '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;

    // TODO: file_put_contents() の第3引数に FILE_APPEND を指定して、
    //       $logPath に $line を追記してください。
    //       FILE_APPEND を付け忘れると、実行するたびにファイルの
    //       中身が上書きされてしまう点に注意してください。
    // file_put_contents($logPath, $line, FILE_APPEND);

    echo "  (writeLog はまだ実装されていません: {$message})" . PHP_EOL;
}


// ---------------------------------------------------------------------
// 3. file_exists() で設定ファイルの存在を確認する
// ---------------------------------------------------------------------
echo "===== 1. file_exists() による存在確認 =====" . PHP_EOL;

// TODO: file_exists($configPath) の結果に応じて、
//       「見つかりました」「見つかりません」のようなメッセージを
//       表示してください。
// if (file_exists($configPath)) {
//     echo "config.txt が見つかりました: {$configPath}" . PHP_EOL;
// } else {
//     echo "config.txt が見つかりません: {$configPath}" . PHP_EOL;
// }
echo "まだ実装されていません" . PHP_EOL;
echo PHP_EOL;


// ---------------------------------------------------------------------
// 4. loadConfig() で設定ファイルを連想配列として読み込む
// ---------------------------------------------------------------------
echo "===== 2. loadConfig() による設定の読み込み =====" . PHP_EOL;

$config = loadConfig($configPath);

if (empty($config)) {
    echo "まだ実装されていません(loadConfig() の結果が空です)" . PHP_EOL;
} else {
    foreach ($config as $key => $value) {
        echo "{$key} = {$value}" . PHP_EOL;
    }
}
echo PHP_EOL;

// TODO: $config['host']、$config['port']、$config['timeout'] を使って
//       "接続先: localhost:8080(タイムアウト: 30秒)" のような
//       メッセージを組み立てて表示してください。
//       (?? 演算子でキーが存在しない場合のデフォルト値を指定できます)
// $host = $config['host'] ?? '不明';
// $port = $config['port'] ?? '不明';
// $timeout = $config['timeout'] ?? '不明';
// echo "接続先: {$host}:{$port}(タイムアウト: {$timeout}秒)" . PHP_EOL;
echo "まだ実装されていません" . PHP_EOL;
echo PHP_EOL;


// ---------------------------------------------------------------------
// 5. fopen / fgets / fclose を使って1行ずつ読み込む(低レベルな方法)
// ---------------------------------------------------------------------
echo "===== 3. fopen/fgets/fclose による1行ずつの読み込み =====" . PHP_EOL;

// TODO: fopen($configPath, 'r') でファイルを開き、
//       while (!feof($handle)) のループの中で fgets($handle) を
//       使って1行ずつ読み込み、表示してください。
//       最後に必ず fclose($handle) でファイルを閉じてください。
// $handle = fopen($configPath, 'r');
// if ($handle === false) {
//     echo "ファイルを開けませんでした。" . PHP_EOL;
// } else {
//     $lineNumber = 1;
//     while (!feof($handle)) {
//         $line = fgets($handle);
//         if ($line === false || trim($line) === '') {
//             continue;
//         }
//         echo "{$lineNumber}行目: " . trim($line) . PHP_EOL;
//         $lineNumber++;
//     }
//     fclose($handle);
// }
echo "まだ実装されていません" . PHP_EOL;
echo PHP_EOL;


// ---------------------------------------------------------------------
// 6. file_get_contents() でファイルの中身をまとめて読む(手軽な方法)
// ---------------------------------------------------------------------
echo "===== 4. file_get_contents() による一括読み込み =====" . PHP_EOL;

// TODO: file_get_contents($configPath) を使って、config.txt の中身を
//       まるごと1つの文字列として読み込み、表示してください。
// $rawContents = file_get_contents($configPath);
// echo $rawContents;
echo "まだ実装されていません" . PHP_EOL;
echo PHP_EOL;


// ---------------------------------------------------------------------
// 7. writeLog() でログファイルに追記する
// ---------------------------------------------------------------------
echo "===== 5. writeLog() によるログの追記(FILE_APPEND) =====" . PHP_EOL;

// TODO: writeLog() を1回以上呼び出して、app.log にメッセージを
//       追記してください。writeLog() の中身を実装したあと、
//       このファイルを何度か実行して app.log の行数が増えていくことを
//       確認してみましょう。
writeLog('practice.php を実行しました');
echo PHP_EOL;

<?php

// =====================================================================
// 演習02: 演算子と条件分岐
// =====================================================================
// このファイルは「未完成のひな形」です。
// "// TODO:" と書かれている部分を自分で実装してください。
// まだ実装していない部分は「まだ実装されていません」と表示されるようになっているので、
// このままの状態でも php コマンドでエラーなく実行できます。
// =====================================================================


// ---------------------------------------------------------------------
// 1. ディスク空き容量の判定
// ---------------------------------------------------------------------
// ディスクの空き容量(パーセント)を表す変数を用意します。
// この数値をいろいろ変えて試してみましょう。
$diskFreePercent = 15;

echo "----- ディスク空き容量の判定 -----" . PHP_EOL;
echo "空き容量: {$diskFreePercent}%" . PHP_EOL;

// TODO: 以下のルールで $diskFreePercent を判定し、$diskStatus に文字列を代入してください。
//   90以上          -> "正常"
//   50以上90未満    -> "注意"
//   50未満          -> "危険"
// if / elseif / else を使って実装してください。
$diskStatus = "まだ実装されていません";

// if ($diskFreePercent >= 90) {
//     $diskStatus = "正常";
// } elseif (...) {
//     ...
// } else {
//     ...
// }

echo "判定結果: {$diskStatus}" . PHP_EOL;
echo PHP_EOL;


// ---------------------------------------------------------------------
// 2. HTTPステータスコードの分類(switch文編)
// ---------------------------------------------------------------------
// HTTPステータスコードを表す変数を用意します。
// 200, 301, 404, 500 などいろいろな値に変えて試してみましょう。
$httpStatusCode = 404;

echo "----- HTTPステータスコードの分類(switch文) -----" . PHP_EOL;
echo "ステータスコード: {$httpStatusCode}" . PHP_EOL;

// TODO: switch文を使って、$httpStatusCode に応じた文字列を $statusMessageBySwitch に代入してください。
//   200 -> "OK"
//   301 -> "リダイレクト"
//   404 -> "Not Found"
//   500 -> "サーバーエラー"
//   それ以外 -> "不明なステータス"
// ※ 各 case の最後に break を書き忘れないように注意してください(フォールスルーに注意)。
$statusMessageBySwitch = "まだ実装されていません";

// switch ($httpStatusCode) {
//     case 200:
//         $statusMessageBySwitch = "OK";
//         break;
//     case 301:
//         ...
//         break;
//     default:
//         ...
// }

echo "switchでの判定結果: {$statusMessageBySwitch}" . PHP_EOL;
echo PHP_EOL;


// ---------------------------------------------------------------------
// 3. HTTPステータスコードの分類(match式編)
// ---------------------------------------------------------------------
// 上と同じ判定を、PHP8から使える match 式で書いてみましょう。

echo "----- HTTPステータスコードの分類(match式) -----" . PHP_EOL;
echo "ステータスコード: {$httpStatusCode}" . PHP_EOL;

// TODO: match式を使って、$httpStatusCode に応じた文字列を $statusMessageByMatch に代入してください。
//   switch文編と同じルールで判定してください。
//   match式は "=>" を使って「値 => 結果」の形で書き、breakは不要です。
//   どのcaseにも一致しない場合は default を使います。
$statusMessageByMatch = "まだ実装されていません";

// $statusMessageByMatch = match ($httpStatusCode) {
//     200 => "OK",
//     301 => ...,
//     default => ...,
// };

echo "matchでの判定結果: {$statusMessageByMatch}" . PHP_EOL;
echo PHP_EOL;

// switchとmatchの結果が一致しているか確認してみましょう。
// (両方きちんと実装できていれば "一致しています" と表示されるはずです)
if ($statusMessageBySwitch === $statusMessageByMatch) {
    echo "switchとmatchの結果は一致しています。" . PHP_EOL;
} else {
    echo "switchとmatchの結果が一致していません。実装を見直してみましょう。" . PHP_EOL;
}
echo PHP_EOL;


// ---------------------------------------------------------------------
// 4. null合体演算子(??)の練習
// ---------------------------------------------------------------------
// タイムアウト秒数の設定値が入った配列を用意します。
// わざと "timeout" キーを設定していないので、未設定の状態を再現しています。
$config = [
    "host" => "example.com",
];

echo "----- null合体演算子(??)の練習 -----" . PHP_EOL;

// TODO: $config["timeout"] が未設定(または null)の場合に、
//       デフォルト値として 30 を使うように、null合体演算子(??)を使って
//       $timeoutSeconds に値を代入してください。
$timeoutSeconds = "まだ実装されていません";

// $timeoutSeconds = $config["timeout"] ?? 30;

echo "タイムアウト秒数: {$timeoutSeconds}" . PHP_EOL;

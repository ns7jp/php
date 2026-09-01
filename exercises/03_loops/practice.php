<?php

// =====================================================================
// 演習03: 繰り返し処理
// =====================================================================
// このファイルは「未完成のひな形」です。
// "// TODO:" と書かれている部分を自分で実装してください。
// まだ実装していない部分は「まだ実装されていません」と表示されるようになっているので、
// このままの状態でも php コマンドでエラーなく実行できます。
// =====================================================================


// ---------------------------------------------------------------------
// 0. 準備運動: for・while・do-whileの比較(実装済みのサンプルです)
// ---------------------------------------------------------------------
// このパートはすでに完成しています。まずは読んで、3つのループの
// 動きの違いを確認してみましょう。
echo "----- 準備運動: for・while・do-whileの比較 -----" . PHP_EOL;

// [1] for文: 「初期化; 条件; 更新」を1行にまとめて書ける
echo "[for文]      ";
for ($i = 1; $i <= 5; $i++) {
    echo $i . " ";
}
echo PHP_EOL;

// [2] while文: 条件を先にチェックしてから中身を実行する
echo "[while文]    ";
$i = 1;
while ($i <= 5) {
    echo $i . " ";
    $i++;
}
echo PHP_EOL;

// [3] do-while文: 先に中身を1回実行してから、あとで条件をチェックする
echo "[do-while文] ";
$i = 1;
do {
    echo $i . " ";
    $i++;
} while ($i <= 5);
echo PHP_EOL;

// do-whileの特徴がわかりやすいように、最初から条件が成り立たない例も見てみましょう。
echo "[while文]    条件が最初からfalseの場合 -> ";
$i = 10;
while ($i <= 5) {
    // $iは10からスタートしているので、この中は一度も実行されない
    echo $i . " ";
    $i++;
}
echo "(何も表示されない = 1回も実行されない)" . PHP_EOL;

echo "[do-while文] 条件が最初からfalseの場合 -> ";
$i = 10;
do {
    // do-whileは条件を確認する前に必ず1回実行するので、ここは実行される
    echo $i . " ";
    $i++;
} while ($i <= 5);
echo "(条件はfalseでも1回だけ実行される)" . PHP_EOL;
echo PHP_EOL;


// ---------------------------------------------------------------------
// 1. FizzBuzz(for文)
// ---------------------------------------------------------------------
// 1から100までの数字について、次のルールで出力してください。
//   3の倍数かつ5の倍数 -> "FizzBuzz"
//   3の倍数のみ        -> "Fizz"
//   5の倍数のみ        -> "Buzz"
//   それ以外            -> 数字そのもの
echo "----- FizzBuzz (1〜100) -----" . PHP_EOL;

// TODO: for文を使って、上記のルールでFizzBuzzを出力してください。
// ヒント: $number % 3 === 0 のように "%"(剰余演算子)を使うと、
//        割り切れるかどうか(倍数かどうか)を判定できます。
//        「15の倍数かどうか」の判定を、いちばん最初に書くのがポイントです。
echo "まだ実装されていません(TODO: for文でFizzBuzzを実装してください)" . PHP_EOL;

// for ($number = 1; $number <= 100; $number++) {
//     if ($number % 15 === 0) {
//         echo "FizzBuzz" . PHP_EOL;
//     } elseif ($number % 3 === 0) {
//         echo "Fizz" . PHP_EOL;
//     } elseif ($number % 5 === 0) {
//         echo "Buzz" . PHP_EOL;
//     } else {
//         echo $number . PHP_EOL;
//     }
// }

echo PHP_EOL;


// ---------------------------------------------------------------------
// 2. サーバー一覧からwebサーバーだけ接続確認(foreach + continue)
// ---------------------------------------------------------------------
$servers = ["web01", "web02", "db01", "cache01"];

echo "----- サーバー一覧からwebサーバーだけ接続確認 -----" . PHP_EOL;

// TODO: $servers を foreach で1つずつ処理してください。
//       サーバー名に "web" という文字列を含まない場合は continue でスキップし、
//       "web" を含む場合だけ「{サーバー名} に接続確認中...」と出力してください。
// ヒント: str_contains($serverName, "web") で、"web"を含むかどうかを判定できます。
echo "まだ実装されていません(TODO: foreachとcontinueを使って実装してください)" . PHP_EOL;

// foreach ($servers as $serverName) {
//     if (!str_contains($serverName, "web")) {
//         continue;
//     }
//     echo "{$serverName} に接続確認中..." . PHP_EOL;
// }

echo PHP_EOL;


// ---------------------------------------------------------------------
// 3. 二重ループで再現するヘルスチェックのリトライ処理
// ---------------------------------------------------------------------
// 3台のサーバーに対する5回分のヘルスチェック結果を、
// 固定の true(成功) / false(失敗) データとしてあらかじめ用意しています。
// (実際の通信は行わず、疑似的なデータで再現します)
$healthCheckResults = [
    "web01" => [true,  true,  true,  true,  true],
    "web02" => [true,  false, false, false, true],
    "db01"  => [false, true,  false, true,  false],
];

echo "----- ヘルスチェックのリトライ処理(3台 × 5回) -----" . PHP_EOL;

// TODO: 二重ループを使って、次の処理を実装してください。
//   ・外側のループ: $healthCheckResults を foreach で1台ずつ処理する
//   ・内側のループ: while文で1回目〜5回目のチェック結果を順番に確認する
//     - 成功したら「◯回目: 成功」と表示し、連続失敗カウントを0に戻す
//     - 失敗したら「◯回目: 失敗(連続失敗◯回目)」と表示し、連続失敗カウントを+1する
//     - 連続失敗カウントが3になったら、break で内側のループだけを打ち切る
//       (外側のループは止めず、次のサーバーの処理へ進みます)
echo "まだ実装されていません(TODO: 二重ループでヘルスチェックのリトライ処理を実装してください)" . PHP_EOL;

// foreach ($healthCheckResults as $serverName => $results) {
//     echo "◆ {$serverName} のヘルスチェックを開始します" . PHP_EOL;
//     $consecutiveFailures = 0;
//     $attempt = 0;
//     $totalAttempts = count($results);
//
//     while ($attempt < $totalAttempts) {
//         $attempt++;
//         $isSuccess = $results[$attempt - 1];
//
//         if ($isSuccess) {
//             echo "  {$attempt}回目: 成功" . PHP_EOL;
//             $consecutiveFailures = 0;
//         } else {
//             $consecutiveFailures++;
//             echo "  {$attempt}回目: 失敗(連続失敗{$consecutiveFailures}回目)" . PHP_EOL;
//
//             if ($consecutiveFailures >= 3) {
//                 echo "  → 3回連続で失敗したため、このサーバーへのチェックを打ち切ります" . PHP_EOL;
//                 break;
//             }
//         }
//     }
//     echo PHP_EOL;
// }

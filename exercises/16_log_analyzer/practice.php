<?php

// =====================================================================
// 演習16: 簡易ログ解析ツール
// =====================================================================
// このファイルは「未完成のひな形」です。
// "// TODO:" と書かれている部分を自分で実装してください。
// まだ実装していない部分は「まだ実装されていません」と表示されるようになっているので、
// このままの状態でも php コマンドでエラーなく実行できます。
//
// 同じディレクトリにある access_sample.log という、Combined Log Format風の
// ダミーのアクセスログファイルを読み込み、
//   - ステータスコードごとのアクセス数
//   - アクセス数が多いIPアドレスのランキング(上位5件)
// を集計して表示するプログラムを作っていきます。
// =====================================================================

echo "===== 演習16: 簡易ログ解析ツール =====" . PHP_EOL;
echo PHP_EOL;


// ---------------------------------------------------------------------
// 0. ログファイルのパスと、解析に使う正規表現パターンを用意する
// ---------------------------------------------------------------------
// __DIR__ は「このPHPファイルが置かれているディレクトリ」を表す
// マジック定数です。実行するときのカレントディレクトリに関係なく、
// 常に同じ場所にある access_sample.log を読み込めるようにしています。
$logFilePath = __DIR__ . "/access_sample.log";

// Combined Log Format(Apache/Nginxでよく使われるアクセスログの形式)は、
// 例えば次のような1行で構成されています。
//   192.168.1.10 - - [15/Jan/2024:10:23:45 +0900] "GET /index.php HTTP/1.1" 200 1234
// このうち、今回の演習で取り出したいのは次の6つの情報です。
//   1. IPアドレス   : 192.168.1.10
//   2. 日時         : 15/Jan/2024:10:23:45 +0900
//   3. メソッド     : GET
//   4. パス         : /index.php
//   5. ステータスコード: 200
//   6. バイト数     : 1234
// これを取り出すための正規表現パターンは、あらかじめ用意してあります。
// ("m" は複数行を含む文字列に対応するための修飾子です。
//  詳しくは6.のコメントで説明します)
$pattern = '/^(\S+) \S+ \S+ \[([^\]]+)\] "(\S+) (\S+) [^"]+" (\d{3}) (\d+)/m';


// ---------------------------------------------------------------------
// 1. ログファイルを読み込む
// ---------------------------------------------------------------------
// file() 関数は、ファイルの中身を「1行ずつの要素を持つ配列」として
// 読み込んでくれる便利な関数です。
//   FILE_IGNORE_NEW_LINES -> 各行の末尾についている改行文字(\n)を
//                            取り除いた状態で配列に入れる
//   FILE_SKIP_EMPTY_LINES -> 中身が空の行(空行)を配列に含めない
echo "----- 1. ログファイルを読み込む -----" . PHP_EOL;

// TODO: file() 関数を使って $logFilePath の中身を1行ずつの配列として
//       読み込み、$lines に代入してください。
//       第2引数には FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES を
//       指定してください。
// $lines = file($logFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$lines = [];

if (empty($lines)) {
    echo "読み込んだ行数: まだ実装されていません" . PHP_EOL;
} else {
    echo "読み込んだ行数: " . count($lines) . "行" . PHP_EOL;
}
echo PHP_EOL;


// ---------------------------------------------------------------------
// 2. まずは1行だけで正規表現をテストしてみる
// ---------------------------------------------------------------------
// 正規表現は、少しでも書き方が実際のログの形式とずれていると
// マッチしなくなってしまいます。そのため、いきなり全部の行を
// 処理するのではなく、まずは1行だけを使ってパターンが正しく
// マッチするかを確認する、というのがデバッグの定石です。
echo "----- 2. 正規表現を1行だけでテストする -----" . PHP_EOL;

if (empty($lines)) {
    echo "1.が未実装のため、テストできません。" . PHP_EOL;
} else {
    $testLine = $lines[0];
    echo "テスト対象の行:" . PHP_EOL;
    echo "  {$testLine}" . PHP_EOL;

    // TODO: preg_match($pattern, $testLine, $testMatches) を呼び出して
    //       $testLine が $pattern にマッチするか調べ、結果を
    //       $testResult に、マッチしたグループを $testMatches に
    //       格納してください。
    // $testResult = preg_match($pattern, $testLine, $testMatches);
    $testResult = 0;
    $testMatches = [];

    if ($testResult === 1) {
        echo "マッチ結果:" . PHP_EOL;
        echo "  IPアドレス     : {$testMatches[1]}" . PHP_EOL;
        echo "  日時           : {$testMatches[2]}" . PHP_EOL;
        echo "  メソッド       : {$testMatches[3]}" . PHP_EOL;
        echo "  パス           : {$testMatches[4]}" . PHP_EOL;
        echo "  ステータスコード: {$testMatches[5]}" . PHP_EOL;
        echo "  バイト数       : {$testMatches[6]}" . PHP_EOL;
    } else {
        echo "マッチ結果: まだ実装されていません" . PHP_EOL;
    }
}
echo PHP_EOL;


// ---------------------------------------------------------------------
// 3. 全行を処理して、必要な情報を配列にまとめる
// ---------------------------------------------------------------------
// 2.でパターンが正しく書けていることを確認できたら、今度は
// foreach で全行を1行ずつ処理していきます。1行ごとに preg_match() を
// 呼び出し、マッチした場合だけ「1件分のログ情報」を連想配列として
// $records に追加していきます(マッチしなかった行は件数だけ数えます)。
echo "----- 3. 全行を解析する -----" . PHP_EOL;

$records = [];       // 解析に成功した1行ずつの情報を溜めていく配列
$unparsedCount = 0;  // 正規表現にマッチしなかった行の数

// TODO: 以下の foreach の中身を実装してください。
//   1. $matches = [] を用意する
//   2. preg_match($pattern, $line, $matches) でマッチを試みる
//   3. マッチした(戻り値が1)場合は、$matches[1]〜$matches[6] を
//      "ip", "datetime", "method", "path", "status", "bytes" という
//      キー名の連想配列にまとめて $records[] に追加する
//      (バイト数は (int) を付けて整数に変換しておきましょう)
//   4. マッチしなかった場合は $unparsedCount を1増やす
foreach ($lines as $line) {
    // $matches = [];
    // $isMatched = preg_match($pattern, $line, $matches);
    //
    // if ($isMatched === 1) {
    //     $records[] = [
    //         "ip"       => $matches[1],
    //         "datetime" => $matches[2],
    //         "method"   => $matches[3],
    //         "path"     => $matches[4],
    //         "status"   => $matches[5],
    //         "bytes"    => (int) $matches[6],
    //     ];
    // } else {
    //     $unparsedCount++;
    // }
}

if (empty($records)) {
    echo "解析に成功した行数  : まだ実装されていません" . PHP_EOL;
    echo "解析できなかった行数: まだ実装されていません" . PHP_EOL;
} else {
    echo "解析に成功した行数  : " . count($records) . "行" . PHP_EOL;
    echo "解析できなかった行数: {$unparsedCount}行" . PHP_EOL;
}
echo PHP_EOL;


// ---------------------------------------------------------------------
// 4. ステータスコードごとのアクセス数を集計する
// ---------------------------------------------------------------------
// ここで使うのが「連想配列をカウンタとして使う」というパターンです。
//   $statusCounts[$status] = ($statusCounts[$status] ?? 0) + 1;
// という1行は、「$statusCounts[$status] がまだ存在しなければ 0 として
// 扱い、そこに1を足した値をあらためて代入する」という意味です。
// "??"(null合体演算子)を使うことで、if文を書かなくても安全に
// 件数を増やしていけます。
echo "----- 4. ステータスコードごとの集計 -----" . PHP_EOL;

$statusCounts = [];

// TODO: $records を foreach で回し、$record["status"] を使って
//       $statusCounts にステータスコードごとの件数を集計してください。
// foreach ($records as $record) {
//     $status = $record["status"];
//     $statusCounts[$status] = ($statusCounts[$status] ?? 0) + 1;
// }

if (empty($statusCounts)) {
    echo "  まだ実装されていません" . PHP_EOL;
} else {
    // ksort() でキー(ステータスコード)の昇順に並び替えてから表示する
    ksort($statusCounts);
    foreach ($statusCounts as $status => $count) {
        echo "  {$status}: {$count}件" . PHP_EOL;
    }
}
echo PHP_EOL;


// ---------------------------------------------------------------------
// 5. IPアドレスごとのアクセス数を集計し、上位5件のランキングを作る
// ---------------------------------------------------------------------
// 考え方は4.のステータスコード集計とまったく同じです。
// 「IPアドレスをキーにしたカウンタ」を作り、出現するたびに+1します。
echo "----- 5. IPアドレスごとのアクセスランキング(上位5件) -----" . PHP_EOL;

$ipCounts = [];

// TODO: $records を foreach で回し、$record["ip"] を使って
//       $ipCounts にIPアドレスごとの件数を集計してください。
//       (書き方は4.のステータスコード集計と同じパターンです)
// foreach ($records as $record) {
//     $ip = $record["ip"];
//     $ipCounts[$ip] = ($ipCounts[$ip] ?? 0) + 1;
// }

if (empty($ipCounts)) {
    echo "  まだ実装されていません" . PHP_EOL;
} else {
    // TODO: arsort() を使って $ipCounts を「値の大きい順」に
    //       並び替えてください。arsort() はキーとの対応を保ったまま
    //       値の降順に並び替えてくれます。
    // arsort($ipCounts);

    // TODO: array_slice() を使って、$ipCounts の先頭(アクセス数が
    //       多い方)から5件だけを取り出し、$topIps に代入してください。
    //       第4引数に true を指定して、IPアドレスのキーを保持しましょう。
    // $topIps = array_slice($ipCounts, 0, 5, true);
    $topIps = [];

    if (empty($topIps)) {
        echo "  まだ実装されていません(arsort/array_sliceを実装してください)" . PHP_EOL;
    } else {
        $rank = 1;
        foreach ($topIps as $ip => $count) {
            echo "  {$rank}位: {$ip} ({$count}件)" . PHP_EOL;
            $rank++;
        }
    }
}
echo PHP_EOL;


// ---------------------------------------------------------------------
// 6. preg_match_all() でファイル全体を一度に処理する(参考)
// ---------------------------------------------------------------------
// ここまでは preg_match() を「1行ずつ」呼び出してきました。
// これに対して preg_match_all() は、渡された文字列の中から
// パターンにマッチする部分を「すべて」まとめて探し出してくれる関数です。
// file_get_contents() でログファイル全体を1つの文字列として読み込み、
// その文字列全体に対して preg_match_all() を1回呼び出すだけで、
// 3.でforeachを回しながら1行ずつ集めたのと同じ数の結果が
// 得られることを確認してみましょう。
//
// (補足: $pattern の末尾についている "m" 修飾子がここで重要になります。
//  "m" が無いと、"^" は「文字列全体の一番最初」にしかマッチしないため、
//  複数行を含む $rawLogText の2行目以降がうまく拾えません。
//  "m" を付けることで、"^" が「各行の先頭」にマッチするようになります)
echo "----- 6. preg_match_allで全体を一括抽出する(参考) -----" . PHP_EOL;

// TODO: file_get_contents($logFilePath) を使って、ログファイル全体を
//       1つの文字列として $rawLogText に読み込んでください。
// $rawLogText = file_get_contents($logFilePath);
$rawLogText = "";

if ($rawLogText === "") {
    echo "  まだ実装されていません" . PHP_EOL;
} else {
    // TODO: preg_match_all($pattern, $rawLogText, $allMatches) を使って、
    //       $rawLogText 全体からマッチする部分をすべて取り出し、
    //       マッチした件数を $matchCount に代入してください。
    // $matchCount = preg_match_all($pattern, $rawLogText, $allMatches);
    $matchCount = 0;

    echo "preg_match_allで一致した件数: " . ($matchCount > 0 ? "{$matchCount}件" : "まだ実装されていません") . PHP_EOL;
}
echo PHP_EOL;

echo "===== 演習16はここまでです =====" . PHP_EOL;

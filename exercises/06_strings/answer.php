<?php

// =====================================================================
// 演習06: 文字列操作 (模範解答)
// =====================================================================
// このファイルは、practice.php の TODO をすべて実装した「完成版」です。
// 上から順番にコメントを読みながら、どんな考え方でコードを書いているか
// 確認してみてください。
// =====================================================================


// ---------------------------------------------------------------------
// 0. ログの1行を表す文字列を用意する
// ---------------------------------------------------------------------
// サーバーのログファイルには、こういった形式の行がよく登場します。
// "日付 時刻 [レベル] メッセージ" という並びになっています。
// このような「決まったフォーマットの1行」から必要な情報を取り出すのが
// 今回の演習のテーマです。
$logLine = "2024-01-15 10:23:45 [ERROR] Disk full on server01";

echo "----- 0. 元のログ行 -----" . PHP_EOL;
echo $logLine . PHP_EOL;
echo PHP_EOL;


// ---------------------------------------------------------------------
// 1. explode() を使って分割する
// ---------------------------------------------------------------------
// explode(区切り文字, 文字列) は、文字列を指定した区切り文字で分割して
// 配列を作る関数です。ここでは半角スペース " " を区切り文字にします。
//
// $logLine を半角スペースで分割すると、次のような配列になります。
//   [0] => "2024-01-15"
//   [1] => "10:23:45"
//   [2] => "[ERROR]"
//   [3] => "Disk"
//   [4] => "full"
//   [5] => "on"
//   [6] => "server01"
//
// メッセージ("Disk full on server01")の中にもスペースが含まれているため、
// 4番目以降の要素がバラバラに分かれてしまう点に注目してください。
// これが「explode() だけでは複雑なパターンに対応しづらい」一例です。
$parts = explode(" ", $logLine);

echo "----- 1. explode() による分割 -----" . PHP_EOL;

// 分割直後の配列の中身を確認してみましょう(学習用の表示です)。
echo "分割結果(配列):" . PHP_EOL;
foreach ($parts as $index => $part) {
    echo "  [{$index}] => \"{$part}\"" . PHP_EOL;
}

// $parts[0] が日付、$parts[1] が時刻です。
$explodedDate = $parts[0];
$explodedTime = $parts[1];

// $parts[2] には "[ERROR]" のように角カッコが付いた状態で
// レベルが入っています。str_replace() を使って "[" と "]" を
// 空文字列("")に置き換える(=取り除く)ことでレベル名だけにします。
// str_replace() の第1引数・第2引数には配列を渡すこともでき、
// その場合は複数の文字を一度に置き換えられます。
$explodedLevel = str_replace(["[", "]"], "", $parts[2]);

// $parts の4番目以降(添字3以降)がメッセージの各単語です。
// array_slice($parts, 3) で「添字3以降の要素だけ」を取り出し、
// implode(" ", ...) でスペースを挟みながら1つの文字列に結合します。
// implode() は explode() のちょうど逆の役割(配列 → 文字列)を持つ関数です。
$messageParts = array_slice($parts, 3);
$explodedMessage = implode(" ", $messageParts);

echo PHP_EOL;
echo "取り出した値:" . PHP_EOL;
echo "日付: {$explodedDate}" . PHP_EOL;
echo "時刻: {$explodedTime}" . PHP_EOL;
echo "レベル: {$explodedLevel}" . PHP_EOL;
echo "メッセージ: {$explodedMessage}" . PHP_EOL;
echo PHP_EOL;


// ---------------------------------------------------------------------
// 2. preg_match() を使って一度に取り出す
// ---------------------------------------------------------------------
// preg_match(パターン, 対象の文字列, 結果を格納する変数) を使うと、
// 正規表現のパターンに一致する部分を一度に取り出すことができます。
//
// 今回使う正規表現は次の通りです。
//   '/^(\S+) (\S+) \[(\w+)\] (.+)$/'
//
// 1文字ずつ分解すると、次のような意味になります。
//   /        ... 正規表現の開始・終了を表す区切り記号
//   ^        ... 文字列の先頭
//   (\S+)    ... 空白以外の文字が1文字以上連続する部分(1つ目のグループ) → 日付
//   (半角スペース)
//   (\S+)    ... 空白以外の文字が1文字以上連続する部分(2つ目のグループ) → 時刻
//   (半角スペース)
//   \[       ... "[" という文字そのもの("["は特殊記号として使われることが
//                あるため、\ を付けてエスケープ=普通の文字として扱っています)
//   (\w+)    ... 英数字・アンダースコアが1文字以上連続する部分(3つ目のグループ) → レベル
//   \]       ... "]" という文字そのもの
//   (半角スペース)
//   (.+)     ... 任意の文字が1文字以上連続する部分(4つ目のグループ) → メッセージ
//   $        ... 文字列の末尾
//   /        ... 正規表現の終了
//
// "()" で囲んだ部分は「グループ」と呼ばれ、preg_match() の第3引数に
// 渡した変数(ここでは $matches)に、マッチした部分文字列がまとめて
// 格納されます。
//   $matches[0] => マッチした文字列全体
//   $matches[1] => 1つ目のグループ(日付)
//   $matches[2] => 2つ目のグループ(時刻)
//   $matches[3] => 3つ目のグループ(レベル)
//   $matches[4] => 4つ目のグループ(メッセージ)
echo "----- 2. preg_match() による抽出 -----" . PHP_EOL;

$pattern = '/^(\S+) (\S+) \[(\w+)\] (.+)$/';

// preg_match() の戻り値は「マッチした回数」で、
// 今回のようにパターン全体が1回マッチすれば 1、
// マッチしなければ 0、エラーが起きた場合は false になります。
// そのため if 文の条件式にそのまま使うことができます。
$matchCount = preg_match($pattern, $logLine, $matches);

if ($matchCount === 1) {
    $regexDate = $matches[1];
    $regexTime = $matches[2];
    $regexLevel = $matches[3];
    $regexMessage = $matches[4];

    echo "マッチしました(preg_matchの戻り値: {$matchCount})" . PHP_EOL;
    echo "日付: {$regexDate}" . PHP_EOL;
    echo "時刻: {$regexTime}" . PHP_EOL;
    echo "レベル: {$regexLevel}" . PHP_EOL;
    echo "メッセージ: {$regexMessage}" . PHP_EOL;
} else {
    // ログの形式が想定と異なる場合など、マッチしなかったときの
    // 対処もあらかじめ考えておくと安全なコードになります。
    echo "パターンにマッチしませんでした。" . PHP_EOL;
    $regexDate = "";
    $regexTime = "";
    $regexLevel = "";
    $regexMessage = "";
}
echo PHP_EOL;


// ---------------------------------------------------------------------
// 3. 2つの方法の結果を比較する
// ---------------------------------------------------------------------
// explode() を使って取り出した値と、preg_match() を使って取り出した値が
// 同じ内容になっているかどうかを、項目ごとに比較してみましょう。
// 文字列の比較には、型と値の両方が一致することを確認する
// 厳密な比較演算子 "===" を使うのが安全です。
echo "----- 3. 2つの方法の結果比較 -----" . PHP_EOL;

$dateMatches = ($explodedDate === $regexDate);
$timeMatches = ($explodedTime === $regexTime);
$levelMatches = ($explodedLevel === $regexLevel);
$messageMatches = ($explodedMessage === $regexMessage);

// 三項演算子(条件 ? 真の場合 : 偽の場合)を使うと、
// 条件によって表示する文字列を簡潔に切り替えられます。
echo "日付   : explode=\"{$explodedDate}\" / preg_match=\"{$regexDate}\" -> " . ($dateMatches ? "OK" : "NG") . PHP_EOL;
echo "時刻   : explode=\"{$explodedTime}\" / preg_match=\"{$regexTime}\" -> " . ($timeMatches ? "OK" : "NG") . PHP_EOL;
echo "レベル : explode=\"{$explodedLevel}\" / preg_match=\"{$regexLevel}\" -> " . ($levelMatches ? "OK" : "NG") . PHP_EOL;
echo "メッセージ: explode=\"{$explodedMessage}\" / preg_match=\"{$regexMessage}\" -> " . ($messageMatches ? "OK" : "NG") . PHP_EOL;

// すべての項目が一致していれば、全体としても「同じ結果」と言えます。
$allMatch = $dateMatches && $timeMatches && $levelMatches && $messageMatches;
echo PHP_EOL;
if ($allMatch) {
    echo "=> explode()の組み合わせとpreg_match()、どちらの方法でも同じ結果が得られました。" . PHP_EOL;
} else {
    echo "=> 2つの方法で結果が異なる項目があります。" . PHP_EOL;
}
echo PHP_EOL;


// ---------------------------------------------------------------------
// 4. sprintf() で見やすい形式に整形する
// ---------------------------------------------------------------------
// sprintf(書式文字列, 値1, 値2, ...) は、書式文字列の中の "%s" や "%d" と
// いった「プレースホルダー(あとで値が当てはめられる場所)」に、
// 続く引数を順番に当てはめた新しい文字列を作る関数です。
// echo とは違い、画面には表示せず「文字列を作って返す」だけなので、
// 変数に代入したり、あとでまとめて出力したりできるのが特徴です。
//
// プレースホルダーの主な種類:
//   %s -> 文字列(string)として当てはめる
//   %d -> 整数(decimal, integer)として当てはめる
//
// ここでは preg_match() で取り出した値を使って、
// "[レベル] 日付 時刻 - メッセージ" という形式に整形します。
echo "----- 4. sprintf() による整形出力 -----" . PHP_EOL;

$formatted = sprintf(
    "[%s] %s %s - %s",
    $regexLevel,
    $regexDate,
    $regexTime,
    $regexMessage
);

echo $formatted . PHP_EOL;
echo PHP_EOL;

// 参考: printf() は sprintf() とほぼ同じ書式指定を使えますが、
// 文字列を作って「返す」のではなく、その場で直接「画面に出力する」
// 関数です。整形した文字列をすぐ表示したいだけなら printf() の方が
// 1行で済みます。
echo "(参考) printf()で直接出力する場合:" . PHP_EOL;
printf("[%s] %s %s - %s%s", $regexLevel, $regexDate, $regexTime, $regexMessage, PHP_EOL);

<?php

// =====================================================================
// 演習07: 日付・時刻操作
// =====================================================================
// このファイルは「未完成のひな形」です。
// "// TODO:" と書かれている部分を自分で実装してください。
// まだ実装していない部分は「まだ実装されていません」と表示されるようになっているので、
// このままの状態でも php コマンドでエラーなく実行できます。
// =====================================================================


// ---------------------------------------------------------------------
// 0. タイムゾーンを明示的に設定する
// ---------------------------------------------------------------------
// PHPで日時を扱うときは「タイムゾーン(どの地域の時刻として計算するか)」
// を意識する必要があります。タイムゾーンを設定しないままだと、
// サーバーの設定(php.iniのdate.timezoneなど)に依存してしまい、
// 「自分のPCでは正しい時刻に見えるのに、本番サーバーでは9時間ずれる」
// といったトラブルの原因になります。
// date_default_timezone_set() で、これ以降の日時処理すべてに使われる
// 標準のタイムゾーンを「日本標準時(Asia/Tokyo)」に固定しています。
date_default_timezone_set("Asia/Tokyo");

echo "===== 演習07: 日付・時刻操作 =====" . PHP_EOL;
echo PHP_EOL;


// ---------------------------------------------------------------------
// 1. date() 関数で現在時刻を取得する
// ---------------------------------------------------------------------
// date(書式, [タイムスタンプ]) は、PHPの中でもっとも手軽に日時の
// 文字列を作れる関数です。第2引数を省略すると「現在時刻」が使われます。
// 書式に使う主なアルファベットは次の通りです。
//   Y -> 西暦4桁(例: 2026)   m -> 月(2桁, 例: 09)
//   d -> 日(2桁, 例: 01)     H -> 時(24時間表記, 2桁)
//   i -> 分(2桁)              s -> 秒(2桁)
// 大文字・小文字で意味が変わる点に注意してください
// (例: "m" は月ですが "M" は "Jan" のような月名の略称になります)。
echo "----- 1. date() 関数で現在時刻を取得 -----" . PHP_EOL;

// TODO: date() 関数を使って、現在時刻を "Y-m-d H:i:s" 形式の文字列で
//       取得し、$nowByDateFunction に代入してください。
// $nowByDateFunction = date("Y-m-d H:i:s");
$nowByDateFunction = "まだ実装されていません";

echo "date()で取得した現在時刻    : {$nowByDateFunction}" . PHP_EOL;
echo PHP_EOL;


// ---------------------------------------------------------------------
// 2. DateTimeクラスで現在時刻を取得する
// ---------------------------------------------------------------------
// DateTimeクラスは、日時を「オブジェクト」として扱うためのクラスです。
// new DateTime() (引数なし)で「現在時刻」を表すオブジェクトが作られます。
// 出来上がったオブジェクトの ->format(書式) メソッドを呼ぶと、
// date() 関数と同じ書式指定を使って文字列に変換できます。
// つまり、
//   date("Y-m-d H:i:s")            <-> 関数を1回呼ぶだけの手軽な方法
//   (new DateTime())->format(...)  <-> 日時をオブジェクトとして持ち回れる方法
// という違いがあります。オブジェクトとして持てるので、あとで紹介する
// diff() のような「2つの日時を比べる」処理は DateTime クラスの方が
// 得意です。
echo "----- 2. DateTimeクラスで現在時刻を取得 -----" . PHP_EOL;

// TODO: new DateTime() で現在時刻を表すオブジェクトを作り、
//       $nowByDateTime に代入してください。
// $nowByDateTime = new DateTime();
$nowByDateTime = null;

// TODO: $nowByDateTime->format("Y-m-d H:i:s") を使って、
//       下の echo で表示できるようにしてください。
//       (ヒント: $nowByDateTime が null のままだとエラーになるので、
//        上のTODOを先に実装してください)
if ($nowByDateTime === null) {
    echo "DateTimeで取得した現在時刻  : まだ実装されていません" . PHP_EOL;
} else {
    echo "DateTimeで取得した現在時刻  : " . $nowByDateTime->format("Y-m-d H:i:s") . PHP_EOL;
}
echo PHP_EOL;


// ---------------------------------------------------------------------
// 3. DateTimeZoneで明示的にタイムゾーンを指定して比較する
// ---------------------------------------------------------------------
// DateTimeZoneクラスを使うと、「この日時はどの地域の時刻なのか」を
// 明示的に指定できます。new DateTime() の第2引数にDateTimeZoneの
// インスタンスを渡すことで、date_default_timezone_set() で設定した
// 標準のタイムゾーンとは別に、特定の地域の時刻を作ることもできます。
// ここでは「日本標準時(Asia/Tokyo)」と「協定世界時(UTC)」で
// 同じ瞬間がどう見えるかを比較してみます。
// (日本標準時はUTCより9時間進んでいます)
echo "----- 3. DateTimeZoneでタイムゾーンを比較 -----" . PHP_EOL;

// TODO: new DateTimeZone("Asia/Tokyo") と new DateTimeZone("UTC") を
//       使って、$tokyoTimeZone と $utcTimeZone を作成してください。
// $tokyoTimeZone = new DateTimeZone("Asia/Tokyo");
// $utcTimeZone = new DateTimeZone("UTC");
$tokyoTimeZone = null;
$utcTimeZone = null;

// TODO: new DateTime("now", タイムゾーン) を使って、
//       $nowInTokyo と $nowInUtc を作成してください。
// $nowInTokyo = new DateTime("now", $tokyoTimeZone);
// $nowInUtc = new DateTime("now", $utcTimeZone);
$nowInTokyo = null;
$nowInUtc = null;

if ($nowInTokyo === null || $nowInUtc === null) {
    echo "まだ実装されていません" . PHP_EOL;
} else {
    echo "日本時間(Asia/Tokyo)       : " . $nowInTokyo->format("Y-m-d H:i:s") . PHP_EOL;
    echo "協定世界時(UTC)            : " . $nowInUtc->format("Y-m-d H:i:s") . PHP_EOL;
    echo "=> 同じ瞬間でも、タイムゾーンによって表示される時刻が変わります。" . PHP_EOL;
}
echo PHP_EOL;


// ---------------------------------------------------------------------
// 4. DateTime::diff() でサーバーの稼働時間(アップタイム)を計算する
// ---------------------------------------------------------------------
// サーバーの運用管理では「このサーバーはいつから動き続けているか」を
// 表す「アップタイム(稼働時間)」がよく話題になります。
// ここでは、サーバーが起動した時刻を表す固定の文字列を用意し、
// 現在時刻との差分を計算してアップタイム風のメッセージを作ります。
//
// new DateTime("文字列") のように文字列を渡すと、その文字列を解析して
// 日時のオブジェクトを作ってくれます("2024-01-01 09:00:00" のような
// よくある形式であれば、特別な指定をしなくても正しく解釈されます)。
//
// $起点となるDateTime->diff($比較したいDateTime) を呼び出すと、
// 2つの日時の差分を表す DateInterval オブジェクトが返ってきます。
// DateInterval は「y(年)」「m(月)」「d(日)」「h(時)」「i(分)」
// 「s(秒)」といったプロパティを持っており、差分を単位ごとに
// 取り出すことができます。
echo "----- 4. DateTime::diff() でサーバーの稼働時間を計算 -----" . PHP_EOL;

// サーバーの起動時刻(固定の過去日時)を表すDateTimeオブジェクト。
// 実際の運用では、この値はサーバー起動時にログへ記録しておいたり、
// OSの起動時刻(uptimeコマンドなど)から取得したりします。
$serverStartTime = new DateTime("2024-01-01 09:00:00");

// 現在時刻を表すDateTimeオブジェクト(1.や2.で使ったものと同様です)。
$currentTime = new DateTime();

echo "サーバー起動時刻: " . $serverStartTime->format("Y-m-d H:i:s") . PHP_EOL;
echo "現在時刻        : " . $currentTime->format("Y-m-d H:i:s") . PHP_EOL;

// TODO: $serverStartTime->diff($currentTime) を使って、
//       2つの日時の差分を表す DateInterval オブジェクトを取得し、
//       $uptimeInterval に代入してください。
// $uptimeInterval = $serverStartTime->diff($currentTime);
$uptimeInterval = null;

echo PHP_EOL;
if ($uptimeInterval === null) {
    echo "diff()の結果(DateIntervalオブジェクトの中身): まだ実装されていません" . PHP_EOL;
    echo PHP_EOL;
    echo "サーバーのアップタイム: まだ実装されていません" . PHP_EOL;
} else {
    // DateIntervalオブジェクトの主なプロパティ:
    //   ->y -> 差分の「年」の部分   ->m -> 差分の「月」の部分
    //   ->d -> 差分の「日」の部分   ->h -> 差分の「時」の部分
    //   ->i -> 差分の「分」の部分   ->s -> 差分の「秒」の部分
    //   ->days -> 差分の合計日数(年月日をすべて日数換算した値)
    echo "diff()の結果(DateIntervalオブジェクトの中身):" . PHP_EOL;
    echo "  年 (y): {$uptimeInterval->y}" . PHP_EOL;
    echo "  月 (m): {$uptimeInterval->m}" . PHP_EOL;
    echo "  日 (d): {$uptimeInterval->d}" . PHP_EOL;
    echo "  時 (h): {$uptimeInterval->h}" . PHP_EOL;
    echo "  分 (i): {$uptimeInterval->i}" . PHP_EOL;
    echo "  合計日数 (days): {$uptimeInterval->days}" . PHP_EOL;

    // TODO: sprintf() を使って「稼働日数: n日 n時間 n分」の形式の
    //       文字列を作り、$uptimeMessage に代入してください。
    //       ($uptimeInterval->days, ->h, ->i を使います)
    // $uptimeMessage = sprintf(
    //     "稼働日数: %d日 %d時間 %d分",
    //     $uptimeInterval->days,
    //     $uptimeInterval->h,
    //     $uptimeInterval->i
    // );
    $uptimeMessage = "まだ実装されていません";

    echo PHP_EOL;
    echo "サーバーのアップタイム: {$uptimeMessage}" . PHP_EOL;
}
echo PHP_EOL;


// ---------------------------------------------------------------------
// 5. strtotime() でログのタイムスタンプ文字列をUNIXタイムスタンプに変換する
// ---------------------------------------------------------------------
// ログファイルの中に書かれている日時は、多くの場合「文字列」です。
// strtotime(文字列) を使うと、人間が読みやすい日時の文字列を
// 「UNIXタイムスタンプ(1970年1月1日からの経過秒数を表す整数)」に
// 変換できます。UNIXタイムスタンプに変換しておくと、
// 「2つの日時のどちらが新しいか比較する」「差分の秒数を計算する」
// といった処理が簡単になります。
echo "----- 5. strtotime() でログのタイムスタンプを変換 -----" . PHP_EOL;

// サーバーのログでよく見かける形式のタイムスタンプ文字列。
$logTimestampText = "2024-06-15 08:30:00";

echo "ログのタイムスタンプ(文字列): {$logTimestampText}" . PHP_EOL;

// TODO: strtotime($logTimestampText) を使って、UNIXタイムスタンプに
//       変換し、$unixTimestamp に代入してください。
//       (strtotime()は変換に失敗すると false を返します)
// $unixTimestamp = strtotime($logTimestampText);
$unixTimestamp = false;

if ($unixTimestamp === false) {
    echo "UNIXタイムスタンプに変換: まだ実装されていません" . PHP_EOL;
    echo "date()で別フォーマットに整形: まだ実装されていません" . PHP_EOL;
} else {
    echo "UNIXタイムスタンプに変換: {$unixTimestamp}" . PHP_EOL;

    // TODO: date(書式, $unixTimestamp) を使って、$unixTimestamp が
    //       表す日時を "Y年m月d日 H時i分" 形式の文字列に整形し、
    //       $reformatted に代入してください。
    // $reformatted = date("Y年m月d日 H時i分", $unixTimestamp);
    $reformatted = "まだ実装されていません";

    echo "date()で別フォーマットに整形: {$reformatted}" . PHP_EOL;
}
echo PHP_EOL;


// ---------------------------------------------------------------------
// 6. strtotime() は相対的な表現も解釈できる(参考)
// ---------------------------------------------------------------------
// strtotime() は "2024-06-15 08:30:00" のような具体的な日時だけでなく、
// "+1 day"(1日後)や "-3 hours"(3時間前)、"next monday"(次の月曜日)
// のような「相対的な表現」も解釈できます。ログ調査などで
// 「1時間前のログを確認したい」といった処理を書くときに便利です。
echo "----- 6. strtotime()で相対的な表現を解釈する(参考) -----" . PHP_EOL;

if ($unixTimestamp === false) {
    echo "元のログ時刻の1時間前: まだ実装されていません" . PHP_EOL;
} else {
    // TODO: strtotime("-1 hour", $unixTimestamp) を使って、
    //       $unixTimestamp より1時間前のタイムスタンプを求め、
    //       $oneHourAgoTimestamp に代入してください。
    // $oneHourAgoTimestamp = strtotime("-1 hour", $unixTimestamp);
    $oneHourAgoTimestamp = false;

    if ($oneHourAgoTimestamp === false) {
        echo "元のログ時刻の1時間前: まだ実装されていません" . PHP_EOL;
    } else {
        $oneHourAgoText = date("Y-m-d H:i:s", $oneHourAgoTimestamp);
        echo "元のログ時刻の1時間前: {$oneHourAgoText}" . PHP_EOL;
    }
}
echo PHP_EOL;

echo "===== 演習07はここまでです =====" . PHP_EOL;

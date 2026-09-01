<?php

// =====================================================================
// 演習09: CSV・JSON操作
// =====================================================================
// このファイルは模範解答です。
// - fgetcsv() を使った CSV ファイルの読み込み
// - json_encode() を使った、配列 → JSON文字列への変換(CSV→JSON)
// - json_decode() を使った、JSON文字列 → 配列への変換
// - fputcsv() を使った、配列 → CSV ファイルへの書き出し(JSON→CSV)
// を、実際に動くコードで確認しながら、CSVとJSONの「往復変換」を
// 体験していきます。
// =====================================================================


// ---------------------------------------------------------------------
// 0. パスを準備する
// ---------------------------------------------------------------------
// 演習08と同じように、__DIR__(このファイルが置かれているディレクトリの
// 絶対パス)を使ってパスを組み立てます。こうしておくと、どの
// ディレクトリから php コマンドを実行しても、ファイルの場所が
// ぶれずに済みます。
$csvPath = __DIR__ . '/servers.csv';
$jsonPath = __DIR__ . '/servers.json';
$csvOutPath = __DIR__ . '/servers_out.csv';


// ---------------------------------------------------------------------
// 1. csvToArray(): CSVファイルを読み込み、連想配列の配列に変換する関数
// ---------------------------------------------------------------------
/**
 * CSVファイルを読み込み、1行目をヘッダーとして扱いながら
 * 「連想配列の配列」に変換する。
 *
 * 例えば以下のようなCSVがあった場合、
 *   name,ip,status
 *   web-01,192.168.1.11,running
 * 次のような配列に変換される。
 *   [
 *       ["name" => "web-01", "ip" => "192.168.1.11", "status" => "running"],
 *   ]
 *
 * @param string $path CSVファイルのパス
 * @return array 連想配列の配列(1要素が1サーバーの情報)
 */
function csvToArray(string $path): array
{
    // ファイルを開く前に、まず存在確認をしておきます。
    // (存在しないファイルに fopen() すると警告が出てしまうため)
    if (!file_exists($path)) {
        echo "CSVファイルが見つかりません: {$path}" . PHP_EOL;
        return [];
    }

    // fopen($path, 'r') で「読み込み専用」モードでファイルを開きます。
    $handle = fopen($path, 'r');

    if ($handle === false) {
        echo "CSVファイルを開けませんでした: {$path}" . PHP_EOL;
        return [];
    }

    $rows = [];
    $header = null;

    // fgetcsv($handle) は、CSVファイルを1行読み込んで、
    // カンマ区切りの値をあらかじめ「配列」に分解した状態で
    // 返してくれる、とても便利な関数です。
    // explode(",", $line) でも似たようなことはできますが、
    // 値の中にカンマや引用符(")が含まれるCSV特有のルールまで
    // 正しく解釈してくれるのは fgetcsv() ならではの利点です。
    //
    // ファイルの終端に達すると fgetcsv() は false を返すので、
    // while ループの条件としてそのまま利用できます。
    while (($columns = fgetcsv($handle)) !== false) {
        // 1行目(最初に読み込んだ行)は列名(ヘッダー)なので、
        // データとしてではなく「キーの名前一覧」として扱います。
        // 例: ["name", "ip", "status"]
        if ($header === null) {
            $header = $columns;
            continue;
        }

        // array_combine(キーの配列, 値の配列) を使うと、
        // ヘッダー行の値をキーとして、データ行の値と組み合わせた
        // 連想配列を簡単に作ることができます。
        // 例: array_combine(["name", "ip"], ["web-01", "192.168.1.11"])
        //     => ["name" => "web-01", "ip" => "192.168.1.11"]
        $rows[] = array_combine($header, $columns);
    }

    // 開いたファイルは必ず閉じます。
    fclose($handle);

    return $rows;
}


// ---------------------------------------------------------------------
// 2. arrayToJsonFile(): 配列をJSON文字列に変換してファイルに書き出す関数
// ---------------------------------------------------------------------
/**
 * 連想配列の配列を整形されたJSON文字列に変換し、ファイルに書き出す。
 *
 * @param array $data 書き出したいデータ(連想配列の配列)
 * @param string $path 書き出し先のJSONファイルのパス
 * @return bool 書き込みに成功したら true、失敗したら false
 */
function arrayToJsonFile(array $data, string $path): bool
{
    // json_encode() は、PHPの配列やオブジェクトをJSON形式の文字列に
    // 変換してくれる関数です。第2引数にはオプションのフラグを
    // ビット演算子 "|" で組み合わせて渡すことができます。
    //
    //   JSON_PRETTY_PRINT
    //     → 人間が読みやすいように、改行とインデントを付けて
    //       整形してくれます。付けない場合は1行にぎゅっと
    //       詰め込まれた読みにくいJSONになります。
    //
    //   JSON_UNESCAPED_UNICODE
    //     → これを付けないと、日本語などのマルチバイト文字が
    //       "サーバー" のような文字コード(\uXXXX形式)
    //       に変換されてしまい、人間には読めなくなってしまいます。
    //       このフラグを付けることで、日本語をそのままの文字で
    //       出力できます。
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    // json_encode() は、変換に失敗すると false を返します。
    // (例えば、変換できないリソース型のデータが含まれている場合など)
    if ($json === false) {
        echo 'JSONへの変換に失敗しました: ' . json_last_error_msg() . PHP_EOL;
        return false;
    }

    // file_put_contents() で、できあがったJSON文字列をそのまま
    // ファイルに書き込みます。第3引数を省略しているので、
    // 呼び出すたびに内容は上書きされます。
    $result = file_put_contents($path, $json);

    return $result !== false;
}


// ---------------------------------------------------------------------
// 3. jsonFileToArray(): JSONファイルを読み込み、配列に変換する関数
// ---------------------------------------------------------------------
/**
 * JSONファイルを読み込み、PHPの配列に変換する。
 *
 * @param string $path JSONファイルのパス
 * @return array 変換後の配列。読み込みや変換に失敗した場合は空配列。
 */
function jsonFileToArray(string $path): array
{
    if (!file_exists($path)) {
        echo "JSONファイルが見つかりません: {$path}" . PHP_EOL;
        return [];
    }

    // file_get_contents() でJSONファイルの中身をまるごと
    // 1つの文字列として読み込みます。
    $json = file_get_contents($path);

    if ($json === false) {
        echo "JSONファイルを読み込めませんでした: {$path}" . PHP_EOL;
        return [];
    }

    // json_decode($json, true) で、JSON文字列をPHPの値に変換します。
    // 第2引数に true を渡すのがポイントです。
    //   - true を渡す  → 連想配列として結果を受け取る
    //   - false(省略) → stdClass というオブジェクトとして受け取る
    // 連想配列のほうが foreach や $data['name'] のように
    // これまで使ってきた配列の書き方でそのまま扱えるので、
    // この演習では true を指定しています。
    $data = json_decode($json, true);

    // 変換に失敗した場合、json_decode() は null を返します。
    // json_last_error() で詳しいエラー原因を調べることができます。
    if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
        echo 'JSONの解析に失敗しました: ' . json_last_error_msg() . PHP_EOL;
        return [];
    }

    return $data;
}


// ---------------------------------------------------------------------
// 4. arrayToCsvFile(): 連想配列の配列をCSVファイルに書き出す関数
// ---------------------------------------------------------------------
/**
 * 連想配列の配列を、ヘッダー行付きのCSVファイルとして書き出す。
 *
 * @param array $rows 書き出したいデータ(連想配列の配列)
 * @param string $path 書き出し先のCSVファイルのパス
 * @return bool 書き込みに成功したら true、失敗したら false
 */
function arrayToCsvFile(array $rows, string $path): bool
{
    if (empty($rows)) {
        echo 'データが空のため、CSVを書き出せません。' . PHP_EOL;
        return false;
    }

    // 'w' モードでファイルを開きます。
    // 'w' は「書き込み専用。ファイルが既に存在する場合は中身を空にする」
    // という意味のモードです(演習08で扱った 'r' は読み込み専用でした)。
    $handle = fopen($path, 'w');

    if ($handle === false) {
        echo "CSVファイルを書き込み用に開けませんでした: {$path}" . PHP_EOL;
        return false;
    }

    // 1件目のデータのキー(name, ip, status など)を取り出して、
    // ヘッダー行として利用します。array_keys() は連想配列から
    // キーだけを取り出して配列にしてくれる関数です。
    $header = array_keys($rows[0]);

    // fputcsv($handle, $配列) は、渡した配列をカンマ区切りのCSVの
    // 1行として書き込んでくれる関数です。値の中にカンマや改行、
    // ダブルクォートが含まれていても、CSVのルールに従って自動的に
    // 正しくエスケープ(引用符で囲むなど)してくれます。
    fputcsv($handle, $header);

    // 続けて、データ本体を1行ずつ書き込んでいきます。
    foreach ($rows as $row) {
        fputcsv($handle, $row);
    }

    // 書き込みが終わったら必ずファイルを閉じます。
    fclose($handle);

    return true;
}


// =======================================================================
// ここから、実際に上で定義した関数を呼び出して動作を確認していきます。
// =======================================================================

echo '===== 1. CSV -> 配列 (fgetcsv) =====' . PHP_EOL;

// servers.csv を読み込み、連想配列の配列に変換します。
$servers = csvToArray($csvPath);

foreach ($servers as $index => $server) {
    $number = $index + 1;
    echo "{$number}: {$server['name']} / {$server['ip']} / {$server['status']}" . PHP_EOL;
}
echo PHP_EOL;


echo '===== 2. 配列 -> JSON文字列 -> ファイル書き出し (json_encode) =====' . PHP_EOL;

$saved = arrayToJsonFile($servers, $jsonPath);

if ($saved) {
    echo "servers.json に書き出しました: {$jsonPath}" . PHP_EOL;
    echo '----- servers.json の中身 -----' . PHP_EOL;
    // 書き出した内容をそのまま画面にも表示して、見た目を確認します。
    echo file_get_contents($jsonPath) . PHP_EOL;
} else {
    echo 'servers.json への書き出しに失敗しました。' . PHP_EOL;
}
echo PHP_EOL;


echo '===== 3. JSONファイル -> 配列 (json_decode) =====' . PHP_EOL;

// 今しがた書き出した servers.json を、今度は読み込み側として扱います。
// (CSVから作ったデータを一旦忘れて、JSONだけから読み直すイメージです)
$serversFromJson = jsonFileToArray($jsonPath);

echo 'servers.json から読み込んだ件数: ' . count($serversFromJson) . '件' . PHP_EOL;
foreach ($serversFromJson as $index => $server) {
    $number = $index + 1;
    echo "{$number}: {$server['name']} / {$server['ip']} / {$server['status']}" . PHP_EOL;
}
echo PHP_EOL;


echo '===== 4. 配列 -> CSVファイル (fputcsv) =====' . PHP_EOL;

// json_decode() で読み込んだデータを、今度は別名のCSVファイル
// servers_out.csv として書き出します。
// ここまでで「CSV → JSON → CSV」という往復変換が完了します。
$written = arrayToCsvFile($serversFromJson, $csvOutPath);

if ($written) {
    echo "servers_out.csv に書き出しました: {$csvOutPath}" . PHP_EOL;
    echo '----- servers_out.csv の中身 -----' . PHP_EOL;
    echo file_get_contents($csvOutPath);
} else {
    echo 'servers_out.csv への書き出しに失敗しました。' . PHP_EOL;
}
echo PHP_EOL;


echo '===== 5. 往復変換の結果を比較する =====' . PHP_EOL;

// 最初にCSVから読み込んだ $servers と、
// CSV -> JSON -> CSV -> 配列 という経路をたどって得られる
// 「もう一度CSVから読み込み直したデータ」を比較してみます。
// (fputcsvが書き出したCSVを、もう一度csvToArray()で読み直します)
$serversRoundTrip = csvToArray($csvOutPath);

// == ではなく === を使うと、配列の中身だけでなく型や順序まで含めて
// 厳密に同じかどうかを比較できます。連想配列の配列同士を比較する際にも
// そのまま使うことができます。
if ($servers === $serversRoundTrip) {
    echo 'OK: CSV -> JSON -> CSV と往復させても、内容は完全に一致しました。' . PHP_EOL;
} else {
    echo 'NG: 往復変換の前後でデータが変化してしまいました。' . PHP_EOL;
}

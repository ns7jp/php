<?php

// =====================================================================
// 演習09: CSV・JSON操作
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
// __DIR__ を使って、このファイルが置かれているディレクトリを基準に
// パスを組み立てます。相対パスに頼らないことで、どのディレクトリから
// 実行しても同じ結果になるようにします。
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
 * @param string $path CSVファイルのパス
 * @return array 連想配列の配列(1要素が1サーバーの情報)
 */
function csvToArray(string $path): array
{
    if (!file_exists($path)) {
        echo "CSVファイルが見つかりません: {$path}" . PHP_EOL;
        return [];
    }

    // TODO: fopen($path, 'r') で読み込み用にファイルを開いてください。
    // $handle = fopen($path, 'r');

    $rows = [];
    $header = null;

    // TODO: fgetcsv($handle) を while ループで繰り返し呼び出し、
    //       1行ずつ配列として読み込んでください。
    //       - 最初の1回(1行目)はヘッダー行なので $header に保存し、
    //         continue で次のループに進んでください。
    //       - 2行目以降は array_combine($header, $columns) で
    //         連想配列に変換し、$rows に追加してください。
    //       - fgetcsv() はファイルの終端で false を返すので、
    //         while (($columns = fgetcsv($handle)) !== false) { ... }
    //         のように書くとループの終了条件に使えます。
    // while (($columns = fgetcsv($handle)) !== false) {
    //     if ($header === null) {
    //         $header = $columns;
    //         continue;
    //     }
    //     $rows[] = array_combine($header, $columns);
    // }

    // TODO: 開いたファイルを fclose($handle) で閉じてください。
    // fclose($handle);

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
    // TODO: json_encode() を使って $data をJSON文字列に変換してください。
    //       第2引数には JSON_PRETTY_PRINT と JSON_UNESCAPED_UNICODE を
    //       "|" で組み合わせて渡してください。
    //       - JSON_PRETTY_PRINT      : 改行とインデントを付けて整形する
    //       - JSON_UNESCAPED_UNICODE : 日本語を \uXXXX にせず、
    //                                  そのままの文字で出力する
    // $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    // TODO: file_put_contents($path, $json) で、変換したJSON文字列を
    //       ファイルに書き込んでください。書き込み結果(バイト数、または
    //       失敗時は false)を判定に使ってください。
    // $result = file_put_contents($path, $json);
    // return $result !== false;

    echo '  (arrayToJsonFile はまだ実装されていません)' . PHP_EOL;
    return false;
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

    // TODO: file_get_contents($path) でJSONファイルの中身を
    //       文字列として読み込んでください。
    // $json = file_get_contents($path);

    // TODO: json_decode($json, true) を使って、JSON文字列を
    //       連想配列に変換してください。
    //       第2引数に true を渡すのがポイントです(true = 連想配列として
    //       受け取る。省略するとオブジェクトとして受け取ってしまいます)。
    // $data = json_decode($json, true);
    // return $data ?? [];

    echo '  (jsonFileToArray はまだ実装されていません)' . PHP_EOL;
    return [];
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

    // TODO: fopen($path, 'w') で書き込み用にファイルを開いてください。
    // $handle = fopen($path, 'w');

    // TODO: array_keys($rows[0]) でヘッダー行(列名の配列)を作り、
    //       fputcsv($handle, $header) で1行目として書き込んでください。
    // $header = array_keys($rows[0]);
    // fputcsv($handle, $header);

    // TODO: foreach ($rows as $row) { fputcsv($handle, $row); } で
    //       データ本体を1行ずつ書き込んでください。
    // foreach ($rows as $row) {
    //     fputcsv($handle, $row);
    // }

    // TODO: fclose($handle) でファイルを閉じてください。
    // fclose($handle);

    echo '  (arrayToCsvFile はまだ実装されていません)' . PHP_EOL;
    return false;
}


// =======================================================================
// ここから、実際に上で定義した関数を呼び出して動作を確認していきます。
// =======================================================================

echo '===== 1. CSV -> 配列 (fgetcsv) =====' . PHP_EOL;

$servers = csvToArray($csvPath);

if (empty($servers)) {
    echo 'まだ実装されていません(csvToArray() の結果が空です)' . PHP_EOL;
} else {
    foreach ($servers as $index => $server) {
        $number = $index + 1;
        echo "{$number}: {$server['name']} / {$server['ip']} / {$server['status']}" . PHP_EOL;
    }
}
echo PHP_EOL;


echo '===== 2. 配列 -> JSON文字列 -> ファイル書き出し (json_encode) =====' . PHP_EOL;

$saved = arrayToJsonFile($servers, $jsonPath);

if ($saved) {
    echo "servers.json に書き出しました: {$jsonPath}" . PHP_EOL;
    echo '----- servers.json の中身 -----' . PHP_EOL;
    echo file_get_contents($jsonPath) . PHP_EOL;
} else {
    echo 'まだ実装されていません(servers.json への書き出しができていません)' . PHP_EOL;
}
echo PHP_EOL;


echo '===== 3. JSONファイル -> 配列 (json_decode) =====' . PHP_EOL;

$serversFromJson = jsonFileToArray($jsonPath);

if (empty($serversFromJson)) {
    echo 'まだ実装されていません(jsonFileToArray() の結果が空です)' . PHP_EOL;
} else {
    echo 'servers.json から読み込んだ件数: ' . count($serversFromJson) . '件' . PHP_EOL;
    foreach ($serversFromJson as $index => $server) {
        $number = $index + 1;
        echo "{$number}: {$server['name']} / {$server['ip']} / {$server['status']}" . PHP_EOL;
    }
}
echo PHP_EOL;


echo '===== 4. 配列 -> CSVファイル (fputcsv) =====' . PHP_EOL;

$written = arrayToCsvFile($serversFromJson, $csvOutPath);

if ($written) {
    echo "servers_out.csv に書き出しました: {$csvOutPath}" . PHP_EOL;
    echo '----- servers_out.csv の中身 -----' . PHP_EOL;
    echo file_get_contents($csvOutPath);
} else {
    echo 'まだ実装されていません(servers_out.csv への書き出しができていません)' . PHP_EOL;
}
echo PHP_EOL;


echo '===== 5. 往復変換の結果を比較する =====' . PHP_EOL;

// TODO: csvToArray($csvOutPath) で servers_out.csv を読み直し、
//       最初に読み込んだ $servers と === で比較してみましょう。
//       すべて実装できていれば、内容が完全に一致するはずです。
// $serversRoundTrip = csvToArray($csvOutPath);
// if ($servers === $serversRoundTrip) {
//     echo 'OK: CSV -> JSON -> CSV と往復させても、内容は完全に一致しました。' . PHP_EOL;
// } else {
//     echo 'NG: 往復変換の前後でデータが変化してしまいました。' . PHP_EOL;
// }
echo 'まだ実装されていません' . PHP_EOL;

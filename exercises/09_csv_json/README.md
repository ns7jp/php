# 演習09: CSV・JSON操作

## この演習で学ぶこと

- `fgetcsv()` を使って、CSVファイルを1行ずつ配列として読み込める
- `fputcsv()` を使って、配列をCSVファイルの1行として書き出せる
- `json_encode()` を使って、PHPの配列をJSON文字列に変換できる
- `json_decode()` を使って、JSON文字列をPHPの配列に変換できる
- `JSON_PRETTY_PRINT` / `JSON_UNESCAPED_UNICODE` フラグの役割を理解する
- CSVとJSONを相互に変換する処理を自分の手で実装できるようになる

## 前提知識

- 演習08(ファイル読み書き)の内容を理解していること
  - `fopen` / `fclose` によるファイルの開閉
  - `file_exists` によるファイルの存在確認
  - `__DIR__` を使った安全なパスの組み立て方
- 演習01〜06で学んだ変数、配列(連想配列)、`foreach`、関数の基本

## 演習内容(要件)

サーバーの監視ツールや外部APIと連携するスクリプトでは、
「表計算ソフトで開けるCSV」と「プログラム同士でやり取りしやすいJSON」の
どちらの形式でもデータを扱えるようにしておく必要があります。
この演習では、その変換処理を実際に手を動かして実装します。

この演習フォルダには、あらかじめ次のようなサーバー一覧のサンプルCSV
`servers.csv` が同梱されています。

```
name,ip,status
web-01,192.168.1.11,running
web-02,192.168.1.12,running
db-01,192.168.1.21,stopped
cache-01,192.168.1.31,running
```

### 1. `csvToArray()` 関数を作る(CSV→配列)

以下のシグネチャを持つ関数 `csvToArray()` を実装してください。

```php
function csvToArray(string $path): array
```

`csvToArray()` は次の流れで処理を行います。

1. `fopen($path, 'r')` でCSVファイルを読み込み用に開く。
2. `fgetcsv($handle)` を使って1行ずつ配列として読み込む
   (`while (($columns = fgetcsv($handle)) !== false) { ... }`)。
3. 最初の1行(1行目)は列名(ヘッダー)として `$header` に保存する。
4. 2行目以降は `array_combine($header, $columns)` を使って、
   `["name" => "web-01", "ip" => "192.168.1.11", "status" => "running"]`
   のような連想配列に変換し、結果の配列に追加していく。
5. 最後に `fclose($handle)` でファイルを閉じ、完成した配列を `return` する。

### 2. `arrayToJsonFile()` 関数を作る(配列→JSON)

以下のシグネチャを持つ関数 `arrayToJsonFile()` を実装してください。

```php
function arrayToJsonFile(array $data, string $path): bool
```

1. `json_encode()` を使って `$data` をJSON文字列に変換する。
   第2引数には `JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE` を渡す。
2. `file_put_contents($path, $json)` で、変換した文字列をファイルに
   書き出す。

`csvToArray()` で読み込んだサーバー一覧をこの関数に渡し、
`servers.json` というファイル名で書き出してください。

### 3. `jsonFileToArray()` 関数を作る(JSON→配列)

以下のシグネチャを持つ関数 `jsonFileToArray()` を実装してください。

```php
function jsonFileToArray(string $path): array
```

1. `file_get_contents($path)` でJSONファイルの中身を文字列として
   読み込む。
2. `json_decode($json, true)` を使って、その文字列をPHPの連想配列に
   変換する(第2引数の `true` を忘れないこと)。

先ほど書き出した `servers.json` をこの関数で読み込み直してください。

### 4. `arrayToCsvFile()` 関数を作る(配列→CSV)

以下のシグネチャを持つ関数 `arrayToCsvFile()` を実装してください。

```php
function arrayToCsvFile(array $rows, string $path): bool
```

1. `fopen($path, 'w')` でCSVファイルを書き込み用に開く。
2. `array_keys($rows[0])` でヘッダー行(列名の配列)を作り、
   `fputcsv($handle, $header)` で1行目として書き込む。
3. `foreach ($rows as $row) { fputcsv($handle, $row); }` で、
   データ本体を1行ずつ書き込む。
4. `fclose($handle)` でファイルを閉じる。

`jsonFileToArray()` で読み込み直した配列をこの関数に渡し、
`servers_out.csv` というファイル名で書き出してください。

### 5. 往復変換ができていることを確認する

最初に `servers.csv` から読み込んだ配列と、
「CSV → JSON → CSV」と変換したあとにもう一度 `csvToArray()` で
読み込み直した配列を `===` で比較し、内容が完全に一致することを
確認してください。一致していれば、CSVとJSONの間で情報を失うことなく
相互変換できていることになります。

## 実行方法

ターミナル(コマンドライン)で、この演習のディレクトリに移動してから
以下のコマンドを実行してください。

```bash
cd exercises/09_csv_json

# 未完成のひな形を実行してみる(エラーにはならず、TODO部分だけ「まだ実装されていません」と表示されます)
php practice.php

# 模範解答を実行する
php answer.php
```

`practice.php` の中の `// TODO:` と書かれた部分を自分で実装してから、
もう一度 `php practice.php` を実行し、`answer.php` と同じような結果が
出るか確認してみましょう。

構文だけを素早く確認したいときは、次のコマンドも便利です。

```bash
php -l practice.php
```

`answer.php` や、実装を終えた `practice.php` を実行すると、同じフォルダに
`servers.json` と `servers_out.csv` というファイルが新しく作成されます。
`cat servers.json`(Windowsの場合は `type servers.json`)などで
中身を見てみましょう。何度実行しても、`servers.csv` の内容を元に
毎回同じ結果で上書きされます。

## ヒント

- `json_encode()` に `JSON_UNESCAPED_UNICODE` を付けないと、日本語が
  `"サーバー"` のような文字コード(`\uXXXX` 形式)で
  出力されてしまいます。人間が読めるJSONにしたい場合は必ず付けましょう。
- `json_decode()` の第2引数に `true` を渡すと、`stdClass` という
  オブジェクトではなく、これまで使い慣れた「連想配列」として結果を
  受け取れます。渡し忘れると `$data->name` のようにアロー演算子で
  アクセスする必要が出てきてしまうので注意しましょう。
- `fgetcsv()` は1行ずつ「配列」で返してくれるので、
  `explode(",", $line)` で自分でカンマ分割するより安全にCSVを
  扱えます。値の中にカンマや引用符(`"`)が含まれている場合でも、
  CSVのルールに従って正しく分解してくれます。
- CSVを書き出す `'w'` モードは「ファイルが既に存在する場合、中身を
  空にしてから書き込む」という意味です。演習08で使った `'r'`
  (読み込み専用)や `FILE_APPEND` の「追記」とは動きが異なるので、
  混同しないようにしましょう。
- `array_keys($rows[0])` で列名の一覧を取り出す前に、`$rows` が
  空でないことを確認しておくと安全です(空配列に対して `$rows[0]`
  を参照するとエラーになります)。

## サーバーエンジニアとの関連

監視ツールや外部APIとの連携では、CSV(表計算ソフトとの受け渡し)と
JSON(API通信)の相互変換が日常的に発生します。

- サーバーの死活監視ツールやクラウドの管理コンソールからは、
  「サーバー一覧をCSVでエクスポートする」機能が用意されていることが
  よくあります。そのCSVを別のシステムに取り込みたい場合、まずは
  この演習のように、CSV→連想配列→JSONという流れでデータを
  変換する処理が必要になります。
- 一方、多くのWeb APIやクラウドサービスのAPIは、リクエストや
  レスポンスの形式としてJSONを使います。API経由で取得したサーバー
  情報のJSONを、運用担当者がExcelなどの表計算ソフトで確認したい
  場合には、JSON→配列→CSVという逆方向の変換が必要になります。
- このように「同じデータを異なるフォーマット間で変換する」処理は、
  監視・運用・自動化のスクリプトを書くうえで非常によく登場します。
  今回学んだ `fgetcsv`/`fputcsv`/`json_encode`/`json_decode` の
  組み合わせは、そうした変換処理の基本形として、そのまま実務でも
  応用できる知識です。

## 発展課題

余裕がある人は、以下にも挑戦してみましょう。

- `servers.csv` に新しいサーバー(例: `app-01,192.168.1.41,running`)を
  1行追加してから `answer.php`(または実装済みの `practice.php`)を
  再実行し、`servers.json` と `servers_out.csv` の両方に、追加した
  サーバーがきちんと反映されることを確認してみましょう。
  さらに、`status` が `"running"` のサーバーだけを抽出して、
  `array_filter()` で絞り込んだ結果を `servers_running.json` として
  別ファイルに書き出す処理を追加してみるのもよい練習になります。

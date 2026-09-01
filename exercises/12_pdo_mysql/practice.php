<?php

// =====================================================================
// 演習12: PDOとデータベース連携
// =====================================================================
// このファイルは「未完成のひな形」です。
// "// TODO:" と書かれている部分を自分で実装してください。
// まだ実装していない部分は「まだ実装されていません」と表示されるように
// なっているので、このままの状態でも php コマンドでエラーなく
// 実行することができます。
//
// この演習では、PDOのSQLiteドライバを使って、
//   接続 -> テーブル作成 -> INSERT -> SELECT -> UPDATE -> DELETE
// という一連のCRUD操作を実装します。
// =====================================================================


// ---------------------------------------------------------------------
// 0. データベースファイルのパスを準備する
// ---------------------------------------------------------------------
$dbPath = __DIR__ . '/servers.db';

// 何度実行しても同じ結果になるように、実行の最初に前回のデータベース
// ファイルが残っていれば削除しておきます。
if (file_exists($dbPath)) {
    unlink($dbPath);
}


echo '===== 1. PDOでデータベースに接続する =====' . PHP_EOL;

$pdo = null;

// TODO: try/catch を使って、PDOでデータベースに接続してください。
//
//   try {
//       $pdo = new PDO('sqlite:' . $dbPath);
//       $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//       echo "データベースに接続しました: {$dbPath}" . PHP_EOL;
//   } catch (PDOException $e) {
//       echo 'データベースへの接続に失敗しました: ' . $e->getMessage() . PHP_EOL;
//       exit(1);
//   }
//
// ポイント:
//   - DSN(データソース名)は "sqlite:" . $dbPath のように、
//     ドライバ名 "sqlite:" のあとにファイルパスをつなげます。
//   - PDO::setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION) を
//     設定しておくと、エラー時に例外(PDOException)が投げられるように
//     なり、try/catch で扱いやすくなります。

if ($pdo === null) {
    echo '  (PDOでの接続はまだ実装されていません)' . PHP_EOL;
}
echo PHP_EOL;


echo '===== 2. テーブルを作成する (CREATE TABLE) =====' . PHP_EOL;

// TODO: $pdo->exec() を使って、servers テーブルを作成してください。
//       列は以下の4つです。
//         - id     : INTEGER PRIMARY KEY AUTOINCREMENT
//         - name   : TEXT
//         - ip     : TEXT
//         - status : TEXT
//
//   $pdo->exec(
//       'CREATE TABLE servers (
//           id INTEGER PRIMARY KEY AUTOINCREMENT,
//           name TEXT,
//           ip TEXT,
//           status TEXT
//       )'
//   );
//   echo 'servers テーブルを作成しました。' . PHP_EOL;

echo '  (テーブル作成はまだ実装されていません)' . PHP_EOL;
echo PHP_EOL;


echo '===== 3. プリペアドステートメントでデータを登録する (INSERT) =====' . PHP_EOL;

// ---- 悪い例(絶対に真似しないでください。実行はしません) ----
// SQL文字列にユーザー入力をそのまま連結してしまうと、
// SQLインジェクションと呼ばれる重大な脆弱性につながります。
// なぜなら、値の中に悪意のある文字列(例えば
// "'); DROP TABLE servers; --" のようなもの)が入っていた場合、
// SQL文の「構造」そのものが書き換えられてしまう可能性があるからです。
//
// $name = $_POST['name'];
// $sql = "INSERT INTO servers (name, ip, status) VALUES ('" . $name . "', '192.168.1.1', 'running')";
// $pdo->exec($sql); // 絶対にこの書き方をしないこと!
// -------------------------------------------------------------

// TODO: prepare() でINSERT用のSQL文のひな形を用意し、
//       execute([...]) でサーバーを2〜3件登録してください。
//
//   $insertStatement = $pdo->prepare(
//       'INSERT INTO servers (name, ip, status) VALUES (?, ?, ?)'
//   );
//   $newServers = [
//       ['web-01', '192.168.1.11', 'running'],
//       ['web-02', '192.168.1.12', 'running'],
//       ['db-01', '192.168.1.21', 'stopped'],
//   ];
//   foreach ($newServers as $server) {
//       $insertStatement->execute($server);
//       echo "登録しました: {$server[0]} / {$server[1]} / {$server[2]}" . PHP_EOL;
//   }

echo '  (INSERTはまだ実装されていません)' . PHP_EOL;
echo PHP_EOL;


echo '===== 4. 全件をSELECTして一覧表示する =====' . PHP_EOL;

// TODO: prepare('SELECT id, name, ip, status FROM servers') と execute() を
//       使って全件を取得し、fetchAll(PDO::FETCH_ASSOC) で連想配列の配列
//       として受け取り、foreach で一覧表示してください。
//
//   $selectStatement = $pdo->prepare('SELECT id, name, ip, status FROM servers');
//   $selectStatement->execute();
//   $servers = $selectStatement->fetchAll(PDO::FETCH_ASSOC);
//   echo '現在登録されているサーバー(' . count($servers) . '件):' . PHP_EOL;
//   foreach ($servers as $server) {
//       echo "  id={$server['id']}: {$server['name']} / {$server['ip']} / status={$server['status']}" . PHP_EOL;
//   }

echo '  (SELECTはまだ実装されていません)' . PHP_EOL;
echo PHP_EOL;


echo '===== 5. 1件をUPDATEしてstatusを変更する =====' . PHP_EOL;

// TODO: prepare('UPDATE servers SET status = ? WHERE name = ?') と
//       execute(['stopped', 'web-02']) を使って、
//       web-02 の status を 'stopped' に変更してください。
//       rowCount() で何件更新されたか確認できます。
//
//   $updateStatement = $pdo->prepare('UPDATE servers SET status = ? WHERE name = ?');
//   $updateStatement->execute(['stopped', 'web-02']);
//   echo "web-02 の status を 'stopped' に変更しました({$updateStatement->rowCount()}件更新)。" . PHP_EOL;

echo '  (UPDATEはまだ実装されていません)' . PHP_EOL;
echo PHP_EOL;


echo '===== 6. 1件をDELETEして削除する =====' . PHP_EOL;

// TODO: prepare('DELETE FROM servers WHERE name = ?') と
//       execute(['db-01']) を使って、db-01 を削除してください。
//
//   $deleteStatement = $pdo->prepare('DELETE FROM servers WHERE name = ?');
//   $deleteStatement->execute(['db-01']);
//   echo "db-01 を削除しました({$deleteStatement->rowCount()}件削除)。" . PHP_EOL;

echo '  (DELETEはまだ実装されていません)' . PHP_EOL;
echo PHP_EOL;


echo '===== 7. 更新後の状態を確認する =====' . PHP_EOL;

// TODO: もう一度SELECTを実行して、更新・削除後の一覧を表示してください。

echo '  (確認用SELECTはまだ実装されていません)' . PHP_EOL;
echo PHP_EOL;


// ---------------------------------------------------------------------
// 8. 後片付け: データベースファイルを削除する
// ---------------------------------------------------------------------
// 何度実行しても同じ結果になるように、最後に servers.db を削除します。
$pdo = null;
if (file_exists($dbPath)) {
    unlink($dbPath);
    echo "データベースファイルを削除しました: {$dbPath}" . PHP_EOL;
}


// =======================================================================
// MySQLに接続する場合について
// =======================================================================
// もし本物のMySQLサーバーに接続したい場合、変更が必要なのは
// 「接続時のDSNとユーザー名・パスワード」だけです。
//
//   // SQLiteのとき
//   $pdo = new PDO('sqlite:' . __DIR__ . '/servers.db');
//
//   // MySQLに接続する場合
//   $pdo = new PDO(
//       'mysql:host=localhost;dbname=servers_db;charset=utf8mb4',
//       'ユーザー名',
//       'パスワード'
//   );
//
// DSNを変えてユーザー名・パスワードを渡すだけで、prepare() や
// execute()、fetchAll() などのコードはほぼ変更せずに使い回せます。
// =======================================================================

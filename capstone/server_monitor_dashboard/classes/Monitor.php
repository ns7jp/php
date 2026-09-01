<?php

// =====================================================================
// Monitor クラス
// =====================================================================
// このクラスが、このcapstoneプロジェクトの「司令塔」です。
// これまでの16本の演習で学んだ内容を、1つのクラスの中に統合します。
//
//   (a) 今このPHPが動いている「実行環境」自体の情報を集める
//       → 演習15(サーバー情報取得)相当
//   (b) config.json から「監視対象サーバー一覧」を読み込む
//       → 演習09(CSV・JSON操作)+ 演習13(OOP)相当
//   (c) access.log を解析し、アクセス数などを簡易集計する
//       → 演習16(簡易ログ解析ツール)相当
//   (d) 上記(b)(c)でファイルが見つからない・壊れているといった
//       問題が起きた場合は、独自の例外クラスで検知し、
//       画面には最低限のメッセージだけを出し、詳細は error.log に記録する
//       → 演習14(例外処理とエラーログ)相当
//
// dashboard.php からは、このクラスの generateReport() を1回呼び出すだけで、
// (a)〜(d)をすべてまとめた「レポート」が連想配列として手に入ります。
// =====================================================================
class Monitor
{
    // ---- コンストラクタで受け取る3つのファイルパス ----
    private string $configPath;
    private string $accessLogPath;
    private string $errorLogPath;

    /**
     * @param string $configPath    監視対象サーバーの設定ファイル(config.json)のパス
     * @param string $accessLogPath 集計対象のアクセスログ(access.log)のパス
     * @param string $errorLogPath  エラー内容を記録するログファイル(error.log)のパス
     */
    public function __construct(string $configPath, string $accessLogPath, string $errorLogPath)
    {
        $this->configPath = $configPath;
        $this->accessLogPath = $accessLogPath;
        $this->errorLogPath = $errorLogPath;
    }

    // =====================================================================
    // (a) PHP実行環境の情報を集める(演習15相当)
    // =====================================================================
    // phpversion() や disk_free_space() など、演習15で学んだ関数群を
    // そのまま活用します。この処理は「ファイルを読み込む」わけではないので
    // 例外が発生する可能性はほとんどありませんが、環境によって
    // 値が取得できない(false が返る)ことがあるため、その場合は
    // 分かりやすいメッセージに差し替えています。
    //
    // 戻り値は [ ["項目名", "値"], ["項目名", "値"], ... ] という形の配列です。
    // dashboard.php 側では、この形をそのまま表の1行ずつとして扱えます。
    public function getPhpEnvironmentInfo(): array
    {
        $rows = [];

        // ---- PHPのバージョン ----
        $rows[] = ['PHPバージョン', phpversion()];

        // ---- OSの情報 ----
        // PHP_OS は定数(あらかじめ用意された値)なので () は付けません。
        // php_uname() は関数で、ホスト名やカーネルバージョンまで含んだ
        // 詳しい文字列を返します。
        $rows[] = ['OS種別 (PHP_OS)', PHP_OS];
        $rows[] = ['OS詳細 (php_uname)', php_uname()];

        // ---- ディスクの空き容量・総容量・使用率 ----
        // "." は「このスクリプトを実行したときのカレントディレクトリ」を表します。
        $free = disk_free_space('.');
        $total = disk_total_space('.');

        if ($free !== false && $total !== false && $total > 0) {
            $used = $total - $free;
            $usedPercent = round(($used / $total) * 100, 1);

            $rows[] = ['ディスク空き容量', $this->formatBytes((int) $free)];
            $rows[] = ['ディスク総容量', $this->formatBytes((int) $total)];
            $rows[] = ['ディスク使用率', $usedPercent . ' % (使用量: ' . $this->formatBytes((int) $used) . ')'];
        } else {
            // 環境によっては取得できないこともあるため、
            // false のまま計算してゼロ除算エラーになるのを防ぎます。
            $rows[] = ['ディスク容量', 'この環境では取得できません'];
        }

        // ---- ロードアベレージ ----
        // sys_getloadavg() はWindows環境には存在しないため、
        // function_exists() で事前に確認してから呼び出します。
        if (function_exists('sys_getloadavg')) {
            $load = sys_getloadavg();
            $rows[] = [
                'ロードアベレージ',
                sprintf('%.2f, %.2f, %.2f (1分, 5分, 15分の平均)', $load[0], $load[1], $load[2]),
            ];
        } else {
            $rows[] = ['ロードアベレージ', 'この環境では取得できません(sys_getloadavg 未対応)'];
        }

        // ---- PHPのメモリ使用量 ----
        $rows[] = ['メモリ使用量 (PHP自身)', $this->formatBytes(memory_get_usage(true))];

        return $rows;
    }

    // バイト数(int)を "1.5 KB" のような読みやすい文字列に変換するヘルパーです。
    // 演習15の formatBytes() と同じ考え方をこのクラスの中に private メソッドとして
    // 持たせています(クラスの外からは使わない、内部だけの補助処理のためprivate)。
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        if ($bytes <= 0) {
            return '0 B';
        }

        $power = (int) floor(log($bytes, 1024));
        $power = min($power, count($units) - 1);

        $value = round($bytes / (1024 ** $power), $precision);

        return $value . ' ' . $units[$power];
    }

    // =====================================================================
    // (b) config.json から監視対象サーバー一覧を読み込む(演習09+演習13相当)
    // =====================================================================
    // 戻り値は Server オブジェクトの配列です。
    // ファイルが存在しない、読み込めない、JSONとして壊れている、
    // 期待する構造("servers" キーを持つ配列)になっていない、
    // といった問題があった場合は、その場で処理を続けず
    // ConfigException を throw(投げる) します。
    //
    // 「エラーが起きても false や空配列をこっそり返す」のではなく、
    // 例外を throw することで、呼び出し元(generateReport())に
    // 「ここで問題が起きたこと」を確実に伝えられます。
    /**
     * @return Server[]
     * @throws ConfigException 設定ファイルが読み込めない・不正な場合
     */
    public function loadServers(): array
    {
        // ---- 1. ファイルの存在確認 ----
        if (!file_exists($this->configPath)) {
            throw new ConfigException($this->configPath, '設定ファイルが見つかりません。');
        }

        // ---- 2. ファイルの読み込み ----
        $json = file_get_contents($this->configPath);
        if ($json === false) {
            throw new ConfigException($this->configPath, '設定ファイルを読み込めませんでした。');
        }

        // ---- 3. JSONとしての解析 ----
        // json_decode() の第2引数に true を渡すと、
        // オブジェクトではなく連想配列として結果を受け取れます。
        $data = json_decode($json, true);

        // json_last_error() は、直前の json_decode() が成功したかどうかを
        // 調べるための関数です。JSON_ERROR_NONE 以外が返ってきた場合は、
        // 「構文的に壊れたJSON(カンマの付け忘れなど)だった」ことを意味します。
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ConfigException(
                $this->configPath,
                'JSONの形式が正しくありません: ' . json_last_error_msg()
            );
        }

        // ---- 4. 期待する構造になっているかの確認 ----
        // 今回の config.json は必ず {"servers": [...]} という形を
        // 想定しているため、"servers" キーが存在し、かつ配列であることを
        // 確認します。この確認を省略すると、後続の foreach で
        // 「配列ではない値」を渡してしまい、予期しないエラーの原因になります。
        if (!isset($data['servers']) || !is_array($data['servers'])) {
            throw new ConfigException(
                $this->configPath,
                '"servers" というキーが見つからないか、配列になっていません。'
            );
        }

        // ---- 5. 連想配列 → Server オブジェクトへの変換 ----
        $servers = [];
        foreach ($data['servers'] as $row) {
            if (!is_array($row)) {
                // 1件分がそもそも配列(オブジェクト)になっていない、
                // 壊れたデータの場合は、その1件だけを読み飛ばします。
                continue;
            }

            // Server::fromArray() に変換処理を任せることで、
            // このクラスは「JSONを読み込むこと」に集中し、
            // 「Serverオブジェクトをどう組み立てるか」の詳細は
            // Serverクラス自身に任せられます(責任の分担)。
            $servers[] = Server::fromArray($row);
        }

        return $servers;
    }

    // =====================================================================
    // (c) access.log の簡易集計(演習16相当)
    // =====================================================================
    // 戻り値は次のキーを持つ連想配列です。
    //   'total_lines'   => 読み込んだ行数
    //   'parsed'        => 正規表現で正しく解析できた行数
    //   'unparsed'      => 解析できなかった(想定外の形式だった)行数
    //   'status_counts' => ["200" => 9, "404" => 3, ...] のような集計
    //   'top_ips'       => ["203.0.113.10" => 6, ...] のような、
    //                      アクセス数が多い順の上位5件
    /**
     * @throws LogFileException アクセスログが読み込めない場合
     */
    public function analyzeAccessLog(): array
    {
        if (!file_exists($this->accessLogPath)) {
            throw new LogFileException($this->accessLogPath, 'アクセスログファイルが見つかりません。');
        }

        // FILE_IGNORE_NEW_LINES: 各行末の改行を取り除く
        // FILE_SKIP_EMPTY_LINES: 空行を配列に含めない
        $lines = file($this->accessLogPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if ($lines === false) {
            throw new LogFileException($this->accessLogPath, 'アクセスログファイルを読み込めませんでした。');
        }

        // Combined Log Format(演習16と同じ形式)からIPアドレス・日時・
        // メソッド・パス・ステータスコード・バイト数を取り出す正規表現です。
        // 例: 203.0.113.10 - - [01/Sep/2026:09:12:01 +0900] "GET /index.php HTTP/1.1" 200 1234
        $pattern = '/^(\S+) \S+ \S+ \[([^\]]+)\] "(\S+) (\S+) [^"]+" (\d{3}) (\d+)/';

        $statusCounts = [];
        $ipCounts = [];
        $parsed = 0;
        $unparsed = 0;

        foreach ($lines as $line) {
            $matches = [];

            if (preg_match($pattern, $line, $matches) === 1) {
                $parsed++;

                $ip = $matches[1];
                $status = $matches[5];

                // 連想配列をカウンタとして使う、おなじみのパターンです。
                // ?? を使うことで「まだ1度も出てきていないキーかどうか」を
                // 自分でif文で調べなくても、安全に件数を増やしていけます。
                $statusCounts[$status] = ($statusCounts[$status] ?? 0) + 1;
                $ipCounts[$ip] = ($ipCounts[$ip] ?? 0) + 1;
            } else {
                $unparsed++;
            }
        }

        // ステータスコードは見やすいように昇順(200, 301, 404, 500, ...)に並び替えます。
        ksort($statusCounts);

        // アクセス数の多い順(値の大きい順)にIPアドレスを並び替えます。
        arsort($ipCounts);

        // 上位5件だけを取り出します。第4引数に true を指定することで、
        // IPアドレスという文字列キーをそのまま保持したまま切り出します。
        $topIps = array_slice($ipCounts, 0, 5, true);

        return [
            'total_lines' => count($lines),
            'parsed' => $parsed,
            'unparsed' => $unparsed,
            'status_counts' => $statusCounts,
            'top_ips' => $topIps,
        ];
    }

    // =====================================================================
    // (d) 上記すべてをまとめてレポートを生成する(演習14の例外処理相当)
    // =====================================================================
    // dashboard.php から実際に呼び出されるのは、基本的にこのメソッドだけです。
    // (a)は例外が起きない前提の情報収集、(b)と(c)はファイル操作を伴うため
    // 例外が起きる可能性がある処理、という違いに注目してください。
    //
    // (b)や(c)で例外が発生した場合でも、このメソッド全体が停止して
    // しまわないよう、それぞれを個別の try/catch で囲んでいます。
    // これにより「config.jsonは壊れているが、access.logは正常」といった
    // 部分的な失敗の場合でも、正常な部分の情報はきちんとレポートに含めて
    // 画面に表示できます(1箇所のエラーで、ダッシュボード全体が
    // 真っ白になってしまう…という事態を避けられます)。
    public function generateReport(): array
    {
        $report = [
            'generated_at' => date('Y-m-d H:i:s'),
            'php_env' => $this->getPhpEnvironmentInfo(),
            'servers' => [],
            'server_error' => null,
            'access_log' => null,
            'access_log_error' => null,
        ];

        // ---- (b) サーバー一覧の読み込み ----
        try {
            $report['servers'] = $this->loadServers();
        } catch (ConfigException $e) {
            // 画面(呼び出し元)には、詳細を伏せた分かりやすいメッセージだけを渡します。
            $report['server_error'] = $e->getMessage();
            // 詳細は error.log に記録し、あとから調査できるようにしておきます。
            $this->logError($e);
        }

        // ---- (c) アクセスログの集計 ----
        try {
            $report['access_log'] = $this->analyzeAccessLog();
        } catch (LogFileException $e) {
            $report['access_log_error'] = $e->getMessage();
            $this->logError($e);
        }

        return $report;
    }

    // ---------------------------------------------------------------
    // logError(): 例外の内容を error.log に追記する
    // ---------------------------------------------------------------
    // 演習14で学んだ「error_log(文字列, 3, ファイルパス)」の書き方と
    // 同じ考え方です。第2引数に 3 を指定すると、第3引数で指定した
    // ファイルにメッセージを追記できます。
    //
    // Throwable は Exception や Error など、PHPで「投げられるすべてのもの」
    // に共通するインターフェース(型)です。ConfigException・
    // LogFileException のどちらが渡されても、このメソッド1つで
    // ログの記録処理を共通化できます。
    private function logError(Throwable $e): void
    {
        $timestamp = date('Y-m-d H:i:s');

        // get_class($e) で「実際にどの例外クラスだったか」
        // (ConfigException か LogFileException か)を文字列として取得できます。
        $logMessage = sprintf(
            '[%s] %s: %s',
            $timestamp,
            get_class($e),
            $e->getMessage()
        );

        error_log($logMessage . PHP_EOL, 3, $this->errorLogPath);
    }
}

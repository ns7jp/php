<?php

// =====================================================================
// capstone: シンプルなサーバー監視ダッシュボード (dashboard.php)
// =====================================================================
// これまでの16本の演習(変数・条件分岐・ループ・配列・関数・文字列・
// 日時・ファイルI/O・CSV/JSON・フォーム・セッション・PDO・OOP・
// 例外処理・サーバー情報取得・ログ解析)のうち、特に
//   ・演習09(CSV・JSON操作)
//   ・演習13(オブジェクト指向プログラミングの基礎)
//   ・演習14(例外処理とエラーログ)
//   ・演習15(サーバー情報取得スクリプト)
//   ・演習16(簡易ログ解析ツール)
// の内容を1つのアプリケーションとして統合したのが、このcapstoneです。
//
// 実行方法:
//   php dashboard.php
//
// 実行すると、
//   1. コンソール(ターミナル)に、罫線付きのダッシュボードを表示する
//   2. 同じ内容を report.html として書き出す(ブラウザで開いて確認できる)
// という2つの出力を行います。
//
// classes/ ディレクトリの中身については、
//   classes/Server.php  … サーバー1台分の情報を表すクラス
//   classes/Monitor.php … 複数のサーバー・実行環境・ログをまとめる司令塔クラス
//   classes/ConfigException.php / LogFileException.php … 独自の例外クラス
// をそれぞれ参照してください。
// =====================================================================


// ---------------------------------------------------------------------
// 簡易オートローダー(spl_autoload_register)
// ---------------------------------------------------------------------
// これまでの演習では require や require_once を使って、必要なファイルを
// 1つずつ明示的に読み込んできました。今回は classes/ ディレクトリに
// 複数のクラスファイルがあるため、それぞれに require_once を書く
// 代わりに「オートローダー」という仕組みを使ってみます。
//
// spl_autoload_register() に「関数」を登録しておくと、PHPは
// 「まだ読み込まれていないクラス名が、プログラムの中で初めて
// 使われた(new されたり、extends されたりした)瞬間」に、
// 登録しておいた関数を自動的に呼び出してくれます。
//
// ここで登録している関数は、
//   「Monitor というクラス名が必要になったら、
//    classes/Monitor.php というファイルを探して読み込む」
// という、とても単純なルールだけを行っています。
// クラス名とファイル名を一致させておくルールにしておけば、
// classes/ ディレクトリに新しいクラスを追加するたびに、
// dashboard.php 側を書き換える必要がなくなります。
spl_autoload_register(function (string $className): void {
    $file = __DIR__ . '/classes/' . $className . '.php';

    if (file_exists($file)) {
        require $file;
    }
});


// ---------------------------------------------------------------------
// 表示用のヘルパー関数たち
// ---------------------------------------------------------------------
// ダッシュボードの見た目(罫線付きの表)を組み立てるための、
// このスクリプト専用の小さな関数群です。演習05(関数の作成)で学んだ
// 「同じ処理をまとめて、名前を付けて再利用する」という考え方を、
// 表の描画処理に応用しています。

/**
 * 文字列を指定した表示幅になるまで右側にスペースで埋める。
 *
 * str_pad() は「バイト数」で長さを数えるため、日本語(全角文字)が
 * 混ざった文字列の幅を正しく揃えられません。そこで mb_strwidth() を
 * 使います。これは全角文字を「2」、半角文字を「1」として数えるため、
 * 日本語と英数字が混在していても表の縦の罫線がずれずに揃います。
 */
function padCell(string $text, int $width): string
{
    $pad = $width - mb_strwidth($text);
    if ($pad < 0) {
        $pad = 0;
    }

    return $text . str_repeat(' ', $pad);
}

/**
 * 罫線付きの表を1つの文字列として組み立てて返す。
 *
 * @param string[]   $headers 表の見出し(1行目)
 * @param array<int, array<int, string>> $rows 表の中身。1要素が1行分。
 */
function renderTable(array $headers, array $rows): string
{
    // ---- 1. 各列の幅を決める ----
    // まず見出しの幅で初期化し、その後すべての行を見て、
    // その列で一番幅が広いセルに合わせます(表がガタガタにならないようにするため)。
    $widths = [];
    foreach ($headers as $index => $header) {
        $widths[$index] = mb_strwidth($header);
    }

    foreach ($rows as $row) {
        foreach ($row as $index => $cell) {
            $widths[$index] = max($widths[$index] ?? 0, mb_strwidth((string) $cell));
        }
    }

    // ---- 2. 罫線(┌┬┐ / ├┼┤ / └┴┘)を組み立てる ----
    // 各列の幅+2(セルの左右の半角スペース分)だけ「─」を繰り返します。
    $buildBorder = function (string $left, string $mid, string $right) use ($widths): string {
        $segments = [];
        foreach ($widths as $width) {
            $segments[] = str_repeat('─', $width + 2);
        }

        return $left . implode($mid, $segments) . $right;
    };

    $lines = [];
    $lines[] = $buildBorder('┌', '┬', '┐');

    // ---- 3. 見出し行 ----
    $headerCells = [];
    foreach ($headers as $index => $header) {
        $headerCells[] = ' ' . padCell($header, $widths[$index]) . ' ';
    }
    $lines[] = '│' . implode('│', $headerCells) . '│';

    $lines[] = $buildBorder('├', '┼', '┤');

    // ---- 4. データ行(1件もない場合は「データがありません」と表示する) ----
    if (count($rows) === 0) {
        $totalWidth = array_sum($widths) + count($widths) * 3 - 1;
        $lines[] = '│ ' . padCell('データがありません', max($totalWidth, mb_strwidth('データがありません'))) . ' │';
    } else {
        foreach ($rows as $row) {
            $rowCells = [];
            foreach ($row as $index => $cell) {
                $rowCells[] = ' ' . padCell((string) $cell, $widths[$index]) . ' ';
            }
            $lines[] = '│' . implode('│', $rowCells) . '│';
        }
    }

    $lines[] = $buildBorder('└', '┴', '┘');

    return implode(PHP_EOL, $lines) . PHP_EOL;
}

/**
 * セクションの見出し(タイトル)を表示用に組み立てる。
 */
function renderSectionTitle(string $title): string
{
    return PHP_EOL . '■ ' . $title . PHP_EOL;
}


// ---------------------------------------------------------------------
// メイン処理: Monitorクラスでレポートを作り、CLIに表示する
// ---------------------------------------------------------------------
$baseDir = __DIR__;

$monitor = new Monitor(
    $baseDir . '/config.json',
    $baseDir . '/access.log',
    $baseDir . '/error.log'
);

// generateReport() の中で、config.json・access.log それぞれの
// try/catchが行われ、問題があれば error.log に記録された上で、
// エラーメッセージだけがこの $report に含まれて返ってきます。
// つまり dashboard.php 側では、例外を直接catchする必要はありません。
$report = $monitor->generateReport();

// ---- 外枠のバナー(二重線)を表示する ----
$title = 'サーバー監視ダッシュボード (Server Monitor Dashboard)';
$subtitle = '生成日時: ' . $report['generated_at'];

$bannerWidth = max(mb_strwidth($title), mb_strwidth($subtitle)) + 4;

echo '╔' . str_repeat('═', $bannerWidth) . '╗' . PHP_EOL;
echo '║  ' . padCell($title, $bannerWidth - 2) . '║' . PHP_EOL;
echo '║  ' . padCell($subtitle, $bannerWidth - 2) . '║' . PHP_EOL;
echo '╚' . str_repeat('═', $bannerWidth) . '╝' . PHP_EOL;

// ---- (a) PHP実行環境情報 ----
echo renderSectionTitle('PHP実行環境情報');
echo renderTable(['項目', '値'], $report['php_env']);

// ---- (b) 監視対象サーバー一覧 ----
echo renderSectionTitle('監視対象サーバー一覧');

if ($report['server_error'] !== null) {
    // 例外が起きていた場合は、詳細を伏せた案内文だけを表示します。
    // (実務でも、画面には利用者向けの簡潔な文言だけを出し、
    //  内部的な詳細はログにのみ残すのが基本です)
    echo '⚠ ' . $report['server_error'] . PHP_EOL;
    echo '  詳細は error.log を確認してください。' . PHP_EOL;
} else {
    $serverRows = [];
    foreach ($report['servers'] as $server) {
        /** @var Server $server */
        $serverRows[] = [
            $server->getName(),
            $server->getIp(),
            $server->getCpuUsage() . ' %',
            $server->getMemoryUsage() . ' %',
            $server->getStatus(),
        ];
    }
    echo renderTable(['サーバー名', 'IPアドレス', 'CPU使用率', 'メモリ使用率', 'ステータス'], $serverRows);
}

// ---- (c) アクセスログ集計 ----
echo renderSectionTitle('アクセスログ集計 (access.log)');

if ($report['access_log_error'] !== null) {
    echo '⚠ ' . $report['access_log_error'] . PHP_EOL;
    echo '  詳細は error.log を確認してください。' . PHP_EOL;
} else {
    $accessLog = $report['access_log'];

    echo sprintf(
        '総行数: %d行 / 解析成功: %d行 / 解析できなかった行: %d行',
        $accessLog['total_lines'],
        $accessLog['parsed'],
        $accessLog['unparsed']
    ) . PHP_EOL;

    // ステータスコード別の集計表
    $statusRows = [];
    foreach ($accessLog['status_counts'] as $status => $count) {
        $statusRows[] = [(string) $status, $count . ' 件'];
    }
    echo PHP_EOL . '【ステータスコード別 集計】' . PHP_EOL;
    echo renderTable(['ステータスコード', '件数'], $statusRows);

    // アクセス数トップ5のIPアドレス
    $rankRows = [];
    $rank = 1;
    foreach ($accessLog['top_ips'] as $ip => $count) {
        $rankRows[] = [(string) $rank, $ip, $count . ' 件'];
        $rank++;
    }
    echo '【アクセス数ランキング トップ5】' . PHP_EOL;
    echo renderTable(['順位', 'IPアドレス', '件数'], $rankRows);
}

echo PHP_EOL;
echo str_repeat('─', 70) . PHP_EOL;


// ---------------------------------------------------------------------
// HTMLレポート(report.html)の書き出し
// ---------------------------------------------------------------------
// CLI(コンソール)向けの罫線表とは別に、同じ $report のデータを使って
// 見やすいHTMLの表として書き出します。file_put_contents() を使うことで、
// ファイルを開く・書き込む・閉じるという一連の処理を1行で行えます
// (演習08のファイルI/Oで学んだ内容の応用です)。
//
// htmlspecialchars() は、文字列に含まれる < や > などの記号を
// &lt; や &gt; のようなHTMLエンティティに変換する関数です。
// もしサーバー名などに < や " のような記号が混ざっていても、
// それがそのままHTMLタグとして解釈されて表示が崩れたり、
// 意図しないHTMLが埋め込まれたりする(クロスサイトスクリプティング、
// いわゆるXSS)のを防ぐための、実務でも必須の基本的な対策です。
function h(string $text): string
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/**
 * 連想配列の行データから、HTMLの<table>を組み立てるヘルパー関数。
 *
 * @param string[] $headers
 * @param array<int, array<int, string>> $rows
 */
function renderHtmlTable(array $headers, array $rows): string
{
    $html = "<table>\n<thead>\n<tr>\n";
    foreach ($headers as $header) {
        $html .= '<th>' . h($header) . "</th>\n";
    }
    $html .= "</tr>\n</thead>\n<tbody>\n";

    if (count($rows) === 0) {
        $colspan = count($headers);
        $html .= '<tr><td colspan="' . $colspan . '" class="empty">データがありません</td></tr>' . "\n";
    } else {
        foreach ($rows as $row) {
            $html .= "<tr>\n";
            foreach ($row as $cell) {
                $html .= '<td>' . h((string) $cell) . "</td>\n";
            }
            $html .= "</tr>\n";
        }
    }

    $html .= "</tbody>\n</table>\n";

    return $html;
}

// ---- HTML全体を組み立てる ----
// ヒアドキュメント(<<<HTML ... HTML)を使うと、複数行の文字列の中に
// 変数を直接埋め込みながら、読みやすく書くことができます。
$phpEnvRows = $report['php_env'];

if ($report['server_error'] !== null) {
    $serverSectionHtml = '<p class="error">⚠ ' . h($report['server_error']) . '(詳細は error.log を参照してください)</p>';
} else {
    $serverRows = [];
    foreach ($report['servers'] as $server) {
        /** @var Server $server */
        // match式(PHP 8.0で追加された構文)は、if/elseifを並べる代わりに
        // 「値が○○のときは、これ」という対応表のように書ける機能です。
        // switch文と似ていますが、値をそのまま返せる・値の比較が
        // 厳密(===と同じ)に行われる・どの条件にも一致しなければ
        // 自動的にエラーになる(ただしここではdefaultを用意しているので
        // 必ずどれかに一致します)という違いがあります。
        // ここでは「サーバーの状態」の文字列から、HTMLの色分け用
        // CSSクラス名を1つ選び出しています。
        $statusClass = match ($server->getStatus()) {
            '危険' => 'status-critical',
            '注意' => 'status-warning',
            default => 'status-ok',
        };
        $serverRows[] = [
            $server->getName(),
            $server->getIp(),
            $server->getCpuUsage() . ' %',
            $server->getMemoryUsage() . ' %',
            '<span class="' . $statusClass . '">' . h($server->getStatus()) . '</span>',
        ];
    }

    // renderHtmlTable() はセルをすべてエスケープしてしまうため、
    // ステータス部分の <span> タグをそのまま活かしたいこの表だけは、
    // 個別に組み立てています(サーバー名・IP・数値部分は h() でエスケープ済みです)。
    $serverSectionHtml = "<table>\n<thead><tr><th>サーバー名</th><th>IPアドレス</th><th>CPU使用率</th><th>メモリ使用率</th><th>ステータス</th></tr></thead>\n<tbody>\n";
    foreach ($serverRows as $row) {
        $serverSectionHtml .= '<tr><td>' . h($row[0]) . '</td><td>' . h($row[1]) . '</td><td>' . h($row[2]) . '</td><td>' . h($row[3]) . '</td><td>' . $row[4] . "</td></tr>\n";
    }
    $serverSectionHtml .= "</tbody>\n</table>\n";
}

if ($report['access_log_error'] !== null) {
    $accessLogSectionHtml = '<p class="error">⚠ ' . h($report['access_log_error']) . '(詳細は error.log を参照してください)</p>';
} else {
    $accessLog = $report['access_log'];

    $statusRows = [];
    foreach ($accessLog['status_counts'] as $status => $count) {
        $statusRows[] = [(string) $status, $count . ' 件'];
    }

    $rankRows = [];
    $rank = 1;
    foreach ($accessLog['top_ips'] as $ip => $count) {
        $rankRows[] = [(string) $rank, $ip, $count . ' 件'];
        $rank++;
    }

    $accessLogSectionHtml = sprintf(
        '<p>総行数: %d行 / 解析成功: %d行 / 解析できなかった行: %d行</p>',
        $accessLog['total_lines'],
        $accessLog['parsed'],
        $accessLog['unparsed']
    );
    $accessLogSectionHtml .= '<h3>ステータスコード別 集計</h3>';
    $accessLogSectionHtml .= renderHtmlTable(['ステータスコード', '件数'], $statusRows);
    $accessLogSectionHtml .= '<h3>アクセス数ランキング トップ5</h3>';
    $accessLogSectionHtml .= renderHtmlTable(['順位', 'IPアドレス', '件数'], $rankRows);
}

$phpEnvHtml = renderHtmlTable(['項目', '値'], $phpEnvRows);
$generatedAt = h($report['generated_at']);

$htmlDocument = <<<HTML
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>サーバー監視ダッシュボード レポート</title>
<style>
    body {
        font-family: "Hiragino Kaku Gothic ProN", "Yu Gothic", "Meiryo", sans-serif;
        background-color: #f4f6f8;
        color: #222;
        margin: 0;
        padding: 24px;
    }
    h1 {
        margin-top: 0;
    }
    .meta {
        color: #555;
        margin-bottom: 24px;
    }
    section {
        background: #ffffff;
        border: 1px solid #dcdcdc;
        border-radius: 6px;
        padding: 16px 20px;
        margin-bottom: 20px;
    }
    section h2 {
        margin-top: 0;
        border-bottom: 2px solid #3b6ea5;
        padding-bottom: 6px;
        color: #2c4c6b;
    }
    table {
        border-collapse: collapse;
        width: 100%;
        margin-top: 10px;
    }
    th, td {
        border: 1px solid #cccccc;
        padding: 6px 10px;
        text-align: left;
        font-size: 14px;
    }
    th {
        background-color: #3b6ea5;
        color: #ffffff;
    }
    tbody tr:nth-child(even) {
        background-color: #f0f4f8;
    }
    td.empty {
        text-align: center;
        color: #888;
    }
    .status-ok {
        color: #1a7a33;
        font-weight: bold;
    }
    .status-warning {
        color: #b8860b;
        font-weight: bold;
    }
    .status-critical {
        color: #c0392b;
        font-weight: bold;
    }
    .error {
        color: #c0392b;
        font-weight: bold;
    }
</style>
</head>
<body>
<h1>サーバー監視ダッシュボード レポート</h1>
<p class="meta">生成日時: {$generatedAt}</p>

<section>
<h2>PHP実行環境情報</h2>
{$phpEnvHtml}
</section>

<section>
<h2>監視対象サーバー一覧</h2>
{$serverSectionHtml}
</section>

<section>
<h2>アクセスログ集計 (access.log)</h2>
{$accessLogSectionHtml}
</section>

</body>
</html>
HTML;

$reportPath = $baseDir . '/report.html';
file_put_contents($reportPath, $htmlDocument);

echo 'HTMLレポートを書き出しました: ' . $reportPath . PHP_EOL;

if (file_exists($baseDir . '/error.log')) {
    echo '※ 処理中にエラーが検出されたため、error.log にも記録されています。' . PHP_EOL;
}

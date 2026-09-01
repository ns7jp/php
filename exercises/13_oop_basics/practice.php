<?php

// =====================================================================
// 演習13: オブジェクト指向プログラミングの基礎
// =====================================================================
// このファイルは「未完成のひな形」です。
// "// TODO:" と書かれている部分を自分で実装してください。
// まだ実装していない部分は「まだ実装されていません」等のわかりやすい表示になるので、
// このままの状態でも php コマンドでエラーなく実行できます。
// =====================================================================


// ---------------------------------------------------------------------
// 1. Server クラスを作る(クラス・プロパティ・コンストラクタ・カプセル化)
// ---------------------------------------------------------------------
// サーバー1台の情報(名前・IPアドレス・CPU使用率)をひとまとめにして
// 扱うための「設計図」として、Server クラスを作ります。
//
// プロパティ($name, $ip, $cpuUsage)はすべて private にすることで、
// クラスの外から直接書き換えられないようにします(カプセル化)。
// 値を取得したいときは、getName() のような「getter(ゲッター)」
// メソッドを通してアクセスするようにします。
class Server
{
    // TODO: プロパティ $name, $ip, $cpuUsage を private で宣言してください。
    //   ・$name      : string 型 (サーバー名)
    //   ・$ip        : string 型 (IPアドレス)
    //   ・$cpuUsage  : int 型 (CPU使用率, 0〜100の整数)
    // ヒント: 実装が終わるまでの間もエラーにならないように、
    //   ひとまず初期値('' や 0)を入れておくと安全です。
    //   例: private string $name = '';
    private string $name = '';
    private string $ip = '';
    private int $cpuUsage = 0;

    // コンストラクタ: new Server(...) でオブジェクトを作るときに
    // 自動的に呼び出される特別なメソッドです。
    public function __construct(string $name, string $ip, int $cpuUsage)
    {
        // TODO: 受け取った引数を、それぞれのプロパティに代入してください。
        // 例: $this->name = $name;
    }

    // すでに実装済みの getter です。書き方の参考にしてください。
    public function getName(): string
    {
        return $this->name;
    }

    // TODO: getIp() メソッドを実装してください。
    //   ・$ip プロパティの値を返す getter メソッドです。
    //   ・上の getName() を参考にしてください。
    public function getIp(): string
    {
        // この行は仮の実装です。上のヒントを参考に書き換えてください。
        return '(まだ実装されていません)';
    }

    // TODO: getCpuUsage() メソッドを実装してください。
    //   ・$cpuUsage プロパティの値を返す getter メソッドです。
    public function getCpuUsage(): int
    {
        // この行は仮の実装です。上のヒントを参考に書き換えてください。
        // (まだ実装されていないことがわかるように、仮の値として -1 を返しておきます)
        return -1;
    }

    // TODO: getStatus() メソッドを実装してください。
    // 要件:
    //   ・$cpuUsage の値に応じて、次の文字列を返す
    //     - 70未満        => "正常"
    //     - 70以上90未満  => "注意"
    //     - 90以上        => "危険"
    // ヒント: if / elseif / else を使いましょう。
    public function getStatus(): string
    {
        // この行は仮の実装です。上のヒントを参考に書き換えてください。
        return '(まだ実装されていません)';
    }
}


// ---------------------------------------------------------------------
// 2. DatabaseServer クラスを作る(継承 extends・parent::)
// ---------------------------------------------------------------------
// Server クラスを継承(extends)して、データベースサーバー専用の
// クラスを作ります。継承すると、親クラス(Server)が持っている
// public なプロパティやメソッドを、そのまま引き継いで使うことができます。
class DatabaseServer extends Server
{
    // TODO: 追加のプロパティ $dbEngine (string型) を private で宣言してください。
    //   ・データベースエンジン名 (例: "MySQL", "PostgreSQL") を保持します。
    private string $dbEngine = '';

    // TODO: コンストラクタを実装してください。
    // 要件:
    //   ・引数: string $name, string $ip, int $cpuUsage, string $dbEngine
    //   ・parent::__construct($name, $ip, $cpuUsage) を呼び出して、
    //     親クラス(Server)側のプロパティを初期化する
    //   ・その後、$this->dbEngine = $dbEngine; で自分自身のプロパティを初期化する
    public function __construct(string $name, string $ip, int $cpuUsage, string $dbEngine)
    {
        // ここに実装してください。
        // (このままだと親クラスのプロパティが初期化されないままになります)
    }

    // TODO: getDbEngine() メソッドを実装してください。
    public function getDbEngine(): string
    {
        // この行は仮の実装です。上のヒントを参考に書き換えてください。
        return '(まだ実装されていません)';
    }

    // TODO: getInfo() メソッドを実装してください。
    // 要件:
    //   ・親クラスの getStatus() を parent::getStatus() で呼び出して、状態を取得する
    //   ・getName(), getIp(), getCpuUsage() は親クラスから継承しているので、
    //     $this->getName() のようにそのまま呼び出せる
    //   ・次のような1行の文字列を作って返す
    //     "db01 (MySQL) - IP: 192.168.1.20 / CPU使用率: 60% / 状態: 正常"
    public function getInfo(): string
    {
        // この行は仮の実装です。上のヒントを参考に書き換えてください。
        return '(まだ実装されていません)';
    }
}


// ---------------------------------------------------------------------
// 3. 複数のサーバーをまとめて一覧表示する(オブジェクトの配列・foreach)
// ---------------------------------------------------------------------
echo "===== サーバー一覧 =====" . PHP_EOL;

// TODO: Server (または DatabaseServer) のオブジェクトを3〜4個作って、
//       配列 $servers にまとめてください。少なくとも1つは
//       DatabaseServer にしてみましょう。
// 例:
//   $servers = [
//       new Server('web01', '192.168.1.10', 45),
//       new Server('web02', '192.168.1.11', 78),
//       new Server('app01', '192.168.1.12', 95),
//       new DatabaseServer('db01', '192.168.1.20', 60, 'MySQL'),
//   ];
$servers = [];

if (count($servers) === 0) {
    echo "(まだサーバーが登録されていません。上の TODO を実装してください)" . PHP_EOL;
}

// TODO: foreach を使って $servers を1つずつ取り出し、一覧表示してください。
// 要件:
//   ・$server が DatabaseServer のインスタンスなら、getInfo() の結果を表示する
//   ・そうでなければ(通常の Server なら)、getName()/getIp()/getCpuUsage()/getStatus()
//     を組み合わせて1行の文字列を作って表示する
// ヒント: instanceof 演算子で「そのオブジェクトが何のクラスか」を判定できます。
//     if ($server instanceof DatabaseServer) { ... } else { ... }
foreach ($servers as $server) {
    // ここに実装してください。
}

<?php

// =====================================================================
// 演習13: オブジェクト指向プログラミングの基礎 (模範解答)
// =====================================================================
// このファイルは、practice.php の TODO をすべて実装した「完成版」です。
// 上から順番にコメントを読みながら、どんな考え方でコードを書いているか
// 確認してみてください。
// =====================================================================


// ---------------------------------------------------------------------
// 1. Server クラスを作る(クラス・プロパティ・コンストラクタ・カプセル化)
// ---------------------------------------------------------------------
// これまでの演習では、サーバー1台分の情報を「連想配列」や「複数の変数」
// で表現してきました。しかし、それだと
//   ・どの変数とどの変数が同じサーバーの情報なのかがわかりにくい
//   ・配列のキー名を間違えてもPHPが教えてくれない
//   ・値のチェック(CPU使用率は0〜100のはず、など)をどこに書けばよいか
//     決まった場所がない
// といった問題が出てきます。
//
// そこで「クラス」を使い、サーバー1台分の情報と、それに関する処理を
// 1つの「設計図」としてまとめます。クラスから作られた実体のことを
// 「オブジェクト」または「インスタンス」と呼びます。
class Server
{
    // ---- プロパティ(オブジェクトが持つデータ) ----
    // private を付けることで、このクラスの「外側」からは
    // 直接 $server->name のようにアクセスできなくなります。
    // これを「カプセル化」と呼びます。
    //
    // カプセル化のメリット:
    //   ・「CPU使用率が急に -50 のようなおかしな値に書き換えられる」
    //     といった、外部からの想定外の書き換えを防げる
    //   ・値を読み書きする経路を getter/setter メソッドに一本化できるので、
    //     あとから「値の範囲をチェックする処理を足したい」となったときも
    //     クラスの中だけを直せばよくなる
    private string $name;
    private string $ip;
    private int $cpuUsage;

    // ---- コンストラクタ ----
    // __construct という特別な名前のメソッドは、
    // 「new Server(...)」としてオブジェクトを作成した瞬間に、
    // PHPが自動的に呼び出してくれます。
    // ここで受け取った引数を、各プロパティに代入(初期化)しておくことで、
    // 「名前もIPもCPU使用率も決まっていない、中途半端なServerオブジェクト」
    // が作られてしまうのを防ぎます。
    public function __construct(string $name, string $ip, int $cpuUsage)
    {
        // $this は「今作られている、このオブジェクト自身」を指す特別な変数です。
        // $this->name のように書くことで、このオブジェクトが持つ
        // $name プロパティに値を代入できます。
        $this->name = $name;
        $this->ip = $ip;
        $this->cpuUsage = $cpuUsage;
    }

    // ---- getter(値を取得するためのメソッド) ----
    // プロパティを private にした代わりに、クラスの外からでも
    // 安全に値を読み取れるように、public な getter メソッドを用意します。
    // 「読み取り専用の窓口」を用意するイメージです。
    public function getName(): string
    {
        return $this->name;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function getCpuUsage(): int
    {
        return $this->cpuUsage;
    }

    // ---- getStatus(): CPU使用率から「状態」を判定するメソッド ----
    // 単に値を返すだけでなく、「プロパティの値をもとに何かを計算・判定する」
    // 処理もメソッドとして持たせることができます。
    // このように「データ(プロパティ)」と「そのデータを使った処理(メソッド)」を
    // 1つのクラスにまとめておくのが、オブジェクト指向の基本的な考え方です。
    public function getStatus(): string
    {
        if ($this->cpuUsage >= 90) {
            // 90%以上は「危険」水準とみなす
            return '危険';
        } elseif ($this->cpuUsage >= 70) {
            // 70%以上90%未満は「注意」水準とみなす
            return '注意';
        }

        // それ以外(70%未満)は「正常」とみなす
        return '正常';
    }
}


// ---------------------------------------------------------------------
// 2. DatabaseServer クラスを作る(継承 extends・parent::)
// ---------------------------------------------------------------------
// 「データベースサーバー」は、名前・IP・CPU使用率という点では
// 普通のサーバー(Server)と同じですが、「どのデータベースエンジンを
// 使っているか(MySQL, PostgreSQLなど)」という追加の情報を持ちます。
//
// こういうとき、Server クラスの内容をそっくりコピーして
// 新しいクラスを書くのではなく、「extends(継承)」を使うことで、
// Server クラスの機能を引き継ぎながら、差分(追加のプロパティや
// メソッド)だけを書き足すことができます。
//
//   class DatabaseServer extends Server
//                         ^^^^^^^^^^^^^
//                         「DatabaseServerはServerを継承する」という宣言
//
// この宣言により、DatabaseServer は Server が持つ
// getName()/getIp()/getCpuUsage()/getStatus() を、
// あらためて書き直すことなくそのまま使えるようになります。
// (Server側のプロパティが private のままでも、Server自身が用意した
//  public な getter メソッド経由でなら、子クラスからも値を取得できます)
class DatabaseServer extends Server
{
    // Server にはない、DatabaseServer だけが持つ追加のプロパティです。
    // これも private にして、カプセル化の考え方を統一しています。
    private string $dbEngine;

    // ---- コンストラクタ ----
    // DatabaseServer は Server よりも引数が1つ多い(dbEngineの分)ため、
    // コンストラクタも新しく定義し直す必要があります。
    public function __construct(string $name, string $ip, int $cpuUsage, string $dbEngine)
    {
        // parent::__construct(...) と書くと、親クラス(Server)の
        // コンストラクタをそのまま呼び出せます。
        // これにより、$name・$ip・$cpuUsage の初期化処理を
        // Server クラス側のコードに任せることができ、
        // 「同じ初期化処理を2つのクラスに二重に書く」ことを防げます。
        //
        // 注意: もしこの行を書き忘れると、親クラス側のプロパティ
        // ($name, $ip, $cpuUsage)が初期化されないままになり、
        // getName() などを呼び出したときにエラーになってしまいます。
        parent::__construct($name, $ip, $cpuUsage);

        // 自分自身(DatabaseServer)だけが持つプロパティは、
        // 通常のプロパティと同じように $this->dbEngine = ... で初期化します。
        $this->dbEngine = $dbEngine;
    }

    // dbEngine 用の getter です。書き方は Server クラスの getter と同じです。
    public function getDbEngine(): string
    {
        return $this->dbEngine;
    }

    // ---- getInfo(): データベースサーバー用のまとめ表示メソッド ----
    // Server クラスにはない、DatabaseServer 独自のメソッドです。
    // 親クラスから引き継いだ getName()/getIp()/getCpuUsage() と、
    // 自分自身の getDbEngine()、そして親クラスの getStatus() を
    // 組み合わせて、1行の説明文を作ります。
    public function getInfo(): string
    {
        // parent::getStatus() と書くと、親クラス(Server)の
        // getStatus() メソッドを明示的に呼び出せます。
        // 今回は DatabaseServer 側で getStatus() を上書き(オーバーライド)
        // していないので、実は $this->getStatus() と書いても同じ結果に
        // なりますが、「親クラスの処理を意図的に使っている」ということが
        // コードを読むだけでわかるように、ここでは parent:: を使っています。
        $status = parent::getStatus();

        // getName()・getIp()・getCpuUsage() は DatabaseServer 自身が
        // 定義しているわけではありませんが、Server クラスを継承しているため、
        // $this->getName() のようにそのまま呼び出すことができます。
        return sprintf(
            '%s (%s) - IP: %s / CPU使用率: %d%% / 状態: %s',
            $this->getName(),
            $this->dbEngine,
            $this->getIp(),
            $this->getCpuUsage(),
            $status
        );
    }
}


// ---------------------------------------------------------------------
// 3. 複数のサーバーをまとめて一覧表示する(オブジェクトの配列・foreach)
// ---------------------------------------------------------------------
echo "===== サーバー一覧 =====" . PHP_EOL;

// new クラス名(...) と書くことで、クラスという「設計図」から
// 実際のオブジェクト(インスタンス)を作ることができます。
// ここでは Server を3個、DatabaseServer を1個作り、
// 同じ1つの配列 $servers にまとめて入れています。
//
// PHPの配列は、Server のオブジェクトと DatabaseServer のオブジェクトを
// 同じ配列に混ぜて入れることができます
// (DatabaseServer は Server を継承しているため、広い意味では
//  「DatabaseServerもServerの一種」として扱えるからです)。
$servers = [
    new Server('web01', '192.168.1.10', 45),
    new Server('web02', '192.168.1.11', 78),
    new Server('app01', '192.168.1.12', 95),
    new DatabaseServer('db01', '192.168.1.20', 60, 'MySQL'),
];

// foreach ($servers as $server) は、これまでの演習で使ってきた
// 「配列を1つずつ取り出す」書き方と全く同じです。
// 配列の中身が数値や文字列ではなく「オブジェクト」に変わっただけで、
// 使い方そのものは変わりません。
foreach ($servers as $server) {
    // instanceof 演算子を使うと、「$server が DatabaseServer クラス
    // (またはその子クラス)のインスタンスかどうか」を true/false で
    // 判定できます。
    //
    // DatabaseServer だけが持っている getInfo() や getDbEngine() を
    // 呼び出す前に、この instanceof でチェックしておくことで、
    // 「Server にはない getInfo() を呼び出そうとしてエラーになる」
    // といった事故を防げます。
    if ($server instanceof DatabaseServer) {
        // DatabaseServer専用の、データベースエンジン名も含めた
        // まとめ表示メソッドを使います。
        echo $server->getInfo() . PHP_EOL;
    } else {
        // 通常の Server の場合は、getter メソッドを組み合わせて、
        // DatabaseServer の getInfo() と近いフォーマットの
        // 1行の文字列を作って表示します。
        echo sprintf(
            '%s - IP: %s / CPU使用率: %d%% / 状態: %s',
            $server->getName(),
            $server->getIp(),
            $server->getCpuUsage(),
            $server->getStatus()
        ) . PHP_EOL;
    }
}

echo PHP_EOL;

// 参考: プロパティが private であることを、実際に確認してみます。
// 下のコメントアウトを外して実行すると、
// 「Cannot access private property Server::$name」という
// Fatal error が発生することを確認できます(カプセル化の効果です)。
//
// echo $servers[0]->name;

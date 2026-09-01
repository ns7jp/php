<?php

// =====================================================================
// Server クラス
// =====================================================================
// 演習13(オブジェクト指向プログラミングの基礎)で作った Server クラスを
// 発展させたものです。監視対象のサーバー1台分の情報
// (名前・IPアドレス・CPU使用率・メモリ使用率)を1つのオブジェクトとして
// まとめて扱います。
//
// 演習13との主な違い:
//   ・CPU使用率だけでなく、メモリ使用率(memoryUsage)も持たせている
//   ・getStatus() が、CPUとメモリの「悪いほう」の値を見て判定するように
//     拡張されている
//   ・config.json から読み込んだ連想配列を、そのまま Server オブジェクトに
//     変換できる fromArray() という「名前付きコンストラクタ」を追加している
//   ・レポート出力(HTML化など)で使いやすいよう、オブジェクトの中身を
//     連想配列に戻す toArray() を追加している
// =====================================================================
class Server
{
    // ---- 状態判定のしきい値を「定数」として定義する ----
    // マジックナンバー(70や90といった、意味の説明がないまま
    // コードのあちこちに書かれた数値)をそのまま使うのではなく、
    // 名前の付いた定数としてまとめておくことで、
    //   ・この数値が「注意」「危険」のしきい値であることが読んだだけで分かる
    //   ・しきい値を変更したくなったときに、この2行を直すだけで済む
    // というメリットがあります。
    private const WARNING_THRESHOLD = 70;
    private const CRITICAL_THRESHOLD = 90;

    // ---- プロパティ(すべて private にしてカプセル化する) ----
    private string $name;
    private string $ip;
    private int $cpuUsage;
    private int $memoryUsage;

    /**
     * @param string $name        サーバー名(例: "web01")
     * @param string $ip          IPアドレス(例: "192.168.1.10")
     * @param int    $cpuUsage    CPU使用率(0〜100のパーセンテージ想定)
     * @param int    $memoryUsage メモリ使用率(0〜100のパーセンテージ想定)
     */
    public function __construct(string $name, string $ip, int $cpuUsage, int $memoryUsage)
    {
        $this->name = $name;
        $this->ip = $ip;
        $this->cpuUsage = $cpuUsage;
        $this->memoryUsage = $memoryUsage;
    }

    // ---------------------------------------------------------------
    // fromArray(): 「名前付きコンストラクタ」パターン
    // ---------------------------------------------------------------
    // config.json を json_decode() すると、1台分のサーバー情報は
    //   ["name" => "web01", "ip" => "192.168.1.10", "cpuUsage" => 42, "memoryUsage" => 55]
    // のような連想配列として手に入ります。
    //
    // この連想配列を毎回
    //   new Server($row['name'], $row['ip'], $row['cpuUsage'], $row['memoryUsage'])
    // と書いて回るのは面倒ですし、キー名のタイプミスにも気づきにくくなります。
    //
    // そこで static(静的)なメソッドとして fromArray() を用意し、
    // 「連想配列を受け取って、Server オブジェクトを組み立てて返す」処理を
    // このクラス自身に持たせています。呼び出す側は
    //   $server = Server::fromArray($row);
    // と書くだけでよくなり、Serverオブジェクトの組み立て方の詳細を
    // 呼び出し側が知らなくても済むようになります(このような
    // 「new クラス名(...)の代わりに使う、分かりやすい名前の静的メソッド」
    // のことを「名前付きコンストラクタ」と呼びます)。
    //
    // ??演算子(null合体演算子)を使うことで、万が一キーが存在しない
    // 壊れたデータが来ても、いきなりエラーで止まるのではなく、
    // 空文字や0などの安全な初期値で補ってオブジェクトを作成します。
    public static function fromArray(array $data): self
    {
        return new self(
            (string) ($data['name'] ?? ''),
            (string) ($data['ip'] ?? ''),
            (int) ($data['cpuUsage'] ?? 0),
            (int) ($data['memoryUsage'] ?? 0)
        );
    }

    // ---- getter(値を取得するためのメソッド) ----
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

    public function getMemoryUsage(): int
    {
        return $this->memoryUsage;
    }

    // ---------------------------------------------------------------
    // getStatus(): CPU使用率とメモリ使用率から「状態」を判定する
    // ---------------------------------------------------------------
    // CPUとメモリのうち、値が大きい(=より逼迫している)ほうを基準にして
    // 判定します。max() を使うことで、「どちらか一方でもしきい値を
    // 超えていれば、そのサーバー全体を注意/危険とみなす」という
    // 安全側に倒した判定ができます。
    //   ・CPU 95% / メモリ 40% のサーバー → 危険(CPUが90%以上のため)
    //   ・CPU 40% / メモリ 95% のサーバー → 危険(メモリが90%以上のため)
    // のように、どちらの指標が悪化していても正しく「危険」を検知できます。
    public function getStatus(): string
    {
        $worst = max($this->cpuUsage, $this->memoryUsage);

        if ($worst >= self::CRITICAL_THRESHOLD) {
            return '危険';
        }

        if ($worst >= self::WARNING_THRESHOLD) {
            return '注意';
        }

        return '正常';
    }

    // ---------------------------------------------------------------
    // toArray(): オブジェクトの中身を連想配列に変換する
    // ---------------------------------------------------------------
    // fromArray() とは逆方向の変換です。ダッシュボードの表を作ったり、
    // HTMLレポートに埋め込んだりする際、オブジェクトのままだと
    // 扱いにくい場面があるため、必要に応じて「配列の形」に戻せるように
    // しておくと便利です(演習09で学んだ「配列とJSONの相互変換」の
    // 考え方にも通じます)。
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'ip' => $this->ip,
            'cpuUsage' => $this->cpuUsage,
            'memoryUsage' => $this->memoryUsage,
            'status' => $this->getStatus(),
        ];
    }
}

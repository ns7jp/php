<?php

// =====================================================================
// ConfigException クラス
// =====================================================================
// 演習14(例外処理とエラーログ)で学んだ「独自の例外クラス」の考え方を、
// このcapstoneプロジェクトでも活用します。
//
// 「設定ファイル(config.json)まわりで何か問題が起きた」ということを
// 表す専用の例外クラスです。標準の RuntimeException を継承しており、
// PHPにもとから用意されている例外の仕組み(throw / catch / getMessage()
// など)をそのまま利用できます。
//
// わざわざ専用のクラスを用意するメリットは、呼び出し側で
//   catch (ConfigException $e) { ... }
// のように書くことで、「設定ファイルに関するエラーだけ」を狙って
// catchできる点です。もし単に Exception だけを使っていた場合、
// 本来まったく別の原因(タイプミスによるバグなど)で発生した例外まで
// 同じ catch ブロックで受け止めてしまう恐れがあります。
class ConfigException extends RuntimeException
{
    // どの設定ファイルが原因だったのかを、あとから取り出せるように
    // 専用のプロパティとして保持しておきます。
    private string $path;

    /**
     * @param string $path   問題が起きた設定ファイルのパス
     * @param string $reason 何が問題だったのか(未検出・JSON不正など)を表す短い説明
     */
    public function __construct(string $path, string $reason)
    {
        // 開発者・運用担当者がログを見てすぐに原因を特定できるよう、
        // 「どのファイルの」「何が」問題だったのかをまとめた
        // 詳しいメッセージを組み立てます。
        $message = sprintf('設定ファイルの読み込みに失敗しました(%s): %s', $path, $reason);

        // 親クラス(RuntimeException、さらにその親のException)の
        // コンストラクタを呼び出し、$message を「例外のメッセージ」として
        // 登録します。これにより $e->getMessage() で取り出せるようになります。
        parent::__construct($message);

        $this->path = $path;
    }

    // 原因となったファイルパスだけを個別に取得したい場合のためのgetterです。
    public function getPath(): string
    {
        return $this->path;
    }
}

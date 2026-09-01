<?php

// =====================================================================
// LogFileException クラス
// =====================================================================
// ConfigException と同じ考え方で、今度は「アクセスログファイル
// (access.log)まわりで何か問題が起きた」ことを表す専用の例外クラスです。
//
// 「設定ファイルの問題」と「ログファイルの問題」を、あえて別々の
// クラスに分けておくことで、catch する側で
//   catch (ConfigException $e) { ... }   // 設定ファイルの問題への対処
//   catch (LogFileException $e) { ... }  // ログファイルの問題への対処
// のように、原因ごとに異なる対応(表示するメッセージや、後続処理を
// 続けるかどうかなど)を書き分けやすくなります。
class LogFileException extends RuntimeException
{
    // どのログファイルが原因だったのかを保持しておきます。
    private string $path;

    /**
     * @param string $path   問題が起きたログファイルのパス
     * @param string $reason 何が問題だったのか(未検出・読み込み失敗など)を表す短い説明
     */
    public function __construct(string $path, string $reason)
    {
        $message = sprintf('アクセスログの読み込みに失敗しました(%s): %s', $path, $reason);

        parent::__construct($message);

        $this->path = $path;
    }

    public function getPath(): string
    {
        return $this->path;
    }
}

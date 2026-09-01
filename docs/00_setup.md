# 環境構築ガイド

このリポジトリの演習は、**PHPさえインストールされていれば、Webサーバーやデータベースサーバーなしで**
すべてコマンドライン(ターミナル)から実行・確認できます。難しい環境構築は不要です。

## 1. PHPをインストールする

自分のOSに合わせて、以下のいずれかの方法でPHPをインストールしてください。
バージョンは **PHP 8.0以上**を推奨します(演習内で`match`式などPHP8以降の構文を使用しています)。

### Windows

1. [PHP公式サイト](https://windows.php.net/download/)から「Non Thread Safe」版のZIPをダウンロードして展開する
2. 展開したフォルダに`PATH`を通す(環境変数`Path`にフォルダのパスを追加する)
3. コマンドプロンプトまたはPowerShellで `php -v` を実行し、バージョンが表示されれば成功

もしくは、[Scoop](https://scoop.sh/)を使っている場合は次のコマンドでも導入できます。

```powershell
scoop install php
```

### macOS

[Homebrew](https://brew.sh/)を使うのが簡単です。

```bash
brew install php
php -v
```

### Linux (Debian/Ubuntu系)

```bash
sudo apt update
sudo apt install php-cli php-sqlite3 php-mbstring
php -v
```

`php-sqlite3` は演習12(PDOとデータベース連携)で、`php-mbstring` は日本語の文字列処理を含む
一部の演習で使用します。ディストリビューションによってパッケージ名が異なる場合があります
(例: Fedora/RHEL系では `sudo dnf install php-cli php-pdo`)。

### Docker を使う場合(インストールしたくない人向け)

PHPをOSに直接インストールしたくない場合は、Dockerの公式PHPイメージをその場限りで使う方法もあります。

```bash
# リポジトリのルートディレクトリで実行する例
docker run --rm -it -v "$PWD":/app -w /app php:8.3-cli bash
```

このコマンドでコンテナ内のシェルに入ったあとは、通常どおり `php exercises/01_variables_datatypes/answer.php`
のようにコマンドを実行できます。

## 2. インストールできたか確認する

ターミナルで次のコマンドを実行し、バージョン情報が表示されればインストール成功です。

```bash
php -v
```

```
PHP 8.x.x (cli) ...
```

## 3. リポジトリを取得する

```bash
git clone <このリポジトリのURL>
cd php
```

## 4. 最初の演習を実行してみる

まずは動作確認として、演習01の模範解答を実行してみましょう。

```bash
php exercises/01_variables_datatypes/answer.php
```

エラーなく、いくつかの変数の中身が表示されれば準備完了です。
続きは [../README.md](../README.md) の「学習の進め方」と [01_roadmap.md](./01_roadmap.md) を参照してください。

## つまずいたときは

- `php: command not found` → PHPのインストール、または`PATH`の設定を見直してください。
- `Parse error: syntax error` → 自分でコードを書き換えている場合は、コピペミスや全角スペースの
  混入(日本語入力時に誤って混ざりやすい)を疑ってください。`php -l ファイル名` で構文チェックだけ
  行うことができます。
- 演習12(PDOとデータベース連携)で `could not find driver` のようなエラーが出る場合は、
  `pdo_sqlite` 拡張機能が有効になっているか `php -m` で確認してください。

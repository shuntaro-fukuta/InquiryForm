# お問い合わせフォーム

### 開発環境
- OS: Ubuntu18.04.5 LTS
- Webサーバー:Apache2.4.29
- DB: MySQL5.7.32-0
- 言語:PHP7.2

- 仮想環境
  - VirtualBox:6.1.10
  - Vagrant:2.2.9

<br>

### 環境構築手順
1. クローン  
  ``` git clone https://github.com/shuntaro-fukuta/InquiryForm.git ```
2. Vagrantfileがあるディレクトリへ移動  
  ``` cd InquiryForm ```
3. 仮想環境作成  
  ``` vagrant up --provision ```
4. 設定ファイルの作成  
    - configファイルのディレクトリへ移動  
        →   ``` cd config ```

      #### DB設定
            1. cp database_config.php.example database_config.php
            ※内容はそのままで問題ありません

      #### メール設定
            1. cp mail_config.php.example mail_config.php
            2. 内容の書き換え(mail_config.php)
                1. 'auth' > 'user_inquiry' 内をユーザーへのメール送信に使用するSMTPサーバの認証情報に書き換えてください
                2. 'auth' > 'admin_inquiry' 内を管理者へのメール送信に使用するSMTPサーバの認証情報に書き換えてください
                3. 'header' > 'user_inquiry' > 'From', 'ReplyTo' を適宜変更してください
                4. 'header' > 'admin_inquiry' > 'To', 'From', 'ReplyTo' を送信先のメールアドレスへ変更してください

#### テーブル内データ確認手順
1. ``` vagrant ssh ```  
2. ``` sudo mysql -u skillcheck -p ```  
  → パスワード = 'password'
3. ``` use skillcheck; ```
4. ``` SELECT * FROM inquiries; ```

<br>

### 実装に費やした時間
- 30時間

<br>

### 実装中の問題となったところ・工夫したところ
- 問題となったところ
  - 開発環境構築  
    → Vagrantでの環境構築が初めてだったので、下記のようなポイントでつまづいた。
      - Vagrantファイルの設定
      - シェルスクリプト
      - サーバー各種設定
- 工夫したところ
  - セキュリティ対策
    - SQLインジェクション
      - プレースホルダの使用
    - クリックジャッキング
      - X-Frame-Optionsヘッダの仕様
    - CSRF
      - トークン認証
    - XSS
      - 入力値の出力箇所はエスケープ処理
  - クラス設計  
    - Configクラス
    - PDOラッパークラス
    - バリデーションクラス
        → バリデーションに関しては機能追加のしやすさを考慮して設計した。  
        → バリデーションのルール名のメソッドを用意+ルールの配列に追加するだけで呼び出せるようになっている。
    - メール送信  
    #### 工夫点
    - 関連処理をまとめてクラス化することで、メインロジックをシンプルにした  
      → 関連処理の責任がクラスへまとまることで、可読性・保守性が向上する  
      → チームでモジュールごとに分担して同時開発を行うことも可能になる。
    - 全体的に引数の内容をチェックし、ミスがあれば例外を投げるようにしているので、間違いがあればすぐに落とすような方針で設計を行った。  
    → このような設計にすることで、"どこかがおかしい"という状態でのバグの原因調査を減らすことができると考えた。
  - 2重サブミット対策
    #### UX的な対策
    - JSを使用してボタンの非活性化  
      → ボタンを連続で押してしまって2重送信したかどうか、ユーザーが不安になることを防ぐ
    #### システム的な対策
    - トークン認証  
      → 意図的なリロードでの再送信を防ぐ

  - 環境情報を設定ファイルへ抽出  
      → 直接ソースコードをいじることなく設定情報を変更できる  
      → 現在は実装できていないが、ENVIRONMENTのような環境変数(値はdevelop, production等)を用意し、それぞれの設定ファイルを用意することで、環境の切り分けがしやすくなる。
  - 環境構築の自動化
      - コマンド1発で環境構築できるようにした(設定ファイルの書き換えは必要)  
        → 環境構築部分を全てコードで管理できるため、メンバーそれぞれの環境に差分が生じる可能性を減らすことができる。

<br>

### 改善点
- オートローダーの導入
- パッケージ管理ツールの導入
- 設定ファイルの環境ごとの切り替え(テスト環境、本番環境等)
- テストコードの作成
- 日時別やログレベル別に応じたログの出力

<br>

### どのような動作テストを行ったか
- 正常形動作確認
  1. フォームへ正常値を入力＆送信ボタン押下  
    → 確認画面へ遷移することを確認
  2. 入力内容の確認  
    → 入力フォームで入力した内容が表示されていることを確認
  3. 送信ボタンを押下  
    → 入力した内容がDBへ保存されいること＆メールが送信されたことを確認(ユーザー宛&管理者宛)
- 異常系動作確認
  - バリデーション確認  
    - 別フォームを用意(formのaction, method属性は正規の入力フォームと同様)し、各項目に対して異常系の入力&送信ボタン押下  
    → 入力画面に戻ること確認  
    → 適切なエラーメッセージが表示されていることを確認
    ```フォーム例
    <body>
      <form action="inquiry_form.php" method="post">
        <input type="text" name="subject" value="">
        <input type="text" name="name" value="">
        <input type="text" name="email" value="">
        <input type="text" name="telephone_number" value="">
        <input type="text" name="inquiry" value="">
        <input type="submit">
      </form>
    </body>
    ```
  - 各種セキュリティ対策の確認
    - SQLインジェクション  
      - 別フォームを用意(上記のフォーム例と同じ)し、各項目に  ' delete from inquiries; を入力して送信
    - クリックジャッキング
      - 別HTMLを用意して,iframeタグのsrc属性に各種ページを指定する
    - CSRF
      - 別フォームを用意して、確認画面、更新画面にPOSTリクエスト送信する
    - XSS
      - 別フォームを用意(上記のフォーム例と同じ)し、各種値に```<script>alert('hoge')</script>```
      を入力して送信
  - メール送信テスト
    - mail_config.phpの書き換えにより、Gmail,iCloud,Yahooのメールで問題なく送信できることを確認。
  - その他チェック  
    - 修正ボタン押下後、入力値が保持されているかどうか
    - 入力画面を経由せず確認画面、登録画面にアクセス(=GETリクエスト送信)
    - 確認画面で送信ボタンを押下後、ブラウザの戻るボタンで確認画面へ戻る


<br>

### 参考資料又は参考サイト
【環境構築】
・Vagrant
- Vagrant+VirtualBoxでUbuntu環境構築  
https://qiita.com/w2-yamaguchi/items/191830191f8af05ac4dd
- VagrantとVirtualBoxを使って仮想環境を構築しよう
https://dev83.com/vagrant01/
- VirtualBoxとVagrantでLAMP環境を構築する  
https://dev83.com/virtualbox-vagrant-lamp/
- Vagrantfile で Linux, Apache, MySQL, PHP (LAMP) を一括構築！  
https://www.superbusinessman.biz/build-lamp-stack-from-vagrantfile/
- VirtualBox + Vagrantで「CentOS7 + LAMP環境」を構築する  
https://www.willstyle.co.jp/blog/2832/
- vagrant において、明示的に VirtualBox の機能(vboxsf)を使いフォルダを同期するには、`config.vm.synced_folder ".", "/vagrant", type: "virtualbox"` のように Vagrantfile に設定するとよい  
https://qiita.com/toby_net/items/6eb74471871ab9fba087

・シェルスクリプト
- bashのfor文を使ってディレクトリ内のファイル一覧とディレクトリ一覧を出力  
http://wordpress.honobono-life.info/code/bash%E3%82%B9%E3%82%AF%E3%83%AA%E3%83%97%E3%83%88%E3%81%A7%E3%83%87%E3%82%A3%E3%83%AC%E3%82%AF%E3%83%88%E3%83%AA%E5%86%85%E3%81%AE%E3%83%95%E3%82%A1%E3%82%A4%E3%83%AB%E4%B8%80%E8%A6%A7%E3%81%A8/

- シェルスクリプトからSQLを実行する3種類の方法  
https://www.bunkei-programmer.net/entry/20110330/1301493631

【PHP】
- 各種リファレンス  
https://www.php.net/

【セキュリティ】
- IPA「安全なウェブサイトの作り方」  
https://www.ipa.go.jp/files/000017316.pdf
- セキュリティ実装 チェックリスト ※下記リンクを踏むとExcelがダウンロードされます
https://www.ipa.go.jp/files/000044403.xlsx
- セキュリティーを考慮したメールフォームの作り方  
https://qiita.com/vber_program/items/5f47dd59dcbd671aa17b
- 【PHP初心者向け】セキュアな掲示板を最小構成から作る  
https://qiita.com/mpyw/items/2c54d0ea95423bd88f60#%E5%8F%82%E8%80%83-%E3%81%9D%E3%81%AE%E4%BB%96%E3%81%AE%E8%84%86%E5%BC%B1%E6%80%A7%E3%81%B8%E3%81%AE%E5%AF%BE%E7%AD%96
- X-Frame-Options について簡単に調べてみた  
https://qiita.com/gotchane/items/4d31b01381f47100de7f

- 【PHP】作成したメールフォームに脆弱性がないか、アドバイスもらえないでしょうか。  
https://teratail.com/questions/71592
- teratailに投稿されたメールフォームにCSRF脆弱性が残存した理由（ひとつ上ののTeratailと関連した記事)
https://blog.tokumaru.org/2017/04/teratailcsrf.html
- CSRF トークンの作成方法と、random_bytes の適切なバイト数  
https://teratail.com/questions/211626
- PHPへのメールヘッダーインジェクション
https://blog.ohgaki.net/php-mail-header-injection

・お問い合わせフォーム関連
- お問い合わせフォームをつくる  
https://qiita.com/raitehu/items/1362dd5201b9e5a270c5
- コンタクトフォーム（お問い合わせページ）の作り方  
https://www.webdesignleaves.com/pr/php/php_contact_form_02.php

・メール
- PHPMailerでメールをSTMP送信する  
https://qiita.com/e__ri/items/857b12e73080019e00b5
- PHPMailerを使ってGmailのSMTP経由でメールを送信する際にSMTP connect() failed.と出て困った  
https://qiita.com/takuya-andou/items/98be291a6a8b6b5515b1
- PHPMailerの使い方  
https://its-office.jp/blog/php/2017/05/28/phpmailer.html
- 主要なメールサーバーのPOP/IMAPメール設定情報・ポート一覧
https://itojisan.xyz/trouble/8173/#Gmail

・PDOラッパークラス  
https://github.com/mikehenrty/thin-pdo-wrapper/blob/master/src/PDOWrapper.php
https://github.com/paragonie/easydb

【JS】
- Chromeの場合、submitボタン押下時にボタンをdisabledにすると、フォームデータが送信されない。  
https://qiita.com/neko_the_shadow/items/1e828a05621aaa3539b1

【HTTP】
- HTTP レスポンスステータスコード  
https://developer.mozilla.org/ja/docs/Web/HTTP/Status

【DB】
- MySQL 文字コード確認  
https://qiita.com/yukiyoshimura/items/d44a98021608c8f8a52a
- mysqlで文字コードをutf8にセットする  
https://qiita.com/YusukeHigaki/items/2cab311d2a559a543e3a

【Git】
GitHub 上でサクッと空のディレクトリを作成する方法
https://qiita.com/tommy_aka_jps/items/b2ae85cbeab77e12a925

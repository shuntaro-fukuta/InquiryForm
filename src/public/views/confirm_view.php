<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>確認画面</title>
</head>
<body>
  <form action="register.php" method="post">
    <p>件名：<?= h(InquiryForm::SUBJECT[$inquiry_form->getSubject()]) ?></p>
    <p>名前：<?= h($inquiry_form->getName()) ?></p>
    <p>メールアドレス：<?= h($inquiry_form->getEmail()) ?></p>
    <p>電話番号：<?= h($inquiry_form->getTelephoneNumber()) ?></p>
    <p>お問い合わせ内容：</p>
    <div>
      <?= nl2br(h($inquiry_form->getInquiry())) ?>
    </div>

    <input type="button" value="修正" onclick="location.href='inquiry_form.php'">
    <input type="submit" value="送信">
  </form>
</body>
</html>

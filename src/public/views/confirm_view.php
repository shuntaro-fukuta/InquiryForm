<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>確認画面</title>
</head>
<body>
  <form id="confirm_form" action="register.php" method="post">
    <p>件名：<?= h(InquiryForm::SUBJECT[$inquiry_form->getSubject()]) ?></p>
    <p>名前：<?= h($inquiry_form->getName()) ?></p>
    <p>メールアドレス：<?= h($inquiry_form->getEmail()) ?></p>
    <p>電話番号：<?= h($inquiry_form->getTelephoneNumber()) ?></p>
    <p>お問い合わせ内容：</p>
    <div>
      <?= nl2br(h($inquiry_form->getInquiry())) ?>
    </div>

    <input type="hidden" name="csrf_token" value="<?= h($_SESSION['csrf_token']) ?>">
    <input id="back_button" type="button" value="修正" onclick="location.href='inquiry_form.php'">
    <input id="submit_button" type="button" value="送信">
  </form>

  <script>
    const submit_button = document.getElementById('submit_button');
    const back_button = document.getElementById('back_button');
    const confirm_form= document.getElementById('confirm_form');
    submit_button.addEventListener('click', () => {
      back_button.disabled = true;
      submit_button.disabled = true;
      confirm_form.submit();
    });
  </script>
</body>
</html>

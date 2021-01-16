<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>お問い合わせフォーム</title>
</head>
<body>
  <h2>お問い合わせ</h2>
    <form action="confirm.php" method="post">　
      <label for="subject">件名</label>
      <select name="subject" id="subject">
        <option value="0" <?php if (isset($inquiry_form) && $inquiry_form->getSubject() === '0') echo 'selected' ?> >ご意見</option>
        <option value="1" <?php if (isset($inquiry_form) && $inquiry_form->getSubject() === '1') echo 'selected' ?>>ご感想</option>
        <option value="2" <?php if (isset($inquiry_form) && $inquiry_form->getSubject() === '2') echo 'selected' ?>>その他</option>
      </select>
      <?php if (!empty($errors) && isset($errors['subject'])) : ?>
        <?php foreach ($errors['subject'] as $error) : ?>
          <br>
          <span style="color: red;"><?= h($error) ?></span>
        <?php endforeach ?>
      <?php endif ?>

      <br>

      <label for="name">名前(必須)</label>
      <input id="name" name="name" type="text" required placeholder="山田 太郎" value="<?= (isset($inquiry_form)) ? h($inquiry_form->getName()) : '' ?>">
      <?php if (!empty($errors) && isset($errors['name'])) : ?>
        <?php foreach ($errors['name'] as $error) : ?>
          <br>
          <span style="color: red;"><?= h($error) ?></span>
        <?php endforeach ?>
      <?php endif ?>

      <br>

      <label for="email">メールアドレス(必須)</label>
      <input id="email" name="email" type="email" required placeholder="tarou@example.com" value="<?= (isset($inquiry_form)) ? h($inquiry_form->getEmail()) : '' ?>">
      <?php if (!empty($errors) && isset($errors['email'])) : ?>
        <?php foreach ($errors['email'] as $error) : ?>
          <br>
          <span style="color: red;"><?= h($error) ?></span>
        <?php endforeach ?>
      <?php endif ?>

      <br>

      <label for="telephone_number">電話番号(必須)</label>
      <small>※半角、ハイフンなし</small>
      <br>
      <input id="telephone_number" name="telephone_number" type="text" required placeholder="08012341234" value="<?= (isset($inquiry_form)) ? h($inquiry_form->getTelephoneNumber()) : '' ?>">
      <?php if (!empty($errors) && isset($errors['telephone_number'])) : ?>
        <?php foreach ($errors['telephone_number'] as $error) : ?>
          <br>
          <span style="color: red;"><?= h($error) ?></span>
        <?php endforeach ?>
      <?php endif ?>

      <br>

      <label for="inquiry">お問い合わせ内容(必須)</label>
      <small>※1000文字以内</small>
      <br>
      <textarea id="inquiry" name="inquiry" cols="30" rows="10"><?= (isset($inquiry_form)) ? h($inquiry_form->getInquiry()) : '' ?></textarea>
      <?php if (!empty($errors) && isset($errors['inquiry'])) : ?>
        <?php foreach ($errors['inquiry'] as $error) : ?>
          <br>
          <span style="color: red;"><?= h($error) ?></span>
        <?php endforeach ?>
      <?php endif ?>

      <br>

      <input type="submit" value="送信">
    </form>
  </body>
</html>

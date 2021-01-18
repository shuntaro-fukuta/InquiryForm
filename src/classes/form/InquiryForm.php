<?php

require_once(CLASSES_DIR . DIR_SEP . 'Validator.php');

class InquiryForm
{
    private $subject;
    private $name;
    private $email;
    private $telephoneNumber;
    private $inquiry;

    const SUBJECT = [
        0 => 'ご意見',
        1 => 'ご感想',
        2 => 'その他',
    ];

    private static $displayNames = [
        'subject' => '件名',
        'name' => '名前',
        'email' => 'メールアドレス',
        'telephone_number' => '電話番号',
        'inquiry' => 'お問い合わせ内容',
    ];

    public function setSubject(?string $subject)
    {
        $this->subject = trim($subject);
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function setName(?string $name)
    {
        $this->name = trim($name);
    }

    public function getName()
    {
        return $this->name;
    }

    public function setEmail(?string $email)
    {
        $this->email = trim($email);
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setTelephoneNumber(?string $telephoneNumber)
    {
        $this->telephoneNumber = trim($telephoneNumber);
    }

    public function getTelephoneNumber()
    {
        return $this->telephoneNumber;
    }

    public function setInquiry(?string $inquiry)
    {
        $this->inquiry = trim($inquiry);
    }

    public function getInquiry()
    {
        return $this->inquiry;
    }

    public static function getValidationRules()
    {
        return [
            'subject' => ['required', 'in:' . implode(',', array_keys(self::SUBJECT))],
            'name' => ['required', 'limit:30'],
            'email' => ['required', 'email'],
            'telephone_number' => ['required', 'phone'],
            'inquiry' => ['required', 'limit:1000'],
        ];
    }

    public function validate()
    {
        $validator = new Validator(self::getValidationRules(), [
            'subject' => $this->getSubject(),
            'name' => $this->getName(),
            'email' => $this->getEmail(),
            'telephone_number' => $this->getTelephoneNumber(),
            'inquiry' => $this->getInquiry(),
        ], self::$displayNames);

        return $validator->execute();
    }

    public static function getDisplayName(string $elementName)
    {
        return self::$displayNames[$elementName] ?? null;
    }
}

<?php

class Validator
{
    private $validationRules;
    private $inputs;
    private $displayNames;

    const ERROR_FORMAT_REQUIRED = '%sを入力してください';
    const ERROR_FORMAT_PHONE = '%sはハイフンなしの10桁又は11桁の数字でで入力してください';
    const ERROR_FORMAT_INVALID = '%sの形式が不正です';
    const ERROR_FORMAT_LIMIT = '%sは%s文字以内で入力してください';
    const ERROR_FORMAT_IN = '%sは(%s)のいずれかで入力してください';

    const CONDITION_SEPARATOR = ':';

    public function __construct(array $validationRules, array $inputs, array $displayNames = []) {
        $this->setRules($validationRules);
        $this->setInputs($inputs);
        $this->displayNames = $displayNames;
    }

    private function setRules(array $validationRules)
    {
        if (!is_array($validationRules)) {
            throw new InvalidArgumentException('validationRules must be an array.');
        }

        foreach ($validationRules as $rules) {
            foreach ($rules as $rule) {
                $ruleName = explode(self::CONDITION_SEPARATOR, $rule)[0];
                if (!method_exists($this, $ruleName)) {
                    throw new InvalidArgumentException('rule "' . $ruleName . '" is undefined.');
                }
            }
        }

        $this->validationRules = $validationRules;
    }

    private function setInputs(array $inputs)
    {
        if (!is_array($inputs)) {
            throw new InvalidArgumentException('inputs must be an array.');
        }

        $this->inputs = $inputs;
    }

    public function execute()
    {
        $errors = [];

        foreach ($this->validationRules as $elementName => $rules) {
            $input = $this->inputs[$elementName] ?? null;
            foreach ($rules as $rule) {
                $error = null;

                $hasCondition = (count(explode(self::CONDITION_SEPARATOR, $rule)) > 1);
                if ($hasCondition) {
                    $ruleName = explode(self::CONDITION_SEPARATOR, $rule)[0];
                    $condition = explode(self::CONDITION_SEPARATOR, $rule)[1];

                    $error = $this->$ruleName($elementName, $condition, $input);
                } else {
                    $ruleName = $rule;

                    $error = $this->$ruleName($elementName, $input);
                }

                if (isset($error)) {
                    if (!isset($errors[$elementName])) {
                        $errors[$elementName] = [];
                    }

                    $errors[$elementName][] = $error;
                }
            }
        }

        return $errors;
    }

    public function required(string $elementName, ?string $input)
    {
        if (!is_empty($input)) return null;

        return sprintf(self::ERROR_FORMAT_REQUIRED, $this->getDisplayName($elementName));
    }

    public function phone(string $elementName, ?string $input)
    {
        if (is_empty($input)) return null;

        if (preg_match('/\A0\d{9,10}+\Z/', $input)) return null;

        return sprintf(self::ERROR_FORMAT_PHONE, $this->getDisplayName($elementName));
    }

    public function email(string $elementName, ?string $input)
    {
        if (is_empty($input)) return null;

        if (filter_var($input, FILTER_VALIDATE_EMAIL) !== false) return null;

        return sprintf(self::ERROR_FORMAT_INVALID, $this->getDisplayName($elementName));
    }

    public function limit(string $elementName, string $condition, ?string $input)
    {
        if (is_empty($input)) return null;

        if (!ctype_digit($condition)) {
            throw new InvalidArgumentException('condition must be "integer". "' . gettype($condition) . '" passed.');
        }

        if (strlen($input) <= $condition) return null;

        return sprintf(
            self::ERROR_FORMAT_LIMIT,
            $this->getDisplayName($elementName),
            $condition
        );
    }

    public function in(string $elementName, string $condition, ?string $input)
    {
        if (is_empty($input)) return null;

        if (in_array($input, explode(',', $condition))) return null;

        return sprintf(self::ERROR_FORMAT_IN, $this->getDisplayName($elementName), $condition);
    }

    private function getDisplayName(string $elementName)
    {
        return $this->displayNames[$elementName] ?? $elementName;
    }
}

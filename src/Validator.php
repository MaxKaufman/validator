<?php
    use Rule\RuleInterface;


    class Validator {

        public $rulesOptions = [];
        public $validated = [];

        public function setRule(string $name, RuleInterface $option): self
        {
            $this->rulesOptions[$name]["$option"] = $option;

            return $this;
        }

        public function validate(array $request): bool
        {
            $filteredFields = array_filter($request, function($key) {
                return array_key_exists($key, $this->rulesOptions);
            }, ARRAY_FILTER_USE_KEY);
            foreach ($this->rulesOptions as $name => $rules) {
                foreach ($rules as $rule) {
                    $result = $rule->validate($filteredFields, $name);

                    if($result === false) {
                        return false;
                    }
                }
            }

            $this->validated = $filteredFields;

            return true;
        }

        public function getData(): array
        {
            return $this->validated;
        }
    }

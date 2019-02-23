<?php
    namespace Rule;


    class Required implements RuleInterface
    {

        public function validate(array $request, string $name): bool
        {
            return array_key_exists($name, $request);
        }

        public function __toString()
        {
            return 'Required';
        }
    }
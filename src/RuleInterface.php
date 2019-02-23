<?php
    namespace Rule;


    interface RuleInterface
    {
        public function validate(array $request, string $name): bool;
        public function __toString();
    }
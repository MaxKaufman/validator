<?php
    namespace Rule;


    class Callback implements RuleInterface
    {
        public $callable;

        public function __construct($function)
        {
            $this->callable = $function;
            return $this;
        }

        public function __toString()
        {
            return 'Callback';
        }

        public function getCallable($value)
        {
            $func = $this->callable;
            return $func($value);
        }

        public function validate(array $request, string $name): bool
        {
            if(!array_key_exists($name, $request)) {
                return true;
            }
            return $this->getCallable($request[$name]);
        }
    }
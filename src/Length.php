<?php
    namespace Rule;


    class Length implements RuleInterface
    {
        /**
         * @var int
         */
        private $min;
        /**
         * @var int
         */
        private $max;
        /**
         * @var string
         */
        public $errorMsg = 'Length not valid';

        /**
         * Length constructor.
         * @param int $min
         * @param int $max
         */
        public function __construct(int $min, int $max)
        {
            $this->min = $min;
            $this->max = $max;
        }

        public function setErrorMessage(string $errorTemplate)
        {
            $startPos = mb_strpos($errorTemplate, '{}');
            $errorTemplate = substr_replace($errorTemplate, $this->min, $startPos, 2);
            $startPos = mb_strpos($errorTemplate, '{}');
            $this->errorMsg = substr_replace($errorTemplate, $this->max, $startPos, 2);

            return $this;
        }

        public function validate(array $request, string $name): bool
        {
            if(!array_key_exists($name, $request)) {
                return true;
            }
            if(strlen($request[$name]) >= $this->min && strlen($request[$name]) <= $this->max) {
                return true;
            }
            throw new \Exception($this->errorMsg);
        }

        public function __toString()
        {
            return 'Length';
        }
    }


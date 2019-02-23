<?php
    use PHPUnit\Framework\TestCase;
    include '../../vendor/autoload.php';

    class ValidateTest extends TestCase
    {

        protected $fixture = null;
        public $request;

        protected function setUp(): void
        {
            $this->fixture = new Validator();
            $this->request = [
                'name' => 'gosha',
                'secondname' => 'dankovtsev',
            ];

        }

        protected function tearDown(): void
        {
            $this->fixture = null;
            $request = null;

        }

        /**
         * @test
         */
        public function validateExample()
        {
            $this->assertInstanceOf('Validator', $this->fixture);
            $this->assertEquals($this->request, [
                'name' => 'gosha',
                'secondname' => 'dankovtsev',
            ]);

        }

        /**
         * @test
         */
        public function addLength()
        {
            $this->fixture->setRule('name', ((new Rule\Length(10, 200))->setErrorMessage("{} min, {} max")));
            $this->assertEquals('10 min, 200 max', $this->fixture->rulesOptions['name']['Length']->errorMsg);
        }


        /**
         *@test
         */
        public function addMultiRules()
        {
            $this->fixture->setRule('name', new Rule\Callback(function ($value) {
                return $value !== 'banana';
            }));
            $this->fixture->setRule('name', new Rule\Required());
            $this->fixture->setRule('name', ((new Rule\Length(1, 200))->setErrorMessage("{} min, {} max")));

            $this->assertFalse($this->fixture->rulesOptions['name']['Callback']->getCallable('banana'));
            $this->assertTrue($this->fixture->rulesOptions['name']['Callback']->getCallable('not-banana'));

            $this->assertEquals('1 min, 200 max', $this->fixture->rulesOptions['name']['Length']->errorMsg);

        }

        /**
         * @test
         */
        public function ValidateTrue()
        {
            $this->fixture->setRule('name', ((new Rule\Length(4, 200))->setErrorMessage("{} min, {} max")));
            $this->fixture->setRule('name', new Rule\Callback(function ($value) {
                return $value !== 'banana';
            }));
            $this->fixture->setRule('name', new Rule\Required());

            $this->assertTrue($this->fixture->validate($this->request));
        }

        /**
         * @test
         * @dataProvider dataForValidateFalse
         */
        public function ValidateFalse(array $providerData)
        {

            $this->fixture->setRule('fakename', new Rule\Callback(function ($value) {
                return $value !== 'banana';
            }));
            $this->fixture->setRule('name', new Rule\Callback(function ($value) {
                return $value !== 'banana';
            }));
            $this->fixture->setRule('name', ((new Rule\Length(5, 20))->setErrorMessage("{} min, {} max")));
            $this->fixture->setRule('name', new Rule\Required());

            $this->assertFalse($this->fixture->validate($providerData));

        }

        public function dataForValidateFalse()
        {

            yield [['name' => 'banana']];

            yield [['fakename' => 'banana',
                'name' => 'banana']];

            yield [['NOT name' => 'value of unregistered index',
                'name' => 'banana']];

            yield [['NOT name' => 'value of unregistered index']];

            yield [[]];
        }

        /**
         * @test
         */
        public function requiredField()
        {
            $this->fixture->setRule('name', new Rule\Required());

            $this->assertFalse($this->fixture->validate(['someField' => 'somValue']));
        }


        /**
         * @test
         * @dataProvider dataForTryGetErrors
         */
        public function tryGetErrors(array $providersData)
        {
            $this->fixture->setRule('name', ((new Rule\Length(10, 20))->setErrorMessage("{} min, {} max")));
            $this->expectException('\Exception');

            $this->fixture->validate($providersData);

        }

        public function dataForTryGetErrors()
        {
            yield [['name' => 'too_short']];
            yield [['name' => 'too_looooooooooooooooooooooooooooooooooooooooooooong']];

        }


        /**
         * @test
         */
        public function tryGetData()
        {

            $this->fixture->setRule('name', new Rule\Callback(function ($value) {
                return $value !== 'banana';
            }));
            $this->fixture->setRule('name', new Rule\Required());
            $this->fixture->setRule('name', ((new Rule\Length(5, 200))->setErrorMessage("{} min, {} max")));

            if ($this->fixture->validate($this->request)) {
                $this->assertTrue($this->fixture->getData() === ['name' => 'gosha']);
            }
        }
    }
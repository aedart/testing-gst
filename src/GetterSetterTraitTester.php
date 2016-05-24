<?php namespace Aedart\Testing\GST;

use Aedart\Testing\GST\Exceptions\IncorrectPropertiesAmountException;
use PHPUnit_Framework_MockObject_MockObject;
use ReflectionClass;

/**
 * Getter Setter Trait Tester
 *
 * Utility for testing custom "getter-setter-traits".
 *
 * @author Alin Eugen Deac <aedart@gmail.com>
 * @package Aedart\Testing\GST
 */
trait GetterSetterTraitTester
{
    /**
     * The name of the property to be tested
     *
     * @var string
     */
    private $traitPropertyName;

    /***********************************************************
     * Helpers and utilities
     **********************************************************/

    /**
     * Returns a mock for the trait in question
     *
     * @param string $traitClassPath The trait's class path
     * @param array $mockedMethods [optional]
     *
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function makeTraitMock($traitClassPath, array $mockedMethods = []){
        return $this->getMockForTrait(
            $traitClassPath,
            [],
            '',
            true,
            true,
            true,
            $mockedMethods,
            false
        );
    }

    /**
     * Returns the property name, camel-cased
     *
     * @see propertyName()
     *
     * @return string
     */
    protected function getPropertyName(){
        return ucwords($this->traitPropertyName);
    }

    /**
     * Returns the name of a 'set-property' method
     *
     * @see getPropertyName()
     *
     * @return string E.g. setDescription, setName, setId
     */
    protected function setPropertyMethodName() {
        return 'set' . $this->getPropertyName();
    }

    /**
     * Returns the name of a 'get-property' method
     *
     * @see getPropertyName()
     *
     * @return string E.g. getDescription, getName, getId
     */
    protected function getPropertyMethodName() {
        return 'get' . $this->getPropertyName();
    }

    /**
     * Returns the name of a 'has-property' method
     *
     * @see getPropertyName()
     *
     * @return string E.g. hasDescription, hasName, hasId
     */
    protected function hasPropertyMethodName() {
        return 'has' . $this->getPropertyName();
    }

    /**
     * Returns the name of a 'get-default-property' method
     *
     * @see getPropertyName()
     *
     * @return string E.g. getDefaultDescription, getDefaultName, getDefaultId
     */
    protected function getDefaultPropertyMethodName() {
        return 'getDefault' . $this->getPropertyName();
    }

    /**
     * Returns the name of a 'has-default-property' method
     *
     * @see getPropertyName()
     *
     * @return string E.g. hasDefaultDescription, hasDefaultName, hasDefaultId
     */
    protected function hasDefaultPropertyMethodName() {
        return 'hasDefault' . $this->getPropertyName();
    }

    /**
     * Attempts to guess the property in question, which
     * is going to be tested.
     *
     * @see traitPropertyName
     *
     * @param string $traitClassPath
     */
    protected function guessPropertyNameFor($traitClassPath)
    {
        $reflection = new ReflectionClass($traitClassPath);

        $properties = $reflection->getProperties();

        if(count($properties) != 1){
            throw new IncorrectPropertiesAmountException(sprintf('Trait %s contains incorrect properties amount. This helper can only test a single property!', $traitClassPath));
        }

        $this->traitPropertyName = $properties[0]->getName();
    }

    /**
     * Outputs a message to the console, if the test is
     * running in debug mode (codeception) or verbose
     *
     * @param string $message
     */
    protected function output($message){
        // get args
        $args = $_SERVER['argv'];
        if(in_array('--debug', $args) || in_array('-vvv', $args) || in_array('--verbose', $args)){
            fwrite(STDERR, PHP_EOL . $message);
        }
    }

    /***********************************************************
     * Assertions
     **********************************************************/

    /**
     * Assert all methods in the given `getter-setter` trait, by invoking
     * all methods, specifying and retrieving the given value, as well as
     * mocking a custom value return.
     *
     * @param string $traitClassPath Class path to the trait in question
     * @param mixed $valueToSetAndObtain
     * @param mixed $customDefaultValue
     */
    public function assertGetterSetterTraitMethods($traitClassPath, $valueToSetAndObtain, $customDefaultValue) {
        $this->output(sprintf('Asserting "%s"', $traitClassPath));

        $this->guessPropertyNameFor($traitClassPath);

        $traitMock = $this->makeTraitMock($traitClassPath);

        // Ensures that no default value has been set (by default)
        $this->assertHasNoDefaultValue($traitMock, $this->hasDefaultPropertyMethodName());

        // Ensures that the default value is null (by default)
        $this->assertDefaultValueIsNull($traitMock, $this->getDefaultPropertyMethodName());

        // Ensures that no value is set (by default)
        $this->assertHasNoValue($traitMock, $this->hasPropertyMethodName());

        // Ensures that a value can be set and retrieved
        $this->assertCanSpecifyAndObtainValue($traitMock, $this->setPropertyMethodName(), $this->getPropertyMethodName(), $valueToSetAndObtain);

        // Ensure that a custom defined default value is returned by default,
        // if no other value has been set prior to invoking the `get-property`
        // method.
        $this->assertReturnsCustomDefaultValue($traitClassPath, $this->getDefaultPropertyMethodName(), $this->getPropertyMethodName(), $customDefaultValue);
    }

    /**
     * Assert that the there is no default value, by invoking the trait's
     * `has-default-property` method
     *
     * @param PHPUnit_Framework_MockObject_MockObject $traitMock
     * @param string $hasDefaultPropertyMethodName
     * @param string $failMessage
     */
    public function assertHasNoDefaultValue(PHPUnit_Framework_MockObject_MockObject $traitMock, $hasDefaultPropertyMethodName, $failMessage = 'Should not contain default value') {
        $this->output(sprintf(' testing %s()', $hasDefaultPropertyMethodName));

        $this->assertFalse($traitMock->$hasDefaultPropertyMethodName(), $failMessage);
    }

    /**
     * Assert that the default value is `null`, by invoking the trait's
     * `get-default-property` method
     *
     * @param PHPUnit_Framework_MockObject_MockObject $traitMock
     * @param string $getDefaultPropertyMethodName
     * @param string $failMessage
     */
    public function assertDefaultValueIsNull(PHPUnit_Framework_MockObject_MockObject $traitMock, $getDefaultPropertyMethodName, $failMessage = 'Default value should be null') {
        $this->output(sprintf(' testing %s()', $getDefaultPropertyMethodName));

        $this->assertNull($traitMock->$getDefaultPropertyMethodName(), $failMessage);
    }

    /**
     * Assert that no value is set, by invoking the trait's
     * `has-property` method
     *
     * @param PHPUnit_Framework_MockObject_MockObject $traitMock
     * @param string $hasPropertyMethodName
     * @param string $failMessage
     */
    public function assertHasNoValue(PHPUnit_Framework_MockObject_MockObject $traitMock, $hasPropertyMethodName, $failMessage = 'Should not have a value set') {
        $this->output(sprintf(' testing %s()', $hasPropertyMethodName));

        $this->assertFalse($traitMock->$hasPropertyMethodName(), $failMessage);
    }

    /**
     * Assert that the given value can be set and retrieved again,
     * by invoking the trait's `set-property` and `get-property`
     * methods
     *
     * @param PHPUnit_Framework_MockObject_MockObject $traitMock
     * @param string $setPropertyMethodName
     * @param string $getPropertyMethodName
     * @param mixed $value
     * @param string $failMessage
     */
    public function assertCanSpecifyAndObtainValue(
        PHPUnit_Framework_MockObject_MockObject $traitMock,
        $setPropertyMethodName,
        $getPropertyMethodName,
        $value,
        $failMessage = 'Incorrect value obtained'
    ){
        if(is_object($value)){
            $this->output(sprintf(' testing %s(%s)', $setPropertyMethodName, get_class($value)));
        } else {
            $this->output(sprintf(' testing %s(%s)', $setPropertyMethodName, var_export($value, true)));
        }

        $traitMock->$setPropertyMethodName($value);

        $this->output(sprintf(' testing %s()', $getPropertyMethodName));

        $this->assertSame($value, $traitMock->$getPropertyMethodName(), $failMessage);
    }

    /**
     * Assert that a custom defined default value is returned,
     * when nothing else has been specified, by invoking
     * the `get-default-property` and `get-property` methods
     *
     * @param string $traitClassPath
     * @param string $getDefaultPropertyMethodName
     * @param string $getPropertyMethodName
     * @param mixed $defaultValue
     * @param string $failMessage
     */
    public function assertReturnsCustomDefaultValue(
        $traitClassPath,
        $getDefaultPropertyMethodName,
        $getPropertyMethodName,
        $defaultValue,
        $failMessage = 'Incorrect default value returned'
    ){
        if(is_object($defaultValue)){
            $this->output(sprintf(' mocking %s(), must return %s', $getDefaultPropertyMethodName, get_class($defaultValue)));
        } else {
            $this->output(sprintf(' mocking %s(), must return %s', $getDefaultPropertyMethodName, var_export($defaultValue, true)));
        }

        $traitMock = $this->makeTraitMock($traitClassPath, [
            $getDefaultPropertyMethodName
        ]);

        $traitMock->expects($this->any())
            ->method($getDefaultPropertyMethodName)
            ->willReturn($defaultValue);

        $this->output(sprintf(' testing %s()', $getPropertyMethodName));

        $this->assertSame($defaultValue, $traitMock->$getPropertyMethodName(), $failMessage);
    }
}
<?php
declare(strict_types=1);

namespace Aedart\Testing\GST;

use Aedart\Testing\GST\Exceptions\IncorrectPropertiesAmountException;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit_Framework_MockObject_MockObject;
use ReflectionClass;

/**
 * @deprecated Replaced by \Aedart\Testing\GetterSetterTraitTester in aedart/athenaeum package
 *
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
    protected function makeTraitMock(string $traitClassPath, array $mockedMethods = []) : PHPUnit_Framework_MockObject_MockObject
    {
        /** @var \PHPUnit\Framework\TestCase $this */
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
    protected function getPropertyName() : string
    {
        return ucwords($this->traitPropertyName);
    }

    /**
     * Returns the name of a 'set-property' method
     *
     * @see getPropertyName()
     *
     * @return string E.g. setDescription, setName, setId
     */
    protected function setPropertyMethodName() : string
    {
        return 'set' . $this->getPropertyName();
    }

    /**
     * Returns the name of a 'get-property' method
     *
     * @see getPropertyName()
     *
     * @return string E.g. getDescription, getName, getId
     */
    protected function getPropertyMethodName() : string
    {
        return 'get' . $this->getPropertyName();
    }

    /**
     * Returns the name of a 'has-property' method
     *
     * @see getPropertyName()
     *
     * @return string E.g. hasDescription, hasName, hasId
     */
    protected function hasPropertyMethodName() : string
    {
        return 'has' . $this->getPropertyName();
    }

    /**
     * Returns the name of a 'get-default-property' method
     *
     * @see getPropertyName()
     *
     * @return string E.g. getDefaultDescription, getDefaultName, getDefaultId
     */
    protected function getDefaultPropertyMethodName() : string
    {
        return 'getDefault' . $this->getPropertyName();
    }

    /**
     * Returns the name of a 'has-default-property' method
     *
     * @see getPropertyName()
     *
     * @return string E.g. hasDefaultDescription, hasDefaultName, hasDefaultId
     */
    protected function hasDefaultPropertyMethodName() : string
    {
        return 'hasDefault' . $this->getPropertyName();
    }

    /**
     * Attempts to guess the property in question, which
     * is going to be tested.
     *
     * @see traitPropertyName
     *
     * @param string $traitClassPath
     *
     * @throws IncorrectPropertiesAmountException
     */
    protected function guessPropertyNameFor(string $traitClassPath)
    {
        $reflection = new ReflectionClass($traitClassPath);

        $properties = $reflection->getProperties();

        if (count($properties) != 1) {
            throw new IncorrectPropertiesAmountException(sprintf(
                'Trait %s contains incorrect properties amount. This helper can only test a single property!',
                $traitClassPath
            ));
        }

        $this->traitPropertyName = $properties[0]->getName();
    }

    /**
     * Outputs a message to the console, if the test is
     * running in debug mode (codeception) or verbose
     *
     * @param string $message
     */
    protected function output(string $message)
    {
        // get args
        $args = $_SERVER['argv'];
        if (in_array('--debug', $args) || in_array('-vvv', $args) || in_array('--verbose', $args)) {
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
     *
     * @throws AssertionFailedError
     */
    public function assertGetterSetterTraitMethods(string $traitClassPath, $valueToSetAndObtain, $customDefaultValue)
    {
        $this->output(sprintf('Asserting "%s"', $traitClassPath));

        $this->guessPropertyNameFor($traitClassPath);

        $traitMock = $this->makeTraitMock($traitClassPath);

        // Ensures that no default value has been set (by default)
        // TODO: Remove this in version 3.
        if(method_exists($traitMock, $this->hasDefaultPropertyMethodName())){
            $this->assertHasNoDefaultValue($traitMock, $this->hasDefaultPropertyMethodName());
        }


        // Ensures that the default value is null (by default)
        $this->assertDefaultValueIsNull($traitMock, $this->getDefaultPropertyMethodName());

        // Ensures that no value is set (by default)
        $this->assertHasNoValue($traitMock, $this->hasPropertyMethodName());

        // Ensures that a value can be set and retrieved
        $this->assertCanSpecifyAndObtainValue($traitMock, $this->setPropertyMethodName(),
            $this->getPropertyMethodName(), $valueToSetAndObtain);

        // Ensure that a custom defined default value is returned by default,
        // if no other value has been set prior to invoking the `get-property`
        // method.
        $this->assertReturnsCustomDefaultValue($traitClassPath, $this->getDefaultPropertyMethodName(),
            $this->getPropertyMethodName(), $customDefaultValue);
    }

    /**
     * @deprecated Since 2.0. Will be removed in version 3. Redesign your trait without a "has-default" check!
     *
     * Assert that the there is no default value, by invoking the trait's
     * `has-default-property` method
     *
     * @param PHPUnit_Framework_MockObject_MockObject $traitMock
     * @param string $hasDefaultPropertyMethodName
     * @param string $failMessage
     *
     * @throws AssertionFailedError
     */
    public function assertHasNoDefaultValue(
        PHPUnit_Framework_MockObject_MockObject $traitMock,
        string $hasDefaultPropertyMethodName,
        string $failMessage = 'Should not contain default value'
    ) {
        trigger_error(sprintf(
            'Deprecated since 2.0. Will be removed in version 3. Please redesign your trait without "%s" check',
            $hasDefaultPropertyMethodName
        ), E_USER_DEPRECATED);

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
     *
     * @throws AssertionFailedError
     */
    public function assertDefaultValueIsNull(
        PHPUnit_Framework_MockObject_MockObject $traitMock,
        string $getDefaultPropertyMethodName,
        string $failMessage = 'Default value should be null'
    ) {
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
     *
     * @throws AssertionFailedError
     */
    public function assertHasNoValue(
        PHPUnit_Framework_MockObject_MockObject $traitMock,
        string $hasPropertyMethodName,
        string $failMessage = 'Should not have a value set'
    ) {
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
     *
     * @throws AssertionFailedError
     */
    public function assertCanSpecifyAndObtainValue(
        PHPUnit_Framework_MockObject_MockObject $traitMock,
        string $setPropertyMethodName,
        string $getPropertyMethodName,
        $value,
        string $failMessage = 'Incorrect value obtained'
    ) {
        if (is_object($value)) {
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
     *
     * @throws AssertionFailedError
     */
    public function assertReturnsCustomDefaultValue(
        string $traitClassPath,
        string $getDefaultPropertyMethodName,
        string $getPropertyMethodName,
        $defaultValue,
        string $failMessage = 'Incorrect default value returned'
    ) {
        if (is_object($defaultValue)) {
            $this->output(sprintf(
                ' mocking %s(), must return %s', $getDefaultPropertyMethodName,
                get_class($defaultValue)
            ));
        } else {
            $this->output(sprintf(
                ' mocking %s(), must return %s', $getDefaultPropertyMethodName,
                var_export($defaultValue, true)
            ));
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

    /**
     * Assert that the given trait is compatible with the given interface
     *
     * @param string $traitClassPath
     * @param string $interfaceClassPath
     *
     * @throws AssertionFailedError
     */
    public function assertTraitCompatibility(string $traitClassPath, string $interfaceClassPath)
    {
        $id = 'Dummy' . str_replace('.', '_', microtime(true));

        $template = "class {$id} implements {$interfaceClassPath} { use {$traitClassPath}; }";

        // PHP will automatically fail if the trait contains
        // less or incorrect interface defined methods.
        // This may not be the best way of testing this - but it works.
        // Future versions will improve on this, and allow for none-blocking failures.
        // --> PHP 7's Anonymous classes could be a good an alternative.
        eval($template);

        $this->assertTrue(true);
    }
}

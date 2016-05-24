<?php

use Aedart\Testing\GST\GetterSetterTraitTester;
use Codeception\TestCase\Test;
use Faker\Factory;

/**
 * Class GetterSetterTraitTesterTest
 *
 * @group traits
 * @group gst-tester
 * @coversDefaultClass Aedart\Testing\GST\GetterSetterTraitTester
 *
 * @author Alin Eugen Deac <aedart@gmail.com>
 */
class GetterSetterTraitTesterTest extends Test
{
    use GetterSetterTraitTester;

    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var \Faker\Generator
     */
    protected $faker = null;

    protected function _before()
    {
        $this->faker = Factory::create();
    }

    protected function _after()
    {
    }

    /**********************************************************
     * Helpers
     *********************************************************/

    /**********************************************************
     * Actual test
     *********************************************************/

    /**
     * @test
     */
    public function canTestAllOfTraitsMethods()
    {
        $this->assertGetterSetterTraitMethods(
            DummyTrait::class,
            $this->faker->unique()->name,
            $this->faker->unique()->name
        );
    }
}
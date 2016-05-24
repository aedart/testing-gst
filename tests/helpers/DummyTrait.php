<?php

/**
 * Dummy Trait
 *
 * To be used for testing only!
 *
 * @author Alin Eugen Deac <aedart@gmail.com>
 */
trait DummyTrait
{
    /**
     * A person's name
     *
     * @var string|null
     */
    protected $person = null;

    /**
     * Set the given person
     *
     * @param string $name A person's name
     *
     * @return void
     */
    public function setPerson($name)
    {
        $this->person = $name;
    }

    /**
     * Get the given person
     *
     * If no person has been set, this method will
     * set and return a default person, if any such
     * value is available
     *
     * @see getDefaultPerson()
     *
     * @return string|null person or null if none person has been set
     */
    public function getPerson()
    {
        if (!$this->hasPerson() && $this->hasDefaultPerson()) {
            $this->setPerson($this->getDefaultPerson());
        }
        return $this->person;
    }

    /**
     * Get a default person value, if any is available
     *
     * @return string|null A default person value or Null if no default value is available
     */
    public function getDefaultPerson()
    {
        return null;
    }

    /**
     * Check if person has been set
     *
     * @return bool True if person has been set, false if not
     */
    public function hasPerson()
    {
        return !is_null($this->person);
    }

    /**
     * Check if a default person is available or not
     *
     * @return bool True of a default person is available, false if not
     */
    public function hasDefaultPerson()
    {
        return !is_null($this->getDefaultPerson());
    }
}
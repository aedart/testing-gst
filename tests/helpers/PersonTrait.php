<?php

/**
 * Person Trait
 *
 * FOR TESTING ONLY
 *
 * Uses "gstx" template for getter / setter
 *
 * @author Alin Eugen Deac <aedart@gmail.com>
 */
trait PersonTrait
{
    /**
     * Name of the person
     *
     * @var string|null
     */
    protected $person = null;

    /**
     * Set person
     *
     * @param string|null $name Name of the person
     *
     * @return self
     */
    public function setPerson(?string $name)
    {
        $this->person = $name;

        return $this;
    }

    /**
     * Get person
     *
     * If no person has been set, this method will
     * set and return a default person, if any such
     * value is available
     *
     * @see getDefaultPerson()
     *
     * @return string|null person or null if none person has been set
     */
    public function getPerson(): ?string
    {
        if (!$this->hasPerson()) {
            $this->setPerson($this->getDefaultPerson());
        }
        return $this->person;
    }

    /**
     * Check if person has been set
     *
     * @return bool True if person has been set, false if not
     */
    public function hasPerson(): bool
    {
        return isset($this->person);
    }

    /**
     * Get a default person value, if any is available
     *
     * @return string|null A default person value or Null if no default value is available
     */
    public function getDefaultPerson(): ?string
    {
        return null;
    }
}
<?php;

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
     * Name of something
     *
     * @var string|null
     */
    protected $name = null;

    /**
     * Set the given name
     *
     * @param string $name Name of something
     *
     * @return void
     */
    public function setName($name)
    {
        $this->name = (string) $name;
    }

    /**
     * Get the given name
     *
     * If no name has been set, this method will
     * set and return a default name, if any such
     * value is available
     *
     * @see getDefaultName()
     *
     * @return string|null name or null if none name has been set
     */
    public function getName()
    {
        if (!$this->hasName() && $this->hasDefaultName()) {
            $this->setName($this->getDefaultName());
        }
        return $this->name;
    }

    /**
     * Get a default name value, if any is available
     *
     * @return string|null A default name value or Null if no default value is available
     */
    public function getDefaultName()
    {
        return null;
    }

    /**
     * Check if name has been set
     *
     * @return bool True if name has been set, false if not
     */
    public function hasName()
    {
        return !is_null($this->name);
    }

    /**
     * Check if a default name is available or not
     *
     * @return bool True of a default name is available, false if not
     */
    public function hasDefaultName()
    {
        return !is_null($this->getDefaultName());
    }
}
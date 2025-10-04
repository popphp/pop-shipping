<?php
/**
 * Pop PHP Framework (https://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2026 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Shipping\Address;

/**
 * Abstract parser class
 *
 * @category   Pop
 * @package    Pop\Shipping
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2026 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    3.0.0
 */
abstract class AbstractParser implements ParserInterface
{

    /**
     * Data to parse
     * @var mixed
     */
    protected mixed $data = null;

    /**
     * Error flag
     * @var bool
     */
    protected bool $error = false;

    /**
     * Error message
     * @var ?string
     */
    protected ?string $errorMessage = null;

    /**
     * Parsed result
     * @var mixed
     */
    protected mixed $result = null;

    /**
     * Parse method
     *
     * @return static
     */
    abstract public function parse(): static;

    /**
     * Constructor
     *
     * Instantiate the parse object
     *
     * @param  mixed $data
     */
    public function __construct(mixed $data = null)
    {
        if (null !== $data) {
            $this->setData($data);
        }
    }

    /**
     * Get data
     *
     * @return mixed
     */
    public function getData(): mixed
    {
        return $this->data;
    }

    /**
     * Set data
     *
     * @param  mixed $data
     * @return static
     */
    public function setData(mixed $data): static
    {
        $this->data = $data;
    }

    /**
     * Get parsed result
     *
     * @return mixed
     */
    public function getResult(): mixed
    {
        return $this->result;
    }

    /**
     * Check if there is an error
     *
     * @return bool
     */
    public function hasError(): bool
    {
        return $this->error;
    }

    /**
     * Get error message
     *
     * @return ?string
     */
    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

}

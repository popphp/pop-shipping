<?php
/**
 * Pop PHP Framework (https://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Shipping\Address;

/**
 * Parser interface
 *
 * @category   Pop
 * @package    Pop\Shipping
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    3.0.0
 */
interface ParserInterface
{

    /**
     * Parse method
     *
     * @return static
     */
    public function parse(): static;

    /**
     * Get data
     *
     * @return mixed
     */
    public function getData(): mixed;

    /**
     * Set data
     *
     * @param  mixed $data
     * @return static
     */
    public function setData(mixed $data): static;

    /**
     * Get parsed result
     *
     * @return mixed
     */
    public function getResult(): mixed;

    /**
     * Check if there is an error
     *
     * @return bool
     */
    public function hasError(): bool;

    /**
     * Get error message
     *
     * @return ?string
     */
    public function getErrorMessage(): ?string;

}

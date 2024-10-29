<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2024 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Shipping\Adapter;

use Pop\Http\Client;

/**
 * Pop shipping adapter interface
 *
 * @category   Pop
 * @package    Pop\Shipping
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2024 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.0.0
 */
interface AdapterInterface
{

    /**
     * Set the HTTP client
     *
     * @param  Client $client
     * @return AdapterInterface
     */
    public function setClient(Client $client): AdapterInterface;

    /**
     * Get the HTTP client
     *
     * @return ?Client
     */
    public function getClient(): ?Client;

    /**
     * Has HTTP client
     *
     * @return bool
     */
    public function hasClient(): bool;

}

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
namespace Pop\Shipping;

/**
 * Pop shipping package class
 *
 * @category   Pop
 * @package    Pop\Shipping
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    3.0.0
 */
class Package
{

    /**
     * Package width
     * @var mixed
     */
    protected mixed $width = null;

    /**
     * Package height
     * @var mixed
     */
    protected mixed $height = null;

    /**
     * Package depth
     * @var mixed
     */
    protected mixed $depth = null;

    /**
     * Package weight
     * @var mixed
     */
    protected mixed $weight = null;

    /**
     * Package overall value (for insurance)
     * @var mixed
     */
    protected mixed $value = null;

    /**
     * Package dimension unit
     * @var mixed
     */
    protected mixed $dimensionUnit = 'IN';

    /**
     * Package weight unit
     * @var mixed
     */
    protected mixed $weightUnit = 'LB';

    /**
     * Package value unit
     * @var mixed
     */
    protected mixed $valueUnit = 'USD';

    /**
     * Packaging cost value (box, wrapping, tape, etc)
     * @var mixed
     */
    protected mixed $packaging = null;

    /**
     * Package ID
     * @var mixed
     */
    protected mixed $id = null;

    /**
     * Package name
     * @var ?string
     */
    protected ?string $name = null;

    /**
     * Package description
     * @var ?string
     */
    protected ?string $description = null;

    /**
     * Package tracking number
     * @var ?string
     */
    protected ?string $trackingNumber = null;

    /**
     * Constructor
     *
     * Instantiate the shipping package object
     *
     * @param  mixed $width
     * @param  mixed $height
     * @param  mixed $depth
     * @param  mixed $weight
     * @param  mixed $value
     * @param  mixed $packaging
     */
    public function __construct(
        mixed $width, mixed $height, mixed $depth, mixed $weight, mixed $value = null, mixed $packaging = null
    )
    {
        $this->setWidth($width);
        $this->setHeight($height);
        $this->setDepth($depth);
        $this->setWeight($weight);

        if (null !== $value) {
            $this->setValue($value);
        }

        if (null !== $packaging) {
            $this->setPackaging($packaging);
        }
    }

    /**
     * Set package width
     *
     * @param  mixed $width
     * @return Package
     */
    public function setWidth(mixed $width): Package
    {
        $this->width = $width;
        return $this;
    }

    /**
     * Get package width
     *
     * @return mixed
     */
    public function getWidth(): mixed
    {
        return $this->width;
    }

    /**
     * Has package width
     *
     * @return bool
     */
    public function hasWidth(): bool
    {
        return !empty($this->width);
    }

    /**
     * Set package height
     *
     * @param  mixed $height
     * @return Package
     */
    public function setHeight(mixed $height): Package
    {
        $this->height = $height;
        return $this;
    }

    /**
     * Get package height
     *
     * @return mixed
     */
    public function getHeight(): mixed
    {
        return $this->height;
    }

    /**
     * Has package height
     *
     * @return bool
     */
    public function hasHeight(): bool
    {
        return !empty($this->height);
    }

    /**
     * Set package depth
     *
     * @param  mixed $depth
     * @return Package
     */
    public function setDepth(mixed $depth): Package
    {
        $this->depth = $depth;
        return $this;
    }

    /**
     * Get package depth
     *
     * @return mixed
     */
    public function getDepth(): mixed
    {
        return $this->depth;
    }

    /**
     * Has package depth
     *
     * @return bool
     */
    public function hasDepth(): bool
    {
        return !empty($this->depth);
    }

    /**
     * Set package weight
     *
     * @param  mixed $weight
     * @return Package
     */
    public function setWeight(mixed $weight): Package
    {
        $this->weight = $weight;
        return $this;
    }

    /**
     * Get package weight
     *
     * @return mixed
     */
    public function getWeight(): mixed
    {
        return $this->weight;
    }

    /**
     * Has package weight
     *
     * @return bool
     */
    public function hasWeight(): bool
    {
        return !empty($this->weight);
    }

    /**
     * Set package value
     *
     * @param  mixed $value
     * @return Package
     */
    public function setValue(mixed $value): Package
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Get package value
     *
     * @return mixed
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * Has package value
     *
     * @return bool
     */
    public function hasValue(): bool
    {
        return (null !== $this->value);
    }

    /**
     * Set package dimension unit
     *
     * @param  string $unit
     * @return Package
     */
    public function setDimensionUnit(string $unit): Package
    {
        $this->dimensionUnit = $unit;
        return $this;
    }

    /**
     * Get package dimension unit
     *
     * @return string
     */
    public function getDimensionUnit(): string
    {
        return $this->dimensionUnit;
    }

    /**
     * Set package weight unit
     *
     * @param  string $unit
     * @return Package
     */
    public function setWeightUnit(string $unit): Package
    {
        $this->weightUnit = $unit;
        return $this;
    }

    /**
     * Get package weight unit
     *
     * @return string
     */
    public function getWeightUnit(): string
    {
        return $this->weightUnit;
    }

    /**
     * Set package value unit
     *
     * @param  string $unit
     * @return Package
     */
    public function setValueUnit(string $unit): Package
    {
        $this->valueUnit = $unit;
        return $this;
    }

    /**
     * Get package value unit
     *
     * @return string
     */
    public function getValueUnit(): string
    {
        return $this->valueUnit;
    }

    /**
     * Set packaging cost
     *
     * @param  mixed $packaging
     * @return Package
     */
    public function setPackaging(mixed $packaging): Package
    {
        $this->packaging = $packaging;
        return $this;
    }

    /**
     * Get packaging cost
     *
     * @return mixed
     */
    public function getPackaging(): mixed
    {
        return $this->packaging;
    }

    /**
     * Has packaging cost
     *
     * @return bool
     */
    public function hasPackaging(): bool
    {
        return (null !== $this->packaging);
    }

    /**
     * Set package id
     *
     * @param  mixed $id
     * @return Package
     */
    public function setId(mixed $id): Package
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get package id
     *
     * @return mixed
     */
    public function getId(): mixed
    {
        return $this->id;
    }

    /**
     * Has package id
     *
     * @return bool
     */
    public function hasId(): bool
    {
        return (null !== $this->id);
    }

    /**
     * Set package name
     *
     * @param  string $name
     * @return Package
     */
    public function setName(string $name): Package
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get package name
     *
     * @return ?string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Has package name
     *
     * @return bool
     */
    public function hasName(): bool
    {
        return (null !== $this->name);
    }

    /**
     * Set package description
     *
     * @param  string $description
     * @return Package
     */
    public function setDescription(string $description): Package
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get package description
     *
     * @return ?string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Has package description
     *
     * @return bool
     */
    public function hasDescription(): bool
    {
        return (null !== $this->description);
    }

    /**
     * Set package tracking number
     *
     * @param  string $trackingNumber
     * @return Package
     */
    public function setTrackingNumber(string $trackingNumber): Package
    {
        $this->trackingNumber = $trackingNumber;
        return $this;
    }

    /**
     * Get package tracking number
     *
     * @return ?string
     */
    public function getTrackingNumber(): ?string
    {
        return $this->trackingNumber;
    }

    /**
     * Has package tracking number
     *
     * @return bool
     */
    public function hasTrackingNumber(): bool
    {
        return (null !== $this->trackingNumber);
    }

}

<?php

namespace Tmv\WhatsApi\Message\Node;

interface NodeInterface
{
    /**
     * @param  array         $data
     * @return NodeInterface
     */
    public static function fromArray(array $data);

    /**
     * Exchange internal values from provided array
     *
     * @param  array $array
     * @return void
     */
    public function exchangeArray(array $array);

    /**
     * @return string
     */
    public function getName();

    /**
     * @return array
     */
    public function getAttributes();

    /**
     * @param  string $name
     * @return bool
     */
    public function hasAttribute($name);

    /**
     * @return string
     */
    public function getData();

    /**
     * @param  string $data
     * @return $this
     */
    public function setData($data);

    /**
     * @return NodeInterface[]
     */
    public function getChildren();

    /**
     * @param  string $name
     * @return bool
     */
    public function hasChild($name);

    /**
     * @param  string        $name
     * @return NodeInterface
     */
    public function getChild($name);

    /**
     * @param  string $name
     * @return string
     */
    public function getAttribute($name);

    /**
     * @param  string $name
     * @param  string $value
     * @return $this
     */
    public function setAttribute($name, $value);

    /**
     * @return string
     */
    public function toString();

    /**
     * @return array
     */
    public function toArray();
}

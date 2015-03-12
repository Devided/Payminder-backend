<?php

namespace Tmv\WhatsApi\Message\Node;

use InvalidArgumentException;

abstract class AbstractNode implements NodeInterface
{
    /**
     * @var string
     */
    protected $name;
    /**
     * @var array
     */
    protected $attributes = [];
    /**
     * @var NodeInterface[]
     */
    protected $children = [];
    /**
     * @var string
     */
    protected $data;

    /**
     * @param  array         $data
     * @return NodeInterface
     */
    public static function fromArray(array $data)
    {
        /** @var NodeInterface $node */
        $node = new static();
        $node->exchangeArray($data);

        return $node;
    }

    /**
     * @param  string                   $name
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setName($name)
    {
        // Check if we already have a name
        if (null !== $this->getName() && $this->getName() != $name) {
            throw new InvalidArgumentException("Name can't be setted or changed");
        }
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param  string $name
     * @return bool
     */
    public function hasAttribute($name)
    {
        return array_key_exists($name, $this->attributes);
    }

    /**
     * @param  array $attributes
     * @return $this
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = [];
        foreach ($attributes as $name => $value) {
            $this->setAttribute($name, $value);
        }

        return $this;
    }

    /**
     * @param  string $name
     * @param  mixed  $value
     * @return $this
     */
    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param  string $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return NodeInterface[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param  array $children
     * @return $this
     */
    public function setChildren(array $children)
    {
        $this->children = [];
        foreach ($children as $child) {
            $this->addChild($child);
        }

        return $this;
    }

    /**
     * @param  NodeInterface|array      $child
     * @return $this
     * @throws InvalidArgumentException
     */
    public function addChild($child)
    {
        if (is_array($child)) {
            $child = Node::fromArray($child);
        }
        if (!$child instanceof NodeInterface) {
            throw new InvalidArgumentException("Argument passed is not an instance of NodeInterface");
        }
        $this->children[] = $child;

        return $this;
    }

    /**
     * @param  string $name
     * @return bool
     */
    public function hasChild($name)
    {
        foreach ($this->getChildren() as $child) {
            if ($child->getName() == $name) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  string        $name
     * @return NodeInterface
     */
    public function getChild($name)
    {
        foreach ($this->getChildren() as $child) {
            if ($child->getName() == $name) {
                return $child;
            }
        }

        return null;
    }

    /**
     * @param  string $name
     * @return string
     */
    public function getAttribute($name)
    {
        return $this->hasAttribute($name) ? $this->attributes[$name] : null;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->getArrayCopy();
    }

    /**
     * Exchange internal values from provided array
     *
     * @param  array $array
     * @return void
     */
    public function exchangeArray(array $array)
    {
        foreach ($array as $key => $value) {
            switch ($key) {
                case 'name':
                    $this->setName($value ?: 'void');
                    break;
                case 'attributes':
                    $this->setAttributes($value);
                    break;
                case 'data':
                    $this->setData($value);
                    break;
                case 'children':
                    $this->setChildren($value);
                    break;
            }
        }
    }

    /**
     * Return an array representation of the object
     *
     * @return array
     */
    public function getArrayCopy()
    {
        $children = [];
        foreach ($this->getChildren() as $child) {
            $children[] = $child->toArray();
        }
        $array = [
            'name'       => $this->getName(),
            'attributes' => $this->getAttributes(),
            'data'       => $this->getData(),
            'children'   => $children,
        ];

        return $array;
    }

    /**
     * @return string
     */
    public function toString()
    {
        return $this->nodeString();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * @return string
     */
    protected function nodeString()
    {
        //formatters
        $lt = "<";
        $gt = ">";
        $nl = PHP_EOL;

        $indent = "";

        $ret = $indent.$lt.$this->getName();
        foreach ($this->getAttributes() as $key => $value) {
            $ret .= " ".$key."=\"".$value."\"";
        }
        $ret .= $gt;
        if (null != $this->getData() && strlen($this->getData()) > 0) {
            if (strlen($this->getData()) <= 1024) {
                //message
                $ret .= $this->getData();
            } else {
                //raw data
                $ret .= " ".strlen($this->getData())." byte data";
            }
        }
        if (count($this->getChildren())) {
            $ret .= $nl;
            $foo = [];
            foreach ($this->getChildren() as $child) {
                $childString = $child->toString();
                $childString =
                    implode(
                        "\n",
                        array_map(
                            function ($value) use ($indent) {
                                return $indent."  ".$value;
                            },
                            explode($nl, $childString)
                        )
                    );
                $foo[] = $childString;
            }
            $ret .= implode($nl, $foo);
            $ret .= $nl.$indent;
        }
        $ret .= $lt."/".$this->getName().$gt;

        return $ret;
    }
}

<?php

namespace Tmv\WhatsApi\Message\Action;

use Tmv\WhatsApi\Client;
use Tmv\WhatsApi\Message\Node\Node;
use Tmv\WhatsApi\Entity\MediaFile;

class RequestFileUpload extends AbstractAction implements IdAwareInterface
{
    /**
     * @var MediaFile
     */
    protected $mediaFile;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $to;
    /**
     * @var string
     */
    protected $icon;

    /**
     * @return MediaFile
     */
    public function getMediaFile()
    {
        return $this->mediaFile;
    }

    /**
     * @param  MediaFile $mediaFile
     * @return $this
     */
    public function setMediaFile(MediaFile $mediaFile)
    {
        $this->mediaFile = $mediaFile;

        return $this;
    }

    /**
     * @param  string $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param  string $to
     * @return $this
     */
    public function setTo($to)
    {
        $this->to = $to;

        return $this;
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param  string $icon
     * @return $this
     */
    public function setIcon($icon)
    {
        if (!file_exists($icon) || !is_readable($icon)) {
            throw new \InvalidArgumentException("Icon file doesn't exist or isn't readable");
        }
        $this->icon = $icon;

        return $this;
    }

    /**
     * @return Node
     */
    public function createNode()
    {
        $b64hash = base64_encode(hash_file("sha256", $this->getMediaFile()->getFilepath(), true));

        $mediaNode = new Node();
        $mediaNode->setName('media')
            ->setAttribute('hash', $b64hash)
            ->setAttribute('type', $this->getMediaFile()->getType())
            ->setAttribute('size', $this->getMediaFile()->getSize());

        $node = new Node();
        $node->setName('iq')
            ->setAttribute('id', 'upload-')
            ->setAttribute('to', Client::WHATSAPP_SERVER)
            ->setAttribute('type', 'set')
            ->setAttribute('xmlns', 'w:m')
            ->addChild($mediaNode);

        return $node;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        $data = [
            $this->getMediaFile()->getType(),
            $this->getMediaFile()->getSize(),
            $this->getMediaFile()->getFilepath(),
        ];

        return count(array_filter($data)) == count($data);
    }
}

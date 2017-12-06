<?php

namespace Netgen\BlockManager\Tests\Item\Stubs;

class Value
{
    /**
     * @var int|string
     */
    private $id;

    /**
     * @var int|string
     */
    private $remoteId;

    /**
     * Constructor.
     *
     * @param int|string $id
     * @param int|string $remoteId
     */
    public function __construct($id, $remoteId)
    {
        $this->id = $id;
        $this->remoteId = $remoteId;
    }

    /**
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int|string
     */
    public function getRemoteId()
    {
        return $this->remoteId;
    }

    /**
     * @return bool
     */
    public function isVisible()
    {
        return $this->id < 100;
    }
}

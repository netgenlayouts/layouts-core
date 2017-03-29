<?php

namespace Netgen\BlockManager\Tests\HttpCache\Block;

use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\HttpCache\Block\CacheableResolver;
use PHPUnit\Framework\TestCase;
use stdClass;

class CacheableResolverTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\HttpCache\Block\CacheableResolver::__construct
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Voter stdClass needs to implement Netgen\BlockManager\HttpCache\Block\CacheableResolver\VoterInterface interface.
     */
    public function testConstructorWithInvalidVoters()
    {
        new CacheableResolver(array(new stdClass()));
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\Block\CacheableResolver::__construct
     * @covers \Netgen\BlockManager\HttpCache\Block\CacheableResolver::isCacheable
     *
     * @param \Netgen\BlockManager\HttpCache\Block\CacheableResolver\VoterInterface[] $voters
     * @param bool $result
     *
     * @dataProvider isCacheableProvider
     */
    public function testIsCacheable(array $voters, $result)
    {
        $cacheableResolver = new CacheableResolver($voters);

        $this->assertEquals($result, $cacheableResolver->isCacheable(new Block()));
    }

    public function isCacheableProvider()
    {
        return array(
            array(
                array(
                    new VoterStub(VoterStub::ABSTAIN),
                    new VoterStub(VoterStub::ABSTAIN),
                ),
                true,
            ),
            array(
                array(
                    new VoterStub(VoterStub::ABSTAIN),
                    new VoterStub(VoterStub::YES),
                ),
                true,
            ),
            array(
                array(
                    new VoterStub(VoterStub::ABSTAIN),
                    new VoterStub(VoterStub::NO),
                ),
                false,
            ),
            array(
                array(
                    new VoterStub(VoterStub::YES),
                    new VoterStub(VoterStub::ABSTAIN),
                ),
                true,
            ),
            array(
                array(
                    new VoterStub(VoterStub::YES),
                    new VoterStub(VoterStub::YES),
                ),
                true,
            ),
            array(
                array(
                    new VoterStub(VoterStub::YES),
                    new VoterStub(VoterStub::NO),
                ),
                true,
            ),
            array(
                array(
                    new VoterStub(VoterStub::NO),
                    new VoterStub(VoterStub::ABSTAIN),
                ),
                false,
            ),
            array(
                array(
                    new VoterStub(VoterStub::NO),
                    new VoterStub(VoterStub::YES),
                ),
                false,
            ),
            array(
                array(
                    new VoterStub(VoterStub::NO),
                    new VoterStub(VoterStub::NO),
                ),
                false,
            ),
        );
    }
}
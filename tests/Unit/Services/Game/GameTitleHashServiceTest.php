<?php

namespace Tests\Unit\Services\Game;

use Tests\TestCase;
use Illuminate\Support\Collection;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Services\GameTitleHashService;
use App\Game;

class GameTitleHashServiceTest extends TestCase
{
    /**
     * @var GameTitleHashService
     */
    private $serviceGameTitleHash;

    public function setUp()
    {
        parent::setUp();
        $this->serviceGameTitleHash = new GameTitleHashService();
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->serviceGameTitleHash);
    }

    public function testSuperMarioOdyssey()
    {
        $title = 'Super Mario Odyssey';
        $hash = '11c597730ed1454f91f9885cdb36fe9e';

        $this->assertEquals($hash, $this->serviceGameTitleHash->generateHash($title));
    }

    public function testBreakforcistBattle()
    {
        $title = '#Breakforcist Battle';
        $hash = '26178fd8980eb3e422a86332055cc886';

        $this->assertEquals($hash, $this->serviceGameTitleHash->generateHash($title));
    }

    public function testAeternoBlade()
    {
        $title = 'AeternoBlade';
        $hash = '9fba7410ddb407b3bf9bdb01d9bb3e6f';

        $this->assertEquals($hash, $this->serviceGameTitleHash->generateHash($title));
    }

    public function testAeternoBladeII()
    {
        $title = 'AeternoBlade II';
        $hash = '2d6c5b8904998f092e367e7a7a398573';

        $this->assertEquals($hash, $this->serviceGameTitleHash->generateHash($title));
    }
}

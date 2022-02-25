<?php

declare(strict_types = 1);

namespace Tests\unit;

use PHPUnit\Framework\TestCase;
use \Statistics\Calculator\AveragePostsPerUser;
use \SocialPost\Dto\SocialPostTo;
use \Statistics\Dto\ParamsTo;
use DateTime;

class AveragePostsPerUserTest extends TestCase
{
    /**
     * @dataProvider calculateProvider
     */
    public function testCalculate(array $socialPosts, $expectedAverage): void
    {
        $calculator = new AveragePostsPerUser;
        $params = new ParamsTo;
        $params->setStartDate(new DateTime())
            ->setEndDate(new DateTime('+ 1 day'))
            ->setStatName('posts');
        $calculator->setParameters($params);
        foreach ($socialPosts as $post) {
            $calculator->accumulateData($post);
        }
        $this->assertEquals($expectedAverage, $calculator->calculate()->getValue());
    }

    public function calculateProvider(): array
    {
        return [
            [
                $this->createPosts('1', 50),
                50
            ],
            [
                array_merge(
                    $this->createPosts('1', 50),
                    $this->createPosts('2', 100),
                    $this->createPosts('3', 200)
                ),
                116.67
            ],
            [
                [],
                0
            ],
            [
                array_merge(
                    $this->createPosts('a', 5),
                    $this->createPosts('b', 7),
                    $this->createPosts('c', 1)
                ),
                4.33
            ],
            [
                $this->createPosts('1', 1),
                1
            ],
        ];
    }

    /**
     * @param string $authorId
     * @return SocialPostTo[]
     */
    public function createPosts(string $authorId, $numOfPosts): array
    {
        $posts = [];
        $num = 0;
        while ($num < $numOfPosts) {
            $post = new SocialPostTo();
            $post->setDate(new DateTime('+ 1 hour'));
            $post->setAuthorId($authorId);
            $posts[] = $post;
            $num++;
        }

        return $posts;
    }
}

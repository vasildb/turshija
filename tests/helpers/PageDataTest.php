<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Vasil\Turshija\Helpers\PageData;

final class PageDataTest extends TestCase
{
    public function testFullDate(): void
    {
        $page = new PageData(['date' => '05-10-2026'], '', '/posts/a.md');

        $this->assertSame('05 Oct 2026', $page->getDisplayDate());
        $this->assertSame('2026-10-05', $page->getW3cDate());
    }

    public function testMonthOnlyDate(): void
    {
        foreach (['Oct 2026', 'October 2026', 'oct 2026', '2026-10'] as $date) {
            $page = new PageData(['date' => $date], '', '/posts/a.md');

            $this->assertSame('Oct 2026', $page->getDisplayDate(), $date);
            $this->assertSame('2026-10', $page->getW3cDate(), $date);
            $this->assertSame('2026-10-01', $page->getDate()->format('Y-m-d'), $date);
        }
    }

    public function testInvalidDate(): void
    {
        $page = new PageData(['date' => 'Foo 2026'], '', '/posts/a.md');

        $this->assertNull($page->getDate());
        $this->assertSame('', $page->getDisplayDate());
    }
}

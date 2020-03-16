<?php

namespace Tests\Unit;

use App\Report;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_should_return_the_correct_count_of_confirmed_and_fixed_reports()
    {
        factory(Report::class, 2)->create();
        factory(Report::class, 6)->state('confirmed')->create();
        factory(Report::class, 3)->state('fixed')->create();
        
        $this->assertEquals(6,Report::confirmed()->count());
        $this->assertEquals(3,Report::fixed()->count());
        $this->assertEquals(2, Report::waiting()->count());
        
    }
}

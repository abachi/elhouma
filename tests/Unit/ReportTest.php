<?php

namespace Tests\Unit;

use App\User;
use App\Report;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_should_return_the_correct_count_of_global_confirmed_and_fixed_reports()
    {
        factory(Report::class, 2)->create();
        factory(Report::class, 6)->state('confirmed')->create();
        factory(Report::class, 3)->state('fixed')->create();
        
        $this->assertEquals(6,Report::confirmed()->count());
        $this->assertEquals(3,Report::fixed()->count());
        $this->assertEquals(2, Report::waiting()->count());
        
    }

    public function test_should_return_how_many_times_a_given_report_were_confirmed()
    {
        $nasser = factory(User::class)->create();
        $otherUsers = factory(User::class, 6)->create();
        $report = factory(Report::class)->make();
        $nasser->reports()->save($report);

        foreach ($otherUsers as $user) {
            $report->confirmBy($user);
        }

        $this->assertEquals(6, $report->totalConfirmations());
    }
}

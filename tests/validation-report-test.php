<?php

class ValidationReportTest extends PHPUnit_Framework_TestCase
{
    public function setUp(){ }
    public function tearDown(){ }

    /**
     * @dataProvider reportProvider
     */
    public function testValidationSuccess($report){
        $report->for_entity('data');
        $report->checkup('length', strlen('nemesis') > 4);

        $this->assertTrue($report->get_status());
    }

    /**
     * @dataProvider reportProvider
     */
    public function testArrayFormat($report){
        $report->for_entity('data');
        $report->checkup('success', 'a' == 'a');
        $report->checkup('failure', 'a' != 'a');

        $report_array = $report->as_array();
        $expected_array = array(
            'status' => false,
            'successes' => array(
                'data' => array('success')
            ),
            'failures' => array(
                'data' => array('failure')
            )
        );

        $this->assertEquals($expected_array, $report_array);
    }

    public function testUnsetedEntity(){
        $report = new Nemesis\ValidationReport();
        $report->checkup('nemesis', true);

        $report_array = $report->as_array();
        $this->assertFalse($report->get_status());
        $this->assertTrue(count($report_array['successes']) == 0);
        $this->assertTrue(count($report_array['failures']) == 0);
    }

    public function testObjectContruct(){
        $report = new Nemesis\ValidationReport();
        $this->assertTrue($report->get_status());

        $report_array = $report->as_array();
        $this->assertTrue(count($report_array['failures']) == 0);
        $this->assertTrue(count($report_array['successes']) == 0);
    }

    /**
     * @dataProvider reportProvider
     */
    public function testValidationError($report){
        $report->for_entity('data');
        $report->checkup('length', strlen('nemesis') > 15);

        $this->assertFalse($report->get_status());
    }

    /**
     * @dataProvider reportProvider
     */
    public function testCriticalFailure($report){
        $report->for_entity('data');
        $report->critical_checkup('critical', false);
        $report->checkup('failure', false);
        $report->checkup('success', true);

        $report_array = $report->as_array();
        $this->assertTrue($report->has_critically_failed());
        $this->assertTrue(in_array('critical', $report_array['failures']['data']));
        $this->assertFalse(in_array('failure', $report_array['failures']['data']));
        $this->assertFalse(isset($report_array['successes']['data']));
    }

    /**
     * @dataProvider reportProvider
     */
    public function testEntityCriticalCheckup($report){
        $report->for_entity('data');
        $report->entity_critical_checkup('critical', false);
        $report->checkup('failure', false);
        $report->checkup('success', true);

        $report->for_entity('other');
        $report->checkup('success', true);
        $report->checkup('failure', false);

        $report_array = $report->as_array();
        $this->assertFalse($report->has_critically_failed());
        $this->assertTrue(in_array('critical', $report_array['failures']['data']));
        $this->assertFalse(in_array('failure', $report_array['failures']['data']));
        $this->assertFalse(isset($report_array['successes']['data']));

        $this->assertTrue(in_array('success', $report_array['successes']['other']));
        $this->assertTrue(in_array('failure', $report_array['failures']['other']));
    }

    public function reportProvider(){
        return array(array(new Nemesis\ValidationReport()));
    }
}
?>

<?php

namespace Tests\Service;

use PHPUnit\Framework\TestCase;
use App\Service\VacationDaysCalculatorService;

class VacationDaysCalculatorServiceTest extends TestCase
{
    private $service;

    public function setUp() : void
    {
        $this->service = new VacationDaysCalculatorService();
    }

    public function provideTestData()
    {
        return
            [
                [
                    'data' => [
                        'yearGiven' => 2017,
                        'employeeData' => [
                            'name' => 'Hans Müller',
                            'date_of_birth' => '30.12.1950',
                            'contract_start_date' => '01.01.2001',
                            'special_contract_days' => 0,
                        ],
                    ],
                    'expected'=> 29
                ],
                [
                    'data' => [
                        'yearGiven' => 2001,
                        'employeeData' => [
                            'name' => 'Hans Müller',
                            'date_of_birth' => '30.12.1950',
                            'contract_start_date' => '01.01.2001',
                            'special_contract_days' => 0,
                        ],
                    ],
                    'expected'=> 26
                ],
                [
                    'data' => [
                        'yearGiven' => 2001,
                        'employeeData' => [
                            'name' => 'Hans Müller',
                            'date_of_birth' => '30.12.1986',
                            'contract_start_date' => '01.10.2001',
                            'special_contract_days' => 0,
                        ],
                    ],
                    'expected'=> 6
                ],
                [
                    'data' => [
                        'yearGiven' => 2010,
                        'employeeData' => [
                            'name' => 'Hans Müller',
                            'date_of_birth' => '30.12.1950',
                            'contract_start_date' => '01.10.2001',
                            'special_contract_days' => 0,
                        ],
                    ],
                    'expected'=> 27
                ],
            ];
    }
    /**
     * @dataProvider provideTestData
     */
    public function testCalculation(array $data, int $expected): void
    {
        $actual = $this->service->calculate($data['yearGiven'], $data['employeeData']);
        self::assertEquals($actual, $expected);
    }
}

<?php

namespace App\Command;

use App\Service\VacationDaysCalculatorService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class VacationDaysCalculatorCommand extends Command
{
    protected static $defaultName = 'app:calculate:vacationdays';

    private $data;

    private $service;

    public function __construct($data)
    {
	    $this->data = $data;

	    $this->service = new VacationDaysCalculatorService();

	    parent::__construct();
    }

    protected function configure()
    {
         $this
            ->setDescription('Calculates vacations days for employees')
	    ->setHelp('Calculates vacations days for employees from table given')
	    ;
         $this
            ->addArgument('year', InputArgument::REQUIRED, 'Year for which to determine the number of vacation days of employees');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
	    $yearGiven = $input->getArgument('year');
	    if ($yearGiven < 1970) {
		    throw new \RuntimeException('Year given should be greater than 1970');
	    }

	    $output->writeln('Calculating vacation days for employees in %d year', sprintf($yearGiven));

	    foreach ($this->data as $employeeData) {

            $vacationDays = $this->service->calculate($yearGiven, $employeeData);

            if (0 === $vacationDays) {
                $contractStartDT = new \DateTime($employeeData['contract_start_date']);
                $yearContractStart = (int) $contractStartDT->format('Y');

                $output->writeln(sprintf('(!) The user %s in the year %d contract has not yet started. The beginning of the contract in %d', $employeeData['name'], $yearGiven, $yearContractStart));
                continue;
            }

		    $output->writeln(sprintf(' * The user %s in the year %d has %d vacation days', $employeeData['name'], $yearGiven, $vacationDays));
	    }
    }
}

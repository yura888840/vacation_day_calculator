<?php

namespace App\Service;

class VacationDaysCalculatorService
{
    const MINIMUM_VACATION_DAYS = 26;

    /**
     * @param $yearGiven
     * @param $employeeData
     *
     * @return int
     */
    public function calculate($yearGiven, $employeeData)
    {
        $yearGivenDT = new \DateTime(sprintf('31-12-%d', $yearGiven));

        $contractStartDT = new \DateTime($employeeData['contract_start_date']);
        $yearContractStart = (int) $contractStartDT->format('Y');

        if ($yearGiven < $yearContractStart) {
            return 0;
        }

        $vacationDays = self::MINIMUM_VACATION_DAYS;

        if (
            $employeeData['special_contract_days'] > 0
            && $employeeData['special_contract_days'] > self::MINIMUM_VACATION_DAYS
        ) {
            $vacationDays = $employeeData['special_contract_days'];
        }

        $employeeDateOfBirthDT = new \DateTime($employeeData['date_of_birth']);
        $ageOfEmployeeAtContractStartDT = $employeeDateOfBirthDT->diff($contractStartDT);
        $ageOfEmployeeAtContractStart = $ageOfEmployeeAtContractStartDT->y;

        $dateFromWhichCalculateAdditionalOneDay = $contractStartDT;

        if ($ageOfEmployeeAtContractStart < 30) {
            $dateFromWhichCalculateAdditionalOneDay = clone $contractStartDT;

            $dateFromWhichCalculateAdditionalOneDay->add(
                new \DateInterval(sprintf('P%dY', 30 - $ageOfEmployeeAtContractStart))
            );
        }

        $yearsBetweenStartWithAdditionalDateAndYearProvided = $dateFromWhichCalculateAdditionalOneDay->diff($yearGivenDT);
        $invertor = $yearsBetweenStartWithAdditionalDateAndYearProvided->invert ? -1 : 1;
        $numberOfYearsForCalculationAdditionalDay = $yearsBetweenStartWithAdditionalDateAndYearProvided->y * $invertor;

        $additionalNumberOfDaysEachFiveYears = 0;

        if ($numberOfYearsForCalculationAdditionalDay > 0) {
            $additionalNumberOfDaysEachFiveYears = floor($numberOfYearsForCalculationAdditionalDay / 5);
        }

        $vacationDays += $additionalNumberOfDaysEachFiveYears;

        if ($yearGiven == $yearContractStart) {
            $monthesForWhichVacationsCalculated = 13 - (int) $contractStartDT->format('m');

            $multiplier = $monthesForWhichVacationsCalculated / 12;
            $vacationDays = (int) floor($vacationDays * $multiplier);
        }

        return $vacationDays;
    }
}

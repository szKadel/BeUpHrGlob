<?php

namespace Functional\Api\Vacation;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Factory\Company\DepartmentFactory;
use App\Factory\Company\EmployeeFactory;
use App\Factory\Settings\NotificationFactory;
use App\Factory\UserFactory;
use App\Factory\Vacation\VacationFactory;
use App\Factory\Vacation\VacationLimitsFactory;
use App\Factory\Vacation\VacationStatusFactory;
use App\Factory\VacationTypesFactory;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\ResetDatabase;

class VacationPostTest extends ApiTestCase
{
    use HasBrowser;
    use ResetDatabase;

    public function testAddNewUnActiveEmployeeVacation()
    {
        VacationStatusFactory::createOne(['name'=>'Oczekujący']);
        VacationStatusFactory::createOne(['name'=>'Zaplanowany']);
        $vacationStatus = VacationStatusFactory::createOne(['name'=>'Zaakceptowany']);

        $department = DepartmentFactory::createOne();
        $department2 = DepartmentFactory::createOne();

        $employee = EmployeeFactory::createOne(['department'=>$department]);
        $employee2 = EmployeeFactory::createOne(['department'=>$department]);
        EmployeeFactory::createOne(['department'=>$department2]);
        $employeeMod = EmployeeFactory::createOne(['department'=>$department]);

        $mod = UserFactory::createOne(['employee' => $employeeMod, 'roles'=>['ROLE_MOD']]);

        $user = UserFactory::createOne(['employee' => $employee, 'roles'=>['ROLE_USER']]);
        $user2 = UserFactory::createOne(['employee' => $employee2, 'roles'=>['ROLE_USER']]);

        VacationTypesFactory::createOne();
        $vacationType = VacationTypesFactory::createOne(['name'=>'Inny']);
        $vacationType2 = VacationTypesFactory::createOne(['name'=>'Urlop']);

        VacationLimitsFactory::createOne(["employee"=>$employee,'vacationType'=>$vacationType, 'daysLimit'=>500]);
        VacationLimitsFactory::createOne(["employee"=>$employeeMod,'vacationType'=>$vacationType, 'daysLimit'=>20]);
        VacationLimitsFactory::createOne(["employee"=>$employee2,'vacationType'=>$vacationType, 'daysLimit'=>20]);
        NotificationFactory::createOne();
        $this->browser()
            ->actingAs($user)
            ->post('/api/vacations',[
                'json'=>[
                    'employee'=>'api/employees/'.$employee->getId(),
                    'type'=> 'api/vacation_types/'.$vacationType->getId(),
                    'dateFrom'=> '2023-09-15',
                    'dateTo'=>'2023-09-21'
                ]
            ])
            ->dd();

        $this->browser()
            ->actingAs($user2)
            ->post('/api/vacations',[
                'json'=>[
                    'employee'=>'api/employees/'.$employee2->getId(),
                    'type'=> 'api/vacation_types/'.$vacationType->getId(),
                    'dateFrom'=> '2023-09-14',
                    'dateTo'=>'2023-09-22',
                    'replacement' => 'api/employees/'.$employee->getId(),
                ]
            ])
            ->assertStatus(400);
    }


}
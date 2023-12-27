<?php

namespace App\Tests\Functional\Api\Company;

use App\Factory\Company\DepartmentFactory;
use App\Factory\Company\EmployeeFactory;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\ResetDatabase;

class DepartmentResourceTest extends KernelTestCase
{
    use HasBrowser;
    use ResetDatabase;

    public function testDepartmentPost()
    {
        $this->browser()
            ->get('/api/departments')
            ->assertStatus(401);

        $employee = EmployeeFactory::createOne();
        $user = UserFactory::createOne(['employee' => $employee, 'roles'=>['ROLE_ADMIN']]);

        $this->browser()
            ->actingAs($user)
            ->post('/api/departments',['json'=>[
                'name'=>'test'
            ]])
            ->assertStatus(201);


            $this->browser()
                ->actingAs($user)
                ->get('/api/departments')
                ->assertStatus(200)
            ->assertJsonMatches('"hydra:totalItems"', 2);
    }

    public function testDepartmentDelete()
    {

        $employee = EmployeeFactory::createOne();
        $user = UserFactory::createOne(['employee' => $employee, 'roles'=>['ROLE_ADMIN']]);

        $department = DepartmentFactory::createOne();

        $this->browser()
            ->actingAs($user)
            ->Delete('/api/departments/'.$department->getId())
            ->assertStatus(204);


        $this->browser()
            ->actingAs($user)
            ->get('/api/departments')
            ->assertStatus(200)
            ->assertJsonMatches('"hydra:totalItems"', 1);
    }
}
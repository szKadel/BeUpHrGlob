<?php

namespace App\EventSubscriber;

use App\Entity\Company\Department;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class DepartmentDeleteValidationSubscriber implements EventSubscriberInterface
{
    public function onPreDelete($event): void
    {
        $department = $event->getRequest()->attributes->get("data");

        if($department instanceof Department)
        {
            if(!empty($department->getEmployees()))
            {
                throw new BadRequestException("Nie można usunąć działu, do teog działu są przypisani pracownicy.");
            }
        }

    }

    public static function getSubscribedEvents(): array
    {
        return [
            'pre_delete' => 'onPreDelete',
        ];
    }
}

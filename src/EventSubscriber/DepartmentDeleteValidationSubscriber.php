<?php

namespace App\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Company\Department;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\KernelEvents;

class DepartmentDeleteValidationSubscriber implements EventSubscriberInterface
{
    public function onPreDelete($event): void
    {
        $department = $event->getControllerResult();

        if($department instanceof Department)
        {
            if(!empty($department->getEmployees()->toArray()))
            {
                throw new BadRequestException("Nie można usunąć działu, do teog działu są przypisani pracownicy.");
            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => [
                'onPreDelete', EventPriorities::PRE_WRITE
            ]
        ];
    }
}

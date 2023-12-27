<?php

namespace App\EventSubscriber;

use ApiPlatform\Metadata\Post;
use App\Entity\Vacation\VacationLimits;
use App\Repository\EmployeeVacationLimitRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class VacationLimitsValidationSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly EmployeeVacationLimitRepository $employeeVacationLimitRepository
    )
    {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $vacationLimit = $event->getRequest()->attributes->get("data");

        if($vacationLimit instanceof VacationLimits)
        {
                if ($this->employeeVacationLimitRepository->findTypeForEmployee(
                        $vacationLimit->getEmployee(),
                        $vacationLimit->getVacationType()
                    ) !== null) {
                    throw new BadRequestException("Limit został już dodany!", 400);
                }
            }
    }


    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}

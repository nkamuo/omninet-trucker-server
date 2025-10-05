<?php

namespace App\State;

use ApiPlatform\Doctrine\Common\State\PersistProcessor;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Booking;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Sets the current user as the renter for new bookings
 */
class BookingProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: PersistProcessor::class)]
        private ProcessorInterface $persistProcessor,
        private Security $security
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if (!$data instanceof Booking) {
            return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
        }

        // Set the current user as the renter for new bookings
        if ($data->getRenter() === null) {
            $user = $this->security->getUser();
            if ($user) {
                $data->setRenter($user);
            }
        }

        // Calculate total amount based on truck daily rate and duration
        if ($data->getTruck() && $data->getStartDate() && $data->getEndDate()) {
            $days = $data->getDurationInDays();
            $dailyRate = (float) $data->getTruck()->getDailyRate();
            $totalAmount = $days * $dailyRate;
            $data->setTotalAmount((string) $totalAmount);
        }

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}

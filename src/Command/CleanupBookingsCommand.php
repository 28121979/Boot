<?php

namespace App\Command;

use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'app:cleanup-bookings',
    description: 'Supprime les réservations non payées après 30 minutes.',
)]
class CleanupBookingsCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private BookingRepository $bookingRepository;

    public function __construct(EntityManagerInterface $entityManager, BookingRepository $bookingRepository)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->bookingRepository = $bookingRepository;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Supprime les réservations non payées après 30 minutes');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $now = new \DateTime();
        $threshold = $now->modify('-1 minutes');

        $bookings = $this->bookingRepository->findUnpaidBookingsOlderThan($threshold);

        foreach ($bookings as $booking) {
            $this->entityManager->remove($booking);
        }

        $this->entityManager->flush();

        $io->success(sprintf('%d réservations non payées ont été supprimées.', count($bookings)));

        return Command::SUCCESS;
    }
}

<?php
namespace App\Command\Panier;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Service\Servicetext\GeneralServicetext;
use App\Repository\Produit\Produit\PanierRepository;
use Doctrine\ORM\EntityManagerInterface;

class PanierAsyncCommand extends Command
{
    private $generalServicetext;
    protected static $defaultName = 'app:panier:async';

    public function __construct(GeneralServicetext $generalServicetext)
    {
        $this->generalServicetext = $generalServicetext;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Synchronisation des comptes d\'utilisateurs')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Dry run')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $result = 'SUCCESS';
        if ($input->getOption('dry-run')) {
            $io->note('Dry mode enabled');
            //$count = $this->commentRepository->countOldRejected();
        } else {
            $result = $this->generalServicetext->asyncPanier();
        }
        $io->success(sprintf('Synchronisation "%s" des comptes d\'utilisateurs.', $result));
        return 0;
    }
}
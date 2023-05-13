<?php

declare(strict_types=1);

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class rex_command_thumbhash_create extends rex_console_command
{
    protected function configure(): void
    {
        $this->setDescription('Create ThumbHashes for all supported files in MediaPool');
        $this->addOption('yes', 'y', InputOption::VALUE_NONE, 'runs the creation without confirmation');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = $this->getStyle($input, $output);

        $skipConfirmation = true === $input->getOption('yes');

        $io->title('Create ThumbHashes for all supported files in MediaPool');

        if (!$input->isInteractive() && !$skipConfirmation) {
            return 1;
        }

        if (!$skipConfirmation && !$io->confirm('Current data will be updated. Would you like to proceed?')) {
            return 1;
        }

        $io->writeln('Run creating ThumbHashes ...');

        $tcount = \FriendsOfRedaxo\ThumbHash\ForThumbHash::createThumbHashes();

        $io->success('Successfully created ThumbHashes for ' . $tcount. ' files!');
        return 0;
    }
}

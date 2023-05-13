<?php

declare(strict_types=1);

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class rex_command_thumbhash_clear extends rex_console_command
{
    protected function configure(): void
    {
        $this->setDescription('Clear all ThumbHashes in MediaPool');
        $this->addOption('yes', 'y', InputOption::VALUE_NONE, 'runs the clearing without confirmation');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = $this->getStyle($input, $output);

        $skipConfirmation = true === $input->getOption('yes');

        $io->title('Clear all ThumbHashes in MediaPool');

        if (!$input->isInteractive() && !$skipConfirmation) {
            return 1;
        }

        if (!$skipConfirmation && !$io->confirm('Current data will be deleted. Would you like to proceed?')) {
            return 1;
        }

        $io->writeln('Run clearing ThumbHashes ...');

        $errors = [];
        $rc = \FriendsOfRedaxo\ThumbHash\ForThumbHash::clearThumbHashes();
        if (true !== $rc) {
            $errors[] = $rc;
        }

        if ([] !== $errors) {
            $io->error($this->decodeMessage('Failed to clear ThumbHashes:' . PHP_EOL . implode(PHP_EOL, $errors)));
            return 1;
        }

        $io->success('Successfully cleared ThumbHashes!');
        return 0;
    }
}

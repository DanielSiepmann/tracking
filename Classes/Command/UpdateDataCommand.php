<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020 Daniel Siepmann <coding@daniel-siepmann.de>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301, USA.
 */

namespace DanielSiepmann\Tracking\Command;

use DanielSiepmann\Tracking\Domain\Repository\Pageview;
use DanielSiepmann\Tracking\Domain\Repository\Recordview;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UpdateDataCommand extends Command
{
    /**
     * @var Pageview
     */
    private $pageviews;

    /**
     * @var Recordview
     */
    private $recordviews;

    public function __construct(
        Pageview $pageviews,
        Recordview $recordviews
    ) {
        $this->pageviews = $pageviews;
        $this->recordviews = $recordviews;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setHelp('Converts legacy data to new format if necessary. Runs incrementel to work with large data sets.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->writeln('Updating: Pageviews');
        $this->update($this->pageviews, $io);
        $io->writeln('Updating: Recordviews');
        $this->update($this->recordviews, $io);

        return 0;
    }

    /**
     * @param Pageview|Recordview $repository
     */
    private function update(
        $repository,
        SymfonyStyle $io
    ): void {
        $count = $repository->findLegacyCount();
        if ($count === 0) {
            $io->writeln('No more data to update.');
            return;
        }

        $io->progressStart($count);
        foreach ($repository->findLegacy() as $data) {
            $repository->update($data);
            $io->progressAdvance();
        }
        $io->progressFinish();
    }
}

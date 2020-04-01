<?php

namespace DanielSiepmann\Tracking\Command;

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

use DanielSiepmann\Tracking\Domain\Repository\Pageview;
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
    private $repository;

    public function __construct(Pageview $repository)
    {
        $this->repository = $repository;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Updates existing data.');
        $this->setHelp('In case some more data can be extracted of the existing data.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->progressStart($this->repository->countAll());

        foreach ($this->repository->findAll() as $pageView) {
            $this->repository->update($pageView);
            $io->progressAdvance();
        }

        $io->progressFinish();

        return 0;
    }
}

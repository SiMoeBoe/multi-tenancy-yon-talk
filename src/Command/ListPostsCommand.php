<?php

namespace App\Command;

use App\Repository\BlogPostRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'blogpost:list',
    description: 'Lists all blog post',
)]
class ListPostsCommand extends Command
{
    public function __construct(private readonly BlogPostRepository $blogPostRepository)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->blogPostRepository->findAll() as $blogPost) {
            $output->writeln([
                $blogPost->getTitle(),
                $blogPost->getContent(),
                ""
            ]);
        }

        return Command::SUCCESS;
    }
}

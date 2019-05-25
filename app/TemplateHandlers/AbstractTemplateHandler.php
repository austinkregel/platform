<?php

namespace App\TemplateHandlers;

use App\Project;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\SplFileInfo;

abstract class AbstractTemplateHandler
{
    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var Project
     */
    protected $project;

    protected $filesystem;

    public function __construct(Project $project, InputInterface $input, OutputInterface $ouput)
    {
        $this->input = $input;
        $this->output = $ouput;
        $this->project = $project;
        $this->filesystem = new Filesystem;
    }

    abstract protected function handle(): void;

    abstract protected function getTemplateVariables(): array;

    abstract protected function getTemplateFilesToMove(): array;

    protected function template(array $mappings, SplFileInfo $file): void
    {
        $contents = $file->getContents();

        foreach($mappings as $fieldToFind => $replacementValue) {
            $contents = str_replace($fieldToFind, $replacementValue, $contents);
        }

        $this->filesystem->replace($file->getRealPath(), $contents);
    }

    public function execute(): void
    {
        $this->handle();
    }

    protected function getProjectFiles()
    {
        return $this->filesystem->allFiles($this->project->get('location'));
    }

    protected function replaceAllTemplateValues()
    {
        $files = $this->getProjectFiles();

        foreach ($files as $file) {
            if (!Str::contains($file->getRelativePath(), ['.lock', '-lock.json'])) {
                $this->template($this->getTemplateVariables(), $file);
            }

            foreach ($this->getTemplateFilesToMove() as $originalFileName => $newFileName) {
                if (Str::contains($file->getRealPath(), [$originalFileName])) {
                    $this->filesystem->move($file->getRealPath(), str_replace($originalFileName, $newFileName, $file->getRealPath()));
                }
            }
        }

        $this->output->writeln('<info>Updated the templates!</info>');
    }
}

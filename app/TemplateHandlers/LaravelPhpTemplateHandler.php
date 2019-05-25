<?php

namespace App\TemplateHandlers;

use Symfony\Component\Process\Process;

class LaravelPhpTemplateHandler extends AbstractTemplateHandler
{
    protected function handle(): void
    {
        $this->replaceAllTemplateValues();
        // At the moment since this is based on the spatie package builder, they have a helper shell command to do this job for us.
        $process = new Process([
            './configure-skeleton.sh',
        ], $this->project->get('location'));

        $this->output->writeln($this->project->get('location'));

        $process->run(function ($type, $line) {
            $this->output->write($line);
        });
    }

    protected function getTemplateVariables(): array
    {
        return [
            'read -p "Author name ($git_name): " author_name' => 'author_name="$git_name"',
            'read -p "Author email ($git_email): " author_email' => 'author_email="$git_email"',
            'read -p "Author username ($username_guess): " author_username' => 'author_username="'.$this->project->title('author').'"',
            'read -p "Package name ($current_directory): " package_name' => 'package_name="'.$this->project->slug('name').'"',
            'read -p "Are you sure you wish to continue? (n/y) " -n 1 -r' => 'REPLY=y',
            'spatie' => $this->project->slug('author'),
            'Spatie' => $this->project->pascal('author'),
            'SkeletonClass' => $this->project->pascal('name') . 'Class',
            'SkeletonFacade' => $this->project->pascal('name') . 'Facade',
            'SkeletonServiceProvider' => $this->project->pascal('name') . 'ServiceProvider',
        ];
    }

    protected function getTemplateFilesToMove(): array
    {
        return [
            'SkeletonClass.php' => $this->project->pascal('name') . 'Class.php',
            'SkeletonFacade.php' => $this->project->pascal('name') . 'Facade.php',
            'SkeletonServiceProvider.php' => $this->project->pascal('name') . 'ServiceProvider.php',
        ];
    }
}

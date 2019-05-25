<?php

namespace App\TemplateHandlers;

class BasePhpTemplateHandler extends AbstractTemplateHandler
{
    protected function getTemplateVariables(): array
    {
        return [
            '2018' => date('Y'),
            'Austin Kregel' => $this->project->title('author'),
            'Kregel' => $this->project->pascal('author'),
            'kregel' => $this->project->slug('author'),
            'Skel' => $this->project->pascal('name'),
            'skel' => $this->project->slug('name'),
        ];
    }

    protected function getTemplateFilesToMove(): array
    {
        return [
            'Skel.php' => $this->project->pascal('name') . '.php',
        ];
    }

    protected function handle(): void
    {
        $this->replaceAllTemplateValues();
    }
}

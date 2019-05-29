<?php

namespace App\TemplateHandlers;

class BaseNodeTemplateHandler extends AbstractTemplateHandler
{
    protected function getTemplateVariables(): array
    {
        return [
            '2018' => date('Y'),
            'package_name' => $this->project->slug('name'),
        ];
    }

    protected function getTemplateFilesToMove(): array
    {
        return [];
    }

    protected function handle(): void
    {
        $this->replaceAllTemplateValues();
    }
}

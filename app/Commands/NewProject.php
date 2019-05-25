<?php

namespace App\Commands;

use App\Project;
use App\TemplateHandlers\AbstractTemplateHandler;
use GuzzleHttp\Client;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\Command;
use RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Process\Process;
use ZipArchive;

class NewProject extends Command
{
    /**
     * @const array|string[]
     */
    protected const SUPPORTED_TYPES = [
        'laravel',
        'php',
    ];

    /**
     * @const array|string[]
     */
    protected const SUPPORTED_LANGUAGE = [
        'php'
    ];

    /**
     * @const string
     */
    protected const TEMPLATE_HANDLER_TEMPLATE = 'App\TemplateHandlers\%s%sTemplateHandler';

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'make:package {name}'
    . ' {--type=base : The template type to copy from.}'
    . ' {--language=php : The language of the template you want to use}'
    . ' {--branch=master : The branch to pull the template you want to use}'
    . ' {--author= : The author of this package}'
    . ' {--force : Force create the thing}';


    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Make a new package. Choose the language, type, and branch of the new package.';

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var Project
     */
    protected $project;

    public function handle()
    {
        $this->filesystem = new Filesystem;

        $branch = $this->option('branch');
        $type = $this->option('type');
        $language = $this->option('language');
        $name = $this->argument('name');
        $author = $this->option('author');
        $downloadUrl = sprintf('https://github.com/project-template/%s-%s.git', $type, $language);
        $directory = $this->directoryJoin(getcwd(), Str::slug($name));

        $this->project = new Project($name, $author, $downloadUrl, $branch, $language, $type, $directory);

        $this->exec();
    }

    protected function exec()
    {
        if (!extension_loaded('zip')) {
            throw new RuntimeException('The Zip PHP extension is not installed. Please install it and try again.');
        }

        if (!$this->option('force')) {
            $this->verifyProjectDoesntExist($this->project->get('location'));
        } else {
            $this->filesystem->deleteDirectory($this->project->get('location'), false);
        }

        $this->info('Crafting project...');

        $this->download();

        $this->cleanUp($this->project->get('location'));

        $templateHandler = sprintf(static::TEMPLATE_HANDLER_TEMPLATE, ucfirst($this->project->get('type')), ucfirst($this->project->get('language')));

        /** @var AbstractTemplateHandler $handler */
        $handler = new $templateHandler($this->project, $this->input, $this->output);

        $handler->execute();

        $this->info('Project ready! Build something kick ass!');
    }

    protected function verifyProjectDoesntExist($directory)
    {
        if ((is_dir($directory) || is_file($directory)) && $directory != getcwd()) {
            throw new RuntimeException('Project already exists!');
        }
    }

    protected function cleanUp()
    {
        $this->filesystem->deleteDirectory($this->directoryJoin($this->project->get('location'), '.git'));

        return $this;
    }

    protected function download()
    {
        $process = Process::fromShellCommandline(
            sprintf(
                'git clone --single-branch --branch %s %s %s',
                $this->project->get('branch'),
                $this->project->get('url'),
                $this->project->slug('name')
            )
        );
        $process->run(function ($type, $line) {
            $this->output->write($line);
        });

        return $this;
    }

    protected function directoryJoin(string ...$parts)
    {
        $firstElement = Arr::first($parts);

        $startsWithSlash = Str::startsWith($firstElement, [DIRECTORY_SEPARATOR]);
        return ($startsWithSlash ? DIRECTORY_SEPARATOR : '') . $this->forRealPath(join(DIRECTORY_SEPARATOR, array_map(function ($part) {
            return trim($part, DIRECTORY_SEPARATOR);
        }, $parts)));
    }

    protected function forRealPath($path, $absolutePathParts = []) {
        $path = str_replace(array(DIRECTORY_SEPARATOR, '\\'), DIRECTORY_SEPARATOR, $path);
        $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), function($part): bool {
            return $part !== '.';
        });

        foreach ($parts as $part) {
            if ('..' === $part) {
                array_pop($absolutePathParts);
            } else {
                $absolutePathParts[] = $part;
            }
        }

        return implode(DIRECTORY_SEPARATOR, $absolutePathParts);
    }
}

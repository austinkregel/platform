# Platform
A potentially super powerful way to stop writing all the boiler plate code every time you want to make a new package for your favorite platform.

## Install
```bash
composer require global kregel/platform
```

### Usage
```bash
platform make:package {name}
    {--type=base : The template type to copy from.}
    {--language=php : The language of the template you want to use}
    {--branch=master : The branch to pull the template you want to use}
    {--author= : The author of this package}
    {--force : Force create the package}
```

At the moment all package templates are pulled from [this organization](https://github.com/project-template) so we can apply some customizations if we need, and ensure that these scripts will continue to work if the base branch applies what could be breaking changes.

### Template requests
To have me add more templates to the aforementioned organization, make an issue and include a link to the source template so we can fork it!


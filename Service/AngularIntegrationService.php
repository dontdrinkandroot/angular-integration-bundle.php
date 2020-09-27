<?php

namespace Dontdrinkandroot\AngularIntegrationBundle\Service;

use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @author Philip Washington Sorst <philip@sorst.net>
 */
class AngularIntegrationService
{
    private KernelInterface $kernel;

    private string $name;

    private string $shortName;

    private string $themeColor;

    private string $backgroundColor;

    private string $packageManager;

    private string $hrefApp;

    private string $hrefApi;

    private string $directoryRoot;

    private string $directorySrc;

    private array $externalStyles;

    public function __construct(
        KernelInterface $kernel,
        string $name,
        string $shortName,
        string $themeColor,
        string $backgroundColor,
        string $packageManager,
        string $hrefApp,
        string $hrefApi,
        string $directoryRoot,
        string $directorySrc,
        array $externalStyles
    ) {
        $this->kernel = $kernel;
        $this->name = $name;
        $this->shortName = $shortName;
        $this->themeColor = $themeColor;
        $this->backgroundColor = $backgroundColor;
        $this->externalStyles = $externalStyles;
        $this->packageManager = $packageManager;
        $this->hrefApp = $hrefApp;
        $this->hrefApi = $hrefApi;
        $this->directoryRoot = $directoryRoot;
        $this->directorySrc = $directorySrc;
    }

    public function getDirectoryRoot(): string
    {
        return realpath($this->directoryRoot);
    }

    public function getDirectorySrc(): string
    {
        return realpath($this->directorySrc);
    }

    public function getHrefApp(): string
    {
        return $this->hrefApp;
    }

    public function getHrefApi(): string
    {
        return $this->hrefApi;
    }

    public function getEnvironment(): string
    {
        return $this->kernel->getEnvironment();
    }

    public function isProd(): bool
    {
        return 'prod' === $this->getEnvironment();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getShortName(): string
    {
        return $this->shortName;
    }

    public function getThemeColor(): string
    {
        return $this->themeColor;
    }

    public function getBackgroundColor(): string
    {
        return $this->backgroundColor;
    }

    public function getExternalStyles(): array
    {
        return $this->externalStyles;
    }

    public function getPackageManager(): string
    {
        return $this->packageManager;
    }
}

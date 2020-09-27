<?php

namespace Dontdrinkandroot\AngularIntegrationBundle\Command;

use Dontdrinkandroot\AngularIntegrationBundle\Service\AngularIntegrationService;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Twig\Environment;

/**
 * @author Philip Washington Sorst <philip@sorst.net>
 */
class AngularCommand extends Command
{
    protected static $defaultName = 'ddr:angular';

    private AngularIntegrationService $angularIntegrationService;

    private Environment $twig;

    public function __construct(Environment $twig, AngularIntegrationService $angularIntegrationService)
    {
        parent::__construct();
        $this->angularIntegrationService = $angularIntegrationService;
        $this->twig = $twig;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->addOption('skip-install', null, InputOption::VALUE_NONE)
            ->addOption('skip-build', null, InputOption::VALUE_NONE)
            ->addOption('skip-icons', null, InputOption::VALUE_NONE)
            ->addOption('force-prod', null, InputOption::VALUE_NONE);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (true !== $input->getOption('skip-install')) {
            $this->runInstall($output);
        }
        $this->configureApiEndpoint($output);
        $this->writeIndex($output);
        $this->writeManifest($output);
        if (true !== $input->getOption('skip-icons')) {
            $this->generateIcons($output);
        }
        if (true !== $input->getOption('skip-build')) {
            $prod = true === $input->getOption('force-prod') || $this->angularIntegrationService->isProd();
            $this->build($output, $prod);
        }
    }

    private function runInstall(OutputInterface $output)
    {
        $packageManager = $this->angularIntegrationService->getPackageManager();

        switch ($packageManager) {

            case 'npm':
                /* Fallthrough */
            case 'yarn':

                $output->writeln('Installing Node Packages');

                var_dump($this->angularIntegrationService->getDirectoryRoot());

                $installProcess = new Process(
                    [$packageManager, 'install'],
                    $this->angularIntegrationService->getDirectoryRoot(),
                    null,
                    null,
                    120
                );
                $installProcess->mustRun(
                    function ($type, $buffer) {
                        if (Process::ERR === $type) {
                            echo 'ERR > ' . $buffer;
                        } else {
                            echo 'OUT > ' . $buffer;
                        }
                    }
                );

                break;

            default:
                throw new RuntimeException('Unsupported package manager: ' . $packageManager);
        }
    }

    private function configureApiEndpoint(OutputInterface $output)
    {
        $output->writeln('Configuring API endpoint: ' . $this->angularIntegrationService->getHrefApi());
        $apiConfigTs = $this->twig->render(
            '@DdrAngularIntegration/api-config.ts.twig',
            [
                'baseUrl' => $this->angularIntegrationService->getHrefApi()
            ]
        );
        file_put_contents(
            $this->angularIntegrationService->getDirectorySrc() . '/environments/api-config.ts',
            $apiConfigTs
        );
    }

    private function writeIndex(OutputInterface $output)
    {
        $output->writeln('Writing Index');
        $manifestContent = $this->twig->render(
            '@DdrAngularIntegration/index.html.twig',
            [
                'startUrl'        => $this->angularIntegrationService->getHrefApp(),
                'name'            => $this->angularIntegrationService->getName(),
                'shortName'       => $this->angularIntegrationService->getShortName(),
                'themeColor'      => $this->angularIntegrationService->getThemeColor(),
                'backgroundColor' => $this->angularIntegrationService->getBackgroundColor(),
                'externalStyles'  => $this->angularIntegrationService->getExternalStyles(),
            ]
        );
        file_put_contents($this->angularIntegrationService->getDirectorySrc() . '/index.html', $manifestContent);
    }

    private function writeManifest(OutputInterface $output)
    {
        $output->writeln('Writing Manifest');
        $manifestContent = $this->twig->render(
            '@DdrAngularIntegration/manifest.json.twig',
            [
                'startUrl'        => $this->angularIntegrationService->getHrefApp(),
                'name'            => $this->angularIntegrationService->getName(),
                'shortName'       => $this->angularIntegrationService->getShortName(),
                'themeColor'      => $this->angularIntegrationService->getThemeColor(),
                'backgroundColor' => $this->angularIntegrationService->getBackgroundColor(),
            ]
        );
        file_put_contents(
            $this->angularIntegrationService->getDirectorySrc() . '/manifest.json',
            $manifestContent
        );
    }

    private function generateIcons(OutputInterface $output)
    {
        $output->writeln('Generating Icons');

        $sizes = [16, 32, 48, 72, 96, 128, 144, 152, 180, 192, 384, 512];

        //convert -background none angular/src/assets/icons/template.svg -resize 192x192 angular/src/assets/icons/icon_192.png

        foreach ($sizes as $size) {
            $convertProcess = new Process(
                [
                    'rsvg-convert',
                    '--width=' . $size,
                    '--height=' . $size,
                    '--output=assets/icons/icon_' . $size . '.png',
                    'assets/icons/template.svg'
                ],
                $this->angularIntegrationService->getDirectorySrc()
            );
            $output->writeln($convertProcess->getCommandLine());
            $convertProcess->mustRun(
                function ($type, $buffer) {
                    if (Process::ERR === $type) {
                        echo 'ERR > ' . $buffer;
                    } else {
                        echo 'OUT > ' . $buffer;
                    }
                }
            );
        }
    }

    private function build(OutputInterface $output, bool $prod = false)
    {
        $output->writeln('Building Angular');
        $command = [
            $this->angularIntegrationService->getPackageManager(),
            'run',
            'ng',
            'build',
            '--no-progress',
            '--base-href',
            $this->angularIntegrationService->getHrefApp()
        ];
        if ($prod) {
            $command[] = '--prod';
            $comment[] = '--aot';
        }
        $angularBuildProcess = new Process(
            $command,
            $this->angularIntegrationService->getDirectoryRoot(),
            null,
            null,
            300
        );
        $output->writeln('Executing: ' . $angularBuildProcess->getCommandLine());
        $angularBuildProcess->mustRun();
    }
}

<?php

namespace Stillat\PhpPackage;

use Illuminate\Support\Str;
use NewUp\Configuration\ConfigurationWriter;
use NewUp\Templates\Package as PackageClass;
use NewUp\Templates\BasePackageTemplate;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class Package extends BasePackageTemplate
{

    protected $travisVersions = [
    ];

    /**
     * Called when the builder has loaded the package class.
     *
     * @return mixed
     */
    public function builderLoaded()
    {
        // Get the parsed vendor and package name from the user input. NewUp's Package class
        // provides a helper method just for this.
        $vendorPackageParts = PackageClass::parseVendorAndPackage($this->argument('package'));
        $vendor = $vendorPackageParts[0];
        $package = $vendorPackageParts[1];

        // Share the vendor and package with the template system.
        $this->with([
            'vendor' => $vendor,
            'package' => $package
        ]);

        $composerJson = PackageClass::getConfiguredPackage();
        $composerJson->setVendor($vendor)->setPackage($package);
        $writer = new ConfigurationWriter($composerJson->toArray());

        // Package requirements.
        $requirements = [
          'php' => $this->argument('phpv')
        ];

        // Package dev requirements.
        $requireDev = [];

        // If the user specified PHPUnit support, we need to add that to the requirements.
        if ($this->option('phpunit')) {
            $requireDev['mockery/mockery'] = '~0.9.2';
            $requireDev['phpunit/phpunit'] = '~4.0';
        } else {
            $this->ignorePath([
                'phpunit.xml',
                'tests/*'
            ]);
        }

        // Gather TravisCI information.
        if ($this->option('travis')) {
            $this->gatherTravisCIRequirements();
            $this->with([
                'travisVersions' => $this->travisVersions
            ]);
        } else {
            $this->ignorePath([
               '.travis.yml'
            ]);
        }

        $writer['require'] = (object)$requirements;
        $writer['require-dev'] = (object)$requireDev;

        $autoloadSection = [
            'psr-0' => (object)[Str::studly($vendor).'\\'.Str::studly($package).'\\' => 'src/']
        ];

        // Now we can add the autoload section.
        $writer['autoload'] = (object)$autoloadSection;
        $writer['minimum-stability'] = 'stable';

        // Now it is time to save the "composer.json" file.
        $writer->save($this->outputDirectory().'/composer.json');
    }

    protected function gatherTravisCIRequirements()
    {
        while ($this->confirm('Would you like to add a PHP version to test? [Y/n]', true)) {
            $phpVersion = $this->ask('Which PHP version would you like to test?');
            $allowFailures = $this->confirm('Do you want to allow failures for PHP version '.$phpVersion.'? [y/N]', false);
            $this->travisVersions[] = ['version' => $phpVersion, 'allowFailure' => $allowFailures];
            $this->line("\n\r");
        }
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    public static function getOptions()
    {
        return [
            ['travis', null, InputOption::VALUE_NONE, 'Add TravisCI configuration.'],
            ['phpunit', null, InputOption::VALUE_NONE, 'Add PHPUnit configuration.'],
        ];
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    public static function getArguments()
    {
        return [
            ['package', InputArgument::REQUIRED, 'The name (vendor/name) of the package.'],
            ['phpv', InputArgument::OPTIONAL, 'The minimum PHP version supported by your package.', '>=5.5.9'],
        ];
    }

}
<?php

/**
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Geotools\CLI\Distance;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Geotools\Geotools;
use Geotools\Coordinate\Coordinate;
use Geotools\Coordinate\Ellipsoid;

/**
 * Command-line distance:all class
 *
 * @author Antoine Corcy <contact@sbin.dk>
 */
class All extends Command
{
    protected function configure()
    {
        $availableEllipsoids = Ellipsoid::getAvailableEllipsoidNames();

        $this
            ->setName('distance:all')
            ->setDescription('Compute the distance between 2 coordinates using all algorithms, in meters by default')
            ->addArgument('origin', InputArgument::REQUIRED, 'The origin "Lat,Long" coordinate')
            ->addArgument('destination', InputArgument::REQUIRED, 'The destination "Lat,Long" coordinate')
            ->addOption('km', null, InputOption::VALUE_NONE, 'If set, the distance will be shown in kilometers')
            ->addOption('mi', null, InputOption::VALUE_NONE, 'If set, the distance will be shown in miles')
            ->addOption('ft', null, InputOption::VALUE_NONE, 'If set, the distance will be shown in feet')
            ->addOption('ellipsoid', null, InputOption::VALUE_REQUIRED,
                'If set, the name of the ellipsoid to use', Ellipsoid::WGS84)
            ->setHelp(<<<EOT
<info>Available ellipsoids</info>: $availableEllipsoids

<info>Exemple with AIRY ellipsoid</info>:

    %command.full_name% "40° 26.7717, -79° 56.93172" "30°16′57″N 029°48′32″W" <comment>--ellipsoid=AIRY</comment>
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ellipsoid = Ellipsoid::createFromName($input->getOption('ellipsoid'));
        $from      = new Coordinate($input->getArgument('origin'), $ellipsoid);
        $to        = new Coordinate($input->getArgument('destination'), $ellipsoid);

        $geotools = new Geotools();
        $distance = $geotools->distance()->setFrom($from)->setTo($to);

        if ($input->getOption('km')) {
            $distance->in('km');
        }

        if ($input->getOption('mi')) {
            $distance->in('mi');
        }

        if ($input->getOption('ft')) {
            $distance->in('ft');
        }

        $result[] = sprintf('<label>Flat:</label>      <value>%s</value>', $distance->flat());
        $result[] = sprintf('<label>Haversine:</label> <value>%s</value>', $distance->haversine());
        $result[] = sprintf('<label>Vincenty:</label>  <value>%s</value>', $distance->vincenty());

        $output->getFormatter()->setStyle('label', new OutputFormatterStyle('yellow', 'black'));
        $output->getFormatter()->setStyle('value', new OutputFormatterStyle('green', 'black'));
        $output->writeln($result);
    }
}

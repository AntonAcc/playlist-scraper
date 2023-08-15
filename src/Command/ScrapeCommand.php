<?php declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use HeadlessChromium\BrowserFactory;

class ScrapeCommand extends Command
{
    protected static $defaultName = 'app:scrape';
    protected static $defaultDescription = 'Scrape playlists';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Start to scrape</info>');

        $radioList = [
            'softrock' => 'Soft Rock',
            'yachtrock' => 'Yacht Rock',
            'poprock' => 'Pop Rock',
            'rockballads' => 'Rock Ballads',

            'symphonicmetal' => 'Symphonic Metal',
            'thrashmetal' => 'Thrash Metal',
            'heavymetal' => 'Heavy Metal',
            'powermetal' => 'Power Metal',
            'metal' => 'Metal',
            'numetal' => 'Nu Metal',

            '00srock' => '00s Rock',
            '60srock' => '60s Rock',
            '70srock' => '70s Rock',
            'alternative80s' => '80s Alternative',
            '80srock' => '80s Rock',
            'alternative90s' => '90s Alternative',
            '90srock' => '90s Rock',
            'alternativerock' => 'Alternative Rock',
            'bluesrock' => 'Blues Rock',
            'classichardrock' => 'Classic Hard Rock',
            'classicmetal' => 'Classic Metal',
            'classicrock' => 'Classic Rock',
            'deathmetal' => 'Death Metal',
            'grunge' => 'Grunge',
            'hairbands' => 'Hair Bands',
            'indierock' => 'Indie Rock',
            'industrial' => 'Industrial',
            'melodicdeathmetal' => 'Melodic Death Metal',
            'modernfolkrock' => 'Modern Folk Rock',
            'progressiverock' => 'Progressive Rock',
            'screamoemo' => 'Screamo-Emo',
            'punkrock' => 'Punk Rock',
        ];

        foreach ($radioList as $radioKey => $radioName) {
            try {
                // TODO: Move browser creation out from foreach. Now it leads to errors.
                $browserFactory = new BrowserFactory("/usr/bin/google-chrome");
                $browser = $browserFactory->createBrowser();
                $page = $browser->createPage();
                $page->navigate(sprintf('https://www.rockradio.com/%s', $radioKey));

                $page->waitUntilContainsElement('.play-button-component');
                $page->mouse()?->find('.play-button-component')->click();
                $page->waitUntilContainsElement('.artist-name');

                $evaluation = $page->evaluate('document.querySelector(".artist-name").innerHTML');
                $artist = str_replace(' - ', '', $evaluation->getReturnValue());
                $evaluation = $page->evaluate('document.querySelector(".track-name").innerHTML');
                $track = $evaluation->getReturnValue();
                $output->writeln(sprintf('Radio %s: %s - %s', $radioName, $artist, $track));

                $file = fopen(sprintf('/app/output/%s.txt', $radioKey), 'ab');
                fwrite($file, sprintf("%s\t%s%s", $artist, $track, PHP_EOL));
                fclose($file);
            } catch (\Throwable $e) {
                $output->writeln(sprintf('Radio %s: [Error] %s', $radioName, $e->getMessage()));
            } finally {
                $browser?->close();
            }
        }

        $output->writeln('<info>Finish to scrape</info>');

        return Command::SUCCESS;
    }
}

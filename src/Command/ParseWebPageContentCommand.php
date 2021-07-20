<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ParseWebPageContentCommand extends Command
{
    private const PARSE_URL = 'http://example.org';

    /**
     * @var HttpClientInterface
     */
    private HttpClientInterface $httpClient;

    protected static $defaultName = 'app:parse-webpage';

    /**
     * ParseWebPageContentCommand constructor.
     * @param HttpClientInterface $httpClient
     */
    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('url', InputArgument::REQUIRED, 'Web page to parse.')
            ->addArgument(
                'tags',
                InputArgument::IS_ARRAY | InputArgument::REQUIRED,
                'Html tags to fetch'
            )
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $tags = $input->getArgument('tags');
        $parseUrl = $input->getArgument('url');
        $output->writeln(['Start parsing...']);
        try {
            $response = $this->httpClient->request('GET', $parseUrl);
            if ($response->getStatusCode() === 200) {
                $content = $response->getContent();
                $crawler = new Crawler($content);
                foreach ($tags as $tag) {
                    $crawler->filter($tag)->each(function (Crawler $node) use ($tag, $output) {
                        $output->writeln([sprintf("Text content of the '%s' tag is '%s'", $tag, $node->text())]);
                    });
                }
                $output->writeln(['Parsing successfully finished.']);
                return Command::SUCCESS;
            }
        } catch (\Exception $e) {
            $output->writeln([$e->getMessage()]);
            return Command::FAILURE;
        }
    }
}

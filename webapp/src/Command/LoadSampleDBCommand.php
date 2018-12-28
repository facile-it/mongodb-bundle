<?php declare(strict_types=1);

namespace App\Command;

use App\Service\SampleDB;
use Facile\MongoDbBundle\Capsule\Database;
use Faker\Factory;
use Faker\Generator;
use MongoDB\Collection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LoadSampleDBCommand extends Command
{
    /** @var Database */
    private $database;
    /** @var Generator */
    private $faker;

    protected static $defaultName = 'sampledb:load';

    public function __construct(Database $database)
    {
        parent::__construct();

        $this->database = $database;
        $this->faker = Factory::create();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Loading db');

        $this->loadPostsCollection();
    }

    private function loadPostsCollection()
    {
        $collection = $this->resetCollection('posts');

        $authors = array_build(
            5,
            function () {
                return [
                    'name' => $this->faker->name,
                    'lastName' => $this->faker->lastName,
                    'email' => $this->faker->email,
                ];
            }
        );

        $documents = array_build(
            40,
            function () use ($authors) {
                return [
                    'title' => $this->faker->sentence,
                    'body' => $this->faker->text,
                    'authors' => array_random_values($authors, random_int(1, 4))
                ];
            }
        );

        $collection->insertMany($documents);
    }

    private function resetCollection(string $collectionName): Collection
    {
        $this->database->dropCollection($collectionName);
        $this->database->createCollection($collectionName);
        return $this->database->selectCollection($collectionName);
    }

}

function array_build(int $length, \Closure $closure): array
{
    $data = [];
    for ($i = 0; $i < $length; $i++) {
        $data[] = $closure($i, $data);
    }
    return $data;
}

function array_random_values(array $array, int $numItems): array
{
    $keys = array_rand($array, $numItems);
    if (! is_array($keys)) {
        $keys = [$keys];
    }

    return array_values_from_keys($array, $keys);
}

function array_values_from_keys(array $array, array $keys): array
{
    return array_filter(
        $array,
        function ($key) use ($keys) {
            return in_array($key, $keys);
        },
        ARRAY_FILTER_USE_KEY
    );
}
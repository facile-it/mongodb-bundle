<?php declare(strict_types=1);

namespace App\Controller;

use Facile\MongoDbBundle\Capsule\Database;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /** @var Database */
    private $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function index()
    {
        $postsCollection = $this->database->selectCollection('posts');
        $postsCollection->find([
            'author.name' => 'ilario'
        ]);

        return $this->render('default/index.html.twig');
    }
}

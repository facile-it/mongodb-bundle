<?php declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class DefaultController
{
    public function __construct()
    {

    }

    public function index()
    {
        return new Response('<html><body>Ok</body></html>', 200);
    }
}

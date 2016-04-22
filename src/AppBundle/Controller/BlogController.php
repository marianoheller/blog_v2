<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class BlogController extends Controller
{
    /**
     * @Route("/blog",
     *     name="blog"
     *
     *     )
     */
    public function indexAction(Request $request)
    {
        return $this->render('blog/blog_content..html.twig');
    }
}
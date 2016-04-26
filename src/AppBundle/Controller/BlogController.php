<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\CssSelector\CssSelectorConverter;

use AppBundle\Entity\blog_author;
use AppBundle\Entity\blog_post;


const imagesFolderName = "images_blog";
const cantPostsAtInit = 10;
define ("Authors", serialize (array ("Jesse Stokes", "Mike Myers", "Micky Vainilla","Skyler Johansson")));



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

    /**
     * @Route( "cmd/init",
     *     name="init"
     *      )
     */
    public function initAction()
    {
        return new Response("Blog inicializado");
    }

    //********************************************************************************************
    // Private functions
    //********************************************************************************************

    private function generateAuthor( $index )
    {
        /*$url = "http://uinames.com/api/";
        $lines_array=file($url);
        $lines_string=implode('',$lines_array);
        $crawler = new Crawler($lines_string);
        $nodeValues = $crawler->filter('body > p')->each(function (Crawler $node, $i) {
            return $node->text();
        });
        $text = implode("<br/>",$nodeValues);*/

        $authors = unserialize(Authors);
        $fullName = $authors[$index];
        $nameArray = explode(" ",$fullName);
        $lastName = "Doe";
        if ( sizeof($nameArray) > 1 )
            $lastName = $nameArray[1];
        $name = $nameArray[0];


        $author = new blog_author();
        $author->setFirstName($name);
        $author->setMail(strtolower($name)."@gmail.com");
        $author->setLastName($lastName);

        $author->setDisplayName($name);

        return $author;
    }

    private function generatePost()
    {
        $post = new blog_post();
        return $post;
    }
}
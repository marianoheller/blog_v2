<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\CssSelector\CssSelectorConverter;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

use AppBundle\Entity\blog_author;
use AppBundle\Entity\blog_post;


const imagesFolderName = "images_blog";
const cantPostsAtInit = 10;
define ("Authors", serialize (array ("Jesse Stokes", "Mike Myers", "Micky Vainilla","Skyler Johansson")));



class BlogController extends Controller
{

    //********************************************************************************************
    // Public functions / Routing
    //********************************************************************************************

    /**
     * @Route("/blog",
     *     name="blog"
     *
     *     )
     */
    public function indexAction()
    {
        return $this->render('blog/blog_content.html.twig');
    }

    /**
     * @Route( "/cmd/init",
     *     name="init"
     *      )
     */
    public function initAction()
    {
        return new Response("Blog inicializado");
    }

    /**
     * @Route( "/cmd/test",
     *     name="test"
     *      )
     */
    public function testAction()
    {
        $url = "http://uinames.com/api/";
        $lines_array=file($url);
        $lines_string=implode('',$lines_array);

        $encoders = array( new JsonEncoder() );
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $lines_string = preg_replace("/\bname\b/","firstName",$lines_string);
        $lines_string = preg_replace("/\bsurname\b/","lastName",$lines_string);
        $lines_string = preg_replace("/\bregion\b/","displayName",$lines_string);
        $deserializedObject = $serializer->deserialize($lines_string, 'AppBundle\Entity\blog_author', 'json');

        return new Response($lines_string);
    }

    //********************************************************************************************
    // Private functions
    //********************************************************************************************

    //*******************************************
    // Generators
    //*******************************************

    private function generateAuthor( $index )
    {
        $url = "http://uinames.com/api/";
        $lines_array=file($url);
        $lines_string=implode('',$lines_array);

        $encoders = array( new JsonEncoder() );
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $lines_string = preg_replace("/\bname\b/","firstName",$lines_string);
        $lines_string = preg_replace("/\bsurname\b/","lastName",$lines_string);
        $lines_string = preg_replace("/\bregion\b/","displayName",$lines_string);
        $author = $serializer->deserialize($lines_string, 'AppBundle\Entity\blog_author', 'json');
        $author->setMail(substr(strtolower($author->getFirstName),0,1).strtolower($author->getLastName)."@gmail.com");

        return $author;
    }

    private function generatePost()
    {
        $post = new blog_post();
        return $post;
    }


    //*******************************************
    // Cleaners
    //*******************************************

    private function clearAllAuthors()
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getRepository('AppBundle:blog_author');
        $query = $repo->createQueryBuilder('p');
        $query->delete();
        $query->getQuery()->execute();
        $em->flush();
    }

    //*******************************************
    // Savers
    //*******************************************

    private function saveAuthorInDB($author)
    {
        $em = $this->getDoctrine()->getManager();
        //Push data
        $em->persist($author);
        $em->flush();
    }

}
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
const cantAuthorsAtInit = 4;



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
        //CLEAR ALL AUTHORS AND POSTS
        $this->clearAllAuthors();

        //CREATE AUTHORS
        for ($i = 0; $i < cantAuthorsAtInit; $i++) {
            $author = $this->generateAuthor();
            $this->saveAuthorInDB($author);
        }

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

    private function generateAuthor()
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
        /** @var blog_author $author */
        $author = $serializer->deserialize($lines_string, 'AppBundle\Entity\blog_author', 'json');

        $mailUser = strtolower(substr($this->stringToUTF8($author->getFirstName()),0,1));
        $mailUser .= strtolower($this->stringToUTF8($author->getLastName()));
        $author->setMail($mailUser."@gmail.com");

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

    //*******************************************
    // Misc.
    //*******************************************

    private function ru2lat($str)
    {
        $tr = array(
            "А"=>"a", "Б"=>"b", "В"=>"v", "Г"=>"g", "Д"=>"d",
            "Е"=>"e", "Ё"=>"yo", "Ж"=>"zh", "З"=>"z", "И"=>"i",
            "Й"=>"j", "К"=>"k", "Л"=>"l", "М"=>"m", "Н"=>"n",
            "О"=>"o", "П"=>"p", "Р"=>"r", "С"=>"s", "Т"=>"t",
            "У"=>"u", "Ф"=>"f", "Х"=>"kh", "Ц"=>"ts", "Ч"=>"ch",
            "Ш"=>"sh", "Щ"=>"sch", "Ъ"=>"", "Ы"=>"y", "Ь"=>"",
            "Э"=>"e", "Ю"=>"yu", "Я"=>"ya", "а"=>"a", "б"=>"b",
            "в"=>"v", "г"=>"g", "д"=>"d", "е"=>"e", "ё"=>"yo",
            "ж"=>"zh", "з"=>"z", "и"=>"i", "й"=>"j", "к"=>"k",
            "л"=>"l", "м"=>"m", "н"=>"n", "о"=>"o", "п"=>"p",
            "р"=>"r", "с"=>"s", "т"=>"t", "у"=>"u", "ф"=>"f",
            "х"=>"kh", "ц"=>"ts", "ч"=>"ch", "ш"=>"sh", "щ"=>"sch",
            "ъ"=>"", "ы"=>"y", "ь"=>"", "э"=>"e", "ю"=>"yu",
            "я"=>"ya", " "=>"-", "."=>"", ","=>"", "/"=>"-",
            ":"=>"", ";"=>"","—"=>"", "–"=>"-"
        );
        return strtr($str,$tr);
    }



    private function stringToUTF8( $input )
    {
        if(strlen($input) != mb_strlen($input, 'utf-8'))
            //$input = utf8_encode($input);
            $input = "fixEncoding";
        return $input;
    }

}
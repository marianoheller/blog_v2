<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\CssSelector\CssSelectorConverter;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

use AppBundle\Entity\blog_author;
use AppBundle\Entity\blog_post;
use AppBundle\Entity\blog_comment;

// On init stuff
/** default image folder name (donde se guardan las imagenes) */
const imagesFolderName = "images_blog";
/** Cantidad de post a ser generador por init action */
const cantPostsAtInit = 5;
/** Cantidad de authors a ser generados por init action */
const cantAuthorsAtInit = 4;

/** Title clean on init */
const titleCleanOnInit = NULL;
/** Enable post comments on init action */
const enableCommentsOnInit = false;
/** Enable post on init action */
const enablePostOnInit = true;
/** Featured post on init action */
const featuredOnInit = false;
/** Initial views of generated post on init */
const viewsOnInit = 0;

// Formatting parameters
/** Image banner post width */
const bannerWidth = 900;
/** Image banner post height */
const bannerHeight = 300;




/**
 * Class BlogController
 * @package AppBundle\Controller
 */
class BlogController extends Controller
{

    //********************************************************************************************
    // Public functions / Routing
    //********************************************************************************************

    /**
     * @Route("/blog",
     *     name="blog"
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
        $this->clearAllPosts();

        // DELETE IMAGES
        $this->deleteAllImages();

        //CREATE AUTHORS
        for ($i = 0; $i < cantAuthorsAtInit; $i++) {
            $author = $this->generateAuthor();
            $this->saveAuthorInDB($author);
        }

        //CREATE POSTS
        for ($i = 0; $i < cantPostsAtInit; $i++) {
            $blog_post = $this->generatePost();
            $this->savePostInDB($blog_post);
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

    /**
     * @return blog_author object
     */
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

    /**
     * @return blog_post object
     */
    private function generatePost()
    {
        /*
        *private $id;
        *private $title;
        *private $article;
        private $titleClean; -> Nullable (default: null)
        *private $authorId;
        *private $datePublished;
        *private $bannerImage;
        *private $featured;
        *private $enabled;
        *private $commentsEnabled;
        *private $views;
         */
        $blog_post = new blog_post();

        //==================
        //Title
        $url='http://loripsum.net/api/2/short';
        $lines_array=file($url);
        $lines_string=implode('',$lines_array);
        $crawler = new Crawler($lines_string);
        $text = $crawler->filter('body > p')->last()->text();
        $text=str_replace(', ','',$text);
        $text=str_replace('. ','',$text);
        $blog_post->setTitle(trim($text));

        //==================
        //Body/Article
        $url='http://loripsum.net/api';
        $lines_array=file($url);
        $lines_string=implode('',$lines_array);
        $crawler = new Crawler($lines_string);
        $nodeValues = $crawler->filter('body > p')->each(function (Crawler $node, $i) {
            return $node->text();
        });
        $text = implode("<br/>",$nodeValues);
        $blog_post->setArticle($text);

        //==================
        //Author
        $repo = $this->getDoctrine()->getRepository('AppBundle:blog_author');
        $query = $repo->createQueryBuilder('a')
            ->where('LENGTH(a.firstName) > :val')
            ->setParameter('val', '1')
            ->getQuery();
        $authorsArray = $query->getResult();
        /** @var blog_author $author */
        $author = $authorsArray[random_int(0, sizeof($authorsArray)-1)];
        $blog_post->setAuthorId($author->getId());

        //==================
        //DateTime
        $d1=new \DateTime(); //now
        $blog_post->setDatePublished($d1);

        //==================
        //Image
        $folder = imagesFolderName;
        if ( !is_dir($folder) )
            mkdir($folder);
        $ficherosArray = scandir($folder);
        $filename_dest=0;
        foreach( $ficherosArray as $fichero) {
            $withoutExt = preg_replace('/\\.[^.\\s]{2,4}$/', '', basename($fichero) );
            $value = intval($withoutExt);
            if ( $value != 0 ) {
                if ( $value > $filename_dest )
                    $filename_dest = $value;
            }
        }
        $filename_dest++;

        $file = "https://unsplash.it/".bannerWidth."/".bannerHeight."?image=".rand(0,70);
        //$dest = "$folder\\$filename_dest.png";
        $dest = "$folder/$filename_dest.png";
        file_put_contents($dest,fopen($file,'r'));
        $blog_post->setBannerImage("/".$dest);

        //==================
        // Title clean
        $blog_post->setTitleClean(titleCleanOnInit);

        //==================
        // Views
        $blog_post->setViews(viewsOnInit);

        //==================
        // Enabled
        $blog_post->setEnabled(enablePostOnInit);

        //==================
        // Featured
        $blog_post->setFeatured(featuredOnInit);

        //==================
        // Comments enabled
        $blog_post->setCommentsEnabled(enableCommentsOnInit);

        //==================
        //That's it
        return $blog_post;
    }


    private function generateComment()
    {
        $comment = new blog_comment();

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

    private function clearAllPosts()
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getRepository('AppBundle:blog_post');
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



    private function savePostInDB($blog_post)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($blog_post);
        $em->flush();
    }

    //*******************************************
    // Misc.
    //*******************************************

    /**
     * @param $str
     * @return string
     */
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

    private function deleteAllImages()
    {
        $folder = imagesFolderName;
        if ( !is_dir($folder) )
            return;
        $ficherosArray = scandir($folder);
        foreach( $ficherosArray as $fichero) {
            if ( !is_dir($fichero) && strlen($fichero)>3)
                unlink($folder."\\".$fichero);
        }


    }
}
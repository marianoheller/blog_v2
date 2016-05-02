<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\CssSelector\CssSelectorConverter;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

use Symfony\Component\Validator\Tests\Fixtures\Entity;      //Esta no sé q hace

use AppBundle\Entity\blog_author;
use AppBundle\Entity\blog_post;
use AppBundle\Entity\blog_comment;
use AppBundle\Entity\blog_user;
use AppBundle\Entity\blog_tag;
use AppBundle\Entity\blog_related;
use AppBundle\Entity\blog_category;
use AppBundle\Entity\blog_post_to_category;

//=======================================================================
// On init stuff
/** default image folder name (donde se guardan las imagenes) */
const imagesFolderName = "images_blog";
/** Cantidad de post a ser generador por init action */
const cantPostsAtInit = 5;
/** Cantidad de authors a ser generados por init action */
const cantAuthorsAtInit = 4;
/** Cantidad de users a ser generados por init action */
const cantUsersAtInit = 6;
/** Cantidad de comments totales del t0do el blog */
const cantCommentsTotal = 3*cantPostsAtInit;

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

/** Comment MarkRead at init */
const commentReadAtInit = false;
/** Comment Enabled on init */
const commentEnabledAtInit = true;

/** Cantidad de tags en total */
const cantTagsTotalOnInit = cantPostsAtInit*3;

/** Max RelatedPosts para cada post (pueden ser menos con random() */
const maxRelatedPostsPerPost = 3;

/** Cantidad de categories en total */
const cantCategoriesTotal = 3;
/** Category enabled on start */
const categoryEnabledOnStart = true;

//=======================================================================
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
    // Private Variables
    //********************************************************************************************

    private $initStatus=0;


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
    public function initAction( Request $request )
    {
        $this->initStatus++;

        //CLEAR ALL AUTHORS, POSTS, USERS, COMMENTS, TAGS, RELATED
        $this->clearAllAuthors();
        $this->clearAllPosts();
        $this->clearAllUsers();
        $this->clearAllComments();
        $this->clearAllTags();
        $this->clearAllRelated();
        $this->clearAllCategories();
        $this->initStatus++;

        // DELETE IMAGES
        $this->deleteAllImages();
        $this->initStatus++;

        //CREATE AUTHORS
        for ($i = 0; $i < cantAuthorsAtInit; $i++) {
            $author = $this->generateAuthor();
            $this->saveAuthorInDB($author);
        }
        $this->initStatus++;

        //CREATE USERS
        for ($i = 0; $i < cantUsersAtInit; $i++) {
            $user = $this->generateUser();
            $this->saveUserInDB($user);
        }
        $this->initStatus++;

        //CREATE POSTS
        for ($i = 0; $i < cantPostsAtInit; $i++) {
            $blog_post = $this->generatePost();
            $this->savePostInDB($blog_post);
        }
        $this->initStatus++;

        //CREATE COMMENTS
        $repo = $this->getDoctrine()->getRepository('AppBundle:blog_post');
        $query = $repo->createQueryBuilder('p')
            ->where('LENGTH(p.title) > :val')
            ->setParameter('val', '1')
            ->getQuery();
        $postArray = $query->getResult();
        for ( $i=0 ; $i<cantCommentsTotal ; $i++) {
            /** @var blog_post $postObject */
            $postObject = $postArray[random_int(0, sizeof($postArray)-1)];
            $comment = $this->generateCommentToPost($postObject->getId());
            $this->saveCommentInDB($comment);
        }
        $this->initStatus++;

        //CREATE TAGS
        $tagsArray = $this->generateTags();
        $this->saveTagsInDB($tagsArray);
        $this->initStatus++;

        //CREATE RELATED
        $relatedArray = $this->generateRelatedPosts();
        $this->saveRelatedInDB($relatedArray);
        $this->initStatus++;

        //CREATE CATEGORIES
        $categoriesArray = $this->generateCategories();
        $this->saveCategoriesInDB($categoriesArray);
        $this->initStatus++;

        //CREATE POST TO CATEGORIES
        $postToCatsArray = $this->generatePostsToCategories();
        $this->savePostToCatInDB($postToCatsArray);
        $this->initStatus++;

        return new Response("Blog inicializado");
    }

    /**
     * @Route( "/cmd/get_init_status",
     *     name="init_status"
     *      )
     *
     */

    public function initStatusAction ( Request $request )
    {
        $ret = new Response();
        if ( $request->isXmlHttpRequest() ) {
            $ret = new Response($this->getInitStatus());
        }
        return $ret;
    }

    /**
     * @Route( "/cmd/test",
     *     name="test"
     *      )
     *
     */

    public function testAction(  Request $request )
    {
        // is it an Ajax request?
        $d1 = new \DateTime();
        if ( $request->isXmlHttpRequest() ) {
            $ret =  new JsonResponse(
                array('data' => 123)
            );
        }
        else {
            $ret =  $this->render("ajax_test.html.twig",
                array("controllerExecutedAt" => $d1)
            );
        }
        return $ret;
    }

    /**
     * @Route( "/cmd/test_ajax",
     *     name="testAjax"
     *     )
     * @param Request $request
     * @return JsonResponse
     */
    public function getContainer( Request $request)
    {
        return $this->render("init.html.twig");
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

    /**
     * @return blog_user
     */
    private function generateUser()
    {

        $url = "http://api.namefake.com/";
        $lines_array=file($url);
        $lines_string=implode('',$lines_array);

        $encoders = array( new JsonEncoder() );
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $lines_string = preg_replace("/\bname\b/","firstName",$lines_string);
        $lines_string = preg_replace("/\busername\b/","name",$lines_string);
        $lines_string = preg_replace("/\bemail_u\b/","mail",$lines_string);
        $lines_string = preg_replace("/\burl\b/","website",$lines_string);
        /** @var blog_user $user */
        $user = $serializer->deserialize($lines_string, 'AppBundle\Entity\blog_user', 'json');
        $auxMail = strtolower($user->getMail());
        $user->setMail($auxMail."@gmail.com");

        return $user;
    }

    /**
     * @param $post_id
     * @return blog_comment
     */
    private function generateCommentToPost( $post_id )
    {
        /*
        private $id; (pk auto)
        *private $userId;    (foreign to blog_user)
        *private $postId;    (foreign to blog_post)
        *private $isReplyToId;
        *private $comment;
        *private $markRead;
        *private $enabled;
        *private $date;
        */

        $comment = new blog_comment();

        //==================
        // postId
        $comment->setPostId($post_id);

        //==================
        // userId (random)
        $repo = $this->getDoctrine()->getRepository('AppBundle:blog_user');
        $query = $repo->createQueryBuilder('u')
            ->where('LENGTH(u.name) > :val')
            ->setParameter('val', '1')
            ->getQuery();
        $usersArray = $query->getResult();
        /** @var blog_user $userObject */
        $userObject = $usersArray[random_int(0, sizeof($usersArray)-1)];
        $comment->setUserId($userObject->getId());

        //==================
        // Comment (text)
        $url='http://loripsum.net/api/1/short';
        $lines_array=file($url);
        $lines_string=implode('',$lines_array);
        $crawler = new Crawler($lines_string);
        $textComment = $crawler->filter('body > p')->last()->text();
        $textComment = str_replace(', ','',$textComment);
        $textComment = str_replace('. ','',$textComment);
        $comment->setComment(trim($textComment));

        //==================
        // markRead
        $comment->setMarkRead(commentReadAtInit);

        //==================
        // enabled
        $comment->setEnabled(commentEnabledAtInit);

        //==================
        // date
        $d1=new \DateTime(); //now
        $comment->setDate($d1);

        return $comment;
    }


    private function generateTags()
    {
        $tagArray = [];
        $url = "http://randomword.setgetgo.com/get.php";

        //Create tags without foreign key to post
        for ( $i=0 ; $i<cantTagsTotalOnInit ; $i++) {
            $tag = new blog_tag();
            $lines_array=file($url);
            $lines_string=implode('',$lines_array);
            $tag->setTag($lines_string);
            array_push($tagArray,$tag);
        }

        // Now create post assignment
        $repo = $this->getDoctrine()->getRepository('AppBundle:blog_post');
        $query = $repo->createQueryBuilder('p')
            ->where('LENGTH(p.title) > :val')
            ->setParameter('val', '1')
            ->getQuery();
        $postArray = $query->getResult();
        /** @var blog_tag $tag */
        foreach ( $tagArray as $tag)
        {
            /** @var blog_post $postObject */
            $postObject = $postArray[random_int(0, sizeof($postArray)-1)];
            $tag->setPostId( $postObject->getId() );
        }

        return $tagArray;
    }

    private function generateRelatedPosts()
    {
        $relatedArray = [];

        //Get data
        $repo = $this->getDoctrine()->getRepository('AppBundle:blog_post');
        $query = $repo->createQueryBuilder('p')
            ->where('LENGTH(p.title) > :val')
            ->setParameter('val', '1')
            ->getQuery();
        $postArray = $query->getResult();

        //Create relations
        for ( $i=0 ; $i<sizeof($postArray) ; $i++)
        {
            $oldTargetIndex = [];
            $cantDeRelacionesEnElPost = random_int(0,maxRelatedPostsPerPost);
            if ( $cantDeRelacionesEnElPost >= sizeof($postArray) )
                $cantDeRelacionesEnElPost = sizeof($postArray)-1;
            for ( $j=0 ; $j<$cantDeRelacionesEnElPost ; $j++) {
                while ( ($targetIndex = random_int(0, sizeof($postArray)-1)) == $i  || in_array($targetIndex,$oldTargetIndex)); //Para q no sea related con si mismo
                array_push($oldTargetIndex,$targetIndex);
                $relatedObject = new blog_related($i, $targetIndex);
                array_push($relatedArray, $relatedObject);
            }
        }

        return $relatedArray;
    }

    private function generateCategories()
    {
        $categoriesArray = [];
        $url = "http://randomword.setgetgo.com/get.php";

        for ( $i=0 ; $i<cantCategoriesTotal ; $i++ )
        {
            $category = new blog_category();
            $lines_array=file($url);
            $lines_string=implode('',$lines_array);
            $lines_string = ucfirst($lines_string);
            $category->setName( $lines_string );
            $category->setEnabled(categoryEnabledOnStart);
            array_push($categoriesArray,$category);
        }

        return $categoriesArray;
    }

    private function generatePostsToCategories()
    {
        $postToCategoryArray = [];

        $repo = $this->getDoctrine()->getRepository('AppBundle:blog_post');
        $query = $repo->createQueryBuilder('p')
            ->where('LENGTH(p.title) > :val')
            ->setParameter('val', '1')
            ->getQuery();
        $postArray = $query->getResult();

        $repo = $this->getDoctrine()->getRepository('AppBundle:blog_category');
        $query = $repo->createQueryBuilder('c')
            ->where('LENGTH(c.name) > :val')
            ->setParameter('val', '1')
            ->getQuery();
        $categoryArray = $query->getResult();

        /** @var blog_post $postObject */
        foreach ($postArray as $postObject) {
            $randomCatIndex = random_int(0, sizeof($categoryArray)-1);
            /** @var blog_category $randomCat */
            $randomCat = $categoryArray[$randomCatIndex];
            $postToCatObject = new blog_post_to_category( $randomCat->getId() ,$postObject->getId());
            array_push($postToCategoryArray,$postToCatObject);
        }

        return $postToCategoryArray;
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

    private function clearAllUsers()
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getRepository('AppBundle:blog_user');
        $query = $repo->createQueryBuilder('p');
        $query->delete();
        $query->getQuery()->execute();
        $em->flush();
    }

    private function  clearAllComments()
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getRepository('AppBundle:blog_comment');
        $query = $repo->createQueryBuilder('p');
        $query->delete();
        $query->getQuery()->execute();
        $em->flush();
    }

    private function  clearAllTags()
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getRepository('AppBundle:blog_tag');
        $query = $repo->createQueryBuilder('t');
        $query->delete();
        $query->getQuery()->execute();
        $em->flush();
    }

    private function  clearAllRelated()
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getRepository('AppBundle:blog_related');
        $query = $repo->createQueryBuilder('r');
        $query->delete();
        $query->getQuery()->execute();
        $em->flush();
    }

    private function  clearAllCategories()
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getRepository('AppBundle:blog_category');
        $query = $repo->createQueryBuilder('c');
        $query->delete();
        $query->getQuery()->execute();
        $em->flush();
    }

    private function  clearAllPostToCat()
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getRepository('AppBundle:blog_post_to_category');
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
        $em->persist($author);
        $em->flush();
    }

    private function savePostInDB($blog_post)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($blog_post);
        $em->flush();
    }

    private function saveUserInDB($user)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
    }

    private function saveCommentInDB($comment)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($comment);
        $em->flush();
    }


    private function saveTagsInDB($tagsArray)
    {
        /** @var blog_tag $tag */
        foreach ( $tagsArray as $tag) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($tag);
        }
        $em->flush();
    }


    private function saveRelatedInDB($relatedArray)
    {
        /** @var blog_related $related */
        foreach ( $relatedArray as $related) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($related);
        }
        $em->flush();
    }

    private function saveCategoriesInDB($categoriesArray)
    {
        /** @var blog_category $category */
        foreach ( $categoriesArray as $category) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
        }
        $em->flush();
    }

    private function savePostToCatInDB($postToCatsArray)
    {
        /** @var blog_category $category */
        foreach ( $postToCatsArray as $postToCat) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($postToCat);
        }
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

    public function getInitStatus()
    {
        return $this->initStatus;
    }
}
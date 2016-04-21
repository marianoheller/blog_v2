<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * blog_post
 *
 * @ORM\Table(name="blog_post")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\blog_postRepository")
 */
class blog_post
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="article", type="text")
     */
    private $article;

    /**
     * @var string
     *
     * @ORM\Column(name="title_clean", type="string", length=255)
     */
    private $titleClean;

    /**
     * @var string
     *
     * @ORM\Column(name="file", type="string", length=255)
     */
    private $file;

    /**
     * @var int
     *
     * @ORM\Column(name="author_id", type="integer")
     */
    private $authorId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_published", type="datetime")
     */
    private $datePublished;

    /**
     * @var string
     *
     * @ORM\Column(name="banner_image", type="string", length=255)
     */
    private $bannerImage;

    /**
     * @var bool
     *
     * @ORM\Column(name="featured", type="boolean")
     */
    private $featured;

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled;

    /**
     * @var bool
     *
     * @ORM\Column(name="comments_enabled", type="boolean")
     */
    private $commentsEnabled;

    /**
     * @var int
     *
     * @ORM\Column(name="views", type="integer")
     */
    private $views;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return blog_post
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set article
     *
     * @param string $article
     *
     * @return blog_post
     */
    public function setArticle($article)
    {
        $this->article = $article;

        return $this;
    }

    /**
     * Get article
     *
     * @return string
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * Set titleClean
     *
     * @param string $titleClean
     *
     * @return blog_post
     */
    public function setTitleClean($titleClean)
    {
        $this->titleClean = $titleClean;

        return $this;
    }

    /**
     * Get titleClean
     *
     * @return string
     */
    public function getTitleClean()
    {
        return $this->titleClean;
    }

    /**
     * Set file
     *
     * @param string $file
     *
     * @return blog_post
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set authorId
     *
     * @param integer $authorId
     *
     * @return blog_post
     */
    public function setAuthorId($authorId)
    {
        $this->authorId = $authorId;

        return $this;
    }

    /**
     * Get authorId
     *
     * @return int
     */
    public function getAuthorId()
    {
        return $this->authorId;
    }

    /**
     * Set datePublished
     *
     * @param \DateTime $datePublished
     *
     * @return blog_post
     */
    public function setDatePublished($datePublished)
    {
        $this->datePublished = $datePublished;

        return $this;
    }

    /**
     * Get datePublished
     *
     * @return \DateTime
     */
    public function getDatePublished()
    {
        return $this->datePublished;
    }

    /**
     * Set bannerImage
     *
     * @param string $bannerImage
     *
     * @return blog_post
     */
    public function setBannerImage($bannerImage)
    {
        $this->bannerImage = $bannerImage;

        return $this;
    }

    /**
     * Get bannerImage
     *
     * @return string
     */
    public function getBannerImage()
    {
        return $this->bannerImage;
    }

    /**
     * Set featured
     *
     * @param boolean $featured
     *
     * @return blog_post
     */
    public function setFeatured($featured)
    {
        $this->featured = $featured;

        return $this;
    }

    /**
     * Get featured
     *
     * @return bool
     */
    public function getFeatured()
    {
        return $this->featured;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     *
     * @return blog_post
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return bool
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set commentsEnabled
     *
     * @param boolean $commentsEnabled
     *
     * @return blog_post
     */
    public function setCommentsEnabled($commentsEnabled)
    {
        $this->commentsEnabled = $commentsEnabled;

        return $this;
    }

    /**
     * Get commentsEnabled
     *
     * @return bool
     */
    public function getCommentsEnabled()
    {
        return $this->commentsEnabled;
    }

    /**
     * Set views
     *
     * @param integer $views
     *
     * @return blog_post
     */
    public function setViews($views)
    {
        $this->views = $views;

        return $this;
    }

    /**
     * Get views
     *
     * @return int
     */
    public function getViews()
    {
        return $this->views;
    }
}


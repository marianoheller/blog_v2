<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * blog_related
 *
 * @ORM\Table(name="blog_related")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\blog_relatedRepository")
 */
class blog_related
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
     * @var int
     *
     * @ORM\Column(name="blog_post_id", type="integer")
     */
    private $blogPostId;

    /**
     * @var int
     *
     * @ORM\Column(name="blog_related_post_id", type="integer")
     */
    private $blogRelatedPostId;


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
     * Set blogPostId
     *
     * @param integer $blogPostId
     *
     * @return blog_related
     */
    public function setBlogPostId($blogPostId)
    {
        $this->blogPostId = $blogPostId;

        return $this;
    }

    /**
     * Get blogPostId
     *
     * @return int
     */
    public function getBlogPostId()
    {
        return $this->blogPostId;
    }

    /**
     * Set blogRelatedPostId
     *
     * @param integer $blogRelatedPostId
     *
     * @return blog_related
     */
    public function setBlogRelatedPostId($blogRelatedPostId)
    {
        $this->blogRelatedPostId = $blogRelatedPostId;

        return $this;
    }

    /**
     * Get blogRelatedPostId
     *
     * @return int
     */
    public function getBlogRelatedPostId()
    {
        return $this->blogRelatedPostId;
    }
}


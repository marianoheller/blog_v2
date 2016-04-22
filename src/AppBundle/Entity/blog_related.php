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
     * @ORM\Id
     *
     * @ORM\Column(name="blog_post_id", type="integer")
     */
    private $blogPostId;

    /**
     * @var int
     *
     * @ORM\Id
     *
     * @ORM\Column(name="blog_related_post_id", type="integer")
     */
    private $blogRelatedPostId;


    public function __construct($blogPostId, $blogRelatedPostId)
    {
        $this->blogPostId = $blogPostId;
        $this->blogRelatedPostId = $blogRelatedPostId;
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
     * Get blogRelatedPostId
     *
     * @return int
     */
    public function getBlogRelatedPostId()
    {
        return $this->blogRelatedPostId;
    }
}


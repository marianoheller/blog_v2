<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * blog_tag
 *
 * @ORM\Table(name="blog_tag")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\blog_tagRepository")
 */
class blog_tag
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
     * @ORM\Column(name="post_id", type="integer")
     *
     * @ORM\OneToOne(targetEntity="blog_post")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id")
     */
    private $postId;

    /**
     * @var string
     *
     * @ORM\Column(name="tag", type="string", length=45)
     */
    private $tag;

    /**
     * @var string
     *
     * @ORM\Column(name="tag_clean", type="string", length=45)
     */
    private $tagClean;


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
     * Set postId
     *
     * @param integer $postId
     *
     * @return blog_tag
     */
    public function setPostId($postId)
    {
        $this->postId = $postId;

        return $this;
    }

    /**
     * Get postId
     *
     * @return int
     */
    public function getPostId()
    {
        return $this->postId;
    }

    /**
     * Set tag
     *
     * @param string $tag
     *
     * @return blog_tag
     */
    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Get tag
     *
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Set tagClean
     *
     * @param string $tagClean
     *
     * @return blog_tag
     */
    public function setTagClean($tagClean)
    {
        $this->tagClean = $tagClean;

        return $this;
    }

    /**
     * Get tagClean
     *
     * @return string
     */
    public function getTagClean()
    {
        return $this->tagClean;
    }
}


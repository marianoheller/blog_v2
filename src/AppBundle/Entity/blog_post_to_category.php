<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * blog_post_to_category
 *
 * @ORM\Table(name="blog_post_to_category")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\blog_post_to_categoryRepository")
 */
class blog_post_to_category
{
    /**
     * @var int
     *
     * @ORM\Id
     *
     * @ORM\Column(name="category_id", type="integer")
     */
    private $categoryId;

    /**
     * @var int
     *
     * @ORM\Id
     *
     * @ORM\Column(name="post_id", type="integer")
     */
    private $postId;



    public function __construct($categoryId, $postId)
    {
        $this->categoryId = $categoryId;
        $this->postId = $postId;
    }


    /**
     * Get categoryId
     *
     * @return int
     */
    public function getCategoryId()
    {
        return $this->categoryId;
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
}


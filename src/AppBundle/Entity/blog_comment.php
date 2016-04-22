<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * blog_comment
 *
 * @ORM\Table(name="blog_comment")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\blog_commentRepository")
 */
class blog_comment
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
     * @var int
     *
     * @ORM\Column(name="is_reply_to_id", type="integer")
     */
    private $isReplyToId;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text")
     */
    private $comment;

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;

    /**
     * @var bool
     *
     * @ORM\Column(name="mark_read", type="boolean")
     */
    private $markRead;

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;


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
     * @return blog_comment
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
     * Set isReplyToId
     *
     * @param integer $isReplyToId
     *
     * @return blog_comment
     */
    public function setIsReplyToId($isReplyToId)
    {
        $this->isReplyToId = $isReplyToId;

        return $this;
    }

    /**
     * Get isReplyToId
     *
     * @return int
     */
    public function getIsReplyToId()
    {
        return $this->isReplyToId;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return blog_comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     *
     * @return blog_comment
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set markRead
     *
     * @param boolean $markRead
     *
     * @return blog_comment
     */
    public function setMarkRead($markRead)
    {
        $this->markRead = $markRead;

        return $this;
    }

    /**
     * Get markRead
     *
     * @return bool
     */
    public function getMarkRead()
    {
        return $this->markRead;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     *
     * @return blog_comment
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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return blog_comment
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }
}


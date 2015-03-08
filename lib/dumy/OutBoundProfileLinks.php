<?php
namespace Hq\CrawlBundle\Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @MongoDB\Document(collection="hq_outboundlinks")
 */
class OutBoundProfileLinks
{
    /**
     * @MongoDB\Id
     */
    private $id;

    /** @MongoDB\ReferenceOne(targetDocument="Profiles", cascade="persist") */
    private $profile ;

    /**
     * @MongoDB\String
     */
    private $source;

    /**
     * @MongoDB\String
     */
    private $name;

    /**
     * @MongoDB\String
     */
    private $summary;

    /**
     * @MongoDB\int
     */
    private $status;


    /**
     * Get id
     *
     * @return id $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set profile
     *
     * @param Hq\CrawlBundle\Document\Profiles $profile
     * @return self
     */
    public function setProfile(\Hq\CrawlBundle\Document\Profiles $profile)
    {
        $this->profile = $profile;
        return $this;
    }

    /**
     * Get profile
     *
     * @return Hq\CrawlBundle\Document\Profiles $profile
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * Set source
     *
     * @param string $source
     * @return self
     */
    public function setSource($source)
    {
        $this->source = $source;
        return $this;
    }

    /**
     * Get source
     *
     * @return string $source
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set summary
     *
     * @param string $summary
     * @return self
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;
        return $this;
    }

    /**
     * Get summary
     *
     * @return string $summary
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * Set status
     *
     * @param int $status
     * @return self
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get status
     *
     * @return int $status
     */
    public function getStatus()
    {
        return $this->status;
    }
}

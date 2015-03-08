<?php
namespace Hq\CrawlBundle\Document;


/**
 * @MongoDB\Document(collection="hq_experience", repositoryClass="Hq\CrawlBundle\Repository\ExperienceRepository")
 * @MongoDB\Index(keys={"profile"="asc", "role"="asc", "companyName"="asc","fromDate"="asc","toDate"="asc"}, unique="true")
 * @MongoDB\Index(keys={"companyName" = "asc"})
 * @MongoDB\Index(keys={"duration" = "desc"})
 * @MongoDB\Index(keys={"role" = "asc"})
 * @MongoDB\Index(keys={"location" = "asc"})
 */
class Experience
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
    private $role;

    /**
     * @MongoDB\String
     */
    private $companyName;

    /**
     * @MongoDB\String
     */
    private $industry;

    /**
     * @MongoDB\Date
     */
    private $fromDate;

    /**
     * @MongoDB\Date
     */
    private $toDate;

    /**
     * @MongoDB\String
     */
    private $duration;

    /**
     * @MongoDB\boolean
     */
    private $current;

    /**
     * @MongoDB\String
     */
    private $location;

    /**
     * @MongoDB\String
     */
    private $description;

    /**
     * @MongoDB\int
     */
    private $seq;

    /**
     * @MongoDB\Date
     */
    private $updated_at;

    /**
     * @MongoDB\Date
     */
    private $created_at;


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
     * Set role
     *
     * @param string $role
     * @return self
     */
    public function setRole($role)
    {
        $this->role = $role;
        return $this;
    }

    /**
     * Get role
     *
     * @return string $role
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set companyName
     *
     * @param string $companyName
     * @return self
     */
    public function setCompanyName($companyName)
    {
        $this->companyName = $companyName;
        return $this;
    }

    /**
     * Get companyName
     *
     * @return string $companyName
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * Set industry
     *
     * @param string $industry
     * @return self
     */
    public function setIndustry($industry)
    {
        $this->industry = $industry;
        return $this;
    }

    /**
     * Get industry
     *
     * @return string $industry
     */
    public function getIndustry()
    {
        return $this->industry;
    }

    /**
     * Set fromDate
     *
     * @param date $fromDate
     * @return self
     */
    public function setFromDate($fromDate)
    {
        $this->fromDate = $fromDate;
        return $this;
    }

    /**
     * Get fromDate
     *
     * @return date $fromDate
     */
    public function getFromDate()
    {
        return $this->fromDate;
    }

    /**
     * Set toDate
     *
     * @param date $toDate
     * @return self
     */
    public function setToDate($toDate)
    {
        $this->toDate = $toDate;
        return $this;
    }

    /**
     * Get toDate
     *
     * @return date $toDate
     */
    public function getToDate()
    {
        return $this->toDate;
    }

    /**
     * Set duration
     *
     * @param string $duration
     * @return self
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
        return $this;
    }

    /**
     * Get duration
     *
     * @return string $duration
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set current
     *
     * @param boolean $current
     * @return self
     */
    public function setCurrent($current)
    {
        $this->current = $current;
        return $this;
    }

    /**
     * Get current
     *
     * @return boolean $current
     */
    public function getCurrent()
    {
        return $this->current;
    }

    /**
     * Set location
     *
     * @param string $location
     * @return self
     */
    public function setLocation($location)
    {
        $this->location = $location;
        return $this;
    }

    /**
     * Get location
     *
     * @return string $location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     *
     * @return string $description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set seq
     *
     * @param int $seq
     * @return self
     */
    public function setSeq($seq)
    {
        $this->seq = $seq;
        return $this;
    }

    /**
     * Get seq
     *
     * @return int $seq
     */
    public function getSeq()
    {
        return $this->seq;
    }

    /**
     * Set updatedAt
     *
     * @param date $updatedAt
     * @return self
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updated_at = $updatedAt;
        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return date $updatedAt
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Set createdAt
     *
     * @param date $createdAt
     * @return self
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return date $createdAt
     */
    public function getCreatedAt()
    {
        return $this->created_at;
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

    private function getStringDate($date) {
        if($date)
            return $date->format('M Y');
        return '';

   }

    public function printDetails() {
        echo("Id: " . $this->getId() . "\n");
        echo("Experience Detail # : ".  $this->getSeq(). " **********\n");
        echo("Company: " .$this->getCompanyName(). "\n");
        echo("Role: " . $this->getRole(). "\n");
        echo("Industry: ".  $this->getIndustry(). "\n");
        echo("From: ".  $this->getStringDate($this->getFromDate()) . " To : " . $this->getStringDate($this->getToDate()) . "\n");
        echo("Duration: ".  $this->getDuration(). "\n");
        echo("Location: ".  $this->getLocation(). "\n");
        echo("Description: ".  $this->getDescription(). "\n");
        echo("Current: ".  $this->getCurrent(). "\n");
    }
}

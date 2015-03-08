<?php
namespace Hq\CrawlBundle\Document;




/**
 * @MongoDB\Document(collection="hq_profiles", repositoryClass="Hq\CrawlBundle\Repository\ProfilesRepository")
 */
class Profiles
{

    /**
     * @MongoDB\Id
     */
    private $id;

    /**
     * @MongoDB\String
     */
    private $name;

    /**
     * @MongoDB\String
     */
    private $title;

    /**
     * @MongoDB\String
     */
    private $gender;

    /**
     * @MongoDB\String
     */
    private $source;

    /** @MongoDB\ReferenceOne(targetDocument="ProfileStore", cascade="persist") */
    private $profileStore;

    /**
     * @MongoDB\String
     */
    private $industry;

    /**
     * @MongoDB\String
     */
    private $locality;

    /** @MongoDB\ReferenceMany(targetDocument="OutBoundProfileLinks", cascade="persist") */
    private $outBoundProfilesLinks = array();


    /** @MongoDB\ReferenceMany(targetDocument="Experience", cascade="persist") */
    private $experience = array();


    /** @MongoDB\ReferenceMany(targetDocument="Education", cascade="persist") */
    private $education = array();

//    /** @MongoDB\ReferenceMany(targetDocument="DeletedForCompanies", cascade="all") */
//    private $DeletedForCompanies = array();

    /**
     * @MongoDB\String
     */
    private $sourceService;

    /**
     * @MongoDB\Date
     */
    private $resumeLastUpdated;

    /**
     * @MongoDB\String
     */
    private $updatedInES;

    /**
     * @MongoDB\int
     */
    private $status;

    /** @MongoDB\ReferenceOne(targetDocument="SHMetadata", cascade="persist") */
    private $shMetadata;


    /**
     * @MongoDB\Date
     */
    private $updated_at;

    /**
     * @MongoDB\Date
     */
    private $created_at;


    public function __construct()
    {
//        $this->experience = new \Doctrine\Common\Collections\ArrayCollection();
//        $this->education = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
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
     * Set gender
     *
     * @param string $gender
     * @return self
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
        return $this;
    }

    /**
     * Get gender
     *
     * @return string $gender
     */
    public function getGender()
    {
        return $this->gender;
    }


    /**
     * Add experience
     *
     * @param Hq\CrawlBundle\Document\Experience $experience
     */
    public function addExperience(Experience $experience)
    {
        $this->experience[] = $experience;
    }

    /**
     * Remove experience
     *
     * @param Hq\CrawlBundle\Document\Experience $experience
     */
    public function removeExperience(Experience $experience)
    {
        $this->experience->removeElement($experience);
    }

    /**
     * Get experience
     *
     * @return Doctrine\Common\Collections\Collection $experience
     */
    public function getExperience()
    {
        return $this->experience;
    }

    /**
     * Add education
     *
     * @param Hq\CrawlBundle\Document\Education $education
     */
    public function addEducation(Education $education)
    {
        $this->education[] = $education;
    }

    /**
     * Remove education
     *
     * @param Hq\CrawlBundle\Document\Education $education
     */
    public function removeEducation(Education $education)
    {
        $this->education->removeElement($education);
    }

    /**
     * Get education
     *
     * @return Doctrine\Common\Collections\Collection $education
     */
    public function getEducation()
    {
        return $this->education;
    }

    /**
     * Set sourceService
     *
     * @param string $sourceService
     * @return self
     */
    public function setSourceService($sourceService)
    {
        $this->sourceService = $sourceService;
        return $this;
    }

    /**
     * Get sourceService
     *
     * @return string $sourceService
     */
    public function getSourceService()
    {
        return $this->sourceService;
    }

    /**
     * Set resumeLastUpdated
     *
     * @param date $resumeLastUpdated
     * @return self
     */
    public function setResumeLastUpdated($resumeLastUpdated)
    {
        $this->resumeLastUpdated = $resumeLastUpdated;
        return $this;
    }

    /**
     * Get resumeLastUpdated
     *
     * @return date $resumeLastUpdated
     */
    public function getResumeLastUpdated()
    {
        return $this->resumeLastUpdated;
    }

    /**
     * Set updatedInES
     *
     * @param string $updatedInES
     * @return self
     */
    public function setUpdatedInES($updatedInES)
    {
        $this->updatedInES = $updatedInES;
        return $this;
    }

    /**
     * Get updatedInES
     *
     * @return string $updatedInES
     */
    public function getUpdatedInES()
    {
        return $this->updatedInES;
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
     * Set locality
     *
     * @param string $locality
     * @return self
     */
    public function setLocality($locality)
    {
        $this->locality = $locality;
        return $this;
    }

    /**
     * Get locality
     *
     * @return string $locality
     */
    public function getLocality()
    {
        return $this->locality;
    }


    /**
     * Set title
     *
     * @param string $title
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Get title
     *
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Add outBoundProfilesLink
     *
     * @param Hq\CrawlBundle\Document\OutBoundProfileLinks $outBoundProfilesLink
     */
    public function addOutBoundProfilesLink(OutBoundProfileLinks $outBoundProfilesLink)
    {
        $this->outBoundProfilesLinks[] = $outBoundProfilesLink;
    }

    /**
     * Remove outBoundProfilesLink
     *
     * @param Hq\CrawlBundle\Document\OutBoundProfileLinks $outBoundProfilesLink
     */
    public function removeOutBoundProfilesLink(OutBoundProfileLinks $outBoundProfilesLink)
    {
        $this->outBoundProfilesLinks->removeElement($outBoundProfilesLink);
    }

    /**
     * Get outBoundProfilesLinks
     *
     * @return Doctrine\Common\Collections\Collection $outBoundProfilesLinks
     */
    public function getOutBoundProfilesLinks()
    {
        return $this->outBoundProfilesLinks;
    }

  

    /**
     * Set shMetadata
     *
     * @param Hq\CrawlBundle\Document\SHMetadata $shMetadata
     * @return self
     */
    public function setShMetadata(SHMetadata $shMetadata)
    {
        $this->shMetadata = $shMetadata;
        return $this;
    }

    /**
     * Get shMetadata
     *
     * @return Hq\CrawlBundle\Document\SHMetadata $shMetadata
     */
    public function getShMetadata()
    {
        return $this->shMetadata;
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
     * Set profileStore
     *
     * @param Hq\CrawlBundle\Document\ProfileStore $profileStore
     * @return self
     */
    public function setProfileStore(Array $profileStore)
    {
        $this->profileStore = $profileStore;
        return $this;
    }

    /**
     * Get profileStore
     *
     * @return Hq\CrawlBundle\Document\ProfileStore $profileStore
     */
    public function getProfileStore()
    {
        return $this->profileStore;
    }

    public function resetExperienceSeq(){
        foreach($this->getExperience() as $exp){
            //sort by startDate

        }
    }

    public function printDetails() {
        echo("Id: " . $this->getId() . "\n");
        echo("Name: " .$this->getName(). "\n");
        echo("Source : " . $this->getSource(). "\n");
        echo("SourceService: ".  $this->getSourceService(). "\n");
        echo("Status: ".  $this->getStatus(). "\n");
        echo("Gender: ".  $this->getGender(). "\n");

        echo('************* Experience **********');
        $exp = $this->getExperience();
        foreach($exp as $ex) {
            $ex->printDetails();

        }

        echo('************* Education **********');
        $edu = $this->getEducation();
        foreach($edu as $e) {
            $e->printDetails();
        }

    }
}

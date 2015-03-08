<?php
namespace Hq\CrawlBundle\Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

use Gedmo\Mapping\Annotation as Gedmo;



/**
 * @MongoDB\Document(collection="hq_sh_metadata")
 */
class SHMetadata
{

    /**
     * @MongoDB\Id
     */
    private $id;

    /** @MongoDB\ReferenceOne(targetDocument="Profiles", cascade="persist") */
    private $profile ;

    /**
     * @MongoDB\String @MongoDB\Index(unique=true, order="asc")
     */
    private $hash ;

    /**
     * @MongoDB\String
     */
    private $zipResumesS3path;

    /**
     * @MongoDB\String
     */
    private $zipMetadataS3path;

    /**
     * @MongoDB\String
     */
    private $downloadedResumesPath;

    /**
     * @MongoDB\String
     */
    private $downloadedMetadataPath;


    /**
     * @MongoDB\String
     */
    private $currentJob;

    /**
     * @MongoDB\String
     */
    private $previousJobs;

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
     * Set currentJob
     *
     * @param string $currentJob
     * @return self
     */
    public function setCurrentJob($currentJob)
    {
        $this->currentJob = $currentJob;
        return $this;
    }

    /**
     * Get currentJob
     *
     * @return string $currentJob
     */
    public function getCurrentJob()
    {
        return $this->currentJob;
    }

    /**
     * Set previousJobs
     *
     * @param string $previousJobs
     * @return self
     */
    public function setPreviousJobs($previousJobs)
    {
        $this->previousJobs = $previousJobs;
        return $this;
    }

    /**
     * Get previousJobs
     *
     * @return string $previousJobs
     */
    public function getPreviousJobs()
    {
        return $this->previousJobs;
    }

    /**
     * Set hash
     *
     * @param string $hash
     * @return self
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
        return $this;
    }

    /**
     * Get hash
     *
     * @return string $hash
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Set zipfilename
     *
     * @param string $zipfilename
     * @return self
     */
    public function setZipfilename($zipfilename)
    {
        $this->zipfilename = $zipfilename;
        return $this;
    }

    /**
     * Get zipfilename
     *
     * @return string $zipfilename
     */
    public function getZipfilename()
    {
        return $this->zipfilename;
    }

    /**
     * Set zipResumesS3path
     *
     * @param string $zipResumesS3path
     * @return self
     */
    public function setZipResumesS3path($zipResumesS3path)
    {
        $this->zipResumesS3path = $zipResumesS3path;
        return $this;
    }

    /**
     * Get zipResumesS3path
     *
     * @return string $zipResumesS3path
     */
    public function getZipResumesS3path()
    {
        return $this->zipResumesS3path;
    }

    /**
     * Set zipMetadataS3path
     *
     * @param string $zipMetadataS3path
     * @return self
     */
    public function setZipMetadataS3path($zipMetadataS3path)
    {
        $this->zipMetadataS3path = $zipMetadataS3path;
        return $this;
    }

    /**
     * Get zipMetadataS3path
     *
     * @return string $zipMetadataS3path
     */
    public function getZipMetadataS3path()
    {
        return $this->zipMetadataS3path;
    }

    /**
     * Set downloadedResumesPath
     *
     * @param string $downloadedResumesPath
     * @return self
     */
    public function setDownloadedResumesPath($downloadedResumesPath)
    {
        $this->downloadedResumesPath = $downloadedResumesPath;
        return $this;
    }

    /**
     * Get downloadedResumesPath
     *
     * @return string $downloadedResumesPath
     */
    public function getDownloadedResumesPath()
    {
        return $this->downloadedResumesPath;
    }

    /**
     * Set downloadedMetadataPath
     *
     * @param string $downloadedMetadataPath
     * @return self
     */
    public function setDownloadedMetadataPath($downloadedMetadataPath)
    {
        $this->downloadedMetadataPath = $downloadedMetadataPath;
        return $this;
    }

    /**
     * Get downloadedMetadataPath
     *
     * @return string $downloadedMetadataPath
     */
    public function getDownloadedMetadataPath()
    {
        return $this->downloadedMetadataPath;
    }
}

<?php

namespace Cf\BIBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CompanyCompetitors
 *
 * @ORM\Table(name="hq_company_settings")
 * @ORM\Entity(repositoryClass="Cf\BIBundle\Entity\CompanyCompetitorsRepository")
 */
class CompanySettings
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="company", type="string", length=255)
     */
    private $company;

    /**
     * @var string
     *
     * @ORM\Column(name="full_name", type="string", length=255)
     */
    private $full_name;

    /**
     * @var string
     *
     * @ORM\Column(name="competitors", type="string", length=5000)
     */
    private $competitors;

    /**
     * @var string
     *
     * @ORM\Column(name="subsidiaries", type="string", length=5000)
     */
    private $subsidiaries;

    /**
     * @var string
     *
     * @ORM\Column(name="acquired", type="string", length=5000)
     */
    private $acquired;

    /**
     * @var string
     *
     * @ORM\Column(name="similar_names", type="string", length=5000)
     */
    private $similar_names;

    /**
     * @var string
     *
     * @ORM\Column(name="searchType", type="string", length=200, nullable=true)
     */
    private $searchType;

    /**
     * @var string
     *
     * @ORM\Column(name="stop_names", type="text", nullable=true)
     */
    private $stop_names;

    /**
     * @var string
     *
     * @ORM\Column(name="titles", type="string")
     */
    private $titles;

    /**
     * @var string
     *
     * @ORM\Column(name="roles", type="string")
     */
    private $roles;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set company
     *
     * @param string $company
     * @return CompanyCompetitors
     */
    public function setCompany($company)
    {
        $this->company = $company;
    
        return $this;
    }

    /**
     * Get company
     *
     * @return string 
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Set competitors
     *
     * @param string $competitors
     * @return CompanyCompetitors
     */
    public function setCompetitors($competitors)
    {
        $this->competitors = $competitors;
    
        return $this;
    }

    /**
     * Get competitors
     *
     * @return string 
     */
    public function getCompetitors()
    {
        return $this->competitors;
    }

    /**
     * @param string $subsidiaries
     */
    public function setSubsidiaries($subsidiaries)
    {
        $this->subsidiaries = $subsidiaries;
    }

    /**
     * @return string
     */
    public function getSubsidiaries()
    {
        return $this->subsidiaries;
    }

    /**
     * @param string $acquired
     */
    public function setAcquired($acquired)
    {
        $this->acquired = $acquired;
    }

    /**
     * @return string
     */
    public function getAcquired()
    {
        return $this->acquired;
    }

    /**
     * @param string $full_name
     */
    public function setFullName($full_name)
    {
        $this->full_name = $full_name;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->full_name;
    }

    /**
     * @param string $similar_names
     */
    public function setSimilarNames($similar_names)
    {
        $this->similar_names = $similar_names;
    }

    /**
     * @return string
     */
    public function getSimilarNames()
    {
        return $this->similar_names;
    }

    /**
     * @param string $crawl_roles
     */
    public function setCrawlRoles($crawl_roles)
    {
        $this->crawl_roles = $crawl_roles;
    }

    /**
     * @return string
     */
    public function getCrawlRoles()
    {
        return $this->crawl_roles;
    }

    /**
     * @param string $crawl_titles
     */
    public function setCrawlTitles($crawl_titles)
    {
        $this->crawl_titles = $crawl_titles;
    }

    /**
     * @return string
     */
    public function getCrawlTitles()
    {
        return $this->crawl_titles;
    }

    /**
     * @param string $stop_names
     */
    public function setStopNames($stop_names)
    {
        $this->stop_names = $stop_names;
    }

    /**
     * @return string
     */
    public function getStopNames()
    {
        return $this->stop_names;
    }

    /**
     * @param string $searchType
     */
    public function setSearchType($searchType)
    {
        $this->searchType = $searchType;
    }

    /**
     * @return string
     */
    public function getSearchType()
    {
        return $this->searchType;
    }


}

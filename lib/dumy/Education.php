<?php
namespace Hq\CrawlBundle\Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @MongoDB\Document(collection="hq_education", repositoryClass="Hq\CrawlBundle\Repository\EducationRepository"))
 * @MongoDB\Index(keys={"profile"="asc","school"="asc","degree" ="asc","program"="asc","year"="asc"}, unique="true")
 * @MongoDB\Index(keys={"school"="asc"})
 * @MongoDB\Index(keys={"degree"="asc"})
 * @MongoDB\Index(keys={"program"="asc"})
 */
class Education
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
    private $school;

    /**
     * @MongoDB\String
     */
    private $degree;


    /**
     * @MongoDB\String
     */
    private $program;

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
    private $year;

    /**
     * @MongoDB\String
     */
    private $gpa;

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
     * Set school
     *
     * @param string $school
     * @return self
     */
    public function setSchool($school)
    {
        $this->school = $school;
        return $this;
    }

    /**
     * Get school
     *
     * @return string $school
     */
    public function getSchool()
    {
        return $this->school;
    }

    /**
     * Set program
     *
     * @param string $program
     * @return self
     */
    public function setProgram($program)
    {
        $this->program = $program;
        return $this;
    }

    /**
     * Get program
     *
     * @return string $program
     */
    public function getProgram()
    {
        return $this->program;
    }

    /**
     * Set year
     *
     * @param string $year
     * @return self
     */
    public function setYear($year)
    {
        $this->year = $year;
        return $this;
    }

    /**
     * Get year
     *
     * @return string $year
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set gpa
     *
     * @param string $gpa
     * @return self
     */
    public function setGpa($gpa)
    {
        $this->gpa = $gpa;
        return $this;
    }

    /**
     * Get gpa
     *
     * @return string $gpa
     */
    public function getGpa()
    {
        return $this->gpa;
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

    public function printDetails() {
        echo("Id: " . $this->getId() . "\n");
        echo("Education Detail # : ".  $this->getSeq(). " **********\n");
        echo("School: " .$this->getSchool(). "\n");
        echo("Degree: ".  $this->getDegree(). "\n");
        echo("Program: " . $this->getProgram(). "\n");
        echo("GPA: ".  $this->getGpa(). "\n");
        echo("Year: ".  $this->getYear(). "\n");

    }

    /**
     * Set degree
     *
     * @param string $degree
     * @return self
     */
    public function setDegree($degree)
    {
        $this->degree = $degree;
        return $this;
    }

    /**
     * Get degree
     *
     * @return string $degree
     */
    public function getDegree()
    {
        return $this->degree;
    }

    /**
     * @param mixed $fromDate
     */
    public function setFromDate($fromDate)
    {
        $this->fromDate = $fromDate;
    }

    /**
     * @return mixed
     */
    public function getFromDate()
    {
        return $this->fromDate;
    }

    /**
     * @param mixed $toDate
     */
    public function setToDate($toDate)
    {
        $this->toDate = $toDate;
    }

    /**
     * @return mixed
     */
    public function getToDate()
    {
        return $this->toDate;
    }

}

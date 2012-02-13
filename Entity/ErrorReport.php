<?php

namespace CCETC\ErrorReportBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * CCETC\ErrorReportBundle\Entity\ErrorReport
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="CCETC\ErrorReportBundle\Entity\ErrorReportRepository")
 */
class ErrorReport
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $writer_email
     *
     * @ORM\Column(name="writerEmail", type="string", length=255, nullable="true")
     */
    private $writerEmail;

    /**
     * @var datetime $date_reported
     *
     * @ORM\Column(name="datetimeReported", type="datetime")
     */
    private $datetimeReported;

    /**
     * @var text $content
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;


    /**
     * Get id
     *
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set writer_email
     *
     * @param string $writerEmail
     */
    public function setWriterEmail($writerEmail)
    {
        $this->writerEmail = $writerEmail;
    }

    /**
     * Get writer_email
     *
     * @return string $writerEmail
     */
    public function getWriterEmail()
    {
        return $this->writerEmail;
    }

    /**
     * Set content
     *
     * @param text $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * Get content
     *
     * @return text $content
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set datetimeReported
     *
     * @param datetime $datetimeReported
     */
    public function setDatetimeReported($datetimeReported)
    {
        $this->datetimeReported = $datetimeReported;
    }

    /**
     * Get datetimeReported
     *
     * @return datetime $datetimeReported
     */
    public function getDatetimeReported()
    {
        return $this->datetimeReported;
    }
}
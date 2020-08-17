<?php

namespace HB\AdminBundle\Entity\SaleFunnel;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use HB\AdminBundle\Entity\AdditionalBlock;
use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\CoursePriceBlock;
use HB\AdminBundle\Entity\UploadCareFile;
use HB\AdminBundle\Entity\SaleFunnel\Organic\CompanyLogo;
use HB\AdminBundle\Entity\SaleFunnel\Organic\FeedBackVideo;
use HB\AdminBundle\Entity\SaleFunnel\Organic\KnowledgeSkills;
use HB\AdminBundle\Entity\SaleFunnel\Organic\PaymentText;

/**
 * SalesFunnel
 *
 * @ORM\Table(name="sales_funnel_organic")
 * @ORM\Entity()
 */
class SalesFunnelOrganic implements CourseAccessInterface
{
    const BLOCK5_TYPE_RECOMMEND = 'RECOMMEND';
    const BLOCK5_TYPE_REQUIRED = 'REQUIRED';

    const BLOCK4_TITLE = 'Регистрийруйся на курс';
    const BLOCK4_SUBTITLE = 'Крутые знания и отличный опыт мы гарантируем!';
    const BLOCK4_BUTTON_TEXT = 'Занять место';

    const BLOCK8_TITLE = self::BLOCK4_TITLE;
    const BLOCK8_SUBTITLE = self::BLOCK4_SUBTITLE;
    const BLOCK8_BUTTON_TEXT = self::BLOCK4_BUTTON_TEXT;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Course
     *
     * @ORM\OneToOne(targetEntity="HB\AdminBundle\Entity\Course", inversedBy="salesFunnelOrganic")
     * @ORM\JoinColumn(name="course_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $course;

    /**
     * @var UploadCareFile
     *
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\UploadCareFile")
     * @ORM\JoinColumn(name="block1_image_banner_file_id", referencedColumnName="id", nullable=true)
     */
    private $block1ImageBanner;

    /**
     * @var UploadCareFile
     *
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\UploadCareFile")
     * @ORM\JoinColumn(name="block1_video_banner_file_id", referencedColumnName="id", nullable=true)
     */
    private $block1VideoBanner;

    /**
     * @var string | null
     *
     * @ORM\Column(name="block1_button_text", type="string", nullable=true)
     */
    private $block1ButtonText = 'Купить';

    /**
     * @var string
     *
     * @ORM\Column(name="block2_course_info", type="text", nullable=true)
     */
    private $block2CourseInfo;

    /**
     * @var UploadCareFile
     *
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\UploadCareFile")
     * @ORM\JoinColumn(name="block2_course_image_file_id", referencedColumnName="id", nullable=true)
     */
    private $block2CourseImage;

    /**
     * @ORM\ManyToMany(targetEntity="HB\AdminBundle\Entity\SaleFunnel\Organic\KnowledgeSkills", cascade={"remove"})
     * @ORM\JoinTable(name="sales_funnel_organic_skills",
     *      joinColumns={@ORM\JoinColumn(name="sales_funnel_organic_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="skill_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     */
    private $block3KnowledgeSkills;

    /**
     * @var UploadCareFile
     *
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\UploadCareFile")
     * @ORM\JoinColumn(name="block4_photo_file_id", referencedColumnName="id", nullable=true)
     */
    private $block4Photo;

    /**
     * @var string
     *
     * @ORM\Column(name="block4_title", type="string", nullable=true)
     */
    private $block4Title;

    /**
     * @var string
     *
     * @ORM\Column(name="block4_subtitle", type="string", nullable=true)
     */
    private $block4Subtitle;

    /**
     * @var string
     *
     * @ORM\Column(name="block4_button_text", type="string", nullable=true)
     */
    private $block4ButtonText;


    /**
     * @var string
     *
     * Assert\NotBlank()
     *
     * @ORM\Column(name="block5_type", type="string", nullable=true)
     */
    private $block5Type;

    /**
     * @var string
     *
     * @ORM\Column(name="block5_text", type="text", nullable=true)
     */
    private $block5Text;

    /**
     * @var string
     *
     * @ORM\Column(name="block6_author_info", type="string", nullable=true)
     */
    private $block6AuthorInfo;

    /**
     * @var UploadCareFile
     *
     * Assert\NotBlank()
     *
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\UploadCareFile")
     * @ORM\JoinColumn(name="block6_author_photo_file_id", referencedColumnName="id", nullable=true)
     */
    private $block6AuthorPhoto;

    /**
     * @var string
     *
     * @ORM\Column(name="block6_author_experience", type="text", nullable=true)
     */
    private $block6AuthorExperience;

    /**
     * @var UploadCareFile
     *
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\UploadCareFile")
     * @ORM\JoinColumn(name="block6_author_video_file_id", referencedColumnName="id", nullable=true)
     */
    private $block6AuthorVideo;

    /**
     * @var boolean
     *
     * @ORM\Column(name="block6_author_video_from_banner", type="boolean")
     */
    private $block6AuthorVideoFromBanner = false;

    /**
     * @ORM\ManyToMany(targetEntity="HB\AdminBundle\Entity\SaleFunnel\Organic\FeedBackVideo")
     * @ORM\JoinTable(name="sales_funnel_organic_feed_back_video",
     *      joinColumns={@ORM\JoinColumn(name="sales_funnel_organic_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="feedback_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     */
    private $block7Feedbacks;

    /**
     * @var UploadCareFile
     *
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\UploadCareFile")
     * @ORM\JoinColumn(name="block8_photo_file_id", referencedColumnName="id", nullable=true)
     */
    private $block8Photo;

    /**
     * @var string
     *
     * @ORM\Column(name="block8_title", type="string", nullable=true)
     */
    private $block8Title;

    /**
     * @var string
     *
     * @ORM\Column(name="block8_subtitle", type="string", nullable=true)
     */
    private $block8Subtitle;

    /**
     * @var string
     *
     * @ORM\Column(name="block8_button_text", type="string", nullable=true)
     */
    private $block8ButtonText;

    /**
     * @ORM\ManyToMany(targetEntity="HB\AdminBundle\Entity\SaleFunnel\Organic\CompanyLogo")
     * @ORM\JoinTable(name="sales_funnel_organic_company_logo",
     *      joinColumns={@ORM\JoinColumn(name="sales_funnel_organic_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="company_logo_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     */
    private $block9CompaniesLogo;

    /**
     * @ORM\ManyToMany(targetEntity="HB\AdminBundle\Entity\AdditionalBlock")
     * @ORM\JoinTable(name="sales_funnel_organic_additional_block",
     *      joinColumns={@ORM\JoinColumn(name="sales_funnel_organic_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="additional_block_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     */
    private $block10AdditionalBlocks;

    /**
     * @ORM\ManyToMany(targetEntity="HB\AdminBundle\Entity\SaleFunnel\Organic\PaymentText")
     * @ORM\JoinTable(name="sales_funnel_organic_payment_text",
     *      joinColumns={@ORM\JoinColumn(name="sales_funnel_organic_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="payment_text_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     */
    private $block11PaymentTexts;

    /**
     * @var UploadCareFile
     *
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\UploadCareFile")
     * @ORM\JoinColumn(name="block12_photo_signature_file_id", referencedColumnName="id", nullable=true)
     */
    private $block12PhotoSignature;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="HB\AdminBundle\Entity\CoursePriceBlock")
     * @ORM\JoinTable(name="sale_funnel_organic_price_blocks",
     *      joinColumns={@ORM\JoinColumn(name="sale_funnel_organic_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="price_block_id", referencedColumnName="id")}
     *      )
     */
    private $priceBlocks;

    /**
     * @var string | null
     *
     * @ORM\Column(name="external_link", type="string", nullable=true)
     */
    private $externalLink;

    /**
     * Constructor
     *
     * @param Course $course
     */
    public function __construct(Course $course)
    {
        $this->block3KnowledgeSkills = new ArrayCollection();
        $this->block7Feedbacks = new ArrayCollection();
        $this->block9CompaniesLogo = new ArrayCollection();
        $this->block10AdditionalBlocks = new ArrayCollection();
        $this->block11PaymentTexts = new ArrayCollection();

        $this->block4ButtonText = self::BLOCK4_BUTTON_TEXT;
        $this->block4Subtitle = self::BLOCK4_SUBTITLE;
        $this->block4Title = self::BLOCK4_TITLE;

        $this->block8ButtonText = self::BLOCK8_BUTTON_TEXT;
        $this->block8Subtitle = self::BLOCK8_SUBTITLE;
        $this->block8Title = self::BLOCK8_TITLE;

        $this->priceBlocks = new ArrayCollection();
        $this->course = $course;
    }


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Course
     */
    public function getCourse(): Course
    {
        return $this->course;
    }

    /**
     * @param Course $course
     */
    public function setCourse(Course $course): void
    {
        $this->course = $course;
    }

    /**
     * Set block1ButtonText.
     *
     * @param string|null $block1ButtonText
     *
     * @return SalesFunnelOrganic
     */
    public function setBlock1ButtonText($block1ButtonText = null)
    {
        $this->block1ButtonText = $block1ButtonText;

        return $this;
    }

    /**
     * Get block1ButtonText.
     *
     * @return string|null
     */
    public function getBlock1ButtonText()
    {
        return $this->block1ButtonText ?: 'Купить';
    }

    /**
     * Set block2CourseInfo.
     *
     * @param string|null $block2CourseInfo
     *
     * @return SalesFunnelOrganic
     */
    public function setBlock2CourseInfo($block2CourseInfo = null)
    {
        $this->block2CourseInfo = $block2CourseInfo;

        return $this;
    }

    /**
     * Get block2CourseInfo.
     *
     * @return string|null
     */
    public function getBlock2CourseInfo()
    {
        return $this->block2CourseInfo;
    }

    /**
     * @return UploadCareFile | null
     */
    public function getBlock2CourseImage(): ?UploadCareFile
    {
        return $this->block2CourseImage;
    }

    /**
     * @param UploadCareFile | null $block2CourseImage
     */
    public function setBlock2CourseImage(UploadCareFile $block2CourseImage = null): void
    {
        $this->block2CourseImage = $block2CourseImage;
    }

    /**
     * Set block4Title.
     *
     * @param string $block4Title
     *
     * @return SalesFunnelOrganic
     */
    public function setBlock4Title($block4Title)
    {
        $this->block4Title = $block4Title;

        return $this;
    }

    /**
     * Get block4Title.
     *
     * @return string
     */
    public function getBlock4Title()
    {
        return $this->block4Title;
    }

    /**
     * Set block4Subtitle.
     *
     * @param string $block4Subtitle
     *
     * @return SalesFunnelOrganic
     */
    public function setBlock4Subtitle($block4Subtitle)
    {
        $this->block4Subtitle = $block4Subtitle;

        return $this;
    }

    /**
     * Get block4Subtitle.
     *
     * @return string
     */
    public function getBlock4Subtitle()
    {
        return $this->block4Subtitle;
    }

    /**
     * Set block4ButtonText.
     *
     * @param string $block4ButtonText
     *
     * @return SalesFunnelOrganic
     */
    public function setBlock4ButtonText($block4ButtonText)
    {
        $this->block4ButtonText = $block4ButtonText;

        return $this;
    }

    /**
     * Get block4ButtonText.
     *
     * @return string
     */
    public function getBlock4ButtonText()
    {
        return $this->block4ButtonText;
    }

    /**
     * Set block5Type.
     *
     * @param string $block5Type
     *
     * @return SalesFunnelOrganic
     */
    public function setBlock5Type($block5Type)
    {
        $this->block5Type = $block5Type;

        return $this;
    }

    /**
     * Get block5Type.
     *
     * @return string
     */
    public function getBlock5Type()
    {
        return $this->block5Type;
    }

    /**
     * Set block5Text.
     *
     * @param string $block5Text
     *
     * @return SalesFunnelOrganic
     */
    public function setBlock5Text($block5Text)
    {
        $this->block5Text = $block5Text;

        return $this;
    }

    /**
     * Get block5Text.
     *
     * @return string
     */
    public function getBlock5Text()
    {
        return $this->block5Text;
    }

    /**
     * Set block6AuthorInfo.
     *
     * @param string $block6AuthorInfo
     *
     * @return SalesFunnelOrganic
     */
    public function setBlock6AuthorInfo($block6AuthorInfo)
    {
        $this->block6AuthorInfo = $block6AuthorInfo;

        return $this;
    }

    /**
     * Get block6AuthorInfo.
     *
     * @return string
     */
    public function getBlock6AuthorInfo()
    {
        return $this->block6AuthorInfo;
    }

    /**
     * Set block6AuthorExperience.
     *
     * @param string $block6AuthorExperience
     *
     * @return SalesFunnelOrganic
     */
    public function setBlock6AuthorExperience($block6AuthorExperience)
    {
        $this->block6AuthorExperience = $block6AuthorExperience;

        return $this;
    }

    /**
     * Get block6AuthorExperience.
     *
     * @return string
     */
    public function getBlock6AuthorExperience()
    {
        return $this->block6AuthorExperience;
    }

    /**
     * Set block6AuthorVideoFromBanner.
     *
     * @param bool $block6AuthorVideoFromBanner
     *
     * @return SalesFunnelOrganic
     */
    public function setBlock6AuthorVideoFromBanner($block6AuthorVideoFromBanner)
    {
        $this->block6AuthorVideoFromBanner = $block6AuthorVideoFromBanner;

        return $this;
    }

    /**
     * Get block6AuthorVideoFromBanner.
     *
     * @return bool
     */
    public function getBlock6AuthorVideoFromBanner()
    {
        return $this->block6AuthorVideoFromBanner;
    }

    /**
     * Set block8Title.
     *
     * @param string $block8Title
     *
     * @return SalesFunnelOrganic
     */
    public function setBlock8Title($block8Title)
    {
        $this->block8Title = $block8Title;

        return $this;
    }

    /**
     * Get block8Title.
     *
     * @return string
     */
    public function getBlock8Title()
    {
        return $this->block8Title;
    }

    /**
     * Set block8Subtitle.
     *
     * @param string $block8Subtitle
     *
     * @return SalesFunnelOrganic
     */
    public function setBlock8Subtitle($block8Subtitle)
    {
        $this->block8Subtitle = $block8Subtitle;

        return $this;
    }

    /**
     * Get block8Subtitle.
     *
     * @return string
     */
    public function getBlock8Subtitle()
    {
        return $this->block8Subtitle;
    }

    /**
     * Set block8ButtonText.
     *
     * @param string $block8ButtonText
     *
     * @return SalesFunnelOrganic
     */
    public function setBlock8ButtonText($block8ButtonText)
    {
        $this->block8ButtonText = $block8ButtonText;

        return $this;
    }

    /**
     * Get block8ButtonText.
     *
     * @return string
     */
    public function getBlock8ButtonText()
    {
        return $this->block8ButtonText;
    }

    /**
     * Set block1ImageBanner.
     *
     * @param UploadCareFile|null $block1ImageBanner
     *
     * @return SalesFunnelOrganic
     */
    public function setBlock1ImageBanner(UploadCareFile $block1ImageBanner = null)
    {
        $this->block1ImageBanner = $block1ImageBanner;

        return $this;
    }

    /**
     * Get block1ImageBanner.
     *
     * @return UploadCareFile|null
     */
    public function getBlock1ImageBanner()
    {
        return $this->block1ImageBanner;
    }

    /**
     * Set block1VideoBanner.
     *
     * @param UploadCareFile|null $block1VideoBanner
     *
     * @return SalesFunnelOrganic
     */
    public function setBlock1VideoBanner(UploadCareFile $block1VideoBanner = null)
    {
        $this->block1VideoBanner = $block1VideoBanner;

        return $this;
    }

    /**
     * Get block1VideoBanner.
     *
     * @return UploadCareFile|null
     */
    public function getBlock1VideoBanner()
    {
        return $this->block1VideoBanner;
    }

    /**
     * Add block3KnowledgeSkill.
     *
     * @param KnowledgeSkills $block3KnowledgeSkill
     *
     * @return SalesFunnelOrganic
     */
    public function addBlock3KnowledgeSkill(KnowledgeSkills $block3KnowledgeSkill)
    {
        $this->block3KnowledgeSkills[] = $block3KnowledgeSkill;

        return $this;
    }

    /**
     * Remove block3KnowledgeSkill.
     *
     * @param KnowledgeSkills $block3KnowledgeSkill
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeBlock3KnowledgeSkill(KnowledgeSkills $block3KnowledgeSkill)
    {
        return $this->block3KnowledgeSkills->removeElement($block3KnowledgeSkill);
    }

    /**
     * Get block3KnowledgeSkills.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBlock3KnowledgeSkills()
    {
        return $this->block3KnowledgeSkills;
    }

    /**
     * Set block4Photo.
     *
     * @param UploadCareFile|null $block4Photo
     *
     * @return SalesFunnelOrganic
     */
    public function setBlock4Photo(UploadCareFile $block4Photo = null)
    {
        $this->block4Photo = $block4Photo;

        return $this;
    }

    /**
     * Get block4Photo.
     *
     * @return UploadCareFile|null
     */
    public function getBlock4Photo()
    {
        return $this->block4Photo;
    }

    /**
     * Set block6AuthorPhoto.
     *
     * @param UploadCareFile|null $block6AuthorPhoto
     *
     * @return SalesFunnelOrganic
     */
    public function setBlock6AuthorPhoto(UploadCareFile $block6AuthorPhoto = null)
    {
        $this->block6AuthorPhoto = $block6AuthorPhoto;

        return $this;
    }

    /**
     * Get block6AuthorPhoto.
     *
     * @return UploadCareFile|null
     */
    public function getBlock6AuthorPhoto()
    {
        return $this->block6AuthorPhoto;
    }

    /**
     * Set block6AuthorVideo.
     *
     * @param UploadCareFile|null $block6AuthorVideo
     *
     * @return SalesFunnelOrganic
     */
    public function setBlock6AuthorVideo(UploadCareFile $block6AuthorVideo = null)
    {
        $this->block6AuthorVideo = $block6AuthorVideo;

        return $this;
    }

    /**
     * Get block6AuthorVideo.
     *
     * @return UploadCareFile|null
     */
    public function getBlock6AuthorVideo()
    {
        return $this->block6AuthorVideo;
    }

    /**
     * Add block7Feedback.
     *
     * @param FeedBackVideo $block7Feedback
     *
     * @return SalesFunnelOrganic
     */
    public function addBlock7Feedback(FeedBackVideo $block7Feedback)
    {
        $this->block7Feedbacks[] = $block7Feedback;

        return $this;
    }

    /**
     * Remove block7Feedback.
     *
     * @param FeedBackVideo $block7Feedback
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeBlock7Feedback(FeedBackVideo $block7Feedback)
    {
        return $this->block7Feedbacks->removeElement($block7Feedback);
    }

    /**
     * Get block7Feedbacks.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBlock7Feedbacks()
    {
        return $this->block7Feedbacks;
    }

    /**
     * Set block8Photo.
     *
     * @param UploadCareFile|null $block8Photo
     *
     * @return SalesFunnelOrganic
     */
    public function setBlock8Photo(UploadCareFile $block8Photo = null)
    {
        $this->block8Photo = $block8Photo;

        return $this;
    }

    /**
     * Get block8Photo.
     *
     * @return UploadCareFile|null
     */
    public function getBlock8Photo()
    {
        return $this->block8Photo;
    }

    /**
     * Add block9CompaniesLogo.
     *
     * @param CompanyLogo $block9CompaniesLogo
     *
     * @return SalesFunnelOrganic
     */
    public function addBlock9CompaniesLogo(CompanyLogo $block9CompaniesLogo)
    {
        $this->block9CompaniesLogo[] = $block9CompaniesLogo;

        return $this;
    }

    /**
     * Remove block9CompaniesLogo.
     *
     * @param CompanyLogo $block9CompaniesLogo
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeBlock9CompaniesLogo(CompanyLogo $block9CompaniesLogo)
    {
        return $this->block9CompaniesLogo->removeElement($block9CompaniesLogo);
    }

    /**
     * Get block9CompaniesLogo.
     *
     * @return Collection
     */
    public function getBlock9CompaniesLogo()
    {
        return $this->block9CompaniesLogo;
    }

    /**
     * @param AdditionalBlock $block
     */
    public function addBlock10AdditionalBlock(AdditionalBlock $block)
    {
        $this->block10AdditionalBlocks[] = $block;
    }

    /**
     * @param AdditionalBlock $block
     *
     * @return bool
     */
    public function removeBlock10AdditionalBlock(AdditionalBlock $block)
    {
        return $this->block10AdditionalBlocks->removeElement($block);
    }

    /**
     * @return ArrayCollection
     */
    public function getBlock10AdditionalBlocks()
    {
        return $this->block10AdditionalBlocks;
    }


    /**
     * Add block11PaymentText.
     *
     * @param PaymentText $block11PaymentText
     */
    public function addBlock11PaymentText(PaymentText $block11PaymentText)
    {
        $this->block11PaymentTexts[] = $block11PaymentText;
    }

    /**
     * Remove block11PaymentText.
     *
     * @param PaymentText $block11PaymentText
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeBlock11PaymentText(PaymentText $block11PaymentText)
    {
        return $this->block11PaymentTexts->removeElement($block11PaymentText);
    }

    /**
     * Get block11PaymentTexts.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBlock11PaymentTexts()
    {
        return $this->block11PaymentTexts;
    }

    /**
     * @return UploadCareFile | null
     */
    public function getBlock12PhotoSignature(): ?UploadCareFile
    {
        return $this->block12PhotoSignature;
    }

    /**
     * @param UploadCareFile $block12PhotoSignature
     */
    public function setBlock12PhotoSignature(UploadCareFile $block12PhotoSignature = null): void
    {
        $this->block12PhotoSignature = $block12PhotoSignature;
    }

    /**
     * @return ArrayCollection
     */
    public function getPriceBlocks()
    {
        return $this->priceBlocks;
    }

    /**
     * @param CoursePriceBlock $priceBlock
     */
    public function addPriceBlock(CoursePriceBlock $priceBlock): void
    {
        $this->priceBlocks[] = $priceBlock;
    }

    /**
     * @param CoursePriceBlock $priceBlock
     */
    public function removePriceBlock(CoursePriceBlock $priceBlock): void
    {
        $this->priceBlocks->removeElement($priceBlock);
    }

    /**
     * @return string|null
     */
    public function getExternalLink(): ?string
    {
        return $this->externalLink;
    }

    /**
     * @param string|null $externalLink
     */
    public function setExternalLink(?string $externalLink): void
    {
        $this->externalLink = $externalLink;
    }
}

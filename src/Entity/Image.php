<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use App\Controller\UploadImageAction;

/**
 * @ORM\Entity()
 * @Vich\Uploadable()
 * @ApiResource(
 *   attributes={"order"={"id":"DESC"}},
 *    collectionOperations={
 *      "get",
 *      "post"={
 *        "method"="POST",
 *        "path"="/images",
 *        "controller"=UploadImageAction::class,
 *        "defaults"={"_api_receive"=false}
 *      }
 *   }
 * )
 */
class Image
{
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 */
	private $id;


	/**
	 * @Vich\UploadableField(mapping="images" , fileNameProperty="url")
	 * @Assert\NotBlank()
	 */
	private $file;

	/**
	 * @ORM\Column(nullable=true)
	 * @Groups({"get-blog-post-with-author"})
	 */
	private $url;

	/**
	 * @ORM\Column(type="datetime" , nullable=true)
	 */
	private $created_at;
	/**
	 * @ORM\Column(type="datetime" , nullable=true)
	 */
	private $updated_at;

	public function __construct()
	{
		$this->created_at = Carbon::now();
		$this->updated_at = Carbon::now();
	}

	public function getId()
	{
		return $this->id;
	}

	public function setId($id): void
	{
		$this->id = $id;
	}

	public function getFile()
	{
		return $this->file;
	}

	public function setFile($file): void
	{
		$this->file = $file;
	}

	public function getUrl()
	{
		return $this->url;
	}

	public function setUrl($url): void
	{
		$this->url = $url;
	}

	public function getCreatedAt()
	{
		return $this->created_at;
	}


	public function getUpdatedAt()
	{
		return $this->updated_at;
	}


}
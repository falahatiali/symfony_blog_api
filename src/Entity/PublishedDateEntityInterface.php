<?php

namespace App\Entity;


interface PublishedDateEntityInterface
{
	public function setPublished(\DateTimeInterface $dateTime): PublishedDateEntityInterface;
}
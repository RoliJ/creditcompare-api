<?php

namespace App\Entity;

use App\Repository\ApiLogRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: ApiLogRepository::class)]
#[ORM\Table(name: 'api_logs')]
class ApiLog
{
    use TimestampableEntity;
    use SoftDeleteableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'text', nullable: false)]
    private string $requestUrl;

    #[ORM\Column(type: 'text', nullable: false)]
    private string $response;

    // Getters and Setters...

    public function getId(): int
    {
        return $this->id;
    }

    public function getRequestUrl(): string
    {
        return $this->requestUrl;
    }

    public function setRequestUrl(string $requestUrl): self
    {
        $this->requestUrl = $requestUrl;
        return $this;
    }

    public function getResponse(): string
    {
        return $this->response;
    }

    public function setResponse(string $response): self
    {
        $this->response = $response;
        return $this;
    }
}

<?php

namespace App\Tests\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\User;
use App\EventSubscriber\AuthoredEntitySubscriber;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class authoredEntitySubscriberTest extends TestCase
{
    public function testConfiguration()
    {
        $result = AuthoredEntitySubscriber::getSubscribedEvents();
        $this->assertArrayHasKey(KernelEvents::CONTROLLER , $result);
        $this->assertEquals(
            ['getAuthenticatedUser', EventPriorities::PRE_WRITE], $result[KernelEvents::VIEW]
        );
    }

    public function testSetAuthorCall()
    {
        $tokenStorageMock = $this->getTokenStorageMock();
        $eventMock = $this->getEventMock();

        (new AuthoredEntitySubscriber($tokenStorageMock))->getAuthenticatedUser(
            $eventMock
        );
    }


    private function a(){

    }

    /**
     * @return MockObject|TokenStorageInterface
     */
    public function getTokenStorageMock(): MockObject
    {
        $tokenMock = $this->getMockBuilder(TokenInterface::class)->getMockForAbstractClass();
        $tokenMock->expects($this->once())->method('getUser')->willReturn(new User());

        $tokenStorageMock = $this->getMockBuilder(TokenStorageInterface::class)->getMockForAbstractClass();
        $tokenStorageMock->expects($this->once())->method('getToken')->willReturn($tokenMock);
        return $tokenStorageMock;
    }

    /**
     * @return MockObject
     */
    public function getEventMock(): MockObject
    {
        $eventMock = $this->getMockBuilder(GetResponseForControllerResultEvent::class)->disableOriginalConstructor()->getMock();
        return $eventMock;
    }
}

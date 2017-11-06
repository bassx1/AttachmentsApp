<?php

namespace Tests\Unit;

use App\Entities\Attachment;
use App\AttachmentsTransferManager;
use App\Clients\Gmail\GmailClient;
use App\Notifications\ConsoleOutputNotifier;
use App\Services\ConsoleOutputService;
use App\Storages\DriveStorage;
use PHPUnit_Framework_MockObject_MockObject;
use Tests\TestCase;

/**
 * Not so many tests here :)
 */
class AttachmentsTransferTest extends TestCase
{


    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $client1;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $client2;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $storage;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $notifier;


    public function setUp()
    {
        parent::setUp();

        $this->client1 = $this->getMockBuilder(GmailClient::class)
            ->disableOriginalConstructor()
            ->setMethods(['fetchAttachments'])
            ->getMock();

        $this->client2 = $this->getMockBuilder(GmailClient::class)
            ->disableOriginalConstructor()
            ->setMethods(['getStorage'])
            ->getMock();

        $this->storage = $this->getMockBuilder(DriveStorage::class)
            ->disableOriginalConstructor()
            ->setMethods(['uploadFile'])
            ->getMock();

        $this->notifier = $this->createMock(ConsoleOutputNotifier::class);

        DI()->set('notifier', $this->notifier);
        DI()->set('ConsoleOutputService',  $this->createMock(ConsoleOutputService::class));
    }


    /**
     * @test
     */
    public function testOfAttachmentsTransfer()
    {
        $attachmentsToBeMoved = [new Attachment(), new Attachment(), new Attachment()];

        $this->notifier
            ->expects($this->exactly(2)) // 2 clients => 2 messages
            ->method('notify');

        $this->storage
            ->expects($this->exactly(count($attachmentsToBeMoved)))
            ->method('uploadFile');

        $this->client1
            ->expects($this->once())
            ->method('fetchAttachments')
            ->willReturn($attachmentsToBeMoved);

        $this->client2
            ->expects($this->once())
            ->method('getStorage')
            ->willReturn($this->storage);


        $uploadManager = new AttachmentsTransferManager($this->client1, $this->client2);
        $uploadManager->run();
    }





}
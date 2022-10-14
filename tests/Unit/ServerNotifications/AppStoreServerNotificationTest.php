<?php

namespace Tests\Unit\ServerNotifications;

use Imdhemy\AppStore\ServerNotifications\ServerNotification;
use Imdhemy\Purchases\Contracts\SubscriptionContract;
use Imdhemy\Purchases\ServerNotifications\AppStoreServerNotification;
use Imdhemy\Purchases\ValueObjects\Time;
use JsonException;
use Tests\TestCase;

class AppStoreServerNotificationTest extends TestCase
{
    /**
     * @var AppStoreServerNotification
     */
    private AppStoreServerNotification $appStoreServerNotification;

    /**
     * @var array
     */
    private array $serverNotificationBody;

    /**
     * @inheritDoc
     * @throws JsonException
     */
    protected function setUp(): void
    {
        parent::setUp();
        $path = $this->testAssetPath('appstore-server-notification.json');
        $this->serverNotificationBody = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);

        $serverNotification = ServerNotification::fromArray($this->serverNotificationBody);
        $this->appStoreServerNotification = new AppStoreServerNotification($serverNotification);
    }

    /**
     * @test
     */
    public function get_notification_type(): void
    {
        $this->assertEquals(
            ServerNotification::DID_CHANGE_RENEWAL_STATUS,
            $this->appStoreServerNotification->getType()
        );
    }

    /**
     * @test
     */
    public function get_subscription(): void
    {
        $subscription = $this->appStoreServerNotification->getSubscription();

        $this->assertEquals(SubscriptionContract::PROVIDER_APP_STORE, $subscription->getProvider());
    }

    /**
     * @test
     */
    public function get_change_renewal_status_data(): void
    {
        $isAutoRenewal = $this->appStoreServerNotification->isAutoRenewal();
        $changeDate = $this->appStoreServerNotification->getAutoRenewStatusChangeDate();

        $this->assertFalse($isAutoRenewal);
        $this->assertInstanceOf(Time::class, $changeDate);
    }

    /**
     * @test
     */
    public function get_bundle(): void
    {
        $this->assertEquals(
            'com.twigano.fashion',
            $this->appStoreServerNotification->getBundle()
        );
    }

    /**
     * @test
     */
    public function get_payload(): void
    {
        $this->assertEquals(
            $this->serverNotificationBody,
            $this->appStoreServerNotification->getPayload()
        );
    }

    /**
     * @test
     */
    public function test_get_auto_renew_product_id()
    {
        $this->assertNotNull($this->appStoreServerNotification->getAutoRenewProductId());
    }
}

<?php

namespace App\Providers;

use App\Contracts\Activity\ActivityInterface;
use App\Contracts\Hotspot\HotspotInterface;
use App\Contracts\ListInterface;
use App\Contracts\Auth\AuthJwtInterface;
use App\Contracts\Notification\NotificationInterface;
use App\Contracts\Notification\PushNotificationInterface;
use App\Contracts\Storage\ImageStorageInterface;
use App\Contracts\User\SportContactInterface;
use App\Contracts\User\SportContactRequestInterface;
use App\Contracts\User\UserDeviceInterface;
use App\Contracts\User\UserInterface;
use App\Contracts\Sport\SportInterface;

use App\Repositories\Activity\ActivityRepository;
use App\Repositories\Hotspot\HotspotRepository;
use App\Repositories\ListRepository;
use App\Repositories\Notification\NotificationRepository;
use App\Repositories\User\SportContactRepository;
use App\Repositories\User\SportContactRequestRepository;
use App\Repositories\User\UserDeviceRepository;
use App\Repositories\User\UserRepository;
use App\Repositories\Sport\SportRepository;
use App\Repositories\Group\GroupRepository;
use App\Contracts\Group\GroupInterface;

use App\Services\Auth\AuthJWTFirebase;

use App\Services\Notification\PushwooshNotification;
use App\Services\Storage\ImageStorage;
use Illuminate\Support\ServiceProvider;

class ContractServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(AuthJwtInterface::class, AuthJWTFirebase::class);
        $this->app->bind(UserInterface::class, UserRepository::class);
        $this->app->bind(SportInterface::class, SportRepository::class);
        $this->app->bind(ActivityInterface::class, ActivityRepository::class);
        $this->app->bind(SportContactRequestInterface::class, SportContactRequestRepository::class);
        $this->app->bind(ListInterface::class, ListRepository::class);
        $this->app->bind(UserDeviceInterface::class, UserDeviceRepository::class);
        $this->app->bind(PushNotificationInterface::class, PushwooshNotification::class);
        $this->app->bind(NotificationInterface::class, NotificationRepository::class);
        $this->app->bind(SportContactInterface::class, SportContactRepository::class);
        $this->app->bind(ImageStorageInterface::class, ImageStorage::class);
        $this->app->bind(HotspotInterface::class, HotspotRepository::class);
        $this->app->bind(GroupInterface::class, GroupRepository::class);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}

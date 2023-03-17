<?php
namespace SocialiteProviders\Nycu;

use SocialiteProviders\Manager\SocialiteWasCalled;

class NycuExtendSocialite
{
    /**
     * Register the provider.
     *
     * @param SocialiteWasCalled $event
     */
    public function handle(SocialiteWasCalled $event)
    {
        $event->extendSocialite('nycu', Provider::class);
    }
}
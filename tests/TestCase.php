<?php

namespace Tests;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Fortify\Features;

abstract class TestCase extends BaseTestCase
{
    public function createApplication()
    {
        $cachedConfig = $this->getCachedConfigPath();

        if (file_exists($cachedConfig)) {
            unlink($cachedConfig);
        }

        return parent::createApplication();
    }

    private function getCachedConfigPath(): string
    {
        return $this->appBasePath().'/bootstrap/cache/config.php';
    }

    private function appBasePath(): string
    {
        return Application::inferBasePath();
    }

    protected function skipUnlessFortifyHas(string $feature, ?string $message = null): void
    {
        if (! Features::enabled($feature)) {
            $this->markTestSkipped($message ?? "Fortify feature [{$feature}] is not enabled.");
        }
    }
}

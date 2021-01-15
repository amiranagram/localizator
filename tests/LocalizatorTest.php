<?php

namespace Amirami\Localizator\Tests;

/**
 * Class LocalizatorTest
 * @package Amirami\Localizator\Tests
 */
class LocalizatorTest extends TestCase
{
    /**
     * @return void
     */
    public function testLocalizeCommandBasic(): void
    {
        $this->createTestView("{{ __('Localizator') }} {{ __('app.name') }}");

        // Run localize command.
        $this->artisan('localize')
            ->assertExitCode(0);

        // Do created locale files exist?
        self::assertDefaultLangFilesExist('en', ['app']);
        self::assertJsonLangFilesExist('en');

        // Do their contents match the expected results?
        $enDefaultContents = $this->getDefaultLangContents('en', 'app');
        $enJsonContents = $this->getJsonLangContents('en');
        self::assertEquals(['name' => ''], $enDefaultContents);
        self::assertEquals(['Localizator' => 'Localizator'], $enJsonContents);

        // Cleanup.
        self::flushDirectories('lang', 'views');
    }

    /**
     * @return void
     */
    public function testLocalizeCommandWithDefinedLocales(): void
    {
        $this->createTestView("{{ __('Localizator') }} {{ __('app.name') }}");

        // Run localize command.
        $this->artisan('localize', ['lang' => 'en,de'])
            ->assertExitCode(0);

        // Do created locale files exist?
        self::assertDefaultLangFilesExist(['en', 'de'], ['app']);
        self::assertJsonLangFilesExist(['en', 'de']);

        // Do their contents match the expected results?
        $enDefaultContents = $this->getDefaultLangContents('en', 'app');
        $enJsonContents = $this->getJsonLangContents('en');
        $deDefaultContents = $this->getDefaultLangContents('de', 'app');
        $deJsonContents = $this->getJsonLangContents('de');
        self::assertEquals(['name' => ''], $enDefaultContents);
        self::assertEquals(['name' => ''], $deDefaultContents);
        self::assertEquals(['Localizator' => 'Localizator'], $enJsonContents);
        self::assertEquals(['Localizator' => ''], $deJsonContents);

        // Cleanup.
        self::flushDirectories('lang', 'views');
    }

    /**
     * @return void
     */
    public function testLocalizeCommandWithSortingKeys(): void
    {
        $this->createTestView("{{ __('Delete') }} {{ __('Cancel') }} {{ __('Login') }}", 'test-1');
        $this->createTestView("{{ __('auth.throttle') }} {{ __('auth.failed') }} {{ __('auth.password') }}", 'test-2');

        // Run localize command.
        $this->artisan('localize')
            ->assertExitCode(0);

        // Do created locale files exist?
        self::assertDefaultLangFilesExist('en', ['auth']);
        self::assertJsonLangFilesExist('en');

        // Do their contents match the expected results?
        $enDefaultContents = $this->getDefaultLangContents('en', 'auth');
        $enJsonContents = $this->getJsonLangContents('en');

        // Did it sort the translation keys like we expected?
        self::assertEquals([
            'failed' => '',
            'password' => '',
            'throttle' => '',
        ], $enDefaultContents);
        self::assertEquals([
            'Cancel' => 'Cancel',
            'Delete' => 'Delete',
            'Login' => 'Login',
        ], $enJsonContents);

        // Cleanup.
        self::flushDirectories('lang', 'views');
    }

    /**
     * @return void
     */
    public function testLocalizeCommandByMergingTheExistingTranslations(): void
    {
        $this->createTestView("{{ __('Delete') }} {{ __('Cancel') }} {{ __('Login') }}", 'test-1');
        $this->createTestView("{{ __('auth.throttle') }} {{ __('auth.failed') }} {{ __('auth.password') }}", 'test-2');

        $this->createTestDefaultLangFile([
            'password' => 'Das eingegebene Passwort ist nicht korrekt.',
        ], 'auth', 'de');
        $this->createTestJsonLangFile([
            'Login' => 'Anmelden',
            'Delete' => 'Löschen',
        ], 'de');

        // Run localize command.
        $this->artisan('localize', ['lang' => 'de'])
            ->assertExitCode(0);

        // Do created locale files exist?
        self::assertDefaultLangFilesExist('de', ['auth']);
        self::assertJsonLangFilesExist('de');

        // Do their contents match the expected results?
        $enDefaultContents = $this->getDefaultLangContents('de', 'auth');
        $enJsonContents = $this->getJsonLangContents('de');

        // Did it preserve the already translated keys?
        self::assertEquals([
            'failed' => '',
            'password' => 'Das eingegebene Passwort ist nicht korrekt.',
            'throttle' => '',
        ], $enDefaultContents);
        self::assertEquals([
            'Cancel' => '',
            'Delete' => 'Löschen',
            'Login' => 'Anmelden',
        ], $enJsonContents);

        // Cleanup.
        self::flushDirectories('lang', 'views');
    }
}

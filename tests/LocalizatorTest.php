<?php

namespace Amirami\Localizator\Tests;

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
        self::assertSame(['name' => ''], $enDefaultContents);
        self::assertSame(['Localizator' => 'Localizator'], $enJsonContents);

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
        self::assertSame(['name' => ''], $enDefaultContents);
        self::assertSame(['name' => ''], $deDefaultContents);
        self::assertSame(['Localizator' => 'Localizator'], $enJsonContents);
        self::assertSame(['Localizator' => ''], $deJsonContents);

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
        self::assertSame([
            'failed' => '',
            'password' => '',
            'throttle' => '',
        ], $enDefaultContents);
        self::assertSame([
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
        self::assertSame([
            'failed' => '',
            'password' => 'Das eingegebene Passwort ist nicht korrekt.',
            'throttle' => '',
        ], $enDefaultContents);
        self::assertSame([
            'Cancel' => '',
            'Delete' => 'Löschen',
            'Login' => 'Anmelden',
        ], $enJsonContents);

        // Cleanup.
        self::flushDirectories('lang', 'views');
    }

    /**
     * @return void
     */
    public function testLocalizeCommandWhereKeysAreEscapedWithSlashes(): void
    {
        $this->createTestView("{{ __('Amir\'s PC') }} {{ __('Jacob\'s Ladder') }} {{ __('mom\'s spaghetti') }}");

        // Run localize command.
        $this->artisan('localize')
            ->assertExitCode(0);

        // Do created locale files exist?
        self::assertJsonLangFilesExist('en');

        // Do their contents match the expected results?
        $enJsonContents = $this->getJsonLangContents('en');

        // Did it sort the translation keys like we expected?
        self::assertSame([
            'Amir\'s PC' => 'Amir\'s PC',
            'Jacob\'s Ladder' => 'Jacob\'s Ladder',
            'mom\'s spaghetti' => 'mom\'s spaghetti',
        ], $enJsonContents);

        // Cleanup.
        self::flushDirectories('lang', 'views');
    }

    public function testLocalizeCommandReturnsNonZeroCodeWhenCiFlagIsOnAndSomeMissingTranslationsHaveBeenAdded(): void
    {
        $this->createTestView("{{ __('Hi mom') }}");

        // Run localize command.
        $this->artisan('localize', ['--ci' => true])
            ->assertExitCode(1);

        // Cleanup.
        self::flushDirectories('lang', 'views');
    }

    public function testLocalizeCommandReturnsZeroCodeWhenCiFlagIsOnAndNoMissingTranslationsHaveBeenFound(): void
    {
        $this->createTestView("");

        // Run localize command.
        $this->artisan('localize', ['--ci' => true])
            ->assertExitCode(0);

        // Cleanup.
        self::flushDirectories('lang', 'views');
    }

    public function testLocalizeCommandReturnsNonZeroCodeThenZeroCodeIfFileDidNotChangedBetweenTwoRun(): void
    {
        $this->createTestView("{{ __('Hi mom') }}");

        // Run localize command.
        $this->artisan('localize', ['--ci' => true])
            ->assertExitCode(1);

        // Run localize command.
        $this->artisan('localize', ['--ci' => true])
            ->assertExitCode(0);

        // Cleanup.
        self::flushDirectories('lang', 'views');
    }
}

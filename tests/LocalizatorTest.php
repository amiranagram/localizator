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

    public function testLocalizeCommandWithMultilineMessages(): void
    {
        $this->createTestView("__(\n'stand with ukraine'\n)");

        // Run localize command.
        $this->artisan('localize')
            ->assertExitCode(0);

        // Do created locale files exist?
        self::assertJsonLangFilesExist('en');

        // Do their contents match the expected results?
        $enJsonContents = $this->getJsonLangContents('en');

        // Did it sort the translation keys like we expected?
        self::assertSame([
            'stand with ukraine' => 'stand with ukraine',
        ], $enJsonContents);

        // Cleanup.
        self::flushDirectories('lang', 'views');
    }

    public function testLocalizeCommandWithMultilineMessagesAndSpaces(): void
    {
        $this->createTestView("{{ __(\n   'stand with ukraine'   \n) }}");

        // Run localize command.
        $this->artisan('localize')
            ->assertExitCode(0);

        // Do created locale files exist?
        self::assertJsonLangFilesExist('en');

        // Do their contents match the expected results?
        $enJsonContents = $this->getJsonLangContents('en');

        // Did it sort the translation keys like we expected?
        self::assertSame([
            'stand with ukraine' => 'stand with ukraine',
        ], $enJsonContents);

        // Cleanup.
        self::flushDirectories('lang', 'views');
    }

    public function testIntTranslationKeysAreBeingSavedAsStrings(): void
    {
        $this->createTestView("{{ __('errors.401.title') }}<br/>{{ __('errors.401.message') }}");

        $this->createTestDefaultLangFile([
            '401' => [
                'title' => '401 - Unauthorized',
                'message' => 'Sorry, you are not authorized to view this page.',
            ],
            '404' => [
                'title' => '404 - Page Not Found',
                'message' => 'Sorry, we couldn\'t find this page.',
            ],
        ], 'errors', 'en');

        config(['localizator.sort' => false]);

        // Run localize command.
        $this->artisan('localize', ['lang' => 'en'])
            ->assertExitCode(0);

        // Do created locale files exist?
        self::assertDefaultLangFilesExist(['en'], ['errors']);

        // Get exported contents.
        $path = $this->getLangFilePath('en'.DIRECTORY_SEPARATOR.'errors.php');
        $contents = file_get_contents($path);
        $expected = <<<'PHP'
<?php

return [
    '401' => [
        'title' => '401 - Unauthorized',
        'message' => 'Sorry, you are not authorized to view this page.',
    ],
    '404' => [
        'title' => '404 - Page Not Found',
        'message' => 'Sorry, we couldn\'t find this page.',
    ],
];

PHP;

        $this->assertSame(preg_replace('/\r\n|\r|\n/', "\n", $expected), $contents);
    }

    public function testIntTranslationNestedKeysAreBeingSavedAsStrings(): void
    {
        $this->createTestView("{{ __('errors.4.401') }}<br/>{{ __('errors.4.404') }}");

        $this->createTestDefaultLangFile([
            '4' => [
                '401' => '401 - Unauthorized',
                '404' => '404 - Page Not Found',
            ],
        ], 'errors', 'en');

        config(['localizator.sort' => false]);

        // Run localize command.
        $this->artisan('localize', ['lang' => 'en'])
            ->assertExitCode(0);

        // Do created locale files exist?
        self::assertDefaultLangFilesExist(['en'], ['errors']);

        // Get exported contents.
        $path = $this->getLangFilePath('en'.DIRECTORY_SEPARATOR.'errors.php');
        $contents = file_get_contents($path);
        $expected = <<<'PHP'
<?php

return [
    '4' => [
        '401' => '401 - Unauthorized',
        '404' => '404 - Page Not Found',
    ],
];

PHP;

        $this->assertSame(preg_replace('/\r\n|\r|\n/', "\n", $expected), $contents);
    }

    public function testRemoveMissingKeys(): void
    {
        self::flushDirectories('lang', 'views');

        $this->createTestDefaultLangFile([
            'missingstring' => 'Missing',
            'name' => 'Name',
        ], 'app', 'en');
        $this->createTestJsonLangFile([
            'Login' => 'Login',
            'Missing' => 'Missing',
        ], 'en');

        $enDefaultContents = $this->getDefaultLangContents('en', 'app');
        $enJsonContents = $this->getJsonLangContents('en');
        self::assertSame(['missingstring' => 'Missing', 'name' => 'Name'], $enDefaultContents);
        self::assertSame(['Login' => 'Login', 'Missing' => 'Missing'], $enJsonContents);

        $this->createTestView("{{ __('Login') }} {{ __('app.name') }}");

        // Run the command with the option to remove keys/strings that are not present anymore
        $this->artisan('localize en --remove-missing')
            ->assertExitCode(0);

        // Do created locale files exist?
        self::assertDefaultLangFilesExist('en', ['app']);
        self::assertJsonLangFilesExist('en');

        // Do their contents match the expected results?
        $enDefaultContents = $this->getDefaultLangContents('en', 'app');
        $enJsonContents = $this->getJsonLangContents('en');
        self::assertSame(['name' => 'Name'], $enDefaultContents);
        self::assertSame(['Login' => 'Login'], $enJsonContents);

        // Cleanup.
        self::flushDirectories('lang', 'views');
    }
}

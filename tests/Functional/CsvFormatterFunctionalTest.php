<?php

namespace AlexSkrypnyk\Shellvar\Tests\Functional;

/**
 * Class CsvFormatterFunctionalTest.
 *
 * Functional tests for extractions.
 *
 * @group scripts
 *
 * @covers \AlexSkrypnyk\Shellvar\Command\ExtractCommand
 * @covers \AlexSkrypnyk\Shellvar\Formatter\CsvFormatter
 *
 * phpcs:disable Drupal.Commenting.DocComment.MissingShort
 * phpcs:disable Drupal.Commenting.FunctionComment.Missing
 */
class CsvFormatterFunctionalTest extends FormatterFunctionalTestCase {

  public static function dataProviderFormatter() : array {
    return [
      // Extract all variables.
      [
        [
          '--exclude-local' => TRUE,
          '--sort' => TRUE,
          'paths' => [self::fixtureFile('test-data.sh')],
        ],
        <<<'EOD'
              Name;"Default value";Description
              VAR1;UNSET;
              VAR10;val10;"Description without a leading space."
              VAR11;val11;"Description without a leading space that goes on multiple lines and has a `VAR7`, `$VAR8`, $VAR9, VAR10 and VAR12 variable reference."
              VAR12;val12;"Description without a leading space that goes on multiple lines.
              
              And has a comment with no content."
              VAR13;val13;"And has an empty line before it without a content."
              VAR14;val14;
              VAR15;val16;
              VAR17;val17;
              VAR2;val2;
              VAR3;val3;
              VAR33;VAR32;
              VAR34;UNSET;
              VAR4;val4;
              VAR5;abc;
              VAR6;VAR5;
              VAR7;VAR5;
              VAR8;val8;
              VAR9;val9;"Description with leading space."
              VARENV1;valenv1;
              VARENV2;UNSET;
              VARENV3;valenv3;"Comment from script with reference to composer.lock and `composer.lock` again and somespecialtoken and `somespecialtoken` again and testorg/test-package and `testorg/test-package` again."
              VARENV4;UNSET;"Comment 2 from script without a leading space that goes on multiple lines."
              EOD,
      ],

      // Extract all variables, custom header.
      [
        [
          '--exclude-local' => TRUE,
          '--sort' => TRUE,
          '--fields' => 'name=Name;path=Path',
          // @phpstan-ignore-next-line
          '--path-strip-prefix' => dirname(realpath(__DIR__ . '/..')),
          'paths' => [
            self::fixtureFile('test-data.bash'),
            self::fixtureFile('test-data.sh'),
          ],
        ],
        <<<'EOD'
              Name;Path
              VAR1;/tests/Fixtures/test-data.sh
              VAR10;/tests/Fixtures/test-data.sh
              VAR11;/tests/Fixtures/test-data.bash
              VAR12;/tests/Fixtures/test-data.sh
              VAR13;/tests/Fixtures/test-data.sh
              VAR14;/tests/Fixtures/test-data.sh
              VAR15;/tests/Fixtures/test-data.sh
              VAR17;/tests/Fixtures/test-data.sh
              VAR2;/tests/Fixtures/test-data.bash
              VAR3;/tests/Fixtures/test-data.sh
              VAR33;/tests/Fixtures/test-data.sh
              VAR34;/tests/Fixtures/test-data.sh
              VAR4;/tests/Fixtures/test-data.sh
              VAR5;/tests/Fixtures/test-data.sh
              VAR6;/tests/Fixtures/test-data.sh
              VAR7;/tests/Fixtures/test-data.sh
              VAR8;/tests/Fixtures/test-data.sh
              VAR9;/tests/Fixtures/test-data.sh
              VARENV1;/tests/Fixtures/test-data.sh
              VARENV2;/tests/Fixtures/test-data.sh
              VARENV3;/tests/Fixtures/test-data.sh
              VARENV4;/tests/Fixtures/test-data.sh
              EOD,
      ],

      // Filter-out variables by exclude file.
      [
        [
          '--exclude-local' => TRUE,
          '--exclude-from-file' => [self::fixtureFile('test-data-excluded.txt')],
          '--sort' => TRUE,
          'paths' => [self::fixtureFile('test-data.sh')],
        ],
        <<<'EOD'
        Name;"Default value";Description
        VAR1;UNSET;
        VAR10;val10;"Description without a leading space."
        VAR11;val11;"Description without a leading space that goes on multiple lines and has a `VAR7`, `$VAR8`, $VAR9, VAR10 and VAR12 variable reference."
        VAR12;val12;"Description without a leading space that goes on multiple lines.

        And has a comment with no content."
        VAR13;val13;"And has an empty line before it without a content."
        VAR15;val16;
        VAR2;val2;
        VAR3;val3;
        VAR33;VAR32;
        VAR34;UNSET;
        VAR4;val4;
        VAR5;abc;
        VAR6;VAR5;
        VAR7;VAR5;
        VAR8;val8;
        VAR9;val9;"Description with leading space."
        VARENV1;valenv1;
        VARENV2;UNSET;
        VARENV3;valenv3;"Comment from script with reference to composer.lock and `composer.lock` again and somespecialtoken and `somespecialtoken` again and testorg/test-package and `testorg/test-package` again."
        VARENV4;UNSET;"Comment 2 from script without a leading space that goes on multiple lines."
        EOD,
      ],

      // Filter-out variables by prefix.
      [
        [
          '--exclude-local' => TRUE,
          '--exclude-from-file' => [self::fixtureFile('test-data-excluded.txt')],
          '--exclude-prefix' => ['VAR1'],
          '--sort' => TRUE,
          'paths' => [self::fixtureFile('test-data.sh')],
        ],
        <<<'EOD'
        Name;"Default value";Description
        VAR2;val2;
        VAR3;val3;
        VAR33;VAR32;
        VAR34;UNSET;
        VAR4;val4;
        VAR5;abc;
        VAR6;VAR5;
        VAR7;VAR5;
        VAR8;val8;
        VAR9;val9;"Description with leading space."
        VARENV1;valenv1;
        VARENV2;UNSET;
        VARENV3;valenv3;"Comment from script with reference to composer.lock and `composer.lock` again and somespecialtoken and `somespecialtoken` again and testorg/test-package and `testorg/test-package` again."
        VARENV4;UNSET;"Comment 2 from script without a leading space that goes on multiple lines."
        EOD,
      ],

      // Extract all variables from a directory.
      [
        [
          '--exclude-local' => TRUE,
          '--sort' => TRUE,
          'paths' => [self::fixtureDir() . '/dir'],
        ],
        <<<'EOD'
        Name;"Default value";Description
        VAR1;UNSET;
        VAR10;val10;"Description without a leading space."
        VAR11;val11bash;"Description from bash without a leading space that goes on multiple lines."
        VAR12;val12;"Description without a leading space that goes on multiple lines.

        And has a comment with no content."
        VAR13;val13;"And has an empty line before it without a content."
        VAR14;val14;
        VAR15;val16;
        VAR17;val17;
        VAR2;val2bash;
        VAR3;val3;
        VAR33;VAR32;
        VAR34;UNSET;
        VAR4;val4;
        VAR5;abc;
        VAR6;VAR5;
        VAR7;VAR5;
        VAR8;val8;
        VAR9;val9;"Description with leading space."
        VARENV1;valenv1_dotenv;
        VARENV2;UNSET;
        VARENV3;valenv3-dotenv;"Comment from script."
        VARENV4;UNSET;"Comment 2 from .env without a leading space that goes on multiple lines."
        EOD,
      ],

      // Extract all variables from multiple files.
      [
        [
          '--exclude-local' => TRUE,
          '--sort' => TRUE,
          'paths' => [
            self::fixtureFile('test-data.bash'),
            self::fixtureFile('test-data.sh'),
          ],
        ],
        <<<'EOD'
        Name;"Default value";Description
        VAR1;UNSET;
        VAR10;val10;"Description without a leading space."
        VAR11;val11bash;"Description from bash without a leading space that goes on multiple lines."
        VAR12;val12;"Description without a leading space that goes on multiple lines.

        And has a comment with no content."
        VAR13;val13;"And has an empty line before it without a content."
        VAR14;val14;
        VAR15;val16;
        VAR17;val17;
        VAR2;val2bash;
        VAR3;val3;
        VAR33;VAR32;
        VAR34;UNSET;
        VAR4;val4;
        VAR5;abc;
        VAR6;VAR5;
        VAR7;VAR5;
        VAR8;val8;
        VAR9;val9;"Description with leading space."
        VARENV1;valenv1;
        VARENV2;UNSET;
        VARENV3;valenv3;"Comment from script with reference to composer.lock and `composer.lock` again and somespecialtoken and `somespecialtoken` again and testorg/test-package and `testorg/test-package` again."
        VARENV4;UNSET;"Comment 2 from script without a leading space that goes on multiple lines."
        EOD,
      ],
    ];
  }

}

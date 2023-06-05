<?php

declare(strict_types=1);

/*
 * This file is part of the gilbertsoft/coding-standards package.
 *
 * (c) Gilbertsoft <gilbertsoft.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace Gilbertsoft\CodingStandards\EventListener;

use Gilbertsoft\CodingStandards\License;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CodingStandards\Console\Event\Application\InitTemplatesDirsEvent;
use TYPO3\CodingStandards\Console\Event\Command\ConfigureEvent;
use TYPO3\CodingStandards\Console\Event\Command\Setup\ConfigureEvent as SetupConfigureEvent;
use TYPO3\CodingStandards\Console\Event\Command\Setup\ExecuteEvent as SetupExecuteEvent;
use TYPO3\CodingStandards\Events;
use TYPO3\CodingStandards\Plugin\PluginInterface;
use TYPO3\CodingStandards\Setup;

use function dirname;
use function in_array;
use function str_replace;

use const true;

final class SetupSubscriber implements PluginInterface
{
    /**
     * @var string
     */
    public const RULE_SET_LICENSE = 'license';

    public static function create(): self
    {
        return new self();
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            Events::APPLICATION_INIT_TEMPLATES_DIRS => 'onInitTemplatesDirs',
            Events::COMMAND_CONFIGURE => 'onCommandConfigure',
            Events::COMMAND_SETUP_CONFIGURE => 'onSetupConfigure',
            Events::COMMAND_SETUP_EXECUTE => 'onSetupExecute',
        ];
    }

    public function onInitTemplatesDirs(InitTemplatesDirsEvent $initTemplatesDirsEvent): void
    {
        $initTemplatesDirsEvent->addTemplatesDir(dirname(__DIR__, 2) . '/templates');
    }

    public function onCommandConfigure(ConfigureEvent $configureEvent): void
    {
        $configureEvent->getCommand()->setDescription(str_replace(
            'TYPO3',
            'Gilbertsoft',
            $configureEvent->getCommand()->getDescription()
        ));
    }

    public function onSetupConfigure(SetupConfigureEvent $setupConfigureEvent): void
    {
        $setupConfigureEvent->addAdditionalRuleSets([self::RULE_SET_LICENSE]);
        $setupConfigureEvent->addDefaultRuleSet(self::RULE_SET_LICENSE);
        $setupConfigureEvent->getCommand()->addOption(
            'license',
            'l',
            InputOption::VALUE_REQUIRED,
            sprintf(
                'License to set up, valid types are <comment>["%s"]</comment>. If not set, the detection is automatic',
                implode('","', License::LICENSES)
            )
        );
    }

    public function onSetupExecute(SetupExecuteEvent $setupExecuteEvent): void
    {
        if (!in_array(self::RULE_SET_LICENSE, $setupExecuteEvent->getRuleSets(), true)) {
            return;
        }

        $application = $setupExecuteEvent->getCommand()->getApplication();

        $license = new License(
            $application->getTargetDir($setupExecuteEvent->getInput()),
            $application->getTemplatesDirs(),
            new SymfonyStyle($setupExecuteEvent->getInput(), $setupExecuteEvent->getOutput())
        );

        $result = $license->copy(
            $setupExecuteEvent->getForce(),
            $setupExecuteEvent->getType() === Setup::EXTENSION ? License::TYPO3_GPL_3_0_OR_LATER : License::MIT
        );

        $setupExecuteEvent->setExitCode($result ? Command::SUCCESS : Command::FAILURE);
    }
}

<?php

declare(strict_types=1);

namespace ILIAS\Plugin\Flashcards\Setup;

use ILIAS\Setup;
use ILIAS\Setup\Config;
use ILIAS\Setup\Agent;
use ILIAS\Setup\Objective;
use ILIAS\Refinery\Transformation;
use ILIAS\Setup\Metrics;
use ILIAS\Setup\ObjectiveConstructor;
use ILIAS\Setup\NullConfig;
use LogicException;

/**
 * New SetupAgent for the plugin LongEssayAssessment
 * Provides new DBUpdateSteps from Version 4 (ILIAS 10) onwards.
 */
class SetupAgent implements Agent
{
    public function __construct()
    {
    }

    /**
     * @inheritdoc
     */
    public function hasConfig(): bool
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function getArrayToConfigTransformation(): Transformation
    {
        throw new LogicException(self::class . " has no Config.");
    }

    /**
     * @inheritdoc
     */
    public function getInstallObjective(Config $config = null): Objective
    {
        return new Objective\NullObjective();
    }

    /**
     * @inheritdoc
     */
    public function getUpdateObjective(Config $config = null): Objective
    {

        return new Objective\NullObjective();

    }

    /**
     * @inheritdoc
     */
    public function getBuildObjective(): Objective
    {
        return new Setup\ObjectiveCollection(
            'ILIAS\Plugin\Flashcards',
            true,
            new ResourcesCopiedObjective()
        );
    }

    /**
     * @inheritdoc
     */
    public function getStatusObjective(Metrics\Storage $storage): Objective
    {
        return new Objective\NullObjective();
    }

    /**
     * @inheritdoc
     */
    public function getMigrations(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getNamedObjectives(?Config $config = null): array
    {
        return [];
    }
}

<?php

namespace ILIAS\Plugin\Flashcards\Setup;

use ILIAS\Setup;
use ILIAS\Setup\Environment;

class ResourcesCopiedObjective implements Setup\Objective
{
    public const ILIAS_ROOT = __DIR__ . "/../../../../../../../../../..";

    public function getHash(): string
    {
        return hash("sha256", self::class);
    }

    public function getLabel(): string
    {
        return "The public folder is populated with Flashcards assets.";
    }

    public function isNotable(): bool
    {
        return true;
    }

    public function getPreconditions(Environment $environment): array
    {
        return [
            new \ILIAS\Component\Setup\PublicAssetsBuildObjective(new \ILIAS\Component\Resource\PublicAssetManager(), [])
        ];
    }

    public function achieve(Environment $environment): Environment
    {
        $root = self::ILIAS_ROOT;

        $source = "$root/public/Customizing/global/plugins/Services/Repository/RepositoryObject/Flashcards/resources";
        $dest = "$root/public/assets";

        if (is_dir("$dest/css")) {
            copy ("$source/flashcards.css", "$dest/css/flashcards.css");
        }
        if (is_dir("$dest/images/standard")) {
            copy ("$source/icon_xflc.svg", "$dest/images/standard/icon_xflc.svg");
        }

        return $environment;
    }

    public function isApplicable(Environment $environment): bool
    {
        return true;
    }
}

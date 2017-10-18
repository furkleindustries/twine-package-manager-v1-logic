<?php
namespace TwinePM\SqlAbstractions\Versions;

use TwinePM\SqlAbstractions\Accounts\IAccount;
use TwinePM\SqlAbstractions\ISqlAbstraction;
use TwinePM\SqlAbstractions\Packages\IPackage;
interface IVersion extends ISqlAbstraction {
    const DATA_LEVELS = [
        "metadata",
        "full",
    ];

    const DEFAULTS = [
        "js" => "",
        "css" => "",
        "description" => "",
        "homepage" => "",
        "tag" => "",
        "packageDataLevel" => "metadata",
    ];

    function getPackage(): IPackage;

    function getOwner(): IAccount;

    function getPackageId(): int;

    function getName(): string;

    function getGlobalVersionId(): ?int;

    function getJs(): ?string;
    function setJs(?string $js): void;

    function getCss(): ?string;
    function setCss(?string $css): void;

    function getDescription(): string;
    function setDescription(string $description): void;

    function getHomepage(): ?string;
    function setHomepage(?string $homepage): void;

    function getVersion(): string;
    function setVersion(string $version): void;

    function getTag(): ?string;
    function setTag(?string $tag): void;

    function getAuthorId(): int;

    function getTimeCreated(): ?int;
}
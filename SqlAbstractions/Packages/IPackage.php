<?php
namespace TwinePM\SqlAbstractions\Packages;

use TwinePM\SqlAbstractions\Accounts\IAccount;
use TwinePM\SqlAbstractions\ISqlAbstraction;
use TwinePM\SqlAbstractions\Versions\IVersion;
interface IPackage extends ISqlAbstraction {
    const TYPES = [
        "macro",
        "script",
        "style",
        "passagetheme",
        "storytheme",
    ];

    function getOwner(): IAccount;

    function getCurrentVersionAbstraction(): IVersion;
    
    function getVersions(string $dataLevel): array;

    function getId(): ?int;

    function getOwnerId(): int;
    function setOwnerId(int $id): void;

    function getAuthorId(): int;

    function getName(): string;
    function setName(string $name): void;

    function getType(): string;
    function setType(string $type): void;

    function getCurrentVersion(): string;
    function setCurrentVersion(string $currentVersion): void;

    function getDescription(): string;
    function setDescription(string $description): void;

    function getHomepage(): string;
    function setHomepage(string $homepage): void;
}
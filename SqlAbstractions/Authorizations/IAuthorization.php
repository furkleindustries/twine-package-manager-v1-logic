<?php
namespace TwinePM\SqlAbstractions\Authorizations;

use TwinePM\SqlAbstractions\Accounts\IAccount;
use TwinePM\SqlAbstractions\Credentials\ICredential;
use TwinePM\SqlAbstractions\ISqlAbstraction;
interface IAuthorization extends ISqlAbstraction {
    function getAccount(): IAccount;
    function getCredential(): ICredential;
    function getClientObject(): ?array;
    
    function getGlobalAuthorizationId(): ?int;
    function getUserId(): int;
    function getClient(): string;
    function getScopes(): array;
    function getOAuthToken(): string;
    function getTimeCreated(): ?int;
    function getIp(): string;
}
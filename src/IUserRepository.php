<?php

namespace PHPAide\User;

interface IUserRepository {
	public function getFromUsername( string $username ): ?IUser;

	public function getFromEmail( string $email ): ?IUser;

	public function getFromId( int $id ): ?IUser;

	public function saveUser( IUser $user ): bool;
}

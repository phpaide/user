<?php

namespace PHPAide\User;

interface IUserRepository {
	public function getFromUsername( string $username ): ?IUser;

	public function getFromId( int $id ): ?IUser;

	public function insertUser( IUser $user ): int;

	public function updateUser( IUser $user ): bool;

	public function setPassword( IUser $user, string $password ): bool;

	public function getPassword( IUser $user );
}

<?php

namespace PHPAide\User;

interface IUserRepository {
	/**
	 * @param string $username
	 * @return IUser|null
	 */
	public function getFromUsername( string $username ): ?IUser;

	/**
	 * @param string $email
	 * @return IUser|null
	 */
	public function getFromEmail( string $email ): ?IUser;

	/**
	 * @param int $id
	 * @return IUser|null
	 */
	public function getFromId( int $id ): ?IUser;

	/**
	 * @param IUser $user
	 * @return int
	 */
	public function insertUser( IUser $user ): int;

	/**
	 * @param IUser $user
	 * @return bool
	 */
	public function updateUser( IUser $user ): bool;

	/**
	 * @param IUser $user
	 * @param string $password
	 * @return bool
	 */
	public function setPassword( IUser $user, string $password ): bool;

	/**
	 * @param IUser $user
	 * @return mixed
	 */
	public function getPassword( IUser $user );
}

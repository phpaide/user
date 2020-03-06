<?php

namespace PHPAide\User;

use Exception;

class UserManager {
	/** @var IUserRepository */
	protected $repo;

	/** @var array */
	protected $passwordPolicy;
	/** @var array */
	protected $usernamePolicy;

	/**
	 * @param IUserRepository $repo
	 * @param array|null $options Array of options for user handling
	 * Default : [
	 * 		'passwordPolicy' => [
	 *			'pattern' => '/(?=.{8,})(?=.*[A-Z])/'
	 *		],
	 *		'usernamePolicy' => [
	 * 			'pattern' => '/(?=.{8,})/'
	 *		]
	 * ]
	 */
	public function __construct( IUserRepository $repo, array $options = null ) {
		$this->repo = $repo;

		if ( $options !== null ) {
			$this->setOptions( $options );
		}
	}

	/**
	 * @param string $username
	 * @return IUser|null if username is not valid
	 * @throws Exception
	 */
	public function getUserByUsername( string $username ): ?IUser {
		if ( !$this->checkUsername( $username ) ) {
			return null;
		}
		$user = $this->repo->getFromUsername( $username );

		return $user ?? new User( $username );
	}

	/**
	 * @param int $id
	 * @return IUser|null if user does not exist
	 */
	public function getUserById( int $id ): ?IUser {
		return $this->repo->getFromId( $id );
	}

	/**
	 * Check if given username is taken
	 *
	 * @param string $username
	 * @return bool
	 * @throws Exception
	 */
	public function usernameExists( string $username ) {
		return $this->getUserByUsername( $username )->exists();
	}

	/**
	 * Check if given email exists in the system
	 *
	 * @param string $email
	 * @return bool
	 */
	public function emailExists( string $email ) {
		return $this->repo->getFromEmail( $email ) !== null;
	}

	/**
	 * Persist user to the database
	 *
	 * @param IUser $user
	 * @return IUser|null User with ID, or null if user cannot be saved
	 * @throws Exception
	 */
	public function saveUser( IUser $user ): ?IUser {
		if ( $user->exists() ) {
			return $this->repo->updateUser( $user ) ? $user : null;
		}
		$id = $this->repo->insertUser( $user );

		if ( !$id ) {
			throw new Exception( 'Cannot save user: ' . $user->getName() );
		}

		return $user->cloneWithId( $id );
	}

	/**
	 * Validate and set user password - with persisting it
	 *
	 * @param IUser $user
	 * @param string $password
	 * @throws Exception
	 */
	public function setUserPassword( IUser $user, string $password ) {
		if ( !$user->exists() ) {
			throw new Exception( 'Cannot set a password to non-existing user' );
		}

		if ( !$this->passwordValid( $password ) ) {
			throw new Exception( "Trying to set invalid password" );
		}

		$hash = md5( $password );
		$this->repo->setPassword( $user, $hash );
	}

	/**
	 * Check if given password matches the user password
	 *
	 * @param IUser $user
	 * @param string $password
	 * @return bool
	 * @throws Exception
	 */
	public function verifyPassword( IUser $user, string $password ): bool {
		$savedHash = $this->repo->getPassword( $user );
		if ( !$savedHash ) {
			throw new Exception( 'User ' . $user->getName() . ' does not have a password saved' );
		}

		return $this->passwordValid( $password ) && $savedHash === md5( $password );
	}

	/**
	 * @param string $username
	 * @return bool
	 */
	public function checkUsername( string $username ) {
		return (bool) preg_match( $this->usernamePolicy['pattern'], $username );
	}

	/**
	 * Check if given password fulfills password requirements
	 *
	 * @param $password
	 * @return bool
	 */
	public function passwordValid( $password ) {
		return (bool) preg_match( $this->passwordPolicy['pattern'], $password );
	}

	/**
	 * @param array $options
	 */
	private function setOptions( array $options ) {
		$defaults = [
			'passwordPolicy' => [
				'pattern' => '/(?=.{8,})(?=.*[A-Z])/'
			],
			'usernamePolicy' => [
				'pattern' => '/(?=.{8,})/'
			]
		];

		$options = array_replace_recursive( $defaults, $options );

		$this->passwordPolicy = $options['passwordPolicy'];
		$this->usernamePolicy = $options['usernamePolicy'];
	}

	/**
	 * Verify username is correct
	 *
	 * @param string $username
	 * @throws Exception
	 */
	private function verifyUsername( string $username ) {
		if ( !$this->checkUsername( $username ) ) {
			throw new Exception( 'Username ' . $username . ' is not valid' );
		}
	}
}
